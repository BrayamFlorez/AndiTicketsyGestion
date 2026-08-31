{{-- resources/views/flujos/tipos-documento/partials/_form.blade.php --}}
{{-- Recibe: $tipo (DocumentType), $plantillas (Collection) --}}

@php
    $iconoActual = old('icono', $tipo->icono ?: 'fa-file-alt');
    // Lista de íconos por defecto
    $iconosPreset = [
        'fa-file-alt'       => 'Documento',
        'fa-file-invoice'   => 'Factura/Cotización',
        'fa-file-signature' => 'Firma/Contrato',
        'fa-file-contract'  => 'Contrato Legal',
        'fa-file-medical'   => 'Médico/Historia',
        'fa-file-code'      => 'Técnico/Código',
        'fa-folder-open'    => 'Expediente',
        'fa-clipboard-list' => 'Lista/Chequeo',
        'fa-receipt'        => 'Recibo/Comprobante',
        'fa-stamp'          => 'Aprobación/Sello',
    ];
    
    $esCustom = !array_key_exists($iconoActual, $iconosPreset);
@endphp

<div class="p-6 space-y-6" x-data="{
    iconoSeleccionado: '{{ $esCustom ? 'custom' : $iconoActual }}',
    iconoCustom: '{{ $iconoActual }}',
    get iconoFinal() {
        return this.iconoSeleccionado === 'custom' ? this.iconoCustom : this.iconoSeleccionado;
    }
}">

    {{-- Errores de Validación --}}
    @if ($errors->any())
        <div class="flex w-full border-l-6 border-meta-1 bg-meta-1/10 px-6 py-4 shadow-md dark:bg-meta-1/20 dark:border-meta-1 rounded-sm">
            <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-meta-1 text-white shrink-0">
                <i class="fas fa-exclamation-triangle text-sm"></i>
            </div>
            <div>
                <h5 class="mb-1 text-xs font-bold text-meta-1 uppercase tracking-wider">Revisa los siguientes campos:</h5>
                <ul class="list-disc list-inside space-y-0.5 text-xs text-meta-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- Nombre --}}
        <div class="md:col-span-2">
            <label for="nombre" class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">
                Nombre <span class="text-meta-1">*</span>
            </label>
            <input type="text" id="nombre" name="nombre" required maxlength="120" autofocus
                   value="{{ old('nombre', $tipo->nombre) }}"
                   placeholder="Ej: Cotización de Servicios"
                   class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white @error('nombre') border-meta-1 @enderror">
            <span class="mt-1 block text-[10px] text-bodydark2">Nombre visible del tipo de documento en el selector de inicio.</span>
            @error('nombre')<p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $message }}</p>@enderror
        </div>

        {{-- Slug / Identificador --}}
        <div>
            <label for="slug" class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">
                Identificador (slug)
            </label>
            <input type="text" id="slug" name="slug" maxlength="140"
                   value="{{ old('slug', $tipo->slug) }}"
                   placeholder="cotizacion-servicios"
                   class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs font-mono outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white @error('slug') border-meta-1 @enderror">
            <span class="mt-1 block text-[10px] text-bodydark2">Se generará automáticamente a partir del nombre si lo dejas vacío.</span>
            @error('slug')<p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $message }}</p>@enderror
        </div>

        {{-- Selección e Ícono --}}
        <div>
            <label for="icono_select" class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">
                Ícono (FontAwesome)
            </label>
            
            {{-- Input Hidden con el valor real que procesará Laravel --}}
            <input type="hidden" name="icono" :value="iconoFinal">

            <div class="flex items-center gap-3">
                {{-- Preview dinámico del ícono --}}
                <div class="flex h-9 w-9 items-center justify-center rounded bg-primary/10 text-primary shrink-0 transition">
                    <i class="fas text-sm" :class="iconoFinal"></i>
                </div>

                {{-- Selector con Preset + Opción Personalizada --}}
                <div class="relative z-20 flex-1">
                    <select id="icono_select" x-model="iconoSeleccionado"
                            class="w-full appearance-none rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white cursor-pointer @error('icono') border-meta-1 @enderror">
                        @foreach ($iconosPreset as $class => $label)
                            <option value="{{ $class }}" class="bg-[#1c2434] text-white">
                                {{ $label }} ({{ $class }})
                            </option>
                        @endforeach
                        <option value="custom" class="bg-[#1c2434] text-primary font-bold">
                            ✏️ Personalizado (Escribir clase)...
                        </option>
                    </select>
                    <span class="absolute top-1/2 right-3 -translate-y-1/2 z-30 pointer-events-none">
                        <i class="fas fa-chevron-down text-[10px] text-bodydark2"></i>
                    </span>
                </div>
            </div>

            {{-- Campo de texto que aparece únicamente al elegir Personalizado --}}
            <div x-show="iconoSeleccionado === 'custom'" x-cloak class="mt-2.5">
                <input type="text" x-model="iconoCustom" maxlength="60"
                       placeholder="Ej: fa-file-invoice"
                       class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs font-mono outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                <span class="mt-1 block text-[10px] text-bodydark2">
                    Escribe la clase de FontAwesome sin el prefijo <code class="font-mono text-black dark:text-white">fas</code>. Ej: <code class="font-mono text-black dark:text-white">fa-chart-pie</code>.
                </span>
            </div>

            @error('icono')<p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $message }}</p>@enderror
        </div>

        {{-- Plantilla de Flujo Asociada --}}
        <div class="md:col-span-2">
            <label for="workflow_template_id" class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">
                Plantilla de Flujo Asociada
            </label>
            <div class="relative z-20">
                <select id="workflow_template_id" name="workflow_template_id"
                        class="w-full appearance-none rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white cursor-pointer @error('workflow_template_id') border-meta-1 @enderror">
                    <option value="" class="bg-[#1c2434] text-gray-400">— Sin plantilla asociada —</option>
                    @foreach ($plantillas as $plantilla)
                        <option value="{{ $plantilla->id }}"
                            @selected(old('workflow_template_id', $tipo->workflow_template_id) == $plantilla->id) class="bg-[#1c2434] text-white">
                            {{ $plantilla->nombre }}
                        </option>
                    @endforeach
                </select>
                <span class="absolute top-1/2 right-3 -translate-y-1/2 z-30 pointer-events-none">
                    <i class="fas fa-chevron-down text-[10px] text-bodydark2"></i>
                </span>
            </div>
            @if ($plantillas->isEmpty())
                <span class="mt-1 block text-[10px] font-medium text-meta-6">
                    <i class="fas fa-info-circle mr-0.5"></i> Aún no hay plantillas de flujo activas. Podés guardarlo desvinculado y asociarlo luego.
                </span>
            @else
                <span class="mt-1 block text-[10px] text-bodydark2">Al iniciar un nuevo documento de este tipo, se ejecutará la secuencia de etapas descrita en la plantilla seleccionada.</span>
            @endif
            @error('workflow_template_id')<p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $message }}</p>@enderror
        </div>

        {{-- Descripción --}}
        <div class="md:col-span-2">
            <label for="descripcion" class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">
                Descripción (Opcional)
            </label>
            <textarea id="descripcion" name="descripcion" rows="3" maxlength="1000"
                      placeholder="Uso previsto de este tipo de documento, normas o instrucciones internas..."
                      class="w-full rounded border border-stroke bg-transparent py-2 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white @error('descripcion') border-meta-1 @enderror">{{ old('descripcion', $tipo->descripcion) }}</textarea>
            @error('descripcion')<p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $message }}</p>@enderror
        </div>

        {{-- Estado Activo --}}
        <div class="md:col-span-2 pt-1">
            <label class="flex items-center gap-2 cursor-pointer select-none">
                <input type="hidden" name="activo" value="0">
                <input type="checkbox" name="activo" value="1"
                       @checked(old('activo', $tipo->activo))
                       class="rounded border-stroke text-primary focus:ring-primary dark:border-form-strokedark dark:bg-form-input">
                <span class="text-xs font-bold text-black dark:text-white">Tipo de documento activo</span>
            </label>
            <span class="mt-0.5 block text-[10px] text-bodydark2">Si está inactivo, no aparecerá en el catálogo de inicio para nuevos trámites.</span>
        </div>

    </div>
</div>