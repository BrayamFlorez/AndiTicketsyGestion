{{-- resources/views/impresoras/partials/_modal_recarga.blade.php --}}
{{-- Recarga parcial de un consumible. Se incluye dentro del @foreach de modales de _consumibles. --}}
{{-- Recibe: $impresora, $c (consumible activo). Usa la variable Alpine "recargando" del componente padre. --}}

@php
    $calc = app(\App\Services\Impresoras\NivelConsumible::class);
    [$nivelRef] = $calc->referencia($c);
    $diasRef = $calc->diasDesdeReferencia($c) ?? 0;
    $estimadoHoy = $calc->nivelEstimado($c) ?? 0;
    $reabrir = old('_modal') === 'recarga-' . $c->id;
    $inputClase = 'w-full rounded border border-stroke bg-transparent py-1.5 pl-3 pr-7 text-sm font-mono font-bold focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white';
@endphp

<template x-teleport="body">
    <div x-show="recargando === {{ $c->id }}" x-cloak
         @keydown.escape.window="recargando = null"
         @if ($reabrir) x-init="recargando = {{ $c->id }}" @endif
         class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">

        <div @click.outside="recargando = null"
             x-data="recargaParcial({
                 nivelReferencia: {{ Js::from($nivelRef) }},
                 diasDesdeReferencia: {{ Js::from($diasRef) }},
                 diasEstimados: {{ Js::from((int) $c->dias_estimados) }},
                 antes: {{ Js::from($reabrir ? old('nivel_antes', '') : '') }},
                 despues: {{ Js::from($reabrir ? old('nivel_despues', '') : '') }},
                 paso: {{ Js::from($reabrir ? 'datos' : 'aviso') }},
             })"
             class="w-full max-w-lg rounded-sm border border-stroke bg-white shadow-2xl dark:border-strokedark dark:bg-boxdark">

            <div class="flex items-center justify-between px-4 py-2.5 border-b border-stroke dark:border-strokedark">
                <h4 class="font-bold text-xs text-black dark:text-white uppercase tracking-wider">
                    <i class="fas fa-fill-drip text-primary mr-1"></i> Recargar porción · {{ $c->descripcion_corta }}
                </h4>
                <button type="button" @click="recargando = null" class="text-bodydark2 hover:text-black dark:hover:text-white">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>

            {{-- Paso 1: aviso --}}
            <div x-show="paso === 'aviso'" class="p-4 space-y-3">
                <div class="rounded border border-meta-6/40 bg-meta-6/10 p-3 text-xs text-black dark:text-white">
                    <p class="font-bold text-meta-6 uppercase text-[10px] tracking-wider mb-1">
                        <i class="fas fa-triangle-exclamation"></i> Solo para recargas en tiempo real
                    </p>
                    <p>
                        Use esta opción <b>únicamente si está recargando el consumible en este momento</b>.
                        El sistema compara el nivel que usted mide ahora con el que estimaba para hoy y, con esa diferencia,
                        recalibra cuántos días dura el consumible.
                    </p>
                    <p class="mt-1.5">
                        Si la recarga se hizo otro día, el cálculo quedaría mal y desajustaría las estimaciones.
                    </p>
                </div>

                <label class="flex items-start gap-2 cursor-pointer text-xs text-black dark:text-white">
                    <input type="checkbox" x-model="confirmado" class="mt-0.5 rounded border-stroke">
                    <span>Confirmo que estoy recargando este consumible ahora mismo.</span>
                </label>

                <div class="flex justify-end gap-2 pt-2 border-t border-stroke dark:border-strokedark">
                    <button type="button" @click="recargando = null"
                            class="rounded border border-stroke py-1 px-3 text-[10px] font-bold uppercase hover:bg-gray dark:border-strokedark dark:hover:bg-meta-4">
                        Cancelar
                    </button>
                    <button type="button" @click="paso = 'datos'; $nextTick(() => $refs.antes.focus())" :disabled="!confirmado"
                            class="rounded bg-primary py-1 px-3 text-[10px] font-bold uppercase text-white hover:bg-opacity-90 disabled:opacity-40 disabled:cursor-not-allowed">
                        Continuar <i class="fas fa-arrow-right ml-0.5"></i>
                    </button>
                </div>
            </div>

            {{-- Paso 2: niveles y cálculo --}}
            <form x-show="paso === 'datos'" method="POST" action="{{ route('impresoras.consumibles.recargar', [$impresora, $c]) }}"
                  class="p-4 space-y-3">
                @csrf
                <input type="hidden" name="_modal" value="recarga-{{ $c->id }}">
                <input type="hidden" name="en_tiempo_real" value="1">

                @if ($reabrir && $errors->any())
                    <div class="rounded border border-meta-1 bg-meta-1/10 p-2">
                        <ul class="text-[10px] text-meta-1 space-y-0.5 pl-4 list-disc">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="flex items-center justify-between rounded border border-stroke dark:border-strokedark bg-gray-2 dark:bg-meta-4/30 px-3 py-2">
                    <span class="text-[10px] font-bold uppercase text-bodydark2">El sistema estima para hoy</span>
                    <span class="font-mono text-sm font-bold text-black dark:text-white">
                        {{ round($estimadoHoy) }}<span class="text-[10px] text-bodydark2">%</span>
                        <span class="text-[10px] font-normal text-bodydark2">· duración {{ $c->dias_estimados }} días</span>
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-black dark:text-white mb-1">Nivel antes de recargar *</label>
                        <div class="relative">
                            <input type="number" name="nivel_antes" x-ref="antes" x-model.number="antes" required min="0" max="99" step="0.5"
                                   placeholder="Ej: 10" class="{{ $inputClase }}">
                            <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-xs text-bodydark2">%</span>
                        </div>
                        <p class="text-[9px] text-bodydark2 mt-0.5">Lo que marca el equipo o el depósito ahora.</p>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-black dark:text-white mb-1">Nivel después de recargar *</label>
                        <div class="relative">
                            <input type="number" name="nivel_despues" x-model.number="despues" required min="1" max="100" step="0.5"
                                   placeholder="Ej: 60" class="{{ $inputClase }}">
                            <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-xs text-bodydark2">%</span>
                        </div>
                        <div class="flex gap-1 mt-1">
                            <template x-for="p in [20, 50]" :key="p">
                                <button type="button" @click="sumar(p)" :disabled="!antesValido()"
                                        class="rounded border border-stroke dark:border-strokedark px-1.5 py-0.5 text-[9px] font-bold text-black dark:text-white hover:border-primary disabled:opacity-40"
                                        x-text="'+' + p + '%'"></button>
                            </template>
                            <button type="button" @click="despues = 100" :disabled="!antesValido()"
                                    class="rounded border border-stroke dark:border-strokedark px-1.5 py-0.5 text-[9px] font-bold text-black dark:text-white hover:border-primary disabled:opacity-40">
                                Lleno
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Vista previa del cálculo (mismas fórmulas que el servidor) --}}
                <div x-show="valido()" x-cloak class="rounded border border-primary/30 bg-primary/5 p-3 text-[11px] text-black dark:text-white space-y-1.5">
                    <p class="text-[10px] font-bold uppercase text-primary tracking-wider">Resultado</p>

                    <div class="flex justify-between gap-3">
                        <span class="text-bodydark2">Diferencia con lo estimado</span>
                        <span class="font-mono font-bold text-right" :class="Math.abs(r().desviacion_porcentaje) < 3 ? 'text-meta-3' : 'text-meta-6'">
                            <span x-text="textoDesviacion()"></span>
                        </span>
                    </div>
                    <div class="flex justify-between gap-3">
                        <span class="text-bodydark2">Porción recargada</span>
                        <span class="font-mono font-bold text-right" x-text="'+' + fmt(r().porcion_recargada) + '% (≈ ' + r().porcion_dias + ' días)'"></span>
                    </div>
                    <div class="flex justify-between gap-3">
                        <span class="text-bodydark2">Duración del consumible</span>
                        <span class="font-mono font-bold text-right">
                            <template x-if="r().recalibrado">
                                <span x-text="r().dias_estimados_anteriores + ' → ' + r().dias_estimados_nuevos + ' días'"></span>
                            </template>
                            <template x-if="!r().recalibrado">
                                <span class="text-bodydark2" x-text="r().dias_estimados_anteriores + ' días (sin cambio)'"></span>
                            </template>
                        </span>
                    </div>
                    <div class="flex justify-between gap-3 pt-1.5 border-t border-primary/20">
                        <span class="text-bodydark2">Días restantes estimados</span>
                        <span class="font-mono font-bold text-primary text-right" x-text="'≈ ' + r().dias_restantes_estimados + ' días'"></span>
                    </div>
                    <p x-show="r().motivo_sin_recalibrar" class="text-[10px] text-bodydark2 italic" x-text="r().motivo_sin_recalibrar"></p>
                </div>

                <p x-show="despues !== '' && antesValido() && despues <= antes" class="text-[10px] text-meta-1">
                    El nivel después de recargar debe ser mayor que el de antes.
                </p>

                <div>
                    <label class="block text-[10px] font-bold uppercase text-black dark:text-white mb-1">Observaciones</label>
                    <input type="text" name="observaciones" maxlength="500" value="{{ $reabrir ? old('observaciones') : '' }}"
                           placeholder="Ej: tinta genérica, se dejó a medio llenar por seguridad"
                           class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                </div>

                <div class="flex justify-between gap-2 pt-2 border-t border-stroke dark:border-strokedark">
                    <button type="button" @click="paso = 'aviso'"
                            class="rounded border border-stroke py-1 px-3 text-[10px] font-bold uppercase hover:bg-gray dark:border-strokedark dark:hover:bg-meta-4">
                        <i class="fas fa-arrow-left mr-0.5"></i> Atrás
                    </button>
                    <button type="submit" :disabled="!valido()"
                            class="rounded bg-primary py-1 px-3 text-[10px] font-bold uppercase text-white hover:bg-opacity-90 disabled:opacity-40 disabled:cursor-not-allowed">
                        <i class="fas fa-check mr-0.5"></i> Registrar recarga
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

@once
    @push('scripts')
        <script>
            /**
             * Vista previa de la recarga parcial.
             * Mismas fórmulas que App\Services\Impresoras\NivelConsumible::calcular().
             */
            function recargaParcial(cfg) {
                const MIN_DIAS = 2, MIN_CONSUMO = 5;

                return {
                    paso: cfg.paso,
                    confirmado: cfg.paso === 'datos',
                    antes: cfg.antes === '' ? '' : Number(cfg.antes),
                    despues: cfg.despues === '' ? '' : Number(cfg.despues),

                    antesValido() {
                        return this.antes !== '' && this.antes >= 0 && this.antes < 100;
                    },
                    valido() {
                        return this.antesValido() && this.despues !== '' && this.despues > this.antes && this.despues <= 100;
                    },
                    sumar(p) {
                        this.despues = Math.min(100, Math.round((this.antes + p) * 2) / 2);
                    },

                    r() {
                        const D = cfg.diasEstimados, dias = cfg.diasDesdeReferencia, ref = cfg.nivelReferencia;
                        const estimado = D > 0 ? Math.max(0, Math.min(100, ref - dias / D * 100)) : 0;
                        const consumo = ref - this.antes;

                        let diasNuevos = D, recalibrado = false, motivo = null;
                        if (dias < MIN_DIAS) {
                            motivo = 'Pasó muy poco tiempo desde la instalación o la última recarga para recalibrar.';
                        } else if (consumo < MIN_CONSUMO) {
                            motivo = 'El consumo medido es muy bajo para recalibrar con confianza.';
                        } else {
                            diasNuevos = Math.max(1, Math.min(3650, Math.round(dias * 100 / consumo)));
                            recalibrado = true;
                        }

                        const desviacion = estimado - this.antes;
                        return {
                            desviacion_porcentaje: desviacion,
                            desviacion_dias: Math.round(desviacion / 100 * D),
                            porcion_recargada: this.despues - this.antes,
                            porcion_dias: Math.round((this.despues - this.antes) / 100 * diasNuevos),
                            dias_estimados_anteriores: D,
                            dias_estimados_nuevos: diasNuevos,
                            recalibrado,
                            motivo_sin_recalibrar: motivo,
                            dias_restantes_estimados: Math.round(this.despues / 100 * diasNuevos),
                        };
                    },

                    textoDesviacion() {
                        const r = this.r(), d = Math.abs(r.desviacion_dias), p = this.fmt(Math.abs(r.desviacion_porcentaje));
                        if (Math.abs(r.desviacion_porcentaje) < 0.5) return 'Coincide con lo estimado';
                        return r.desviacion_porcentaje > 0
                            ? `Se gastó ${p}% más rápido (≈ ${d} días)`
                            : `Se gastó ${p}% más lento (≈ ${d} días)`;
                    },

                    fmt(n) {
                        return (Math.round(n * 10) / 10).toString().replace('.', ',');
                    },
                };
            }
        </script>
    @endpush
@endonce
