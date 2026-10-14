{{-- resources/views/tickets/partials/_partes.blade.php --}}
{{-- Solicitudes de partes del ticket (la más reciente primero). Recibe: $ticket --}}

@if ($ticket->solicitudesPartes->isNotEmpty())
    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="border-b border-stroke py-2.5 px-4 dark:border-strokedark bg-gray-2 dark:bg-meta-4/20 flex items-center justify-between">
            <h3 class="font-bold text-[11px] uppercase tracking-wider text-black dark:text-white flex items-center gap-1.5">
                <i class="fas fa-box-open text-meta-6"></i> Solicitudes de Partes ({{ $ticket->solicitudesPartes->count() }})
            </h3>
            @php $diasTotales = $ticket->solicitudesPartes->sum('dias_espera'); @endphp
            <span class="text-[9px] font-mono text-bodydark2">Espera total: {{ $diasTotales }} {{ $diasTotales === 1 ? 'día' : 'días' }}</span>
        </div>

        <div class="p-4 space-y-3">
            @foreach ($ticket->solicitudesPartes as $parte)
                @php
                    [$estadoTexto, $estadoClase] = match (true) {
                        $parte->atrasada  => ['Atrasada', 'bg-meta-1/10 text-meta-1 border-meta-1/20'],
                        $parte->pendiente => ['Pendiente', 'bg-meta-6/10 text-meta-6 border-meta-6/20'],
                        default           => ['Recibida', 'bg-meta-3/10 text-meta-3 border-meta-3/20'],
                    };
                @endphp
                <div class="rounded border border-stroke dark:border-strokedark p-3 {{ ! $loop->first ? 'opacity-80' : '' }}">
                    <div class="flex items-start justify-between gap-2">
                        <p class="text-xs text-black dark:text-white whitespace-pre-wrap font-mono leading-relaxed min-w-0">{{ $parte->descripcion }}</p>
                        <span class="shrink-0 inline-flex rounded-full px-2 py-0.5 text-[8px] font-bold uppercase border {{ $estadoClase }}">{{ $estadoTexto }}</span>
                    </div>

                    <div class="mt-2 grid grid-cols-2 sm:grid-cols-4 gap-2 text-[10px]">
                        <div>
                            <span class="text-[8px] font-bold text-bodydark2 uppercase block">Solicitada</span>
                            <span class="text-black dark:text-white font-mono">{{ $parte->created_at->format('d/m/Y') }}</span>
                            <span class="text-bodydark2 block truncate">{{ $parte->solicitante?->name }}</span>
                        </div>
                        <div>
                            <span class="text-[8px] font-bold text-bodydark2 uppercase block">Llegada estimada</span>
                            <span class="font-mono {{ $parte->atrasada ? 'text-meta-1 font-bold' : 'text-black dark:text-white' }}">
                                {{ $parte->fecha_estimada?->format('d/m/Y') ?? '—' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-[8px] font-bold text-bodydark2 uppercase block">Costo estimado</span>
                            <span class="text-black dark:text-white font-mono">
                                {{ $parte->costo_estimado !== null ? '$ ' . number_format((float) $parte->costo_estimado, 0, ',', '.') : '—' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-[8px] font-bold text-bodydark2 uppercase block">{{ $parte->pendiente ? 'Esperando' : 'Recibida' }}</span>
                            <span class="text-black dark:text-white font-mono">
                                @if ($parte->pendiente)
                                    {{ $parte->dias_espera }} {{ $parte->dias_espera === 1 ? 'día' : 'días' }}
                                @else
                                    {{ $parte->recibido_at->format('d/m/Y') }}
                                    <span class="text-bodydark2">({{ $parte->dias_espera }}d)</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    @if ($parte->evidencia || $parte->notas_recepcion)
                        <div class="mt-2 pt-2 border-t border-dashed border-stroke dark:border-strokedark flex flex-wrap items-start gap-3">
                            @if ($parte->evidencia)
                                <a href="{{ $parte->evidencia_url }}" target="_blank" rel="noopener" class="block shrink-0">
                                    @if ($parte->evidencia_es_imagen)
                                        <img src="{{ $parte->evidencia_url }}" alt="Evidencia" class="h-14 rounded border border-stroke dark:border-strokedark hover:opacity-80 transition">
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded border border-stroke dark:border-strokedark px-2 py-1 text-[9px] font-bold uppercase text-primary hover:bg-gray dark:hover:bg-meta-4">
                                            <i class="fas fa-file-pdf"></i> Evidencia
                                        </span>
                                    @endif
                                </a>
                            @endif
                            @if ($parte->notas_recepcion)
                                <p class="text-[10px] text-bodydark2 italic min-w-0">
                                    <i class="fas fa-comment-dots mr-0.5"></i> {{ $parte->notas_recepcion }}
                                    @if ($parte->receptor) — {{ $parte->receptor->name }} @endif
                                </p>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endif
