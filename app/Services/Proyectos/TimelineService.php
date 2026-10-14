<?php

namespace App\Services\Proyectos;

use App\Models\Proyecto;
use Illuminate\Support\Collection;

/**
 * Arma la línea de tiempo del proyecto mezclando etapas, solicitudes y bitácora,
 * ordenada por fecha real del hecho (más reciente primero) y agrupada por mes.
 * Cada item lleva sus categorías para los filtros de la vista.
 */
class TimelineService
{
    private const MESES = [
        1 => 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre',
    ];

    public const FILTROS = [
        'todo'        => 'Todo',
        'etapas'      => 'Etapas',
        'solicitudes' => 'Solicitudes',
        'economico'   => 'Económico',
        'evidencias'  => 'Con evidencias',
    ];

    /**
     * Requiere cargadas: etapas.{autor,adjuntos,solicitudesAtendidas},
     * solicitudes.{autor,adjuntos,etapaResolucion}, actividades.usuario
     *
     * @return array{grupos: Collection, conteos: array<string,int>}
     */
    public function construir(Proyecto $proyecto): array
    {
        $items = collect();

        foreach ($proyecto->etapas as $etapa) {
            $cats = ['etapas'];
            if ($etapa->esEconomica()) {
                $cats[] = 'economico';
            }
            if ($etapa->adjuntos->isNotEmpty()) {
                $cats[] = 'evidencias';
            }

            $items->push($this->item('etapa', $etapa, $etapa->fecha->format('Y-m-d'), $cats));
        }

        foreach ($proyecto->solicitudes as $solicitud) {
            $cats = ['solicitudes'];
            if ($solicitud->adjuntos->isNotEmpty()) {
                $cats[] = 'evidencias';
            }

            $items->push($this->item('solicitud', $solicitud, $solicitud->fecha_solicitud->format('Y-m-d'), $cats));
        }

        foreach ($proyecto->actividades as $actividad) {
            $items->push($this->item('actividad', $actividad, $actividad->created_at->format('Y-m-d'), ['sistema']));
        }

        // Fecha del hecho desc; a igual fecha, lo registrado después va primero
        $ordenados = $items->sort(function ($a, $b) {
            return [$b['fecha'], $b['registrado']] <=> [$a['fecha'], $a['registrado']];
        })->values();

        $grupos = $ordenados
            ->groupBy(fn ($i) => substr($i['fecha'], 0, 7))
            ->map(fn (Collection $lista, string $mes) => [
                'mes'   => $mes,
                'label' => self::MESES[(int) substr($mes, 5, 2)] . ' ' . substr($mes, 0, 4),
                'cats'  => $lista->pluck('cats')->flatten()->unique()->values()->all(),
                'items' => $lista,
            ])
            ->values();

        $conteos = ['todo' => $items->count()];
        foreach (array_keys(self::FILTROS) as $filtro) {
            if ($filtro !== 'todo') {
                $conteos[$filtro] = $items->filter(fn ($i) => in_array($filtro, $i['cats'], true))->count();
            }
        }

        return ['grupos' => $grupos, 'conteos' => $conteos];
    }

    private function item(string $clase, $modelo, string $fecha, array $cats): array
    {
        return [
            'clase'      => $clase,
            'modelo'     => $modelo,
            'fecha'      => $fecha,
            'registrado' => $modelo->created_at?->format('Y-m-d H:i:s') . sprintf('%010d', $modelo->id),
            'cats'       => $cats,
        ];
    }
}
