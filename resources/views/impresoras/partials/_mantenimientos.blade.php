{{-- Recibe: $impresora, $mantenimientos, $consumiblesActivos, $tiposMantenimiento, $usuariosParaRealizar, $puede --}}

<div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark"
     x-data="{ modalRegistrar: false }">

    <div class="flex items-center justify-between border-b border-stroke py-3 px-5 dark:border-strokedark">
        <h3 class="font-bold text-xs uppercase tracking-wider text-black dark:text-white flex items-center gap-2">
            <i class="fas fa-tools text-primary"></i>
            Mantenimientos ({{ $mantenimientos->count() }})
        </h3>

        @if ($puede['gestionar_mantenimientos'])
            <button type="button" @click="modalRegistrar = true"
                    class="inline-flex items-center gap-1 rounded bg-primary py-1 px-3 text-[11px] font-bold uppercase text-white hover:bg-opacity-90">
                <i class="fas fa-plus text-[9px]"></i> Registrar
            </button>
        @endif
    </div>

    {{-- Aviso de próximo mantenimiento --}}
    @if ($impresora->proximo_mantenimiento_sugerido)
        @php
            $vencido = $impresora->mantenimientoVencido();
            $proximo = $impresora->mantenimientoProximo();
            $diasHasta = (int) now()->startOfDay()->diffInDays($impresora->proximo_mantenimiento_sugerido->startOfDay(), false);
        @endphp

        <div class="px-5 pt-4">
            @if ($vencido)
                <div class="rounded border-l-4 border-meta-1 bg-meta-1/5 p-3 flex items-center gap-3">
                    <i class="fas fa-exclamation-triangle text-meta-1"></i>
                    <div class="flex-1 text-xs">
                        <div class="font-bold text-meta-1">Mantenimiento vencido</div>
                        <div class="text-bodydark2 text-[11px]">
                            Programado para {{ $impresora->proximo_mantenimiento_sugerido->format('d/m/Y') }}
                            · hace {{ abs($diasHasta) }} días
                        </div>
                    </div>
                </div>
            @elseif ($proximo)
                <div class="rounded border-l-4 border-meta-6 bg-meta-6/5 p-3 flex items-center gap-3">
                    <i class="fas fa-clock text-meta-6"></i>
                    <div class="flex-1 text-xs">
                        <div class="font-bold text-meta-6">Próximo mantenimiento en {{ $diasHasta }} días</div>
                        <div class="text-bodydark2 text-[11px]">
                            Programado para {{ $impresora->proximo_mantenimiento_sugerido->format('d/m/Y') }}
                        </div>
                    </div>
                </div>
            @else
                <div class="rounded border-l-4 border-meta-3 bg-meta-3/5 p-3 flex items-center gap-3">
                    <i class="fas fa-check-circle text-meta-3"></i>
                    <div class="flex-1 text-xs">
                        <div class="font-bold text-meta-3">Próximo mantenimiento en {{ $diasHasta }} días</div>
                        <div class="text-bodydark2 text-[11px]">
                            Programado para {{ $impresora->proximo_mantenimiento_sugerido->format('d/m/Y') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    {{-- Timeline --}}
    <div class="p-5">
        @if ($mantenimientos->isEmpty())
            <div class="text-center py-8">
                <i class="fas fa-tools text-2xl text-bodydark2 mb-2 block"></i>
                <p class="text-xs text-bodydark2">
                    Sin mantenimientos registrados.
                    @if ($puede['gestionar_mantenimientos'])
                        Registrá el primero para empezar el historial.
                    @endif
                </p>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($mantenimientos as $m)
                    <div class="rounded border border-stroke bg-gray-2 p-3 dark:border-strokedark dark:bg-meta-4/20">
                        <div class="flex items-start gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-{{ $m->color_tipo }} text-white shrink-0">
                                <i class="fas {{ $m->icono_tipo }} text-xs"></i>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2 flex-wrap mb-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-xs font-bold text-black dark:text-white">{{ $m->label_tipo }}</span>
                                        <span class="text-[10px] font-mono text-bodydark2">
                                            {{ $m->fecha_realizado->format('d/m/Y') }}
                                        </span>
                                        @if ($m->realizador)
                                            <span class="text-[10px] text-bodydark2">
                                                · por {{ $m->realizador->name }}
                                            </span>
                                        @endif
                                    </div>

                                    @if ($puede['gestionar_mantenimientos'])
                                        <form method="POST" action="{{ route('impresoras.mantenimientos.destroy', [$impresora, $m]) }}"
                                              onsubmit="return confirm('¿Eliminar este mantenimiento?')" class="shrink-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-bodydark2 hover:text-meta-1 transition text-xs p-1">
                                                <i class="fas fa-trash text-[10px]"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>

                                <p class="text-xs text-black dark:text-white leading-relaxed whitespace-pre-line mb-1">
                                    {{ $m->descripcion }}
                                </p>

                                @if ($m->observaciones)
                                    <p class="text-[11px] text-bodydark2 italic mb-1">
                                        <i class="fas fa-sticky-note text-[9px]"></i> {{ $m->observaciones }}
                                    </p>
                                @endif

                                <div class="flex items-center gap-3 flex-wrap text-[10px] text-bodydark2 mt-1">
                                    @if ($m->contador_paginas)
                                        <span>
                                            <i class="fas fa-file text-[9px]"></i>
                                            Contador: {{ number_format($m->contador_paginas) }}
                                        </span>
                                    @endif
                                    @if ($m->proximo_programado)
                                        <span>
                                            <i class="fas fa-calendar text-[9px]"></i>
                                            Próximo: {{ $m->proximo_programado->format('d/m/Y') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Modal registrar mantenimiento --}}
    @if ($puede['gestionar_mantenimientos'])
        <div x-show="modalRegistrar" x-cloak
             @keydown.escape.window="modalRegistrar = false"
             class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/60 backdrop-blur-sm py-8">
            <div @click.outside="modalRegistrar = false"
                 class="w-full max-w-2xl rounded-sm border border-stroke bg-white shadow-2xl dark:border-strokedark dark:bg-boxdark mx-4">

                <div class="flex items-center justify-between px-5 py-3 border-b border-stroke dark:border-strokedark sticky top-0 bg-white dark:bg-boxdark">
                    <h4 class="font-bold text-xs text-black dark:text-white uppercase tracking-wider">
                        <i class="fas fa-tools text-primary"></i>
                        Registrar mantenimiento
                    </h4>
                    <button type="button" @click="modalRegistrar = false" class="text-bodydark2 hover:text-black dark:hover:text-white">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form method="POST" action="{{ route('impresoras.mantenimientos.store', $impresora) }}"
                      class="p-5 space-y-4">
                    @csrf

                    {{-- Errores de validación --}}
                    @if ($errors->any())
                        <div class="rounded border border-meta-1 bg-meta-1/10 p-3">
                            <div class="text-[11px] font-bold text-meta-1 mb-1">
                                <i class="fas fa-exclamation-triangle text-[9px]"></i> Revisá lo siguiente:
                            </div>
                            <ul class="text-[10px] text-meta-1 space-y-0.5 pl-4 list-disc">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Datos base --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold uppercase text-black dark:text-white mb-1">
                                Tipo <span class="text-meta-1">*</span>
                            </label>
                            <select name="tipo" required
                                    class="w-full rounded border border-stroke bg-transparent py-1.5 px-2 text-xs focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                                @foreach ($tiposMantenimiento as $key => $label)
                                    <option value="{{ $key }}" @selected(old('tipo') === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold uppercase text-black dark:text-white mb-1">
                                Fecha realizado <span class="text-meta-1">*</span>
                            </label>
                            <input type="date" name="fecha_realizado" required
                                   value="{{ old('fecha_realizado', now()->format('Y-m-d')) }}"
                                   max="{{ now()->format('Y-m-d') }}"
                                   class="w-full rounded border border-stroke bg-transparent py-1.5 px-2 text-xs focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold uppercase text-black dark:text-white mb-1">
                                Realizado por
                            </label>
                            <select name="realizado_por"
                                    class="w-full rounded border border-stroke bg-transparent py-1.5 px-2 text-xs focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                                <option value="">— Yo mismo —</option>
                                @foreach ($usuariosParaRealizar as $u)
                                    <option value="{{ $u->id }}" @selected(old('realizado_por') == $u->id)>{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold uppercase text-black dark:text-white mb-1">
                                Próximo programado
                            </label>
                            <input type="date" name="proximo_programado"
                                   value="{{ old('proximo_programado') }}"
                                   class="w-full rounded border border-stroke bg-transparent py-1.5 px-2 text-xs focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                            <p class="text-[9px] text-bodydark2 mt-1">
                                Si tenés intervalo configurado ({{ $impresora->intervalo_mantenimiento_dias ?? '—' }} días), se calcula solo.
                            </p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase text-black dark:text-white mb-1">
                            Contador de páginas (opcional)
                        </label>
                        <input type="number" name="contador_paginas" min="0"
                               value="{{ old('contador_paginas', $impresora->contador_paginas_actual) }}"
                               class="w-full rounded border border-stroke bg-transparent py-1.5 px-2 text-xs focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                        <p class="text-[9px] text-bodydark2 mt-1">
                            Lectura actual del contador físico de la impresora.
                        </p>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase text-black dark:text-white mb-1">
                            Descripción <span class="text-meta-1">*</span>
                        </label>
                        <textarea name="descripcion" required minlength="5" maxlength="3000" rows="3"
                                  placeholder="¿Qué se hizo? Ej: Limpieza general del cabezal, cambio del rodillo, etc."
                                  class="w-full rounded border border-stroke bg-transparent py-2 px-3 text-xs focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">{{ old('descripcion') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase text-black dark:text-white mb-1">
                            Observaciones
                        </label>
                        <textarea name="observaciones" rows="2" maxlength="2000"
                                  class="w-full rounded border border-stroke bg-transparent py-2 px-3 text-xs focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">{{ old('observaciones') }}</textarea>
                    </div>

                    {{-- Reemplazo integrado de consumibles --}}
                    @if ($consumiblesActivos->isNotEmpty())
                        <div class="rounded border border-primary/30 bg-primary/5 p-3">
                            <h5 class="text-xs font-bold text-primary mb-2 flex items-center gap-1">
                                <i class="fas fa-fill-drip text-[10px]"></i>
                                ¿Reemplazaste algún consumible durante este mantenimiento?
                            </h5>
                            <p class="text-[10px] text-bodydark2 mb-3">
                                Marcá los que reemplazaste. Se archiva el actual y se crea uno nuevo con la fecha de este mantenimiento.
                            </p>

                            <div class="space-y-2">
                                @foreach ($consumiblesActivos as $idx => $c)
                                    @php
                                        $duracionActual = $c->fecha_instalacion
                                            ? (int) $c->fecha_instalacion->diffInDays(now())
                                            : null;
                                    @endphp

                                    <div class="rounded border border-stroke bg-white dark:border-strokedark dark:bg-boxdark p-2.5"
                                         x-data="{ marcado: false, recalibrar: false }">

                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" x-model="marcado" class="rounded border-stroke">
                                            <span class="w-3 h-3 rounded-full shrink-0" style="background: {{ $c->color_hex }};"></span>
                                            <span class="text-xs font-bold text-black dark:text-white">
                                                {{ $c->descripcion_corta }}
                                            </span>
                                            @if ($duracionActual !== null)
                                                <span class="text-[10px] text-bodydark2 ml-auto">
                                                    Duró {{ $duracionActual }} días
                                                </span>
                                            @endif
                                        </label>

                                        {{-- Solo se renderiza (y por ende se envía) cuando marcado = true --}}
                                        <template x-if="marcado">
                                            <div class="mt-2 pl-6 space-y-2 border-t border-stroke pt-2 dark:border-strokedark">
                                                <input type="hidden"
                                                       name="consumibles_reemplazados[{{ $idx }}][consumible_id]"
                                                       value="{{ $c->id }}">

                                                @if ($duracionActual !== null)
                                                    <label class="flex items-center gap-2 cursor-pointer text-[11px]">
                                                        <input type="checkbox"
                                                               name="consumibles_reemplazados[{{ $idx }}][recalibrar]"
                                                               value="1" x-model="recalibrar"
                                                               class="rounded border-stroke">
                                                        <span class="text-black dark:text-white">
                                                            Recalibrar: el anterior duró {{ $duracionActual }} días (estimado: {{ $c->dias_estimados }})
                                                        </span>
                                                    </label>
                                                @endif

                                                <div>
                                                    <label class="block text-[10px] font-bold uppercase text-bodydark2 mb-1">
                                                        Días estimados del nuevo
                                                    </label>
                                                    <input type="number"
                                                           name="consumibles_reemplazados[{{ $idx }}][dias_estimados]"
                                                           min="1" max="3650" required
                                                           :value="recalibrar ? {{ $duracionActual ?? $c->dias_estimados }} : {{ $c->dias_estimados }}"
                                                           :readonly="recalibrar"
                                                           class="w-full rounded border border-stroke bg-transparent py-1 px-2 text-xs focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white read-only:opacity-50">
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-end gap-2 pt-3 border-t border-stroke dark:border-strokedark">
                        <button type="button" @click="modalRegistrar = false"
                                class="rounded border border-stroke py-1.5 px-3 text-[11px] font-bold uppercase hover:bg-gray dark:border-strokedark dark:hover:bg-meta-4">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="rounded bg-primary py-1.5 px-4 text-[11px] font-bold uppercase text-white hover:bg-opacity-90">
                            <i class="fas fa-save text-[9px]"></i> Registrar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>