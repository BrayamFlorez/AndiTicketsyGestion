{{-- resources/views/equipos/partials/form.blade.php --}}

@php
    $editing = isset($equipo) && $equipo !== null;
    $action  = $editing ? route('equipos.update', $equipo) : route('equipos.store');

    $valEmpresa     = old('empresa_id',     $editing ? $equipo->empresa_id : '');
    $valSede        = old('sede_id',        $editing ? $equipo->sede_id : '');
    $valResponsable = old('responsable_id', $editing ? $equipo->responsable_id : '');
    $valCategoria   = old('categoria',      $editing ? $equipo->categoria : '');
    $valEspecs      = old('especificaciones', $editing ? ($equipo->especificaciones ?? []) : []);
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
      })"
      class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
    @csrf
    @if ($editing) @method('PUT') @endif

    <div class="p-6 space-y-6">

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
                                <option :value="sede.id" x-text="sede.nombre" class="bg-[#1c2434] text-white"></option>
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
                                <option :value="u.id" x-text="u.name" class="bg-[#1c2434] text-white"></option>
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
            <div class="mb-3 pb-2 border-b border-stroke dark:border-strokedark">
                <h3 class="font-bold text-black dark:text-white uppercase tracking-wider text-xs flex items-center gap-1.5">
                    <i class="fas fa-microchip text-primary"></i> Identificación del Equipo
                </h3>
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
                           value="{{ old('nombre', $editing ? $equipo->nombre : '') }}"
                           placeholder="Ej: PC-Recepcion-01"
                           class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs font-medium outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white @error('nombre') border-meta-1 @enderror">
                    @error('nombre')<p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $message }}</p>@enderror
                </div>

                {{-- Marca --}}
                <div>
                    <label for="marca" class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Marca</label>
                    <input type="text" id="marca" name="marca" maxlength="100"
                           value="{{ old('marca', $editing ? $equipo->marca : '') }}"
                           placeholder="Ej: HP, Lenovo, Epson"
                           class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs font-medium outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white @error('marca') border-meta-1 @enderror">
                    @error('marca')<p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $message }}</p>@enderror
                </div>

                {{-- Modelo --}}
                <div>
                    <label for="modelo" class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Modelo</label>
                    <input type="text" id="modelo" name="modelo" maxlength="100"
                           value="{{ old('modelo', $editing ? $equipo->modelo : '') }}"
                           placeholder="Ej: ThinkPad E14"
                           class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs font-medium outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white @error('modelo') border-meta-1 @enderror">
                    @error('modelo')<p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $message }}</p>@enderror
                </div>

                {{-- Número de Serie --}}
                <div>
                    <label for="numero_serie" class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Número de Serie</label>
                    <input type="text" id="numero_serie" name="numero_serie" maxlength="100"
                           value="{{ old('numero_serie', $editing ? $equipo->numero_serie : '') }}"
                           placeholder="S/N Único"
                           class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs font-mono outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white @error('numero_serie') border-meta-1 @enderror">
                    @error('numero_serie')<p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $message }}</p>@enderror
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
                                    <template x-for="op in config.options" :key="op">
                                        <option :value="op" x-text="op" class="bg-[#1c2434] text-white"></option>
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
    function equipoForm({ schema, sedesPorEmpresa, usuariosPorEmpresa,
                          empresaInicial, sedeInicial, responsableInicial,
                          categoriaInicial, valoresIniciales }) {
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
        };
    }
</script>
@endpush