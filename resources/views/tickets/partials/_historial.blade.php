{{-- resources/views/tickets/partials/_historial.blade.php --}}
{{-- Línea de tiempo del ticket: estados, asignaciones, reasignaciones y partes. Recibe: $ticket --}}

@php
    // Clases completas (Tailwind no detecta clases armadas dinámicamente)
    $colores = [
        'primary'   => 'bg-primary/10 text-primary border-primary/30',
        'meta-3'    => 'bg-meta-3/10 text-meta-3 border-meta-3/30',
        'meta-6'    => 'bg-meta-6/10 text-meta-6 border-meta-6/30',
        'meta-1'    => 'bg-meta-1/10 text-meta-1 border-meta-1/30',
        'bodydark2' => 'bg-gray-2 text-bodydark2 border-stroke dark:bg-meta-4 dark:border-strokedark',
    ];
    $estados = \App\Models\Ticket::ESTADOS;
@endphp

<div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
    <div class="border-b border-stroke py-2.5 px-4 dark:border-strokedark bg-gray-2 dark:bg-meta-4/20">
        <h3 class="font-bold text-[11px] uppercase tracking-wider text-black dark:text-white flex items-center gap-1.5">
            <i class="fas fa-stream text-primary"></i> Historial del Ticket
        </h3>
    </div>

    <div class="p-4">
        @forelse ($ticket->eventos as $evento)
            <div class="relative flex gap-3 {{ $loop->last ? '' : 'pb-4' }}">
                @unless ($loop->last)
                    <span class="absolute left-[13px] top-7 bottom-0 w-px bg-stroke dark:bg-strokedark"></span>
                @endunless

                <div class="relative z-10 flex h-7 w-7 shrink-0 items-center justify-center rounded-full border {{ $colores[$evento->color] ?? $colores['bodydark2'] }}">
                    <i class="fas {{ $evento->icono }} text-[10px]"></i>
                </div>

                <div class="min-w-0 flex-1 pt-0.5">
                    <div class="flex flex-wrap items-baseline justify-between gap-x-2">
                        <span class="text-[11px] font-bold text-black dark:text-white">{{ $evento->etiqueta }}</span>
                        <span class="text-[9px] font-mono text-bodydark2" title="{{ $evento->created_at->format('d/m/Y H:i:s') }}">
                            {{ $evento->created_at->format('d/m/Y H:i') }}
                        </span>
                    </div>

                    <div class="text-[10px] text-bodydark2 mt-0.5 space-y-0.5">
                        {{-- Técnico: anterior → nuevo --}}
                        @if ($evento->tecnico_nuevo_id)
                            <p>
                                <i class="fas fa-headset text-[8px] mr-0.5"></i>
                                @if ($evento->tecnicoAnterior)
                                    <span class="line-through">{{ $evento->tecnicoAnterior->name }}</span>
                                    <i class="fas fa-long-arrow-alt-right mx-0.5"></i>
                                @endif
                                <span class="font-bold text-black dark:text-white">{{ $evento->tecnicoNuevo?->name ?? 'Usuario eliminado' }}</span>
                            </p>
                        @endif

                        {{-- Cambio de estado --}}
                        @if ($evento->estado_anterior && $evento->estado_anterior !== $evento->estado_nuevo)
                            <p class="font-mono text-[9px] uppercase">
                                {{ $estados[$evento->estado_anterior] ?? $evento->estado_anterior }}
                                <i class="fas fa-long-arrow-alt-right mx-0.5"></i>
                                {{ $estados[$evento->estado_nuevo] ?? $evento->estado_nuevo }}
                            </p>
                        @endif

                        {{-- Datos de partes --}}
                        @if ($evento->tipo === 'partes_solicitadas' && $evento->datos)
                            <p>
                                @isset($evento->datos['fecha_estimada'])
                                    Llegada estimada: {{ \Illuminate\Support\Carbon::parse($evento->datos['fecha_estimada'])->format('d/m/Y') }}
                                @endisset
                                @isset($evento->datos['costo_estimado'])
                                    · Costo: $ {{ number_format((float) $evento->datos['costo_estimado'], 0, ',', '.') }}
                                @endisset
                            </p>
                        @endif
                        @if ($evento->tipo === 'partes_recibidas' && isset($evento->datos['dias_espera']))
                            <p>Esperó {{ $evento->datos['dias_espera'] }} {{ $evento->datos['dias_espera'] == 1 ? 'día' : 'días' }}</p>
                        @endif

                        {{-- Motivo / descripción --}}
                        @if ($evento->comentario)
                            <p class="text-black dark:text-white italic" x-data="{ todo: false }">
                                <span class="whitespace-pre-wrap" x-show="todo || {{ mb_strlen($evento->comentario) <= 160 ? 'true' : 'false' }}">«{{ $evento->comentario }}»</span>
                                @if (mb_strlen($evento->comentario) > 160)
                                    <span x-show="!todo">«{{ \Illuminate\Support\Str::limit($evento->comentario, 160) }}»</span>
                                    <button type="button" @click="todo = !todo" class="not-italic text-primary text-[9px] font-bold ml-1" x-text="todo ? 'ver menos' : 'ver más'"></button>
                                @endif
                            </p>
                        @endif

                        <p class="text-[9px]">
                            <i class="fas fa-user text-[8px] mr-0.5"></i>{{ $evento->usuario?->name ?? 'Sistema' }}
                        </p>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-[10px] font-mono text-bodydark2 text-center py-2">> SIN EVENTOS REGISTRADOS</p>
        @endforelse
    </div>
</div>
