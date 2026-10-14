<?php

namespace App\Services\Impresoras;

use App\Models\Impresora;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Histórico de lecturas del contador: páginas entre lecturas, días y promedio diario,
 * más un resumen por mes. Base para cruzar después las páginas con el consumo de tinta.
 */
class HistorialContador
{
    /**
     * @return array{lecturas: Collection, meses: Collection, total_paginas: int, promedio_diario: ?float,
     *               ultimos_30_dias: int, primera: ?Carbon, ultima: ?Carbon}
     */
    public function para(Impresora $impresora): array
    {
        $lecturas = $impresora->contadores()
            ->with(['registrador:id,name', 'mantenimiento:id,tipo'])
            ->oldest()->oldest('id')
            ->get();

        // Días y promedio respecto a la lectura anterior
        $anterior = null;
        foreach ($lecturas as $lectura) {
            $lectura->dias_desde_anterior = $anterior
                ? round($anterior->created_at->diffInHours($lectura->created_at) / 24, 1)
                : null;
            $lectura->paginas_por_dia = $lectura->paginas_desde_anterior !== null && $lectura->dias_desde_anterior >= 1
                ? (int) round($lectura->paginas_desde_anterior / $lectura->dias_desde_anterior)
                : null;
            $anterior = $lectura;
        }

        $conPaginas = $lecturas->whereNotNull('paginas_desde_anterior');
        $total = (int) $conPaginas->sum('paginas_desde_anterior');
        $primera = $lecturas->first()?->created_at;
        $ultima = $lecturas->last()?->created_at;
        $dias = $primera && $ultima ? $primera->diffInHours($ultima) / 24 : 0;

        return [
            'lecturas'        => $lecturas->reverse()->values(), // la más reciente primero
            'meses'           => $this->porMes($conPaginas),
            'total_paginas'   => $total,
            'promedio_diario' => $dias >= 1 ? round($total / $dias) : null,
            'ultimos_30_dias' => (int) $conPaginas->where('created_at', '>=', now()->subDays(30))->sum('paginas_desde_anterior'),
            'primera'         => $primera,
            'ultima'          => $ultima,
        ];
    }

    /** Páginas registradas por mes (últimos 12 meses con lecturas), según la fecha de cada lectura */
    private function porMes(Collection $lecturas): Collection
    {
        return $lecturas
            ->groupBy(fn ($l) => $l->created_at->format('Y-m'))
            ->map(fn ($grupo, $mes) => [
                'mes'     => Carbon::createFromFormat('Y-m', $mes)->startOfMonth(),
                'paginas' => (int) $grupo->sum('paginas_desde_anterior'),
            ])
            ->sortKeys()
            ->take(-12)
            ->values();
    }
}
