<?php

namespace App\Actions\Flujos;

use App\Models\User;
use App\Models\WorkflowStage;
use App\Models\WorkflowStageActor;
use App\Models\WorkflowTemplate;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class GuardarPlantillaAction
{
    /**
     * Crea o actualiza una plantilla completa (con etapas y actores) en una
     * sola transacción. Si la plantilla ya tiene instancias, ignora los datos
     * estructurales y solo actualiza metadatos.
     */
    public function ejecutar(?WorkflowTemplate $plantilla, array $datos, User $usuario): WorkflowTemplate
    {
        return DB::transaction(function () use ($plantilla, $datos, $usuario) {

            $esNueva = $plantilla === null;

            if ($esNueva) {
                $plantilla = new WorkflowTemplate();
                $plantilla->created_by = $usuario->id;
                $plantilla->version = 1;
            }

            $plantilla->fill(Arr::only($datos, ['nombre', 'descripcion', 'activo']));
            $plantilla->save();

            // Si no vienen etapas en el payload (caso: edición bloqueada por instancias),
            // no tocamos la estructura.
            if (empty($datos['etapas'])) {
                return $plantilla->fresh(['stages.actors']);
            }

            $this->sincronizarEstructura($plantilla, $datos['etapas']);

            return $plantilla->fresh(['stages.actors']);
        });
    }

    /**
     * Estrategia: como en creación no hay nada, y en edición solo permitimos
     * cambio estructural cuando NO hay instancias, podemos borrar todas las
     * etapas y recrearlas. Cascadea a los actores (por FK).
     */
    private function sincronizarEstructura(WorkflowTemplate $plantilla, array $etapas): void
    {
        $plantilla->stages()->delete();

        // Primera pasada: crear etapas (sin resolver el destino de rechazo)
        $idsPorIndice = [];

        foreach ($etapas as $idx => $etapaData) {
            $stage = new WorkflowStage([
                'workflow_template_id' => $plantilla->id,
                'nombre'               => $etapaData['nombre'],
                'descripcion'          => $etapaData['descripcion'] ?? null,
                'codigo_externo'       => $etapaData['codigo_externo'] ?? null,   // ← nuevo
                'orden'                => (int) $etapaData['orden'],
                'modo'                 => $etapaData['modo'],
                'politica_rechazo'     => $etapaData['politica_rechazo'],
                'requiere_adjunto'     => (bool) ($etapaData['requiere_adjunto'] ?? false),
                'sla_horas'            => $etapaData['sla_horas'] ?? null,
            ]);
            $stage->save();

            $idsPorIndice[$idx] = $stage->id;

            // Actores de la etapa
            foreach ($etapaData['actores'] as $actorData) {
                WorkflowStageActor::create([
                    'workflow_stage_id' => $stage->id,
                    'actor_type'        => $actorData['actor_type'],
                    'actor_id'          => (int) $actorData['actor_id'],
                    'etiqueta'          => $actorData['etiqueta'] ?? null,
                ]);
            }
        }

        // Segunda pasada: resolver etapa_destino_rechazo_id usando el mapa índice → id
        foreach ($etapas as $idx => $etapaData) {
            if ($etapaData['politica_rechazo'] !== WorkflowStage::RECHAZO_DEVOLVER_ETAPA_ESPECIFICA) {
                continue;
            }

            $destinoIdx = $etapaData['etapa_destino_rechazo_idx'] ?? null;

            if ($destinoIdx === null || ! isset($idsPorIndice[$destinoIdx])) {
                continue;
            }

            WorkflowStage::where('id', $idsPorIndice[$idx])
                ->update(['etapa_destino_rechazo_id' => $idsPorIndice[$destinoIdx]]);
        }
    }
}