<?php

namespace App\Services\Reportes;

use App\Models\Empresa;
use App\Models\Equipo;
use App\Models\Impresora;
use App\Models\Sede;
use App\Models\User;
use App\Support\Roles;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Arma los datos del reporte de inventario (equipos + impresoras)
 * respetando filtros y alcance del usuario. Lo consumen el Excel y el PDF.
 */
class InventarioReporteService
{
    private ?Collection $equiposCache = null;
    private ?Collection $impresorasCache = null;

    public function __construct(
        private User $usuario,
        private array $filtros,
    ) {}

    public static function para(User $usuario, array $filtros): self
    {
        return new self($usuario, $filtros);
    }

    public function incluye(string $tipo): bool
    {
        return in_array($tipo, $this->filtros['tipos'] ?? [], true);
    }

    // ─────────────────────────────────────────────────────────────────────
    //  Datos
    // ─────────────────────────────────────────────────────────────────────

    public function equipos(): Collection
    {
        if (! $this->incluye('equipos')) {
            return collect();
        }

        return $this->equiposCache ??= $this->consultaEquipos()
            ->with(['empresa:id,nombre', 'sede:id,nombre', 'responsable:id,name'])
            ->get()
            ->sortBy(fn (Equipo $e) => [$e->empresa?->nombre, $e->sede?->nombre, $e->categoria, $e->nombre])
            ->values();
    }

    public function impresoras(): Collection
    {
        if (! $this->incluye('impresoras')) {
            return collect();
        }

        return $this->impresorasCache ??= $this->consultaImpresoras()
            ->with(['modelo', 'empresa:id,nombre', 'sede:id,nombre', 'consumiblesActivos'])
            ->get()
            ->sortBy(fn (Impresora $i) => [$i->empresa?->nombre, $i->sede?->nombre, $i->nombre_visible])
            ->values();
    }

    /**
     * Métricas agregadas para los KPIs y gráficos del PDF.
     * Los conteos vienen con la clave cruda (ej: 'activo'); las etiquetas y colores
     * los resuelve la vista.
     */
    public function resumen(): array
    {
        $equipos    = $this->equipos();
        $impresoras = $this->impresoras();

        $consumiblesAlerta = $impresoras
            ->flatMap(fn (Impresora $imp) => $imp->consumiblesActivos
                ->filter(fn ($c) => in_array($c->estado_nivel, ['critico', 'agotado'], true))
                ->map(fn ($c) => ['impresora' => $imp, 'consumible' => $c]))
            ->sortBy(fn ($fila) => $fila['consumible']->nivel_estimado)
            ->values();

        $porEmpresa = $this->porEmpresa($equipos, $impresoras);

        return [
            'total_equipos'             => $equipos->count(),
            'total_impresoras'          => $impresoras->count(),
            'equipos_por_estado'        => $equipos->countBy('estado')->sortDesc()->all(),
            'equipos_por_categoria'     => $equipos->countBy('categoria')->sortDesc()->all(),
            'impresoras_por_estado'     => $impresoras->countBy('estado')->sortDesc()->all(),
            'impresoras_por_tecnologia' => $impresoras
                ->countBy(fn (Impresora $i) => $i->modelo?->tipo_tecnologia ?? 'sin_modelo')
                ->sortDesc()->all(),
            'por_empresa'               => $porEmpresa,
            'empresas_incluidas'        => count($porEmpresa),
            'consumibles_alerta'        => $consumiblesAlerta,
            'mantenimientos_vencidos'   => $impresoras
                ->filter(fn (Impresora $i) => $i->mantenimientoVencido())
                ->sortBy('proximo_mantenimiento_sugerido')
                ->values(),
        ];
    }

    /**
     * Descripción legible de los filtros aplicados (encabezado del PDF).
     */
    public function descripcionFiltros(): array
    {
        $tipos = collect($this->filtros['tipos'])
            ->map(fn ($t) => $t === 'equipos' ? 'Equipos' : 'Impresoras')
            ->implode(' + ');

        return array_filter([
            'Inventario' => $tipos,
            'Empresa'    => $this->filtros['empresa_id']
                ? Empresa::find($this->filtros['empresa_id'])?->nombre
                : 'Todas las de mi alcance',
            'Sede'       => $this->filtros['sede_id']
                ? Sede::find($this->filtros['sede_id'])?->nombre
                : null,
            'Categoría'  => ($this->incluye('equipos') && $this->filtros['categoria'])
                ? (Equipo::CATEGORIAS[$this->filtros['categoria']] ?? $this->filtros['categoria'])
                : null,
            'Dados de baja' => $this->filtros['incluir_bajas'] ? 'Incluidos' : 'Excluidos',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    //  Helpers de formato (compartidos por Excel y PDF)
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Convierte el JSON de especificaciones en texto: "Procesador: i5 · RAM (GB): 16"
     */
    public static function resumenEspecificaciones(Equipo $equipo): string
    {
        $schema = Equipo::ESPECIFICACIONES_SCHEMA[$equipo->categoria] ?? [];

        return collect($equipo->especificaciones ?? [])
            ->filter(fn ($valor) => $valor !== null && $valor !== '')
            ->map(fn ($valor, $clave) => ($schema[$clave]['label'] ?? $clave) . ': ' . $valor)
            ->implode(' · ');
    }

    /**
     * Consumibles activos en texto: "Tóner — Negro 45% · Tambor S/I"
     */
    public static function resumenConsumibles(Impresora $impresora): string
    {
        return $impresora->consumiblesActivos
            ->map(fn ($c) => $c->descripcion_corta . ' ' .
                ($c->nivel_estimado === null ? 'S/I' : $c->nivel_estimado . '%'))
            ->implode(' · ');
    }

    /**
     * Empresas que el usuario puede incluir en el reporte.
     */
    public static function empresasDisponiblesPara(User $usuario): Collection
    {
        if (Roles::usuarioCumple($usuario, 'esTransversal')) {
            return Empresa::activas()->orderBy('nombre')->get(['id', 'nombre']);
        }

        if (Roles::usuarioCumple($usuario, 'requiereCobertura')) {
            $ids = DB::table('empresa_user')
                ->where('user_id', $usuario->id)
                ->distinct()
                ->pluck('empresa_id');

            return Empresa::activas()->whereIn('id', $ids)->orderBy('nombre')->get(['id', 'nombre']);
        }

        if (Roles::usuarioCumple($usuario, 'requiereEmpresaPrincipal') && $usuario->empresa_id) {
            return Empresa::where('id', $usuario->empresa_id)->get(['id', 'nombre']);
        }

        return collect();
    }

    // ─────────────────────────────────────────────────────────────────────
    //  Consultas con alcance y filtros
    // ─────────────────────────────────────────────────────────────────────

    private function consultaEquipos(): Builder
    {
        $query = Equipo::query();

        // Alcance por rol
        if (Roles::usuarioCumple($this->usuario, 'esTransversal')) {
            // sin restricción
        } elseif (Roles::usuarioCumple($this->usuario, 'requiereCobertura')) {
            $query->accesiblesParaTecnico($this->usuario);
        } elseif (Roles::usuarioCumple($this->usuario, 'requiereEmpresaPrincipal') && $this->usuario->empresa_id) {
            $query->where('empresa_id', $this->usuario->empresa_id);
        } else {
            $query->whereRaw('1 = 0');
        }

        return $query
            ->when($this->filtros['empresa_id'], fn ($q, $id) => $q->where('empresa_id', $id))
            ->when($this->filtros['sede_id'],    fn ($q, $id) => $q->where('sede_id', $id))
            ->when($this->filtros['categoria'],  fn ($q, $cat) => $q->where('categoria', $cat))
            ->when(! $this->filtros['incluir_bajas'], fn ($q) => $q->where('estado', '!=', 'dado_de_baja'));
    }

    private function consultaImpresoras(): Builder
    {
        $query = Impresora::query();

        if (Roles::usuarioCumple($this->usuario, 'esTransversal')) {
            // sin restricción
        } elseif (Roles::usuarioCumple($this->usuario, 'requiereCobertura')) {
            $ids = DB::table('empresa_user')
                ->where('user_id', $this->usuario->id)
                ->distinct()
                ->pluck('empresa_id');
            $query->whereIn('empresa_id', $ids);
        } elseif (Roles::usuarioCumple($this->usuario, 'requiereEmpresaPrincipal') && $this->usuario->empresa_id) {
            $query->where('empresa_id', $this->usuario->empresa_id);
        } else {
            $query->whereRaw('1 = 0');
        }

        return $query
            ->when($this->filtros['empresa_id'], fn ($q, $id) => $q->where('empresa_id', $id))
            ->when($this->filtros['sede_id'],    fn ($q, $id) => $q->where('sede_id', $id))
            ->when(! $this->filtros['incluir_bajas'], fn ($q) => $q->where('estado', '!=', Impresora::ESTADO_DADA_DE_BAJA));
    }

    /**
     * [nombre empresa => ['equipos' => n, 'impresoras' => m]], de mayor a menor total.
     */
    private function porEmpresa(Collection $equipos, Collection $impresoras): array
    {
        $resultado = [];

        foreach ($equipos as $e) {
            $nombre = $e->empresa?->nombre ?? 'Sin empresa';
            $resultado[$nombre]['equipos'] = ($resultado[$nombre]['equipos'] ?? 0) + 1;
        }

        foreach ($impresoras as $i) {
            $nombre = $i->empresa?->nombre ?? 'Sin empresa';
            $resultado[$nombre]['impresoras'] = ($resultado[$nombre]['impresoras'] ?? 0) + 1;
        }

        return collect($resultado)
            ->map(fn ($f) => ['equipos' => $f['equipos'] ?? 0, 'impresoras' => $f['impresoras'] ?? 0])
            ->sortByDesc(fn ($f) => $f['equipos'] + $f['impresoras'])
            ->all();
    }
}