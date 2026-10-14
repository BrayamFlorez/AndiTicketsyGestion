{{-- resources/views/equipos/partials/form.blade.php --}}

@php
    $editing = isset($equipo) && $equipo !== null;
    $action  = $editing ? route('equipos.update', $equipo) : route('equipos.store');

    $valEmpresa     = old('empresa_id',     $editing ? $equipo->empresa_id : '');
    $valSede        = old('sede_id',        $editing ? $equipo->sede_id : '');
    $valResponsable = old('responsable_id', $editing ? $equipo->responsable_id : '');
    $valCategoria   = old('categoria',      $editing ? $equipo->categoria : '');
    $valEspecs      = old('especificaciones', $editing ? ($equipo->especificaciones ?? []) : []);

    // Campos de identificación: van a Alpine para poder autollenarlos con el JSON de PowerShell
    $valIdentificacion = [
        'nombre'       => old('nombre',       $editing ? $equipo->nombre : ''),
        'marca'        => old('marca',        $editing ? $equipo->marca : ''),
        'modelo'       => old('modelo',       $editing ? $equipo->modelo : ''),
        'numero_serie' => old('numero_serie', $editing ? $equipo->numero_serie : ''),
    ];
@endphp

<form action="{{ $action }}"
      method="POST"
      x-data="equipoForm({
          schema: {{ Js::from(\App\Models\Equipo::ESPECIFICACIONES_SCHEMA) }},
          sedesPorEmpresa: {{ Js::from($sedesPorEmpresa) }},
          usuariosPorEmpresa: {{ Js::from($usuariosPorEmpresa) }},
          empresaInicial: '{{ $valEmpresa }}',
          sedeInicial: '{{ $valSede }}',
          responsableInicial: '{{ $valResponsable }}',
          categoriaInicial: '{{ $valCategoria }}',
          valoresIniciales: {{ Js::from($valEspecs) }},
          identificacionInicial: {{ Js::from($valIdentificacion) }},
      })"
      class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
    @csrf
    @if ($editing) @method('PUT') @endif

    <div class="p-6 space-y-6">

        {{-- Resumen de errores: el campo con error puede quedar fuera de la vista --}}
        @if ($errors->any())
            <div class="flex items-start gap-2 rounded border border-meta-1/40 bg-meta-1/10 px-3 py-2 text-xs text-meta-1">
                <i class="fas fa-circle-exclamation mt-0.5"></i>
                <div>
                    <p class="font-bold">No se pudo guardar el equipo. Revisá los campos marcados:</p>
                    <ul class="mt-0.5 list-disc pl-4 text-[11px]">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- SECCIÓN 1: Vinculación & Contexto --}}
        <div>
            <div class="mb-3 pb-2 border-b border-stroke dark:border-strokedark">
                <h3 class="font-bold text-black dark:text-white uppercase tracking-wider text-xs flex items-center gap-1.5">
                    <i class="fas fa-building text-primary"></i> Vinculación y Ubicación
                </h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Empresa --}}
                <div>
                    <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">
                        Empresa <span class="text-meta-1">*</span>
                    </label>
                    <div class="relative z-20">
                        <select id="empresa_id" name="empresa_id" required
                                x-model="empresaSeleccionada"
                                class="w-full appearance-none rounded border border-stroke bg-transparent py-1.5 px-3 text-xs font-medium outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white @error('empresa_id') border-meta-1 @enderror">
                            <option value="" class="bg-[#1c2434] text-gray-400">— Seleccionar —</option>
                            @foreach ($empresas as $empresa)
                                <option value="{{ $empresa->id }}" class="bg-[#1c2434] text-white">{{ $empresa->nombre }}</option>
                            @endforeach
                        </select>
                        <span class="absolute top-1/2 right-2.5 -translate-y-1/2 z-30 pointer-events-none">
                            <i class="fas fa-chevron-down text-[10px] text-bodydark2"></i>
                        </span>
                    </div>
                    @error('empresa_id')<p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $message }}</p>@enderror
                </div>

                {{-- Sede --}}
                <div>
                    <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Sede (Opcional)</label>
                    <div class="relative z-20">
                        <select id="sede_id" name="sede_id"
                                x-model="sedeSeleccionada"
                                :disabled="!empresaSeleccionada"
                                class="w-full appearance-none rounded border border-stroke bg-transparent py-1.5 px-3 text-xs font-medium outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white disabled:bg-whiten disabled:cursor-not-allowed dark:disabled:bg-meta-4 @error('sede_id') border-meta-1 @enderror">
                            <option value="" class="bg-[#1c2434] text-gray-400">— Sin sede —</option>
                            <template x-for="sede in sedesFiltradas" :key="sede.id">
                                <option :value="sede.id" x-text="sede.nombre" :selected="mismoValor(sede.id, sedeSeleccionada)" class="bg-[#1c2434] text-white"></option>
                            </template>
                        </select>
                        <span class="absolute top-1/2 right-2.5 -translate-y-1/2 z-30 pointer-events-none">
                            <i class="fas fa-chevron-down text-[10px] text-bodydark2"></i>
                        </span>
                    </div>
                    @error('sede_id')<p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $message }}</p>@enderror
                </div>

                {{-- Responsable --}}
                <div>
                    <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Responsable (Opcional)</label>
                    <div class="relative z-20">
                        <select id="responsable_id" name="responsable_id"
                                x-model="responsableSeleccionado"
                                :disabled="!empresaSeleccionada"
                                class="w-full appearance-none rounded border border-stroke bg-transparent py-1.5 px-3 text-xs font-medium outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white disabled:bg-whiten disabled:cursor-not-allowed dark:disabled:bg-meta-4 @error('responsable_id') border-meta-1 @enderror">
                            <option value="" class="bg-[#1c2434] text-gray-400">— Sin responsable —</option>
                            <template x-for="u in usuariosFiltrados" :key="u.id">
                                <option :value="u.id" x-text="u.name" :selected="mismoValor(u.id, responsableSeleccionado)" class="bg-[#1c2434] text-white"></option>
                            </template>
                        </select>
                        <span class="absolute top-1/2 right-2.5 -translate-y-1/2 z-30 pointer-events-none">
                            <i class="fas fa-chevron-down text-[10px] text-bodydark2"></i>
                        </span>
                    </div>
                    @error('responsable_id')<p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $message }}</p>@enderror
                </div>

                {{-- Ubicación física --}}
                <div>
                    <label for="ubicacion" class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Ubicación Física</label>
                    <input type="text" id="ubicacion" name="ubicacion" maxlength="255"
                           value="{{ old('ubicacion', $editing ? $equipo->ubicacion : '') }}"
                           placeholder="Ej: Recepción, Oficina 203, Bodega"
                           class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs font-medium outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white @error('ubicacion') border-meta-1 @enderror">
                    @error('ubicacion')<p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- SECCIÓN 2: Identificación Hardware --}}
        <div>
            <div class="mb-3 pb-2 border-b border-stroke dark:border-strokedark flex flex-wrap items-center justify-between gap-2">
                <h3 class="font-bold text-black dark:text-white uppercase tracking-wider text-xs flex items-center gap-1.5">
                    <i class="fas fa-microchip text-primary"></i> Identificación del Equipo
                </h3>

                {{-- Autollenado desde PowerShell --}}
                <div class="flex items-center gap-2">
                    <button type="button" @click="copiarComandoPS()"
                            title="Copia un comando para ejecutar en PowerShell en el equipo a registrar"
                            class="inline-flex items-center gap-1.5 rounded bg-primary/10 text-primary py-1 px-3 text-[10px] font-bold uppercase tracking-wider hover:bg-primary hover:text-white transition shadow-sm cursor-pointer">
                        <i class="fas fa-terminal"></i> <span x-text="textoBotonCopiar"></span>
                    </button>
                    <button type="button" @click="pegarJson()"
                            title="Llena los campos con el JSON que dejó el comando en el portapapeles"
                            class="inline-flex items-center gap-1.5 rounded bg-success/10 text-success py-1 px-3 text-[10px] font-bold uppercase tracking-wider hover:bg-success hover:text-white transition shadow-sm cursor-pointer">
                        <i class="fas fa-clipboard"></i> Pegar JSON
                    </button>
                </div>
            </div>

            {{-- Resultado del autollenado --}}
            <div x-show="avisoJson.texto" x-cloak x-transition.opacity
                 class="mb-3 flex items-start gap-2 rounded border px-3 py-2 text-[11px]"
                 :class="avisoJson.tipo === 'error'
                     ? 'border-meta-1/40 bg-meta-1/10 text-meta-1'
                     : 'border-success/40 bg-success/10 text-success'">
                <i class="fas mt-0.5" :class="avisoJson.tipo === 'error' ? 'fa-circle-exclamation' : 'fa-circle-check'"></i>
                <span x-text="avisoJson.texto"></span>
                <button type="button" @click="avisoJson.texto = ''" class="ml-auto opacity-70 hover:opacity-100">
                    <i class="fas fa-times text-[10px]"></i>
                </button>
            </div>

            {{-- Pegado manual: el navegador no deja leer el portapapeles fuera de HTTPS/localhost --}}
            <div x-show="pegadoManual" x-cloak class="mb-3 rounded border border-stroke dark:border-strokedark p-3">
                <label for="json_manual" class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">
                    Pegá aquí el JSON (Ctrl + V)
                </label>
                <textarea id="json_manual" x-ref="jsonManual" rows="3" x-model="jsonManual"
                          @keydown.ctrl.enter.prevent="aplicarJsonManual()"
                          placeholder='{"nombre":"PC-01","marca":"HP",...}'
                          class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-[11px] font-mono outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white"></textarea>
                <div class="mt-2 flex justify-end gap-2">
                    <button type="button" @click="pegadoManual = false; jsonManual = ''"
                            class="rounded border border-stroke py-1 px-3 text-[10px] font-medium text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition">
                        Cancelar
                    </button>
                    <button type="button" @click="aplicarJsonManual()"
                            class="inline-flex items-center gap-1.5 rounded bg-success py-1 px-3 text-[10px] font-bold uppercase text-white hover:bg-opacity-90 transition">
                        <i class="fas fa-wand-magic-sparkles"></i> Aplicar
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                {{-- Categoría --}}
                <div>
                    <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">
                        Categoría <span class="text-meta-1">*</span>
                    </label>
                    <div class="relative z-20">
                        <select id="categoria" name="categoria" required
                                x-model="categoria"
                                class="w-full appearance-none rounded border border-stroke bg-transparent py-1.5 px-3 text-xs font-medium outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white cursor-pointer @error('categoria') border-meta-1 @enderror">
                            <option value="" class="bg-[#1c2434] text-gray-400">— Seleccionar —</option>
                            @foreach (\App\Models\Equipo::CATEGORIAS as $key => $label)
                                <option value="{{ $key }}" class="bg-[#1c2434] text-white">{{ $label }}</option>
                            @endforeach
                        </select>
                        <span class="absolute top-1/2 right-2.5 -translate-y-1/2 z-30 pointer-events-none">
                            <i class="fas fa-chevron-down text-[10px] text-bodydark2"></i>
                        </span>
                    </div>
                    @if ($editing)
                        <p class="mt-1 text-[10px] text-meta-6 font-medium" x-show="categoria !== categoriaInicial" x-cloak>
                            <i class="fas fa-exclamation-triangle mr-0.5"></i> Cambiar de categoría reseteará las especificaciones técnicas.
                        </p>
                    @endif
                    @error('categoria')<p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $message }}</p>@enderror
                </div>

                {{-- Nombre --}}
                <div>
                    <label for="nombre" class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">
                        Nombre Identificador <span class="text-meta-1">*</span>
                    </label>
                    <input type="text" id="nombre" name="nombre" required maxlength="150"
                           x-model="identificacion.nombre"
                           placeholder="Ej: PC-Recepcion-01"
                           class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs font-medium outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white @error('nombre') border-meta-1 @enderror">
                    @error('nombre')<p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $message }}</p>@enderror
                </div>

                {{-- Marca --}}
                <div>
                    <label for="marca" class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Marca</label>
                    <input type="text" id="marca" name="marca" maxlength="100"
                           x-model="identificacion.marca"
                           placeholder="Ej: HP, Lenovo, Epson"
                           class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs font-medium outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white @error('marca') border-meta-1 @enderror">
                    @error('marca')<p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $message }}</p>@enderror
                </div>

                {{-- Modelo --}}
                <div>
                    <label for="modelo" class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Modelo</label>
                    <input type="text" id="modelo" name="modelo" maxlength="100"
                           x-model="identificacion.modelo"
                           placeholder="Ej: ThinkPad E14"
                           class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs font-medium outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white @error('modelo') border-meta-1 @enderror">
                    @error('modelo')<p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $message }}</p>@enderror
                </div>

                {{-- Número de Serie --}}
                <div>
                    <label for="numero_serie" class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Número de Serie</label>
                    <input type="text" id="numero_serie" name="numero_serie" maxlength="100"
                           x-model="identificacion.numero_serie"
                           placeholder="S/N Único"
                           @error('numero_serie') autofocus aria-invalid="true" aria-describedby="numero_serie_error" @enderror
                           class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs font-mono outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white @error('numero_serie') border-meta-1 bg-meta-1/5 @enderror">
                    @error('numero_serie')
                        <div id="numero_serie_error" class="mt-1 flex items-start gap-1.5 rounded border border-meta-1/40 bg-meta-1/10 px-2 py-1.5 text-[10px] font-medium text-meta-1">
                            <i class="fas fa-triangle-exclamation mt-px"></i>
                            <span>{{ $message }}</span>
                        </div>
                    @else
                        <p class="mt-0.5 text-[10px] text-bodydark2">Debe ser único. Si el equipo no tiene serial, dejalo vacío.</p>
                    @enderror
                </div>

                {{-- Estado --}}
                <div>
                    <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Estado</label>
                    <div class="relative z-20">
                        <select id="estado" name="estado" class="w-full appearance-none rounded border border-stroke bg-transparent py-1.5 px-3 text-xs font-medium outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white cursor-pointer @error('estado') border-meta-1 @enderror">
                            @foreach (\App\Models\Equipo::ESTADOS as $key => $label)
                                <option value="{{ $key }}" @selected(old('estado', $editing ? $equipo->estado : 'activo') === $key) class="bg-[#1c2434] text-white">
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <span class="absolute top-1/2 right-2.5 -translate-y-1/2 z-30 pointer-events-none">
                            <i class="fas fa-chevron-down text-[10px] text-bodydark2"></i>
                        </span>
                    </div>
                    @error('estado')<p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- SECCIÓN 3: Especificaciones Dinámicas por Categoría --}}
        <div x-show="categoria" x-cloak>
            <div class="mb-3 pb-2 border-b border-stroke dark:border-strokedark flex items-center gap-2">
                <h3 class="font-bold text-black dark:text-white uppercase tracking-wider text-xs flex items-center gap-1.5">
                    <i class="fas fa-list-ul text-primary"></i> Especificaciones Técnicas
                </h3>
                <span class="text-[10px] font-bold text-primary uppercase" x-text="`(${labelCategoria})`"></span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <template x-for="(config, campo) in camposDelEsquema" :key="campo">
                    <div>
                        <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white" :for="`espec_${campo}`">
                            <span x-text="config.label"></span>
                            <span x-show="config.required !== false" class="text-meta-1">*</span>
                        </label>

                        {{-- SELECT --}}
                        <template x-if="config.type === 'select'">
                            <div class="relative z-20">
                                <select :id="`espec_${campo}`"
                                        :name="`especificaciones[${campo}]`"
                                        :required="config.required !== false"
                                        x-model="valores[campo]"
                                        class="w-full appearance-none rounded border border-stroke bg-transparent py-1.5 px-3 text-xs font-medium outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white cursor-pointer">
                                    <option value="" class="bg-[#1c2434] text-gray-400">— Seleccionar —</option>
                                    <template x-for="op in opcionesDe(config, campo)" :key="op">
                                        <option :value="op" :selected="mismoValor(op, valores[campo])"
                                                x-text="config.options.includes(op) ? op : `${op} (valor anterior)`"
                                                class="bg-[#1c2434] text-white"></option>
                                    </template>
                                </select>
                                <span class="absolute top-1/2 right-2.5 -translate-y-1/2 z-30 pointer-events-none">
                                    <i class="fas fa-chevron-down text-[10px] text-bodydark2"></i>
                                </span>
                            </div>
                        </template>

                        {{-- NUMBER --}}
                        <template x-if="config.type === 'number'">
                            <input type="number" min="0" step="any"
                                   :id="`espec_${campo}`"
                                   :name="`especificaciones[${campo}]`"
                                   :required="config.required !== false"
                                   x-model="valores[campo]"
                                   :placeholder="config.placeholder ?? ''"
                                   class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs font-medium outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                        </template>

                        {{-- TEXT --}}
                        <template x-if="config.type === 'text' || !config.type">
                            <input type="text" maxlength="200"
                                   :id="`espec_${campo}`"
                                   :name="`especificaciones[${campo}]`"
                                   :required="config.required !== false"
                                   x-model="valores[campo]"
                                   :placeholder="config.placeholder ?? ''"
                                   class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs font-medium outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                        </template>
                    </div>
                </template>
            </div>
        </div>

        {{-- SECCIÓN 4: Notas Internas --}}
        <div>
            <div class="mb-2 pb-2 border-b border-stroke dark:border-strokedark">
                <h3 class="font-bold text-black dark:text-white uppercase tracking-wider text-xs flex items-center gap-1.5">
                    <i class="fas fa-sticky-note text-primary"></i> Notas Internas
                </h3>
            </div>
            <div>
                <textarea id="notas" name="notas" rows="3" maxlength="2000"
                          placeholder="Observaciones de garantía, particularidades técnicas..."
                          class="w-full rounded border border-stroke bg-transparent py-2 px-3 text-xs font-medium outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white @error('notas') border-meta-1 @enderror">{{ old('notas', $editing ? $equipo->notas : '') }}</textarea>
                @error('notas')<p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $message }}</p>@enderror
            </div>
        </div>

    </div>

    {{-- Pie de Acciones Compacto --}}
    <div class="flex items-center justify-end gap-2 bg-gray-2 dark:bg-meta-4/40 px-6 py-3 border-t border-stroke dark:border-strokedark">
        <a href="{{ route('equipos.index') }}" 
           class="inline-flex items-center justify-center rounded border border-stroke py-1.5 px-4 text-center text-xs font-medium text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition">
            Cancelar
        </a>
        <button type="submit" 
                class="inline-flex items-center gap-1.5 rounded bg-primary py-1.5 px-5 text-center text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm cursor-pointer">
            <i class="fas fa-{{ $editing ? 'save' : 'check' }} text-[10px]"></i>
            {{ $editing ? 'Guardar Cambios' : 'Registrar Equipo' }}
        </button>
    </div>

</form>

@push('scripts')
<script>
    /*
     * Comando que se ejecuta en PowerShell en el equipo a registrar.
     * Deja en el portapapeles un JSON con: nombre, marca, modelo, numero_serie,
     * procesador, sistema, ram (GB), disco (GB) y tipo_disco.
     */
    const COMANDO_INVENTARIO_PS = `$c=Get-CimInstance Win32_ComputerSystem; $b=Get-CimInstance Win32_BIOS; $p=Get-CimInstance Win32_Processor | Select -First 1; $o=Get-CimInstance Win32_OperatingSystem; $d=Get-CimInstance Win32_LogicalDisk | Where-Object DeviceID -like 'C*'; $td=try{(Get-PhysicalDisk | Select -First 1).MediaType}catch{$null}; $ram=if($c.TotalPhysicalMemory){[math]::Round($c.TotalPhysicalMemory/1GB)}else{$null}; $disco=if($d.Size){[math]::Round($d.Size/1GB)}else{$null}; $so=if($o.Caption){$o.Caption.Replace('Microsoft','').Trim()}else{$null}; $data=[ordered]@{ nombre=$env:COMPUTERNAME; marca=$c.Manufacturer.Trim(); modelo=$c.Model.Trim(); numero_serie=$b.SerialNumber.Trim(); procesador=$p.Name.Trim(); sistema=$so; ram=$ram; disco=$disco; tipo_disco=$td }; $data | ConvertTo-Json -Compress | Set-Clipboard; Write-Host 'JSON copiado al portapapeles' -ForegroundColor Green`;

    /*
     * Dato del JSON → posibles nombres del campo en ESPECIFICACIONES_SCHEMA.
     * Solo se llenan los que existan en el esquema de la categoría elegida.
     */
    const ALIAS_ESPECIFICACIONES = {
        procesador: ['procesador', 'cpu'],
        sistema:    ['sistema', 'sistema_operativo', 'so'],
        ram:        ['ram', 'ram_gb', 'memoria_ram'],
        disco:      ['disco', 'almacenamiento', 'almacenamiento_gb', 'disco_gb'],
        tipo_disco: ['tipo_disco', 'tipo_almacenamiento'],
    };

    // Categoría que se elige sola si el formulario no tiene ninguna al pegar
    const CATEGORIAS_COMPUTADOR = ['pc', 'computador', 'portatil', 'laptop'];

    function equipoForm({ schema, sedesPorEmpresa, usuariosPorEmpresa,
                          empresaInicial, sedeInicial, responsableInicial,
                          categoriaInicial, valoresIniciales, identificacionInicial }) {
        return {
            schema,
            sedesPorEmpresa,
            usuariosPorEmpresa,

            empresaSeleccionada:     empresaInicial || '',
            sedeSeleccionada:        sedeInicial || '',
            responsableSeleccionado: responsableInicial || '',
            categoria:               categoriaInicial || '',
            categoriaInicial:        categoriaInicial || '',
            valores:                 { ...(valoresIniciales || {}) },
            identificacion:          { ...(identificacionInicial || {}) },

            // Autollenado desde PowerShell
            textoBotonCopiar: 'Copiar comando PS',
            pegadoManual:     false,
            jsonManual:       '',
            avisoJson:        { tipo: 'ok', texto: '' },

            get sedesFiltradas() {
                return this.sedesPorEmpresa[this.empresaSeleccionada] ?? [];
            },
            get usuariosFiltrados() {
                return this.usuariosPorEmpresa[this.empresaSeleccionada] ?? [];
            },
            get camposDelEsquema() {
                return this.schema[this.categoria] ?? {};
            },
            get labelCategoria() {
                const labels = {{ Js::from(\App\Models\Equipo::CATEGORIAS) }};
                return labels[this.categoria] ?? '';
            },

            /**
             * Compara ids/valores sin importar si vienen como número o texto.
             * Se usa en :selected porque en tu versión de Alpine, x-model no marca la opción
             * cuando las <option> se generan con x-for después de inicializar el select.
             */
            mismoValor(a, b) {
                return b !== null && b !== undefined && b !== '' && String(a) === String(b);
            },

            /**
             * Opciones del select de una especificación. Si el equipo tiene guardado un valor
             * que ya no está en el esquema, se agrega para no borrarlo en silencio al guardar.
             */
            opcionesDe(config, campo) {
                const opciones = config.options ?? [];
                const actual = this.valores[campo];
                const existe = opciones.some(op => this.mismoValor(op, actual));

                return (actual !== undefined && actual !== null && actual !== '' && ! existe)
                    ? [...opciones, actual]
                    : opciones;
            },

            init() {
                this.$watch('empresaSeleccionada', () => {
                    this.sedeSeleccionada = '';
                    this.responsableSeleccionado = '';
                });

                this.$watch('categoria', (nueva, anterior) => {
                    if (anterior && nueva !== anterior) {
                        this.valores = {};
                    }
                });
            },

            // ── Autollenado desde PowerShell ────────────────────────────────

            /** El navegador solo expone el portapapeles en HTTPS o localhost */
            portapapelesDisponible() {
                return window.isSecureContext && !! navigator.clipboard;
            },

            async copiarComandoPS() {
                try {
                    if (this.portapapelesDisponible()) {
                        await navigator.clipboard.writeText(COMANDO_INVENTARIO_PS);
                    } else {
                        this.copiarConTextarea(COMANDO_INVENTARIO_PS);
                    }

                    this.textoBotonCopiar = '¡Copiado!';
                    this.avisar('ok', 'Comando copiado. Pegalo en PowerShell en el equipo a registrar y después usá "Pegar JSON".');
                } catch {
                    this.avisar('error', 'No se pudo copiar el comando al portapapeles.');
                } finally {
                    setTimeout(() => this.textoBotonCopiar = 'Copiar comando PS', 2500);
                }
            },

            /** Respaldo para HTTP: el navegador aún permite copiar desde un textarea seleccionado */
            copiarConTextarea(texto) {
                const ta = document.createElement('textarea');
                ta.value = texto;
                ta.setAttribute('readonly', '');
                ta.style.position = 'fixed';
                ta.style.opacity = '0';
                document.body.appendChild(ta);
                ta.select();
                const ok = document.execCommand('copy');
                ta.remove();
                if (! ok) throw new Error('execCommand falló');
            },

            async pegarJson() {
                if (! this.portapapelesDisponible()) {
                    return this.abrirPegadoManual();
                }

                try {
                    this.aplicarJson(await navigator.clipboard.readText());
                } catch {
                    // Permiso de portapapeles denegado: se pega a mano
                    this.abrirPegadoManual();
                }
            },

            abrirPegadoManual() {
                this.pegadoManual = true;
                this.$nextTick(() => this.$refs.jsonManual?.focus());
            },

            aplicarJsonManual() {
                if (this.aplicarJson(this.jsonManual)) {
                    this.pegadoManual = false;
                    this.jsonManual = '';
                }
            },

            /** Llena el formulario con el JSON. Devuelve true si se aplicó */
            aplicarJson(texto) {
                let data;
                try {
                    data = JSON.parse(String(texto ?? '').trim());
                } catch {
                    data = null;
                }

                if (! data || typeof data !== 'object' || Array.isArray(data)) {
                    this.avisar('error', 'El texto no es un JSON válido. Ejecutá el comando en PowerShell y volvé a intentar.');
                    return false;
                }

                const limpiar = v => (v === null || v === undefined) ? '' : String(v).trim();
                const llenados = [];

                // Identificación
                for (const campo of ['nombre', 'marca', 'modelo', 'numero_serie']) {
                    if (limpiar(data[campo])) {
                        this.identificacion[campo] = limpiar(data[campo]);
                        llenados.push(campo);
                    }
                }

                // Sin categoría: se elige la de computador para que aparezcan las especificaciones
                if (! this.categoria) {
                    const clave = CATEGORIAS_COMPUTADOR.find(c => this.schema[c]);
                    if (clave) this.categoria = clave;
                }

                // Especificaciones: solo los campos que existen en el esquema de la categoría
                const esquema = this.camposDelEsquema;
                const nuevos = { ...this.valores };
                const noReconocidos = [];

                for (const [dato, alias] of Object.entries(ALIAS_ESPECIFICACIONES)) {
                    const valor = limpiar(data[dato]);
                    if (! valor) continue;

                    for (const campo of alias.filter(a => esquema[a])) {
                        const convertido = this.convertirValor(esquema[campo], valor);

                        if (convertido === null) {
                            noReconocidos.push(`${esquema[campo].label ?? campo}: "${valor}"`);
                            continue;
                        }

                        nuevos[campo] = convertido;
                        llenados.push(campo);
                    }
                }

                this.valores = nuevos;

                if (! llenados.length) {
                    this.avisar('error', 'El JSON no trae datos que coincidan con este formulario.');
                    return false;
                }

                let mensaje = `Se completaron ${llenados.length} campos. Revisalos antes de guardar.`;
                if (noReconocidos.length) {
                    mensaje += ` Sin opción equivalente (elegila a mano): ${noReconocidos.join(', ')}.`;
                }
                this.avisar(noReconocidos.length ? 'error' : 'ok', mensaje);

                return true;
            },

            /**
             * Ajusta el valor al tipo del campo. En los select busca la opción equivalente
             * (PowerShell devuelve "SSD", "HDD" o "Unspecified"); null si no hay ninguna.
             */
            convertirValor(config, valor) {
                if (config.type === 'number') {
                    const n = Number(valor);
                    return Number.isFinite(n) ? n : null;
                }

                if (config.type === 'select') {
                    const buscado = valor.toLowerCase();
                    const opciones = config.options ?? [];

                    return opciones.find(op => String(op).toLowerCase() === buscado)
                        ?? opciones.find(op => String(op).toLowerCase().includes(buscado))
                        ?? null;
                }

                return valor;
            },

            avisar(tipo, texto) {
                this.avisoJson = { tipo, texto };
            },
        };
    }
</script>
@endpush