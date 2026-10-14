<?php

namespace App\Services\Proyectos;

use App\Models\Proyecto;
use App\Models\ProyectoActividad;
use App\Models\ProyectoSolicitud;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

class SolicitudService
{
    private const CAMPOS = ['titulo', 'descripcion', 'fecha_solicitud', 'canal', 'prioridad'];

    public function __construct(private readonly AdjuntoService $adjuntos)
    {
    }

    /** Toda solicitud nueva entra como pendiente */
    public function registrar(Proyecto $proyecto, array $datos, array $archivos, User $user): ProyectoSolicitud
    {
        $rutas = [];

        try {
            return DB::transaction(function () use ($proyecto, $datos, $archivos, $user, &$rutas) {
                $solicitud = $proyecto->solicitudes()->create(array_merge(
                    collect($datos)->only(self::CAMPOS)->all(),
                    [
                        'estado'         => ProyectoSolicitud::ESTADO_PENDIENTE,
                        'registrado_por' => $user->id,
                    ],
                ));

                $this->adjuntos->guardar($proyecto, $solicitud, $archivos, $user, $rutas);
                $proyecto->touch();

                return $solicitud;
            });
        } catch (Throwable $e) {
            $this->adjuntos->limpiar($rutas);
            throw $e;
        }
    }

    public function actualizar(ProyectoSolicitud $solicitud, array $datos, array $archivos, User $user): ProyectoSolicitud
    {
        $rutas = [];

        try {
            return DB::transaction(function () use ($solicitud, $datos, $archivos, $user, &$rutas) {
                $solicitud->update(collect($datos)->only(self::CAMPOS)->all());
                $this->adjuntos->guardar($solicitud->proyecto, $solicitud, $archivos, $user, $rutas);
                $solicitud->proyecto->touch();

                return $solicitud;
            });
        } catch (Throwable $e) {
            $this->adjuntos->limpiar($rutas);
            throw $e;
        }
    }

    /**
     * Cambio manual de estado. Si deja de estar "atendida" se rompe el vínculo
     * con la etapa que la había resuelto.
     */
    public function cambiarEstado(ProyectoSolicitud $solicitud, string $estado, ?string $respuesta): ProyectoSolicitud
    {
        $cambios = ['estado' => $estado];

        if ($estado !== ProyectoSolicitud::ESTADO_ATENDIDA) {
            $cambios['etapa_resolucion_id'] = null;
        }

        if ($respuesta !== null) {
            $cambios['respuesta'] = $respuesta;
        }

        $solicitud->update($cambios);
        $solicitud->proyecto->touch();

        return $solicitud;
    }

    public function eliminar(ProyectoSolicitud $solicitud, User $user): void
    {
        DB::transaction(function () use ($solicitud, $user) {
            $solicitud->delete();

            ProyectoActividad::registrar(
                $solicitud->proyecto,
                'solicitud_eliminada',
                "Se eliminó la solicitud \"{$solicitud->titulo}\" del {$solicitud->fecha_solicitud->format('d/m/Y')}.",
                $user,
            );
        });
    }
}
