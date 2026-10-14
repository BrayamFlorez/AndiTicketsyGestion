{{-- resources/views/impresoras/partials/_historial_contador.blade.php --}}
{{-- Histórico de lecturas del contador: fecha, páginas entre lecturas, promedio diario y páginas por mes. --}}
{{-- Recibe: $impresora --}}

@php
    $hist = app(\App\Services\Impresoras\HistorialContador::class)->para($impresora);
    $lecturas = $hist['lecturas'];
    $maxMes = max(1, (int) $hist['meses']->max('paginas'));
    $num = fn ($n) => $n !== null ? number_format($n, 0, ',', '.') : '—';
    $nombresMes = ['', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
@endphp

<div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark" x-data="{ todas: false }">
    <div class="flex items-center justify-between border-b border-stroke py-3 px-5 dark:border-strokedark">
        <h3 class="font-bold text-xs uppercase tracking-wider text-black dark:text-white flex items-center gap-2">
            <i class="fas fa-chart-bar text-primary"></i> Histórico de contador ({{ $lecturas->count() }})
        </h3>
        @if ($hist['primera'])
            <span class="text-[10px] font-mono text-bodydark2">desde {{ $hist['primera']->format('d/m/Y') }}</span>
        @endif
    </div>

    @if ($lecturas->isEmpty())
        <p class="text-[10px] font-mono text-bodydark2 text-center py-6">> SIN LECTURAS DEL CONTADOR</p>
    @else
        <div class="p-5 space-y-4">

            {{-- Resumen --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                @foreach ([
                    ['Páginas registradas', $num($hist['total_paginas']), 'entre la primera y la última lectura'],
                    ['Promedio diario', $hist['promedio_diario'] !== null ? $num($hist['promedio_diario']) : '—', 'páginas por día'],
                    ['Últimos 30 días', $num($hist['ultimos_30_dias']), 'páginas en lecturas recientes'],
                    ['Última lectura', $hist['ultima']?->format('d/m/Y'), $num($impresora->contador_paginas_actual) . ' en el contador'],
                ] as [$etiqueta, $valor, $nota])
                    <div class="rounded border border-stroke dark:border-strokedark p-2.5">
                        <span class="text-[8px] font-bold text-bodydark2 uppercase block">{{ $etiqueta }}</span>
                        <span class="text-sm font-bold font-mono text-black dark:text-white block">{{ $valor }}</span>
                        <span class="text-[9px] text-bodydark2 block truncate">{{ $nota }}</span>
                    </div>
                @endforeach
            </div>

            {{-- Páginas por mes (según la fecha de cada lectura) --}}
            @if ($hist['meses']->count() > 1)
                <div>
                    <span class="text-[9px] font-bold uppercase text-bodydark2 block mb-2">Páginas registradas por mes</span>
                    <div class="flex items-end gap-0.5 h-28 border-b border-stroke dark:border-strokedark">
                        @foreach ($hist['meses'] as $mes)
                            @php $alto = $mes['paginas'] > 0 ? max(3, round($mes['paginas'] / $maxMes * 100)) : 0; @endphp
                            <div class="group relative flex-1 h-full flex items-end justify-center cursor-default">
                                <div class="w-full max-w-[36px] rounded-t bg-primary group-hover:bg-opacity-80 transition" style="height: {{ $alto }}%"></div>
                                <div class="pointer-events-none absolute bottom-full mb-1 hidden group-hover:block whitespace-nowrap rounded bg-black px-2 py-1 text-[10px] text-white shadow z-10">
                                    {{ ucfirst($nombresMes[$mes['mes']->month]) }} {{ $mes['mes']->year }}: <b>{{ $num($mes['paginas']) }}</b> págs.
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex gap-0.5 mt-1">
                        @foreach ($hist['meses'] as $mes)
                            <span class="flex-1 text-center text-[8px] font-mono text-bodydark2">{{ $mes['mes']->format('m/y') }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Lecturas --}}
            <div class="overflow-x-auto">
                <table class="w-full text-[11px]">
                    <thead>
                        <tr class="text-left text-[8px] font-bold uppercase text-bodydark2 border-b border-stroke dark:border-strokedark">
                            <th class="py-1.5 pr-2">Fecha</th>
                            <th class="py-1.5 pr-2 text-right">Contador</th>
                            <th class="py-1.5 pr-2 text-right">Páginas</th>
                            <th class="py-1.5 pr-2 text-right">Días</th>
                            <th class="py-1.5 pr-2 text-right">Págs./día</th>
                            <th class="py-1.5 pr-2">Origen</th>
                            <th class="py-1.5">Registró</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lecturas as $l)
                            <tr class="border-b border-stroke/60 dark:border-strokedark/60" @if ($loop->index >= 10) x-show="todas" x-cloak @endif>
                                <td class="py-1.5 pr-2 font-mono text-black dark:text-white whitespace-nowrap">{{ $l->created_at->format('d/m/Y H:i') }}</td>
                                <td class="py-1.5 pr-2 font-mono font-bold text-black dark:text-white text-right">{{ $num($l->contador) }}</td>
                                <td class="py-1.5 pr-2 font-mono text-right whitespace-nowrap">
                                    @if ($l->reinicio)
                                        <span class="text-meta-6" title="El contador se reinició">reinicio</span>
                                    @elseif ($l->paginas_desde_anterior !== null)
                                        <span class="text-black dark:text-white">+{{ $num($l->paginas_desde_anterior) }}</span>
                                    @else
                                        <span class="text-bodydark2">—</span>
                                    @endif
                                </td>
                                <td class="py-1.5 pr-2 font-mono text-bodydark2 text-right">{{ $l->dias_desde_anterior !== null ? str_replace('.', ',', (string) $l->dias_desde_anterior) : '—' }}</td>
                                <td class="py-1.5 pr-2 font-mono text-bodydark2 text-right">{{ $num($l->paginas_por_dia) }}</td>
                                <td class="py-1.5 pr-2 whitespace-nowrap">
                                    @if ($l->mantenimiento_id)
                                        <a href="{{ route('impresoras.mantenimientos.show', [$impresora, $l->mantenimiento_id]) }}" class="text-primary hover:underline text-[10px]">
                                            <i class="fas fa-tools text-[9px]"></i> Mant. #{{ $l->mantenimiento_id }}
                                        </a>
                                    @else
                                        <span class="text-bodydark2 text-[10px]"><i class="fas fa-hand-pointer text-[9px]"></i> Manual</span>
                                    @endif
                                </td>
                                <td class="py-1.5 text-bodydark2 text-[10px] truncate max-w-[120px]">{{ $l->registrador?->name ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($lecturas->count() > 10)
                <button type="button" @click="todas = !todas" class="text-[10px] font-bold uppercase text-bodydark2 hover:text-primary">
                    <span x-text="todas ? 'Ver menos' : 'Ver las {{ $lecturas->count() }} lecturas'"></span>
                </button>
            @endif
        </div>
    @endif
</div>
