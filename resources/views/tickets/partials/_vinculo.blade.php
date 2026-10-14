{{-- resources/views/tickets/partials/_vinculo.blade.php --}}
{{-- Equipo o impresora vinculada al ticket. Recibe: $ticket --}}

@php
    $activo = $ticket->activo_vinculado;
    $esImpresora = $activo instanceof \App\Models\Impresora;
    $rutaFicha = $esImpresora ? 'impresoras.show' : 'equipos.show';
    $filtro = $esImpresora ? ['impresora_id' => $activo?->id] : ['equipo_id' => $activo?->id];
@endphp

@if ($activo)
    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="border-b border-stroke py-2.5 px-4 dark:border-strokedark">
            <h3 class="font-bold text-black dark:text-white text-[10px] uppercase tracking-wider flex items-center gap-1.5">
                <i class="fas {{ $esImpresora ? 'fa-print' : 'fa-desktop' }} text-primary"></i>
                {{ $esImpresora ? 'Impresora vinculada' : 'Equipo vinculado' }}
            </h3>
        </div>
        <div class="p-3.5 space-y-2 text-xs">
            <div>
                @if (Route::has($rutaFicha))
                    <a href="{{ route($rutaFicha, $activo) }}" class="font-bold text-primary hover:underline truncate block">
                        {{ $esImpresora ? $activo->nombre_visible : $activo->nombre }}
                        <i class="fas fa-external-link-alt text-[8px] ml-0.5"></i>
                    </a>
                @else
                    <span class="font-bold text-black dark:text-white truncate block">{{ $esImpresora ? $activo->nombre_visible : $activo->nombre }}</span>
                @endif
                <div class="text-[9px] text-bodydark2 mt-0.5 font-mono truncate">
                    @if ($esImpresora)
                        {{ $activo->codigo }}{{ $activo->numero_serie ? ' · S/N ' . $activo->numero_serie : '' }}
                    @else
                        {{ $activo->label_categoria }}{{ $activo->numero_serie ? ' · S/N ' . $activo->numero_serie : '' }}
                    @endif
                </div>
                @if ($activo->sede)
                    <div class="text-[9px] text-bodydark2 mt-0.5 truncate">
                        <i class="fas fa-map-marker-alt text-primary text-[8px] mr-0.5"></i>{{ $activo->sede->nombre }}
                    </div>
                @endif
            </div>
            <a href="{{ route('tickets.index', $filtro) }}"
               class="inline-flex items-center gap-1 text-[9px] font-bold uppercase text-bodydark2 hover:text-primary transition">
                <i class="fas fa-history text-[8px]"></i> {{ $esImpresora ? 'Ver tickets de esta impresora' : 'Ver tickets de este equipo' }}
            </a>
        </div>
    </div>
@endif
