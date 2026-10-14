<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuardarEquipoMantenimientoRequest;
use App\Models\Equipo;
use App\Models\EquipoMantenimiento;
use App\Models\User;
use App\Support\Roles;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Servicios técnicos a PCs (preventivos y correctivos) con el formato
 * "Contrato de prestación de servicio". La evidencia, las firmas y el PDF
 * están en EquipoMantenimientoReporteController.
 */
class EquipoMantenimientoController extends Controller
{
    public function create(Request $request, Equipo $equipo): View
    {
        $this->autorizarGestion($request, $equipo);
        abort_unless($equipo->admiteMantenimiento(), 422, 'Este módulo es para computadores. Las impresoras tienen su propio registro de mantenimientos.');

        $equipo->load('perifericos');
        $anterior = $equipo->mantenimientos()->first();

        return view('equipos.mantenimientos.create', $this->datosFormulario($request, $equipo) + [
            'mantenimiento' => new EquipoMantenimiento([
                'tipo'           => 'preventivo',
                'fecha_ingreso'  => today(),
                'fecha_servicio' => today(),
                'hora_inicio'    => now()->format('H:i'),
                'tecnico_id'     => $request->user()->id,
                'ticket_id'      => $request->integer('ticket') ?: null,
                // Lo habitual es que el equipo siga en la misma modalidad y depósito
                'modalidad'      => $anterior?->modalidad,
                'deposito'       => $anterior?->deposito,
            ]),
            'unidades' => $equipo->unidadesParaRevision(),
            'partes'   => [],
        ]);
    }

    public function store(GuardarEquipoMantenimientoRequest $request, Equipo $equipo): RedirectResponse
    {
        abort_unless($equipo->admiteMantenimiento(), 422);

        $mantenimiento = DB::transaction(function () use ($request, $equipo) {
            $mantenimiento = $equipo->mantenimientos()->create(
                $this->datosServicio($request) + ['registrado_por' => $request->user()->id]
            );

            $this->guardarDetalle($mantenimiento, $request);

            return $mantenimiento;
        });

        return redirect()
            ->route('equipos.mantenimientos.show', [$equipo, $mantenimiento])
            ->with('success', "Servicio {$mantenimiento->numero_orden} registrado. Agregue las fotos y las firmas para finalizarlo.");
    }

    public function show(Request $request, Equipo $equipo, EquipoMantenimiento $mantenimiento): View
    {
        $this->validarPertenencia($equipo, $mantenimiento);
        $this->autorizarVista($request, $equipo);

        $mantenimiento->load(['tecnico:id,name', 'registrador:id,name', 'finalizador:id,name', 'ticket:id,titulo,estado',
            'unidades', 'partes', 'evidencias']);
        $equipo->loadMissing(['empresa', 'sede', 'responsable:id,name']);

        return view('equipos.mantenimientos.show', [
            'equipo'         => $equipo,
            'mantenimiento'  => $mantenimiento,
            'puedeGestionar' => $this->puedeGestionar($request, $equipo),
        ]);
    }

    public function edit(Request $request, Equipo $equipo, EquipoMantenimiento $mantenimiento): View
    {
        $this->validarPertenencia($equipo, $mantenimiento);
        $this->autorizarGestion($request, $equipo);
        $this->exigirPendiente($mantenimiento);

        $mantenimiento->load(['unidades', 'partes']);
        $equipo->load('perifericos');

        return view('equipos.mantenimientos.edit', $this->datosFormulario($request, $equipo, $mantenimiento) + [
            'mantenimiento' => $mantenimiento,
            'unidades'      => $mantenimiento->unidades->toArray(),
            'partes'        => $mantenimiento->partes->toArray(),
        ]);
    }

    public function update(GuardarEquipoMantenimientoRequest $request, Equipo $equipo, EquipoMantenimiento $mantenimiento): RedirectResponse
    {
        $this->validarPertenencia($equipo, $mantenimiento);
        $this->exigirPendiente($mantenimiento);

        DB::transaction(function () use ($request, $mantenimiento) {
            $mantenimiento->update($this->datosServicio($request));

            // Las filas se reemplazan completas: es lo que se ve en el formulario
            $mantenimiento->unidades()->delete();
            $mantenimiento->partes()->delete();
            $this->guardarDetalle($mantenimiento, $request);
        });

        return redirect()
            ->route('equipos.mantenimientos.show', [$equipo, $mantenimiento])
            ->with('success', 'Servicio actualizado.');
    }

    public function destroy(Request $request, Equipo $equipo, EquipoMantenimiento $mantenimiento): RedirectResponse
    {
        $this->validarPertenencia($equipo, $mantenimiento);
        abort_unless($request->user()->can('equipos.mantenimientos.eliminar') && $equipo->puedeSerGestionadoPor($request->user()), 403);

        $carpeta = $mantenimiento->carpetaArchivos();
        $numero = $mantenimiento->numero_orden;
        $mantenimiento->delete();

        Storage::disk('local')->deleteDirectory($carpeta);

        return redirect()->route('equipos.show', $equipo)->with('success', "Servicio {$numero} eliminado.");
    }

    // ─────────────────────────────────────────────────────────────────────

    private function datosServicio(GuardarEquipoMantenimientoRequest $request): array
    {
        return $request->safe()->only([
            'tipo', 'modalidad', 'deposito', 'ticket_id', 'tecnico_id',
            'fecha_ingreso', 'fecha_servicio', 'hora_inicio', 'hora_fin',
            'contador_inicial', 'contador_final', 'falla', 'observaciones',
        ]) + ['ticket_id' => null, 'modalidad' => null];
    }

    private function guardarDetalle(EquipoMantenimiento $mantenimiento, GuardarEquipoMantenimientoRequest $request): void
    {
        $acciones = array_keys(EquipoMantenimiento::ACCIONES);

        foreach (array_values($request->validated('unidades', [])) as $orden => $u) {
            $mantenimiento->unidades()->create(
                ['orden' => $orden, 'unidad' => $u['unidad'], 'periferico_id' => $u['periferico_id'] ?? null]
                + collect($u)->only($acciones)->all()
            );
        }

        foreach ($request->validated('partes', []) as $p) {
            $mantenimiento->partes()->create(collect($p)->only(['numero_parte', 'nombre', 'serial_original', 'serial_reemplazo'])->all());
        }
    }

    private function datosFormulario(Request $request, Equipo $equipo, ?EquipoMantenimiento $mantenimiento = null): array
    {
        $equipo->loadMissing(['empresa:id,nombre', 'sede:id,nombre']);

        return [
            'equipo'   => $equipo,
            'tecnicos' => $this->tecnicosPara($equipo, collect([$request->user()->id, $mantenimiento?->tecnico_id])->filter()),
            // Orden de trabajo: los tickets del equipo (abiertos primero)
            'tickets'  => $equipo->tickets()
                ->whereNotIn('estado', ['cancelado'])
                ->orderByRaw("CASE WHEN estado IN ('cerrado', 'resuelto') THEN 1 ELSE 0 END")
                ->latest()
                ->limit(30)
                ->get(['id', 'titulo', 'estado']),
        ];
    }

    /** Técnicos con cobertura sobre la empresa/sede del equipo, más los indicados (quien registra, el actual) */
    private function tecnicosPara(Equipo $equipo, Collection $incluir): Collection
    {
        return User::query()
            ->where('activo', true)
            ->where(function ($q) use ($equipo, $incluir) {
                $q->whereIn('id', $incluir)
                  ->orWhere(function ($q) use ($equipo) {
                      $q->role(Roles::CON_COBERTURA)->whereExists(function ($sub) use ($equipo) {
                          $sub->from('empresa_user')
                              ->whereColumn('empresa_user.user_id', 'users.id')
                              ->where('empresa_user.empresa_id', $equipo->empresa_id)
                              ->where(fn ($w) => $w->whereNull('empresa_user.sede_id')->orWhere('empresa_user.sede_id', $equipo->sede_id));
                      });
                  });
            })
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    private function puedeGestionar(Request $request, Equipo $equipo): bool
    {
        return $request->user()->can('equipos.mantenimientos.gestionar') && $equipo->puedeSerGestionadoPor($request->user());
    }

    private function autorizarGestion(Request $request, Equipo $equipo): void
    {
        abort_unless($this->puedeGestionar($request, $equipo), 403);
    }

    private function autorizarVista(Request $request, Equipo $equipo): void
    {
        abort_unless($request->user()->can('equipos.mantenimientos.ver') && $equipo->puedeSerGestionadoPor($request->user()), 403);
    }

    private function validarPertenencia(Equipo $equipo, EquipoMantenimiento $mantenimiento): void
    {
        abort_if((int) $mantenimiento->equipo_id !== (int) $equipo->id, 404);
    }

    private function exigirPendiente(EquipoMantenimiento $mantenimiento): void
    {
        abort_if($mantenimiento->estaFinalizado(), 422, 'El servicio ya está firmado y finalizado: no se puede modificar.');
    }
}
