<div class="form-card-header">
    <h3>Detalles del Mantenimiento</h3>
    <p>Registra las acciones preventivas realizadas al equipo: <strong>{{ $equipo->nombre }}</strong></p>
</div>

<div class="form-card-body">
    
    {{-- Campo oculto para cumplir con la validación del backend sin estorbar al técnico --}}
    <input type="hidden" name="estado" value="{{ old('estado', $mantenimiento->estado ?? 'completado') }}">

    {{-- ── SECCIÓN 1: Tiempos de Ejecución ─────────────────────────────── --}}
    <div class="form-section">
        <div class="form-section-title">1. Tiempos de ejecución</div>
        <div class="form-grid form-grid-2">
            
            <div class="form-group">
                <label class="form-label" for="fecha_inicio">Fecha y Hora de Inicio <span class="required">*</span></label>
                <input type="datetime-local" name="fecha_inicio" id="fecha_inicio" class="form-input @error('fecha_inicio') is-invalid @enderror"
                       value="{{ old('fecha_inicio', $mantenimiento->fecha_inicio?->format('Y-m-d\TH:i')) }}" required>
                @error('fecha_inicio')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="fecha_finalizacion">Fecha y Hora de Finalización <span class="required">*</span></label>
                <input type="datetime-local" name="fecha_finalizacion" id="fecha_finalizacion" class="form-input @error('fecha_finalizacion') is-invalid @enderror"
                       value="{{ old('fecha_finalizacion', $mantenimiento->fecha_finalizacion?->format('Y-m-d\TH:i')) }}" required>
                @error('fecha_finalizacion')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            
        </div>
    </div>

    {{-- ── SECCIÓN 2: Checklists ────────────────────────────────────────── --}}
    <div class="form-section">
        <div class="form-section-title">2. Checklist de revisión</div>
        <div class="form-grid form-grid-2">
            
            {{-- Hardware --}}
            <div style="background-color: var(--db-bg, #f8fafc); padding: 1.25rem; border-radius: 0.5rem; border: 1px solid var(--db-border, #e2e8f0);">
                <h4 style="font-size: 0.95rem; font-weight: 600; margin-bottom: 1rem; color: var(--db-text-bright);"><i class="fas fa-microchip"></i> Tareas de Hardware</h4>
                <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--db-text); font-size: 0.9rem;">
                        <input type="checkbox" name="hw_limpieza_interna" value="1" @checked(old('hw_limpieza_interna', $mantenimiento->hw_limpieza_interna))> Limpieza Física Interna (Soplado/Brocha)
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--db-text); font-size: 0.9rem;">
                        <input type="checkbox" name="hw_limpieza_externa" value="1" @checked(old('hw_limpieza_externa', $mantenimiento->hw_limpieza_externa))> Limpieza Estética Externa
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--db-text); font-size: 0.9rem;">
                        <input type="checkbox" name="hw_cambio_pasta_termica" value="1" @checked(old('hw_cambio_pasta_termica', $mantenimiento->hw_cambio_pasta_termica))> Cambio de Pasta Térmica (CPU)
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--db-text); font-size: 0.9rem;">
                        <input type="checkbox" name="hw_revision_ventiladores" value="1" @checked(old('hw_revision_ventiladores', $mantenimiento->hw_revision_ventiladores))> Revisión/Lubricación de Coolers
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--db-text); font-size: 0.9rem;">
                        <input type="checkbox" name="hw_verificacion_cables" value="1" @checked(old('hw_verificacion_cables', $mantenimiento->hw_verificacion_cables))> Verificación y Peinado de Cables
                    </label>
                </div>
            </div>

            {{-- Software --}}
            <div style="background-color: var(--db-bg, #f8fafc); padding: 1.25rem; border-radius: 0.5rem; border: 1px solid var(--db-border, #e2e8f0);">
                <h4 style="font-size: 0.95rem; font-weight: 600; margin-bottom: 1rem; color: var(--db-text-bright);"><i class="fas fa-laptop-code"></i> Tareas de Software</h4>
                <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--db-text); font-size: 0.9rem;">
                        <input type="checkbox" name="sw_limpieza_temporales" value="1" @checked(old('sw_limpieza_temporales', $mantenimiento->sw_limpieza_temporales))> Limpieza de Temporales y Caché
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--db-text); font-size: 0.9rem;">
                        <input type="checkbox" name="sw_actualizacion_so" value="1" @checked(old('sw_actualizacion_so', $mantenimiento->sw_actualizacion_so))> Ejecutar Actualizaciones (SO)
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--db-text); font-size: 0.9rem;">
                        <input type="checkbox" name="sw_revision_antivirus" value="1" @checked(old('sw_revision_antivirus', $mantenimiento->sw_revision_antivirus))> Análisis y Actualización de Antivirus
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--db-text); font-size: 0.9rem;">
                        <input type="checkbox" name="sw_revision_disco_duro" value="1" @checked(old('sw_revision_disco_duro', $mantenimiento->sw_revision_disco_duro))> Revisión de Salud de Disco Duro
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--db-text); font-size: 0.9rem;">
                        <input type="checkbox" name="sw_desinstalacion_basura" value="1" @checked(old('sw_desinstalacion_basura', $mantenimiento->sw_desinstalacion_basura))> Desinstalación de Bloatware / Basura
                    </label>
                </div>
            </div>

        </div>
    </div>

    {{-- ── SECCIÓN 3: Textos Libres ─────────────────────────────────────── --}}
    <div class="form-section">
        <div class="form-section-title">3. Observaciones y recomendaciones</div>
        <div class="form-grid form-grid-2">
            <div class="form-group">
                <label class="form-label" for="observaciones_tecnicas">Observaciones Técnicas Internas</label>
                <textarea name="observaciones_tecnicas" id="observaciones_tecnicas" rows="4" class="form-textarea" 
                          placeholder="Estado físico de piezas, ruidos extraños, alertas...">{{ old('observaciones_tecnicas', $mantenimiento->observaciones_tecnicas) }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label" for="recomendaciones_cliente">Recomendaciones al Usuario</label>
                <textarea name="recomendaciones_cliente" id="recomendaciones_cliente" rows="4" class="form-textarea" 
                          placeholder="Ej: Mantener despejada la ventilación, se sugiere cambiar disco SSD...">{{ old('recomendaciones_cliente', $mantenimiento->recomendaciones_cliente) }}</textarea>
            </div>
        </div>
    </div>

    {{-- ── SECCIÓN 4: Evidencias ────────────────────────────────────────── --}}
    <div class="form-section">
        <div class="form-section-title">4. Evidencias del servicio</div>
        <div class="form-grid form-grid-2">
            <div class="form-group">
                <label class="form-label" for="evidencia_fotografica_path">Evidencia Fotográfica</label>
                <input type="file" name="evidencia_fotografica_path" id="evidencia_fotografica_path" class="form-input" accept="image/*">
                @if($mantenimiento->evidencia_fotografica_path)
                    <p class="form-help" style="color: #10b981; margin-top: 0.5rem;"><i class="fas fa-check-circle"></i> Ya existe una evidencia guardada.</p>
                @endif
                @error('evidencia_fotografica_path')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="firma_cliente_path">Firma de Conformidad</label>
                <input type="file" name="firma_cliente_path" id="firma_cliente_path" class="form-input" accept="image/*">
                @if($mantenimiento->firma_cliente_path)
                    <p class="form-help" style="color: #10b981; margin-top: 0.5rem;"><i class="fas fa-check-circle"></i> Ya existe una firma registrada.</p>
                @endif
                @error('firma_cliente_path')<p class="form-error">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

</div>

<div class="form-card-footer">
    <a href="{{ route('equipos.index', $equipo) }}" class="db-btn ghost lg">Cancelar</a>
    <button type="submit" class="db-btn solid lg">
        <i class="fas fa-save"></i> {{ $mantenimiento->id ? 'Actualizar' : 'Guardar' }} Mantenimiento
    </button>
</div>