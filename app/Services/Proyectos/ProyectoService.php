<?php

namespace App\Services\Proyectos;

use App\Models\Proyecto;
use App\Models\ProyectoActividad;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ProyectoService
{
    public function crear(array $datos, User $user): Proyecto
    {
        return DB::transaction(function () use ($datos, $user) {
            $equipo = $datos['equipo'] ?? [];
            unset($datos['equipo']);

            $proyecto = Proyecto::create(array_merge($datos, [
                'codigo'       => Proyecto::generarCodigo(),
                'estado'       => $datos['estado'] ?? Proyecto::ESTADO_ACTIVO,
                'fecha_cierre' => $this->fechaCierre($datos['estado'] ?? Proyecto::ESTADO_ACTIVO, null),
                'creado_por'   => $user->id,
            ]));

            $this->sincronizarEquipo($proyecto, $equipo);

            $nombres = $proyecto->equipo()->pluck('name')->join(', ');
            ProyectoActividad::registrar($proyecto, 'creado', "Proyecto creado. Equipo: {$nombres}.", $user);

            return $proyecto;
        });
    }

    public function actualizar(Proyecto $proyecto, array $datos, User $user): Proyecto
    {
        return DB::transaction(function () use ($proyecto, $datos, $user) {
            $equipo = $datos['equipo'] ?? [];
            unset($datos['equipo']);

            $estadoAnterior = $proyecto->estado;
            $equipoAnterior = $proyecto->equipo()->pluck('users.id')->sort()->values()->all();

            $datos['fecha_cierre'] = $this->fechaCierre($datos['estado'], $proyecto->fecha_cierre);
            $proyecto->update($datos);

            $this->sincronizarEquipo($proyecto, $equipo);

            // ── Bitácora ────────────────────────────────────────────────
            if ($estadoAnterior !== $proyecto->estado) {
                $de = Proyecto::ESTADOS[$estadoAnterior]['label'] ?? $estadoAnterior;
                ProyectoActividad::registrar($proyecto, 'estado', "Estado cambiado de {$de} a {$proyecto->label_estado}.", $user);
            }

            $equipoNuevo = $proyecto->equipo()->pluck('users.id')->sort()->values()->all();
            if ($equipoAnterior !== $equipoNuevo) {
                $nombres = $proyecto->equipo()->pluck('name')->join(', ');
                ProyectoActividad::registrar($proyecto, 'equipo', "Equipo actualizado: {$nombres}.", $user);
            }

            return $proyecto;
        });
    }

    /**
     * Sincroniza el equipo. El responsable siempre queda incluido.
     * $equipo llega como [user_id => ['activo' => '1', 'rol' => '...']].
     */
    private function sincronizarEquipo(Proyecto $proyecto, array $equipo): void
    {
        $sync = [];

        foreach ($equipo as $userId => $fila) {
            if (! empty($fila['activo'])) {
                $sync[(int) $userId] = ['rol' => $fila['rol'] ?? null];
            }
        }

        $rolResponsable = $sync[$proyecto->responsable_id]['rol'] ?? null;
        $sync[$proyecto->responsable_id] = ['rol' => $rolResponsable ?: 'Responsable'];

        $proyecto->equipo()->sync($sync);
    }

    /** Llena la fecha de cierre al finalizar/cancelar y la limpia al reabrir */
    private function fechaCierre(string $estado, $actual)
    {
        if (in_array($estado, Proyecto::ESTADOS_CERRADOS, true)) {
            return $actual ?? now()->toDateString();
        }

        return null;
    }
}
