<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reporte de inventario</title>
@php
    // Paletas de color por clave
    $coloresEstadoEquipo    = ['activo' => '#16a34a', 'mantenimiento' => '#f59e0b', 'dado_de_baja' => '#ef4444'];
    $coloresEstadoImpresora = ['activa' => '#16a34a', 'en_mantenimiento' => '#f59e0b', 'dada_de_baja' => '#ef4444'];
    $coloresCategoria       = ['computadora' => '#007acc', 'portatil' => '#22a3ff', 'impresora' => '#6366f1'];
    $coloresTecnologia      = ['laser' => '#007acc', 'inyeccion' => '#06b6d4', 'termica' => '#8b5cf6', 'matricial' => '#64748b', 'sin_modelo' => '#94a3b8'];
    $etiquetasTecnologia    = \App\Models\ImpresoraModelo::TIPOS_TECNOLOGIA + ['sin_modelo' => 'Sin modelo'];

    $colorNivel = fn ($estado) => match ($estado) {
        'agotado', 'critico' => '#ef4444',
        'bajo'               => '#f59e0b',
        'normal'             => '#16a34a',
        default              => '#94a3b8',
    };

    $maxEmpresa = collect($resumen['por_empresa'])->map(fn ($f) => max($f['equipos'], $f['impresoras']))->max() ?: 1;
@endphp
<style>
    @page { margin: 14mm 12mm 16mm 12mm; }
    * { font-family: 'DejaVu Sans', sans-serif; }
    body { font-size: 8.5px; color: #1e293b; }

    /* Cabecera */
    .cabecera { background: #0b1220; color: #ffffff; padding: 14px 18px; border-radius: 6px; }
    .cabecera table { width: 100%; }
    .marca { font-size: 8px; letter-spacing: 2px; color: #7cc4ff; text-transform: uppercase; }
    .titulo { font-size: 19px; font-weight: bold; margin-top: 3px; }
    .subtitulo { font-size: 8.5px; color: #cbd5e1; margin-top: 2px; }
    .meta { text-align: right; font-size: 7.5px; color: #cbd5e1; line-height: 1.7; }

    .filtros { margin: 8px 0 12px; }
    .chip { display: inline-block; background: #e0f2fe; color: #075985; border: 1px solid #bae6fd; border-radius: 10px; padding: 2px 8px; font-size: 7px; margin: 0 4px 3px 0; }
    .chip b { color: #0c4a6e; }

    /* KPIs */
    table.kpis { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
    .kpis td { width: 20%; padding: 0 3px; vertical-align: top; }
    .kpi { border: 1px solid #e2e8f0; border-top: 3px solid #007acc; border-radius: 5px; padding: 8px 10px; background: #f8fafc; }
    .kpi.alerta { border-top-color: #ef4444; }
    .kpi.aviso { border-top-color: #f59e0b; }
    .kpi-num { font-size: 18px; font-weight: bold; color: #0f172a; }
    .kpi-lbl { font-size: 6.5px; text-transform: uppercase; color: #64748b; letter-spacing: .5px; margin-top: 2px; }

    .seccion { font-size: 11px; font-weight: bold; color: #0f172a; border-bottom: 2px solid #007acc; padding-bottom: 3px; margin: 14px 0 8px; }
    .seccion span { font-size: 8px; color: #64748b; font-weight: normal; }

    /* Rejilla de 2 columnas para gráficos */
    table.grid2 { width: 100%; border-collapse: collapse; }
    .grid2 td.col { width: 50%; vertical-align: top; padding: 0 4px; }

    /* Gráficos de barras */
    .chart { border: 1px solid #e2e8f0; border-radius: 5px; padding: 8px 10px; margin-bottom: 8px; }
    .chart-title { font-size: 7.5px; font-weight: bold; text-transform: uppercase; color: #334155; margin-bottom: 6px; letter-spacing: .5px; }
    table.bars { width: 100%; border-collapse: collapse; }
    .bars td { padding: 2.5px 0; vertical-align: middle; }
    .bar-label { width: 32%; font-size: 7.5px; color: #475569; padding-right: 6px; }
    .bar-cell { width: 50%; }
    .track { background: #f1f5f9; height: 9px; border-radius: 4px; }
    .fill { height: 9px; border-radius: 4px; }
    .bar-value { width: 18%; text-align: right; font-size: 7.5px; font-weight: bold; }
    .bar-value span { color: #94a3b8; font-weight: normal; }
    .fill.fina, .track.fina { height: 5px; }
    .leyenda { font-size: 7px; color: #64748b; margin-bottom: 5px; }
    .leyenda .dot { margin-left: 8px; }

    /* Tablas de detalle */
    table.datos { width: 100%; border-collapse: collapse; }
    .datos thead { display: table-header-group; }
    .datos th { background: #0f172a; color: #ffffff; font-size: 6.5px; text-transform: uppercase; text-align: left; padding: 5px; letter-spacing: .3px; }
    .datos td { font-size: 7.2px; padding: 4px 5px; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
    .datos tr { page-break-inside: avoid; }
    .datos tr.grupo td { background: #e0f2fe; color: #075985; font-weight: bold; font-size: 8px; padding: 5px; border-bottom: 1px solid #bae6fd; }
    .datos tr.par td { background: #f8fafc; }

    .badge { display: inline-block; padding: 1px 6px; border-radius: 8px; font-size: 6px; font-weight: bold; text-transform: uppercase; color: #ffffff; }
    .mono { font-family: 'DejaVu Sans Mono', monospace; }
    .muted { color: #94a3b8; }
    .cons { display: inline-block; margin: 0 6px 2px 0; }
    .dot { display: inline-block; width: 6px; height: 6px; border-radius: 3px; margin-right: 2px; }
    .salto { page-break-before: always; }
    .vacio { color: #94a3b8; font-style: italic; padding: 6px 0; }
    .nota { font-size: 7px; color: #64748b; margin-top: 4px; }
</style>
</head>
<body>

{{-- ═══════════════════════ CABECERA ═══════════════════════ --}}
<div class="cabecera">
    <table>
        <tr>
            <td>
                <div class="marca">Peñaranda Digital</div>
                <div class="titulo">Reporte de inventario</div>
                <div class="subtitulo">Equipos e impresoras registrados en el sistema</div>
            </td>
            <td class="meta">
                Generado el {{ $generadoEn->format('d/m/Y') }} a las {{ $generadoEn->format('H:i') }}<br>
                Por {{ $generadoPor }}
            </td>
        </tr>
    </table>
</div>

<div class="filtros">
    @foreach ($filtros as $campo => $valor)
        <span class="chip"><b>{{ $campo }}:</b> {{ $valor }}</span>
    @endforeach
</div>

{{-- ═══════════════════════ KPIs ═══════════════════════ --}}
<table class="kpis">
    <tr>
        <td>
            <div class="kpi">
                <div class="kpi-num">{{ $incluyeEquipos ? $resumen['total_equipos'] : '—' }}</div>
                <div class="kpi-lbl">Equipos</div>
            </div>
        </td>
        <td>
            <div class="kpi">
                <div class="kpi-num">{{ $incluyeImpresoras ? $resumen['total_impresoras'] : '—' }}</div>
                <div class="kpi-lbl">Impresoras</div>
            </div>
        </td>
        <td>
            <div class="kpi">
                <div class="kpi-num">{{ $resumen['empresas_incluidas'] }}</div>
                <div class="kpi-lbl">Empresas incluidas</div>
            </div>
        </td>
        <td>
            <div class="kpi alerta">
                <div class="kpi-num">{{ $incluyeImpresoras ? $resumen['consumibles_alerta']->count() : '—' }}</div>
                <div class="kpi-lbl">Consumibles críticos</div>
            </div>
        </td>
        <td>
            <div class="kpi aviso">
                <div class="kpi-num">{{ $incluyeImpresoras ? $resumen['mantenimientos_vencidos']->count() : '—' }}</div>
                <div class="kpi-lbl">Mantenimientos vencidos</div>
            </div>
        </td>
    </tr>
</table>

{{-- ═══════════════════════ GRÁFICOS ═══════════════════════ --}}
<div class="seccion">Distribución del inventario</div>

<table class="grid2">
    @if ($incluyeEquipos)
        <tr>
            <td class="col">
                @include('reportes.inventario.pdf._barras', [
                    'titulo'  => 'Equipos por estado',
                    'datos'   => $resumen['equipos_por_estado'],
                    'labels'  => \App\Models\Equipo::ESTADOS,
                    'colores' => $coloresEstadoEquipo,
                ])
            </td>
            <td class="col">
                @include('reportes.inventario.pdf._barras', [
                    'titulo'  => 'Equipos por categoría',
                    'datos'   => $resumen['equipos_por_categoria'],
                    'labels'  => \App\Models\Equipo::CATEGORIAS,
                    'colores' => $coloresCategoria,
                ])
            </td>
        </tr>
    @endif

    @if ($incluyeImpresoras)
        <tr>
            <td class="col">
                @include('reportes.inventario.pdf._barras', [
                    'titulo'  => 'Impresoras por estado',
                    'datos'   => $resumen['impresoras_por_estado'],
                    'labels'  => \App\Models\Impresora::ESTADOS,
                    'colores' => $coloresEstadoImpresora,
                ])
            </td>
            <td class="col">
                @include('reportes.inventario.pdf._barras', [
                    'titulo'  => 'Impresoras por tecnología',
                    'datos'   => $resumen['impresoras_por_tecnologia'],
                    'labels'  => $etiquetasTecnologia,
                    'colores' => $coloresTecnologia,
                ])
            </td>
        </tr>
    @endif
</table>

{{-- Inventario por empresa (barras dobles) --}}
<div class="chart">
    <div class="chart-title">Inventario por empresa</div>
    <div class="leyenda">
        @if ($incluyeEquipos)<span class="dot" style="background: #007acc;"></span> Equipos @endif
        @if ($incluyeImpresoras)<span class="dot" style="background: #06b6d4;"></span> Impresoras @endif
    </div>

    @if (empty($resumen['por_empresa']))
        <div class="vacio">Sin datos para los filtros aplicados.</div>
    @else
        <table class="bars">
            @foreach ($resumen['por_empresa'] as $empresa => $fila)
                <tr>
                    <td class="bar-label">{{ $empresa }}</td>
                    <td class="bar-cell">
                        @if ($incluyeEquipos)
                            <div class="track fina" style="margin-bottom: 2px;">
                                <div class="fill fina" style="width: {{ $fila['equipos'] ? max(2, round($fila['equipos'] / $maxEmpresa * 100)) : 0 }}%; background: #007acc;"></div>
                            </div>
                        @endif
                        @if ($incluyeImpresoras)
                            <div class="track fina">
                                <div class="fill fina" style="width: {{ $fila['impresoras'] ? max(2, round($fila['impresoras'] / $maxEmpresa * 100)) : 0 }}%; background: #06b6d4;"></div>
                            </div>
                        @endif
                    </td>
                    <td class="bar-value">
                        @if ($incluyeEquipos){{ $fila['equipos'] }}@endif
                        @if ($incluyeEquipos && $incluyeImpresoras) <span>/</span> @endif
                        @if ($incluyeImpresoras){{ $fila['impresoras'] }}@endif
                    </td>
                </tr>
            @endforeach
        </table>
    @endif
</div>

{{-- ═══════════════════════ ALERTAS DE IMPRESORAS ═══════════════════════ --}}
@if ($incluyeImpresoras && ($resumen['consumibles_alerta']->isNotEmpty() || $resumen['mantenimientos_vencidos']->isNotEmpty()))
    <div class="seccion">Requiere atención <span>· impresoras con consumibles críticos o mantenimiento vencido</span></div>

    <table class="grid2">
        <tr>
            <td class="col">
                <div class="chart">
                    <div class="chart-title">Consumibles críticos o agotados</div>
                    @if ($resumen['consumibles_alerta']->isEmpty())
                        <div class="vacio">Ninguno.</div>
                    @else
                        <table class="datos">
                            <thead>
                                <tr><th>Impresora</th><th>Empresa / Sede</th><th>Consumible</th><th>Nivel</th></tr>
                            </thead>
                            <tbody>
                                @foreach ($resumen['consumibles_alerta']->take(15) as $fila)
                                    <tr>
                                        <td>{{ \Illuminate\Support\Str::limit($fila['impresora']->nombre_visible, 28) }}</td>
                                        <td>{{ $fila['impresora']->empresa?->nombre }}<br><span class="muted">{{ $fila['impresora']->sede?->nombre ?? 'Sin sede' }}</span></td>
                                        <td><span class="dot" style="background: {{ $fila['consumible']->color_hex }};"></span>{{ $fila['consumible']->descripcion_corta }}</td>
                                        <td style="color: #ef4444; font-weight: bold;">{{ $fila['consumible']->nivel_estimado }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if ($resumen['consumibles_alerta']->count() > 15)
                            <div class="nota">… y {{ $resumen['consumibles_alerta']->count() - 15 }} más (ver detalle de impresoras).</div>
                        @endif
                    @endif
                </div>
            </td>
            <td class="col">
                <div class="chart">
                    <div class="chart-title">Mantenimientos vencidos</div>
                    @if ($resumen['mantenimientos_vencidos']->isEmpty())
                        <div class="vacio">Ninguno.</div>
                    @else
                        <table class="datos">
                            <thead>
                                <tr><th>Impresora</th><th>Empresa / Sede</th><th>Programado</th><th>Atraso</th></tr>
                            </thead>
                            <tbody>
                                @foreach ($resumen['mantenimientos_vencidos']->take(15) as $imp)
                                    <tr>
                                        <td>{{ \Illuminate\Support\Str::limit($imp->nombre_visible, 28) }}</td>
                                        <td>{{ $imp->empresa?->nombre }}<br><span class="muted">{{ $imp->sede?->nombre ?? 'Sin sede' }}</span></td>
                                        <td class="mono">{{ $imp->proximo_mantenimiento_sugerido->format('d/m/Y') }}</td>
                                        <td style="color: #f59e0b; font-weight: bold;">
                                            {{ (int) $imp->proximo_mantenimiento_sugerido->startOfDay()->diffInDays(now()->startOfDay()) }} días
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if ($resumen['mantenimientos_vencidos']->count() > 15)
                            <div class="nota">… y {{ $resumen['mantenimientos_vencidos']->count() - 15 }} más.</div>
                        @endif
                    @endif
                </div>
            </td>
        </tr>
    </table>
@endif

{{-- ═══════════════════════ DETALLE DE EQUIPOS ═══════════════════════ --}}
@if ($incluyeEquipos)
    <div class="salto"></div>
    <div class="seccion">Detalle de equipos <span>· {{ $equipos->count() }} registros</span></div>

    @if ($equipos->isEmpty())
        <div class="vacio">No hay equipos para los filtros aplicados.</div>
    @else
        <table class="datos">
            <thead>
                <tr>
                    <th style="width: 9%;">Categoría</th>
                    <th style="width: 13%;">Nombre</th>
                    <th style="width: 12%;">Marca / Modelo</th>
                    <th style="width: 9%;">N° serie</th>
                    <th style="width: 9%;">Sede</th>
                    <th style="width: 10%;">Ubicación</th>
                    <th style="width: 9%;">Responsable</th>
                    <th style="width: 7%;">Estado</th>
                    <th style="width: 22%;">Especificaciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($equipos->groupBy(fn ($e) => $e->empresa?->nombre ?? 'Sin empresa') as $empresa => $grupo)
                    <tr class="grupo">
                        <td colspan="9">{{ $empresa }} · {{ $grupo->count() }} {{ $grupo->count() === 1 ? 'equipo' : 'equipos' }}</td>
                    </tr>
                    @foreach ($grupo as $e)
                        <tr class="{{ $loop->even ? 'par' : '' }}">
                            <td>{{ $e->label_categoria }}</td>
                            <td><b>{{ $e->nombre }}</b></td>
                            <td>{{ trim($e->marca . ' ' . $e->modelo) ?: '—' }}</td>
                            <td class="mono">{{ $e->numero_serie ?: '—' }}</td>
                            <td>{{ $e->sede?->nombre ?? 'Sin sede' }}</td>
                            <td>{{ $e->ubicacion ?: '—' }}</td>
                            <td>{{ $e->responsable?->name ?? '—' }}</td>
                            <td>
                                <span class="badge" style="background: {{ $coloresEstadoEquipo[$e->estado] ?? '#94a3b8' }};">
                                    {{ $e->label_estado }}
                                </span>
                            </td>
                            <td>{{ \Illuminate\Support\Str::limit(\App\Services\Reportes\InventarioReporteService::resumenEspecificaciones($e), 140) ?: '—' }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    @endif
@endif

{{-- ═══════════════════════ DETALLE DE IMPRESORAS ═══════════════════════ --}}
@if ($incluyeImpresoras)
    <div class="salto"></div>
    <div class="seccion">Detalle de impresoras <span>· {{ $impresoras->count() }} registros</span></div>

    @if ($impresoras->isEmpty())
        <div class="vacio">No hay impresoras para los filtros aplicados.</div>
    @else
        <table class="datos">
            <thead>
                <tr>
                    <th style="width: 10%;">Código</th>
                    <th style="width: 13%;">Nombre</th>
                    <th style="width: 13%;">Modelo</th>
                    <th style="width: 10%;">Sede</th>
                    <th style="width: 9%;">IP</th>
                    <th style="width: 8%;">Estado</th>
                    <th style="width: 27%;">Consumibles (nivel estimado)</th>
                    <th style="width: 10%;">Próx. mantenimiento</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($impresoras->groupBy(fn ($i) => $i->empresa?->nombre ?? 'Sin empresa') as $empresa => $grupo)
                    <tr class="grupo">
                        <td colspan="8">{{ $empresa }} · {{ $grupo->count() }} {{ $grupo->count() === 1 ? 'impresora' : 'impresoras' }}</td>
                    </tr>
                    @foreach ($grupo as $imp)
                        <tr class="{{ $loop->even ? 'par' : '' }}">
                            <td class="mono">
                                {{ $imp->codigo }}
                                @if ($imp->codigo_interno)<br><span class="muted">{{ $imp->codigo_interno }}</span>@endif
                            </td>
                            <td>
                                <b>{{ $imp->nombre_visible }}</b>
                                @if ($imp->ubicacion_fisica)<br><span class="muted">{{ $imp->ubicacion_fisica }}</span>@endif
                            </td>
                            <td>{{ $imp->modelo?->nombre_completo ?? 'Sin modelo' }}</td>
                            <td>{{ $imp->sede?->nombre ?? 'Sin sede' }}</td>
                            <td class="mono">{{ $imp->ip_red ?: '—' }}</td>
                            <td>
                                <span class="badge" style="background: {{ $coloresEstadoImpresora[$imp->estado] ?? '#94a3b8' }};">
                                    {{ $imp->label_estado }}
                                </span>
                            </td>
                            <td>
                                @forelse ($imp->consumiblesActivos as $c)
                                    <span class="cons">
                                        <span class="dot" style="background: {{ $c->color_hex }};"></span>{{ \Illuminate\Support\Str::limit($c->descripcion_corta, 18) }}
                                        <b style="color: {{ $colorNivel($c->estado_nivel) }};">
                                            {{ $c->nivel_estimado === null ? 'S/I' : $c->nivel_estimado . '%' }}
                                        </b>
                                    </span>
                                @empty
                                    <span class="muted">Sin consumibles</span>
                                @endforelse
                            </td>
                            <td class="mono">
                                @if ($imp->proximo_mantenimiento_sugerido)
                                    <span style="color: {{ $imp->mantenimientoVencido() ? '#ef4444' : ($imp->mantenimientoProximo() ? '#f59e0b' : '#1e293b') }};">
                                        {{ $imp->proximo_mantenimiento_sugerido->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="muted">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>

        <div class="nota">
            Los niveles de consumibles son estimaciones calculadas por días de uso configurados en cada impresora; no son una lectura física del equipo.
        </div>
    @endif
@endif

</body>
</html>