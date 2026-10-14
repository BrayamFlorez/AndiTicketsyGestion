{{-- resources/views/mpreventivo/form.blade.php --}}
@php
    $eq = $equipo ?? $mantenimiento->equipo ?? null;
@endphp

{{-- Cabecera Interna de la Tarjeta --}}
<div class="border-b border-stroke py-2.5 px-4 dark:border-strokedark bg-gray-2 dark:bg-meta-4/20">
    <h3 class="font-bold text-[11px] uppercase tracking-wider text-black dark:text-white flex items-center gap-1.5">
        <i class="fas fa-clipboard-check text-primary"></i> Detalles del Mantenimiento
    </h3>
    <p class="text-[10px] text-bodydark2 mt-0.5">
        Acciones preventivas realizadas al equipo: <strong class="text-black dark:text-white">{{ $eq?->nombre ?? 'N/A' }}</strong>
    </p>
</div>

<div class="p-4 space-y-3.5">

    {{-- Campo oculto para cumplir con la validación del backend --}}
    <input type="hidden" name="estado" value="{{ old('estado', $mantenimiento->estado ?? 'completado') }}">

    {{-- ── SECCIÓN 1: Tiempos de Ejecución ─────────────────────────────── --}}
    <div>
        <h4 class="text-[10px] font-bold uppercase tracking-wider text-primary mb-1.5 pb-1 border-b border-stroke dark:border-strokedark flex items-center gap-1.5">
            <i class="fas fa-clock"></i> 1. Tiempos de ejecución
        </h4>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label class="block text-[9px] font-bold uppercase text-black dark:text-white mb-0.5" for="fecha_inicio">
                    Inicio <span class="text-meta-1">*</span>
                </label>
                <input type="datetime-local" name="fecha_inicio" id="fecha_inicio" required
                       value="{{ old('fecha_inicio', $mantenimiento->fecha_inicio?->format('Y-m-d\TH:i')) }}"
                       class="w-full rounded border border-stroke bg-transparent py-1 px-2.5 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                @error('fecha_inicio')<p class="text-[9px] text-meta-1 mt-0.5">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-[9px] font-bold uppercase text-black dark:text-white mb-0.5" for="fecha_finalizacion">
                    Finalización <span class="text-meta-1">*</span>
                </label>
                <input type="datetime-local" name="fecha_finalizacion" id="fecha_finalizacion" required
                       value="{{ old('fecha_finalizacion', $mantenimiento->fecha_finalizacion?->format('Y-m-d\TH:i')) }}"
                       class="w-full rounded border border-stroke bg-transparent py-1 px-2.5 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                @error('fecha_finalizacion')<p class="text-[9px] text-meta-1 mt-0.5">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    {{-- ── SECCIÓN 2: Checklists ────────────────────────────────────────── --}}
    <div>
        <h4 class="text-[10px] font-bold uppercase tracking-wider text-primary mb-1.5 pb-1 border-b border-stroke dark:border-strokedark flex items-center gap-1.5">
            <i class="fas fa-tasks"></i> 2. Checklist de revisión
        </h4>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

            {{-- Hardware --}}
            <div class="rounded-sm border border-stroke bg-gray-2 p-2.5 dark:border-strokedark dark:bg-meta-4/20" x-data="{ checkHw: false }">
                <div class="flex items-center justify-between mb-1.5 pb-1.5 border-b border-stroke dark:border-strokedark">
                    <h5 class="text-[10px] font-bold text-black dark:text-white uppercase flex items-center gap-1.5">
                        <i class="fas fa-microchip text-primary"></i> Hardware
                    </h5>
                    <button type="button" @click="checkHw = !checkHw; $root.querySelectorAll('input[type=checkbox]').forEach(el => el.checked = checkHw)"
                            class="text-[8px] font-bold uppercase tracking-wider text-primary hover:text-opacity-80 transition flex items-center gap-1">
                        <i class="fas" :class="checkHw ? 'fa-times-circle text-meta-1' : 'fa-check-double'"></i> 
                        <span x-text="checkHw ? 'Desmarcar' : 'Marcar Todos'" :class="checkHw ? 'text-meta-1' : ''"></span>
                    </button>
                </div>
                <div class="space-y-1.5 text-[10px] text-black dark:text-white">
                    <label class="flex items-center gap-1.5 cursor-pointer select-none hover:text-primary transition">
                        <input type="checkbox" name="hw_limpieza_interna" value="1" @checked(old('hw_limpieza_interna', $mantenimiento->hw_limpieza_interna))
                               class="rounded border-stroke text-primary focus:ring-primary dark:border-strokedark w-3 h-3 cursor-pointer">
                        <span>Limpieza Física Interna (Soplado/Brocha)</span>
                    </label>
                    <label class="flex items-center gap-1.5 cursor-pointer select-none hover:text-primary transition">
                        <input type="checkbox" name="hw_limpieza_externa" value="1" @checked(old('hw_limpieza_externa', $mantenimiento->hw_limpieza_externa))
                               class="rounded border-stroke text-primary focus:ring-primary dark:border-strokedark w-3 h-3 cursor-pointer">
                        <span>Limpieza Estética Externa</span>
                    </label>
                    <label class="flex items-center gap-1.5 cursor-pointer select-none hover:text-primary transition">
                        <input type="checkbox" name="hw_cambio_pasta_termica" value="1" @checked(old('hw_cambio_pasta_termica', $mantenimiento->hw_cambio_pasta_termica))
                               class="rounded border-stroke text-primary focus:ring-primary dark:border-strokedark w-3 h-3 cursor-pointer">
                        <span>Cambio de Pasta Térmica (CPU)</span>
                    </label>
                    <label class="flex items-center gap-1.5 cursor-pointer select-none hover:text-primary transition">
                        <input type="checkbox" name="hw_revision_ventiladores" value="1" @checked(old('hw_revision_ventiladores', $mantenimiento->hw_revision_ventiladores))
                               class="rounded border-stroke text-primary focus:ring-primary dark:border-strokedark w-3 h-3 cursor-pointer">
                        <span>Revisión/Lubricación de Coolers</span>
                    </label>
                    <label class="flex items-center gap-1.5 cursor-pointer select-none hover:text-primary transition">
                        <input type="checkbox" name="hw_verificacion_cables" value="1" @checked(old('hw_verificacion_cables', $mantenimiento->hw_verificacion_cables))
                               class="rounded border-stroke text-primary focus:ring-primary dark:border-strokedark w-3 h-3 cursor-pointer">
                        <span>Verificación y Peinado de Cables</span>
                    </label>
                </div>
            </div>

            {{-- Software --}}
            <div class="rounded-sm border border-stroke bg-gray-2 p-2.5 dark:border-strokedark dark:bg-meta-4/20" x-data="{ checkSw: false }">
                <div class="flex items-center justify-between mb-1.5 pb-1.5 border-b border-stroke dark:border-strokedark">
                    <h5 class="text-[10px] font-bold text-black dark:text-white uppercase flex items-center gap-1.5">
                        <i class="fas fa-laptop-code text-primary"></i> Software
                    </h5>
                    <button type="button" @click="checkSw = !checkSw; $root.querySelectorAll('input[type=checkbox]').forEach(el => el.checked = checkSw)"
                            class="text-[8px] font-bold uppercase tracking-wider text-primary hover:text-opacity-80 transition flex items-center gap-1">
                        <i class="fas" :class="checkSw ? 'fa-times-circle text-meta-1' : 'fa-check-double'"></i> 
                        <span x-text="checkSw ? 'Desmarcar' : 'Marcar Todos'" :class="checkSw ? 'text-meta-1' : ''"></span>
                    </button>
                </div>
                <div class="space-y-1.5 text-[10px] text-black dark:text-white">
                    <label class="flex items-center gap-1.5 cursor-pointer select-none hover:text-primary transition">
                        <input type="checkbox" name="sw_limpieza_temporales" value="1" @checked(old('sw_limpieza_temporales', $mantenimiento->sw_limpieza_temporales))
                               class="rounded border-stroke text-primary focus:ring-primary dark:border-strokedark w-3 h-3 cursor-pointer">
                        <span>Limpieza de Temporales y Caché</span>
                    </label>
                    <label class="flex items-center gap-1.5 cursor-pointer select-none hover:text-primary transition">
                        <input type="checkbox" name="sw_actualizacion_so" value="1" @checked(old('sw_actualizacion_so', $mantenimiento->sw_actualizacion_so))
                               class="rounded border-stroke text-primary focus:ring-primary dark:border-strokedark w-3 h-3 cursor-pointer">
                        <span>Ejecutar Actualizaciones (SO)</span>
                    </label>
                    <label class="flex items-center gap-1.5 cursor-pointer select-none hover:text-primary transition">
                        <input type="checkbox" name="sw_revision_antivirus" value="1" @checked(old('sw_revision_antivirus', $mantenimiento->sw_revision_antivirus))
                               class="rounded border-stroke text-primary focus:ring-primary dark:border-strokedark w-3 h-3 cursor-pointer">
                        <span>Análisis y Actualización de Antivirus</span>
                    </label>
                    <label class="flex items-center gap-1.5 cursor-pointer select-none hover:text-primary transition">
                        <input type="checkbox" name="sw_revision_disco_duro" value="1" @checked(old('sw_revision_disco_duro', $mantenimiento->sw_revision_disco_duro))
                               class="rounded border-stroke text-primary focus:ring-primary dark:border-strokedark w-3 h-3 cursor-pointer">
                        <span>Revisión de Salud de Disco Duro</span>
                    </label>
                    <label class="flex items-center gap-1.5 cursor-pointer select-none hover:text-primary transition">
                        <input type="checkbox" name="sw_desinstalacion_basura" value="1" @checked(old('sw_desinstalacion_basura', $mantenimiento->sw_desinstalacion_basura))
                               class="rounded border-stroke text-primary focus:ring-primary dark:border-strokedark w-3 h-3 cursor-pointer">
                        <span>Desinstalación de Bloatware / Basura</span>
                    </label>
                </div>
            </div>

        </div>
    </div>

    {{-- ── SECCIÓN 3: Textos Libres ─────────────────────────────────────── --}}
    <div>
        <h4 class="text-[10px] font-bold uppercase tracking-wider text-primary mb-1.5 pb-1 border-b border-stroke dark:border-strokedark flex items-center gap-1.5">
            <i class="fas fa-comment-alt"></i> 3. Observaciones
        </h4>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label class="block text-[9px] font-bold uppercase text-black dark:text-white mb-0.5" for="observaciones_tecnicas">
                    Técnicas Internas
                </label>
                <textarea name="observaciones_tecnicas" id="observaciones_tecnicas" rows="2"
                          placeholder="Estado físico, ruidos extraños, alertas..."
                          class="w-full rounded border border-stroke bg-transparent py-1 px-2.5 text-[11px] outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">{{ old('observaciones_tecnicas', $mantenimiento->observaciones_tecnicas) }}</textarea>
            </div>

            <div>
                <label class="block text-[9px] font-bold uppercase text-black dark:text-white mb-0.5" for="recomendaciones_cliente">
                    Recomendaciones al Usuario
                </label>
                <textarea name="recomendaciones_cliente" id="recomendaciones_cliente" rows="2"
                          placeholder="Ej: Mantener despejada la ventilación..."
                          class="w-full rounded border border-stroke bg-transparent py-1 px-2.5 text-[11px] outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">{{ old('recomendaciones_cliente', $mantenimiento->recomendaciones_cliente) }}</textarea>
            </div>
        </div>
    </div>

    {{-- ── SECCIÓN 4: Evidencias ────────────────────────────────────────── --}}
    <div>
        <h4 class="text-[10px] font-bold uppercase tracking-wider text-primary mb-1.5 pb-1 border-b border-stroke dark:border-strokedark flex items-center gap-1.5">
            <i class="fas fa-paperclip"></i> 4. Evidencias
        </h4>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label class="block text-[9px] font-bold uppercase text-black dark:text-white mb-0.5" for="evidencia_fotografica_path">
                    Evidencia Fotográfica
                </label>
                <input type="file" name="evidencia_fotografica_path" id="evidencia_fotografica_path" accept="image/*"
                       class="w-full text-[10px] text-bodydark2 file:mr-2 file:py-0.5 file:px-2 file:rounded file:border-0 file:text-[9px] file:font-bold file:uppercase file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer">
                @if($mantenimiento->evidencia_fotografica_path)
                    <p class="text-[9px] text-meta-3 mt-1 flex items-center gap-1 font-bold"><i class="fas fa-check-circle"></i> Evidencia guardada.</p>
                @endif
                @error('evidencia_fotografica_path')<p class="text-[9px] text-meta-1 mt-0.5">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-[9px] font-bold uppercase text-black dark:text-white mb-0.5" for="firma_cliente_path">
                    Firma Conformidad
                </label>
                <input type="file" name="firma_cliente_path" id="firma_cliente_path" accept="image/*"
                       class="w-full text-[10px] text-bodydark2 file:mr-2 file:py-0.5 file:px-2 file:rounded file:border-0 file:text-[9px] file:font-bold file:uppercase file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer">
                @if($mantenimiento->firma_cliente_path)
                    <p class="text-[9px] text-meta-3 mt-1 flex items-center gap-1 font-bold"><i class="fas fa-check-circle"></i> Firma registrada.</p>
                @endif
                @error('firma_cliente_path')<p class="text-[9px] text-meta-1 mt-0.5">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

</div>

{{-- Footer con Botones de Acción --}}
<div class="flex items-center justify-end gap-2 border-t border-stroke bg-gray-2 px-5 py-2.5 dark:border-strokedark dark:bg-meta-4/40">
    <a href="{{ $eq ? route('equipos.show', $eq) : route('equipos.index') }}"
       class="inline-flex items-center rounded border border-stroke py-1.5 px-3.5 text-xs font-bold uppercase text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition">
        Cancelar
    </a>
    <button type="submit"
            class="inline-flex items-center gap-1.5 rounded bg-primary py-1.5 px-4 text-xs font-bold uppercase text-white hover:bg-opacity-90 transition shadow-sm cursor-pointer">
        <i class="fas fa-save text-[10px]"></i> {{ $mantenimiento->id ? 'Actualizar' : 'Guardar' }}
    </button>
</div>