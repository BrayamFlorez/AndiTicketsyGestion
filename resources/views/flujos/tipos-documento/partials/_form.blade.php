{{-- Recibe: $tipo (DocumentType), $plantillas (Collection) --}}
<div class="form-card-body" style="padding: 1.5rem;">

    {{-- Errores de validación --}}
    @if ($errors->any())
        <div class="mb-4 px-4 py-3 rounded-lg bg-red-500/10 border border-red-500/30 text-red-300 text-sm">
            <div class="font-bold mb-2 flex items-center gap-2">
                <i class="fas fa-exclamation-triangle"></i> Revisa los siguientes campos:
            </div>
            <ul class="list-disc list-inside space-y-1 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- Nombre --}}
        <div class="form-group md:col-span-2">
            <label class="form-label" for="nombre">Nombre <span class="text-red-400">*</span></label>
            <input type="text" id="nombre" name="nombre" class="form-input"
                value="{{ old('nombre', $tipo->nombre) }}"
                placeholder="Ej: Cotización de Servicios" required maxlength="120" autofocus>
            <small class="text-xs text-gray-500 mt-1">Nombre visible del tipo de documento.</small>
        </div>

        {{-- Slug --}}
        <div class="form-group">
            <label class="form-label" for="slug">Identificador (slug)</label>
            <input type="text" id="slug" name="slug" class="form-input font-mono text-sm"
                value="{{ old('slug', $tipo->slug) }}"
                placeholder="cotizacion-servicios" maxlength="140">
            <small class="text-xs text-gray-500 mt-1">Se genera automáticamente si lo dejas vacío.</small>
        </div>

        {{-- Ícono --}}
        <div class="form-group">
            <label class="form-label" for="icono">Ícono</label>
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-lg bg-[#007acc]/10 flex items-center justify-center text-[#007acc] shrink-0">
                    <i class="fas {{ old('icono', $tipo->icono ?: 'fa-file-alt') }}" id="icono-preview"></i>
                </span>
                <input type="text" id="icono" name="icono" class="form-input font-mono text-sm"
                    value="{{ old('icono', $tipo->icono ?: 'fa-file-alt') }}"
                    placeholder="fa-file-invoice" maxlength="60"
                    oninput="document.getElementById('icono-preview').className = 'fas ' + this.value;">
            </div>
            <small class="text-xs text-gray-500 mt-1">Clase FontAwesome. Ej: <code>fa-file-invoice</code>, <code>fa-file-signature</code>.</small>
        </div>

        {{-- Plantilla asociada --}}
        <div class="form-group md:col-span-2">
            <label class="form-label" for="workflow_template_id">Plantilla de Flujo Asociada</label>
            <select id="workflow_template_id" name="workflow_template_id" class="form-select">
                <option value="">— Sin plantilla asociada —</option>
                @foreach ($plantillas as $plantilla)
                    <option value="{{ $plantilla->id }}"
                        @selected(old('workflow_template_id', $tipo->workflow_template_id) == $plantilla->id)>
                        {{ $plantilla->nombre }}
                    </option>
                @endforeach
            </select>
            @if ($plantillas->isEmpty())
                <small class="text-xs text-yellow-500/80 mt-1">
                    <i class="fas fa-info-circle"></i> Aún no hay plantillas activas. Podés dejarlo sin asociar y vincular después.
                </small>
            @else
                <small class="text-xs text-gray-500 mt-1">Al iniciar un documento de este tipo, se ejecutará la plantilla elegida.</small>
            @endif
        </div>

        {{-- Descripción --}}
        <div class="form-group md:col-span-2">
            <label class="form-label" for="descripcion">Descripción</label>
            <textarea id="descripcion" name="descripcion" rows="3" class="form-input"
                placeholder="Uso previsto del tipo, políticas internas, etc." maxlength="1000">{{ old('descripcion', $tipo->descripcion) }}</textarea>
        </div>

        {{-- Activo --}}
        <div class="form-group md:col-span-2">
            <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                <input type="hidden" name="activo" value="0">
                <input type="checkbox" name="activo" value="1" class="form-checkbox"
                    @checked(old('activo', $tipo->activo))>
                <span class="text-sm text-white font-semibold">Activo</span>
                <span class="text-xs text-gray-500">— si está inactivo no aparecerá al iniciar nuevos flujos</span>
            </label>
        </div>

    </div>
</div>