<?php

namespace App\Services\Proyectos;

use App\Models\Proyecto;
use App\Models\ProyectoActividad;
use App\Models\ProyectoEtapa;
use App\Models\ProyectoSolicitud;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

class EtapaService
{
    public function __construct(private readonly AdjuntoService $adjuntos)
    {
    }

    /**
     * Registra una etapa. Si es de un tipo que atiende solicitudes,
     * las solicitudes abiertas marcadas pasan a "atendida" y quedan vinculadas.
     */
    public function registrar(Proyecto $proyecto, array $datos, array $archivos, User $user): ProyectoEtapa
    {
        $rutas = [];

        try {
            return DB::transaction(function () use ($proyecto, $datos, $archivos, $user, &$rutas) {
                $atiende = $datos['atiende'] ?? [];

                $etapa = $proyecto->etapas()->create(array_merge(
                    $this->normalizar($datos),
                    ['registrado_por' => $user->id],
                ));

                $this->adjuntos->guardar($proyecto, $etapa, $archivos, $user, $rutas);

                if (in_array($etapa->tipo, ProyectoEtapa::TIPOS_QUE_ATIENDEN, true) && $atiende) {
                    $proyecto->solicitudes()
                        ->whereIn('id', $atiende)
                        ->whereIn('estado', ProyectoSolicitud::ESTADOS_ABIERTOS)
                        ->update([
                            'estado'              => ProyectoSolicitud::ESTADO_ATENDIDA,
                            'etapa_resolucion_id' => $etapa->id,
                            'updated_at'          => now(),
                        ]);
                }

                $proyecto->touch();

                return $etapa;
            });
        } catch (Throwable $e) {
            $this->adjuntos->limpiar($rutas);
            throw $e;
        }
    }

    /** Edita la etapa y agrega nuevos adjuntos (los existentes se borran uno a uno desde la vista) */
    public function actualizar(ProyectoEtapa $etapa, array $datos, array $archivos, User $user): ProyectoEtapa
    {
        $rutas = [];

        try {
            return DB::transaction(function () use ($etapa, $datos, $archivos, $user, &$rutas) {
                $etapa->update($this->normalizar($datos));
                $this->adjuntos->guardar($etapa->proyecto, $etapa, $archivos, $user, $rutas);
                $etapa->proyecto->touch();

                return $etapa;
            });
        } catch (Throwable $e) {
            $this->adjuntos->limpiar($rutas);
            throw $e;
        }
    }

    /**
     * Elimina (soft delete) la etapa. Las solicitudes que había resuelto
     * vuelven a "en proceso" para que no queden atendidas sin respaldo.
     */
    public function eliminar(ProyectoEtapa $etapa, User $user): void
    {
        DB::transaction(function () use ($etapa, $user) {
            $etapa->solicitudesAtendidas()->update([
                'estado'              => ProyectoSolicitud::ESTADO_EN_PROCESO,
                'etapa_resolucion_id' => null,
                'updated_at'          => now(),
            ]);

            $label = $etapa->config_tipo['label'];
            $etapa->delete();

            ProyectoActividad::registrar(
                $etapa->proyecto,
                'etapa_eliminada',
                "Se eliminó el registro de {$label} \"{$etapa->titulo}\" del {$etapa->fecha->format('d/m/Y')}.",
                $user,
            );
        });
    }

    /** Deja solo los campos que aplican al tipo y completa el título por defecto */
    private function normalizar(array $datos): array
    {
        $tipo      = $datos['tipo'];
        $aplicables = ProyectoEtapa::CAMPOS_POR_TIPO[$tipo] ?? [];

        foreach (ProyectoEtapa::CAMPOS_OPCIONALES as $campo) {
            if (! in_array($campo, $aplicables, true) || ($datos[$campo] ?? '') === '') {
                $datos[$campo] = null;
            }
        }

        $datos['titulo'] = trim((string) ($datos['titulo'] ?? '')) ?: ProyectoEtapa::tituloPorDefecto($tipo, $datos);

        return collect($datos)->only([
            'tipo', 'titulo', 'descripcion', 'fecha', 'estado', ...ProyectoEtapa::CAMPOS_OPCIONALES,
        ])->all();
    }
}
