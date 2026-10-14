{{-- resources/views/impresoras/partials/_contador.blade.php --}}
{{-- Tarjeta "Contador Páginas" del show + modal "Agregar contador de páginas" con el histórico. --}}
{{-- Reemplaza la tarjeta del contador en la franja de métricas. Recibe: $impresora --}}

@php
    $lecturas = $impresora->contadores()->with('registrador:id,name')->latest()->limit(10)->get();
    $reabrirContador = old('_modal') === 'contador';
    $actual = $impresora->contador_paginas_actual;
@endphp

<div x-data="{ modalContador: {{ $reabrirContador ? 'true' : 'false' }}, valor: {{ Js::from($reabrirContador ? old('contador', '') : '') }} }"
     class="rounded-sm border border-stroke bg-white p-2 shadow-default dark:border-strokedark dark:bg-boxdark flex items-center gap-2">
    <div class="flex h-7 w-7 items-center justify-center rounded bg-meta-3/10 text-meta-3 shrink-0">
        <i class="fas fa-tachometer-alt text-xs"></i>
    </div>
    <div class="min-w-0 flex-1">
        <span class="text-[8px] font-bold text-bodydark2 uppercase block">Contador Páginas</span>
        <span class="text-[11px] font-bold font-mono text-black dark:text-white block"
              title="{{ $impresora->contador_actualizado_en ? 'Actualizado el ' . $impresora->contador_actualizado_en->format('d/m/Y H:i') : 'Sin lecturas' }}">
            {{ number_format($actual ?? 0) }}
        </span>
    </div>

    @can('gestionarMantenimientos', $impresora)
        <button type="button" @click="modalContador = true; $nextTick(() => $refs.contador?.focus())" title="Agregar contador de páginas"
                class="flex h-6 w-6 shrink-0 items-center justify-center rounded border border-stroke text-bodydark2 hover:border-meta-3 hover:text-meta-3 dark:border-strokedark transition">
            <i class="fas fa-plus text-[9px]"></i>
        </button>

        <template x-teleport="body">
            <div x-show="modalContador" x-cloak @keydown.escape.window="modalContador = false"
                 class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
                <div @click.outside="modalContador = false"
                     class="w-full max-w-sm rounded-sm border border-stroke bg-white shadow-2xl dark:border-strokedark dark:bg-boxdark">

                    <div class="flex items-center justify-between px-4 py-2.5 border-b border-stroke dark:border-strokedark">
                        <h4 class="font-bold text-xs text-black dark:text-white uppercase tracking-wider">
                            <i class="fas fa-tachometer-alt text-meta-3 mr-1"></i> Agregar contador de páginas
                        </h4>
                        <button type="button" @click="modalContador = false" class="text-bodydark2 hover:text-black dark:hover:text-white">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('impresoras.contadores.store', $impresora) }}" class="p-4 space-y-3">
                        @csrf
                        <input type="hidden" name="_modal" value="contador">

                        @if ($reabrirContador && $errors->any())
                            <div class="rounded border border-meta-1 bg-meta-1/10 p-2 text-[10px] text-meta-1">
                                @foreach ($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif

                        <div class="flex items-center justify-between rounded border border-stroke dark:border-strokedark bg-gray-2 dark:bg-meta-4/30 px-3 py-2 text-[10px]">
                            <span class="font-bold uppercase text-bodydark2">Último registrado</span>
                            <span class="font-mono font-bold text-black dark:text-white">
                                {{ $actual !== null ? number_format($actual) : '—' }}
                                @if ($impresora->contador_actualizado_en)
                                    <span class="font-normal text-bodydark2">· {{ $impresora->contador_actualizado_en->format('d/m/Y') }}</span>
                                @endif
                            </span>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold uppercase text-black dark:text-white mb-1">Contador actual *</label>
                            <input type="number" name="contador" x-ref="contador" x-model.number="valor" required min="0" step="1"
                                   placeholder="Ej: {{ number_format(($actual ?? 0) + 1500, 0, '', '') }}"
                                   class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-sm font-mono font-bold focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                            <p class="text-[9px] text-bodydark2 mt-0.5">Se registra con la fecha y hora actuales.</p>

                            @if ($actual !== null)
                                <p x-show="valor !== '' && valor >= {{ $actual }}" x-cloak class="text-[10px] text-meta-3 mt-1 font-mono"
                                   x-text="'+' + (valor - {{ $actual }}).toLocaleString('es-CO') + ' páginas desde la última lectura'"></p>
                            @endif
                        </div>

                        @if ($actual !== null)
                            {{-- Solo aparece si el valor es menor al último: el contador se reinició --}}
                            <label x-show="valor !== '' && valor < {{ $actual }}" x-cloak
                                   class="flex items-start gap-2 rounded border border-meta-6/40 bg-meta-6/10 p-2 text-[10px] text-black dark:text-white cursor-pointer">
                                <input type="checkbox" name="reinicio" value="1" class="mt-0.5 rounded border-stroke" @checked($reabrirContador && old('reinicio'))>
                                <span>El valor es menor al último registrado. Marque si el contador <b>se reinició</b> (cambio de tarjeta, reseteo de fábrica).</span>
                            </label>
                        @endif

                        <div class="flex justify-end gap-2 pt-2 border-t border-stroke dark:border-strokedark">
                            <button type="button" @click="modalContador = false"
                                    class="rounded border border-stroke py-1 px-3 text-[10px] font-bold uppercase hover:bg-gray dark:border-strokedark dark:hover:bg-meta-4">
                                Cancelar
                            </button>
                            <button type="submit"
                                    class="rounded bg-meta-3 py-1 px-3 text-[10px] font-bold uppercase text-white hover:bg-opacity-90">
                                <i class="fas fa-check mr-0.5"></i> Registrar
                            </button>
                        </div>
                    </form>

                    {{-- Histórico --}}
                    @if ($lecturas->isNotEmpty())
                        <div class="border-t border-stroke dark:border-strokedark px-4 py-3">
                            <p class="text-[10px] font-bold uppercase text-bodydark2 mb-1.5">Últimas lecturas</p>
                            <div class="max-h-48 overflow-y-auto space-y-1">
                                @foreach ($lecturas as $l)
                                    <div class="flex items-center justify-between gap-2 rounded bg-gray-2 dark:bg-meta-4/30 px-2 py-1 text-[10px] font-mono">
                                        <span class="text-bodydark2 shrink-0">{{ $l->created_at->format('d/m/Y H:i') }}</span>
                                        <span class="font-bold text-black dark:text-white">{{ number_format($l->contador) }}</span>
                                        <span class="shrink-0 w-20 text-right {{ $l->reinicio ? 'text-meta-6' : 'text-meta-3' }}"
                                              title="{{ $l->registrador?->name }}">
                                            @if ($l->reinicio)
                                                reinicio
                                            @elseif ($l->paginas_desde_anterior !== null)
                                                +{{ number_format($l->paginas_desde_anterior) }}
                                            @else
                                                —
                                            @endif
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </template>
    @endcan
</div>
