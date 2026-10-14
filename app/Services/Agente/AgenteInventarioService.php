<?php

namespace App\Services\Agente;

use App\Exceptions\AgenteException;
use App\Models\Empresa;
use App\Models\Equipo;
use App\Models\EquipoAgente;
use App\Models\Sede;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Registro de un PC desde el agente y actualización periódica de sus datos.
 *
 * Al instalar, el agente busca el serial del PC en la empresa:
 *  - No existe            → se crea un equipo nuevo con ese serial.
 *  - Existe en la empresa → el técnico elige:
 *        reemplazar: el PC toma ese equipo y actualiza sus datos.
 *        nuevo:      se crea otro equipo con el serial "SERIAL-NOMBREPC" (el serial es único en la BD).
 *  - Existe en otra empresa → solo se puede crear nuevo con "SERIAL-NOMBREPC".
 *
 * El técnico elige además la sede del PC. El serial queda fijo desde el registro:
 * los reportes nunca lo cambian (si viniera combinado, volver a escribirlo chocaría).
 */
class AgenteInventarioService
{
    public const ACCION_NUEVO = 'nuevo';
    public const ACCION_REEMPLAZAR = 'reemplazar';

    /** Campos de texto del equipo que se toman tal cual del hardware */
    private const CAMPOS_BASE = ['marca', 'modelo'];

    // ─────────────────────────────────────────────────────────────────────
    //  Verificación (antes de instalar)
    // ─────────────────────────────────────────────────────────────────────

    /** Qué pasará al registrar este PC en la empresa. No modifica nada. */
    public function verificar(Empresa $empresa, array $inventario): array
    {
        $serial = $this->normalizarSerial($inventario['numero_serie'] ?? null);
        $existente = $serial ? $this->equipoConSerial($empresa, $serial) : null;
        $enOtraEmpresa = $serial && ! $existente && Equipo::where('numero_serie', $serial)->exists();

        return [
            'empresa'         => $empresa->nombre,
            'serial'          => $serial,
            'equipo'          => $existente ? $this->resumenEquipo($existente) : null,
            'en_otra_empresa' => $enOtraEmpresa,
            // Serial con el que quedaría si se crea como nuevo
            'serial_nuevo'    => $serial && ($existente || $enOtraEmpresa)
                ? $this->serialCombinado($serial, $inventario['hostname'])
                : $serial,
            // Para que el instalador pregunte a qué sede pertenece el PC
            'sedes'           => $this->sedesDe($empresa)->map->only(['id', 'nombre'])->values(),
            // Y qué tipo de equipo es (solo aplica si se crea uno nuevo)
            'categorias'         => collect($this->categoriasDisponibles())
                ->map(fn ($nombre, $clave) => ['clave' => $clave, 'nombre' => $nombre])->values(),
            'categoria_sugerida' => $this->categoriaSugerida($inventario),
        ];
    }

    // ─────────────────────────────────────────────────────────────────────
    //  Registro
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Registra el PC y emite su token.
     *
     * @return array{0: EquipoAgente, 1: string} [agente, token en claro]
     */
    public function registrar(Empresa $empresa, array $inventario, string $accion, ?int $sedeId = null, ?string $categoria = null): array
    {
        if ($sedeId !== null && ! $this->sedesDe($empresa)->contains('id', $sedeId)) {
            throw AgenteException::sedeInvalida();
        }

        try {
            return DB::transaction(function () use ($empresa, $inventario, $accion, $sedeId, $categoria) {
                $serial = $this->normalizarSerial($inventario['numero_serie'] ?? null);

                $equipo = $accion === self::ACCION_REEMPLAZAR
                    ? $this->equipoParaReemplazar($empresa, $serial)
                    : $this->crearEquipo($empresa, $inventario, $serial, $categoria);

                // La sede que eligió el técnico en el instalador
                if ($sedeId !== null && $equipo->sede_id !== $sedeId) {
                    $equipo->forceFill(['sede_id' => $sedeId])->save();
                }

                [$token, $hash] = EquipoAgente::generarToken();

                // Si el equipo ya tenía agente, su token anterior deja de servir
                $agente = EquipoAgente::updateOrCreate(
                    ['equipo_id' => $equipo->id],
                    ['token_hash' => $hash, 'vinculado_at' => now(), 'revocado_at' => null, 'revocado_por' => null]
                );

                $this->registrarReporte($agente, $inventario);

                return [$agente->setRelation('equipo', $equipo->refresh()), $token];
            });
        } catch (UniqueConstraintViolationException) {
            // Otro PC se registró con el mismo serial en el mismo instante
            throw AgenteException::noSePuedeCrear('El serial se acaba de registrar desde otro PC. Vuelva a intentar.');
        }
    }

    private function equipoParaReemplazar(Empresa $empresa, ?string $serial): Equipo
    {
        $equipo = $serial ? $this->equipoConSerial($empresa, $serial, bloquear: true) : null;

        return $equipo ?? throw AgenteException::sinEquipoParaReemplazar();
    }

    private function crearEquipo(Empresa $empresa, array $inventario, ?string $serial, ?string $categoria): Equipo
    {
        // La que eligió el técnico; si el instalador no la envía (versiones anteriores), la sugerida
        $categoria ??= $this->categoriaSugerida($inventario);

        if ($categoria === null || ! array_key_exists($categoria, $this->categoriasDisponibles())) {
            throw AgenteException::noSePuedeCrear('No hay una categoría de equipo válida para registrar este PC. Revise Equipo::CATEGORIAS.');
        }

        // El serial es único en la BD: si ya lo tiene otro equipo se combina con el nombre del PC
        if ($serial && Equipo::where('numero_serie', $serial)->exists()) {
            $serial = $this->serialCombinado($serial, $inventario['hostname']);
        }

        return Equipo::create([
            'empresa_id'       => $empresa->id,
            'categoria'        => $categoria,
            'nombre'           => Str::limit($inventario['hostname'], 150, ''),
            'numero_serie'     => $serial,
            'estado'           => 'activo',
            'especificaciones' => [],
            'creado_por'       => config('agente.usuario_creador_id'),
            'notas'            => 'Registrado por el agente de inventario.',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    //  Reporte periódico
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Actualiza el equipo con el inventario recibido.
     *
     * @return array<string, array{antes: mixed, despues: mixed}> campos que cambiaron
     */
    public function registrarReporte(EquipoAgente $agente, array $inventario): array
    {
        return DB::transaction(function () use ($agente, $inventario) {
            $equipo = Equipo::lockForUpdate()->findOrFail($agente->equipo_id);

            $cambios = $this->aplicarInventario($equipo, $inventario);

            $agente->fill([
                'hostname'          => $inventario['hostname'] ?? null,
                'usuario_sesion'    => $inventario['usuario'] ?? null,
                'ip'                => $inventario['ip'] ?? null,
                'mac'               => $inventario['mac'] ?? null,
                'version_agente'    => $inventario['version_agente'] ?? null,
                'ultimo_inventario' => $inventario,
                'ultimo_reporte_at' => now(),
            ])->save();

            return $cambios;
        });
    }

    /**
     * Aplica marca, modelo y especificaciones. No toca nombre, sede, responsable ni estado.
     * El serial se fija al registrar (puede estar combinado con el nombre del PC) y no se cambia aquí.
     */
    private function aplicarInventario(Equipo $equipo, array $inventario): array
    {
        foreach (self::CAMPOS_BASE as $campo) {
            $valor = $this->texto($inventario[$campo] ?? null);
            if ($valor !== null) {
                $equipo->{$campo} = Str::limit($valor, 100, '');
            }
        }

        $equipo->especificaciones = $this->mezclarEspecificaciones(
            $equipo->categoria, $equipo->especificaciones ?? [], $inventario
        );

        $cambios = collect($equipo->getDirty())
            ->mapWithKeys(fn ($valor, $campo) => [$campo => [
                'antes'   => $equipo->getOriginal($campo),
                'despues' => $valor,
            ]])
            ->all();

        if ($cambios) {
            $equipo->save();
        }

        return $cambios;
    }

    /** Llena solo los campos que existen en el esquema de la categoría y conserva los demás. */
    private function mezclarEspecificaciones(?string $categoria, array $actuales, array $inventario): array
    {
        $esquema = Equipo::ESPECIFICACIONES_SCHEMA[$categoria] ?? [];

        foreach (config('agente.alias_especificaciones') as $dato => $alias) {
            $valor = $inventario[$dato] ?? null;
            if ($valor === null || $valor === '') {
                continue;
            }

            foreach ($alias as $campo) {
                if (! isset($esquema[$campo])) {
                    continue;
                }

                $convertido = $this->convertir($esquema[$campo], $dato, $valor, $inventario);
                if ($convertido !== null) {
                    $actuales[$campo] = $convertido;
                }
            }
        }

        return $actuales;
    }

    /** Ajusta el valor al tipo del campo. En los select busca la opción equivalente; null si no hay. */
    private function convertir(array $config, string $dato, mixed $valor, array $inventario): mixed
    {
        $tipo = $config['type'] ?? 'text';

        if ($tipo === 'number') {
            return is_numeric($valor) ? $valor + 0 : null;
        }

        if ($tipo === 'select') {
            $buscados = [(string) $valor];

            // Un SSD NVMe se registra como "NVMe" si el esquema tiene esa opción
            if ($dato === 'tipo_disco' && strcasecmp((string) ($inventario['bus_disco'] ?? ''), 'NVMe') === 0) {
                array_unshift($buscados, 'NVMe');
            }

            return $this->opcionEquivalente($config['options'] ?? [], $buscados);
        }

        return Str::limit($this->texto($valor) ?? '', 200, '') ?: null;
    }

    private function opcionEquivalente(array $opciones, array $buscados): ?string
    {
        foreach ($buscados as $buscado) {
            foreach ($opciones as $op) {
                if (strcasecmp($op, $buscado) === 0) {
                    return $op;
                }
            }
            foreach ($opciones as $op) {
                if (stripos($op, $buscado) !== false) {
                    return $op;
                }
            }
        }

        return null;
    }

    // ─────────────────────────────────────────────────────────────────────
    //  Helpers
    // ─────────────────────────────────────────────────────────────────────

    /** El PC se desinstaló: su token deja de servir. El equipo y su historial se conservan. */
    public function desvincular(EquipoAgente $agente): void
    {
        $agente->forceFill(['revocado_at' => now()])->save();
    }

    // ─────────────────────────────────────────────────────────────────────
    //  Categorías
    // ─────────────────────────────────────────────────────────────────────

    /** Las de Equipo::CATEGORIAS que aplican a un PC: [clave => nombre] */
    public function categoriasDisponibles(): array
    {
        return array_diff_key(Equipo::CATEGORIAS, array_flip(config('agente.categorias_excluidas', [])));
    }

    /** Según el chasis que detectó el agente; si no corresponde a ninguna, la primera disponible. */
    public function categoriaSugerida(array $inventario): ?string
    {
        $disponibles = $this->categoriasDisponibles();
        $porChasis = config('agente.categoria_por_chasis')[$inventario['chasis'] ?? ''] ?? null;

        return $porChasis !== null && array_key_exists($porChasis, $disponibles)
            ? $porChasis
            : array_key_first($disponibles);
    }

    private function sedesDe(Empresa $empresa)
    {
        return Sede::query()
            ->where('empresa_id', $empresa->id)
            ->where('activo', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);
    }

    private function equipoConSerial(Empresa $empresa, string $serial, bool $bloquear = false): ?Equipo
    {
        return Equipo::query()
            ->where('empresa_id', $empresa->id)
            ->where('numero_serie', $serial)
            ->when($bloquear, fn ($q) => $q->lockForUpdate())
            ->with('sede:id,nombre')
            ->first();
    }

    /**
     * "SERIAL-NOMBREPC"; si ese también existe, "SERIAL-NOMBREPC-2", "-3"...
     * Se recorta para no superar los 100 caracteres de la columna.
     */
    public function serialCombinado(string $serial, string $hostname): string
    {
        $pc = Str::upper(preg_replace('/[^A-Za-z0-9-]/', '', $hostname)) ?: 'PC';
        $base = Str::limit("{$serial}-{$pc}", 95, '');

        $candidato = $base;
        for ($n = 2; Equipo::where('numero_serie', $candidato)->exists(); $n++) {
            $candidato = "{$base}-{$n}";
        }

        return $candidato;
    }

    private function resumenEquipo(Equipo $equipo): array
    {
        $agente = EquipoAgente::activos()->where('equipo_id', $equipo->id)->first();

        return [
            'id'           => $equipo->id,
            'nombre'       => $equipo->nombre,
            'marca'        => $equipo->marca,
            'modelo'       => $equipo->modelo,
            'sede'         => $equipo->sede?->nombre,
            'sede_id'      => $equipo->sede_id,
            // Si otro PC ya reporta este equipo, reemplazarlo lo desconecta
            'agente_en'    => $agente?->hostname,
        ];
    }

    public function normalizarSerial(?string $serial): ?string
    {
        $serial = $this->texto($serial);

        if ($serial === null || in_array(mb_strtolower($serial), config('agente.seriales_invalidos'), true)) {
            return null;
        }

        return Str::limit($serial, 100, '');
    }

    private function texto(mixed $valor): ?string
    {
        $valor = trim((string) $valor);

        return $valor === '' ? null : $valor;
    }
}
