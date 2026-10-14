{{-- resources/views/tickets/partials/form_admin.blade.php --}}

@php
    // Desde la ficha de un equipo/impresora llegan empresa, sede y equipo ya elegidos
    $vinculoInicial ??= null;
@endphp

<form action="{{ route('tickets.store') }}"
      method="POST"
      enctype="multipart/form-data"
      x-data="ticketAdminForm({
          sedesPorEmpresa: {{ Js::from($sedesPorEmpresa) }},
          usuariosPorEmpresa: {{ Js::from($usuariosPorEmpresa) }},
          tecnicos: {{ Js::from($tecnicos) }},
          coberturas: {{ Js::from($coberturas) }},
          activosPorEmpresa: {{ Js::from($activosPorEmpresa ?? []) }},
          empresaInicial: {{ Js::from((string) old('empresa_id', $vinculoInicial['empresa_id'] ?? '')) }},
          solicitanteInicial: {{ Js::from((string) old('solicitante_id', auth()->id())) }},
          solicitantePorDefecto: {{ Js::from((string) auth()->id()) }},
          sedeInicial: {{ Js::from((string) old('sede_id', $vinculoInicial['sede_id'] ?? '')) }},
          asignadoInicial: '{{ old('asignado_a') }}',
          vinculoInicial: {{ Js::from((string) old('vinculo', $vinculoInicial['valor'] ?? '')) }},
      })"
      class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
    @csrf

    <div class="p-5 space-y-5">

        {{-- SECCIÓN 1: Contexto y Asignación --}}
        <div>
            <div class="mb-3 pb-2 border-b border-stroke dark:border-strokedark flex items-center justify-between">
                <h3 class="font-bold text-black dark:text-white uppercase tracking-wider text-xs flex items-center gap-1.5">
                    <i class="fas fa-building text-primary"></i> Contexto y Asignación
                </h3>
                <span class="text-[10px] text-bodydark2">Campos marcados con (*) son obligatorios</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                {{-- Empresa --}}
                <div>
                    <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">
                        Empresa <span class="text-meta-1">*</span>
                    </label>
                    <div class="relative z-20">
                        <select id="empresa_id" name="empresa_id" required
                                x-model="empresaSeleccionada"
                                class="w-full appearance-none rounded border border-stroke bg-transparent py-1.5 px-3 text-xs font-medium outline-none transition focus:border-primary active:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white @error('empresa_id') border-meta-1 @enderror">
                            <option value="" class="text-body dark:text-bodydark">— Seleccionar —</option>
                            @foreach ($empresas as $empresa)
                                <option value="{{ $empresa->id }}" class="text-body dark:text-bodydark">{{ $empresa->nombre }}</option>
                            @endforeach
                        </select>
                        <span class="absolute top-1/2 right-2.5 -translate-y-1/2 z-30 pointer-events-none">
                            <i class="fas fa-chevron-down text-[10px] text-bodydark2"></i>
                        </span>
                    </div>
                    @error('empresa_id')<p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $message }}</p>@enderror
                </div>

                {{-- Solicitante --}}
                <div>
                    <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">
                        Solicitante <span class="text-meta-1">*</span>
                    </label>
                    <div class="relative z-20">
                        <select id="solicitante_id" name="solicitante_id" required
                                x-model="solicitanteSeleccionado"
                                :disabled="!empresaSeleccionada"
                                class="w-full appearance-none rounded border border-stroke bg-transparent py-1.5 px-3 text-xs font-medium outline-none transition focus:border-primary active:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white disabled:bg-whiten disabled:cursor-not-allowed dark:disabled:bg-meta-4 @error('solicitante_id') border-meta-1 @enderror">
                            <option value="" class="text-body dark:text-bodydark">— Seleccionar —</option>
                            {{-- Por defecto, quien crea el ticket --}}
                            <option value="{{ auth()->id() }}" :selected="solicitanteSeleccionado == '{{ auth()->id() }}'" class="text-body dark:text-bodydark">{{ auth()->user()->name }} (yo)</option>
                            <template x-for="usuario in usuariosFiltrados" :key="usuario.id">
                                <option :value="usuario.id" x-text="usuario.name" :selected="usuario.id == solicitanteSeleccionado" class="text-body dark:text-bodydark"></option>
                            </template>
                        </select>
                        <span class="absolute top-1/2 right-2.5 -translate-y-1/2 z-30 pointer-events-none">
                            <i class="fas fa-chevron-down text-[10px] text-bodydark2"></i>
                        </span>
                    </div>
                    @error('solicitante_id')<p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $message }}</p>@enderror
                </div>

                {{-- Sede --}}
                <div>
                    <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">
                        Sede (Opcional)
                    </label>
                    <div class="relative z-20">
                        <select id="sede_id" name="sede_id"
                                x-model="sedeSeleccionada"
                                :disabled="!empresaSeleccionada"
                                class="w-full appearance-none rounded border border-stroke bg-transparent py-1.5 px-3 text-xs font-medium outline-none transition focus:border-primary active:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white disabled:bg-whiten disabled:cursor-not-allowed dark:disabled:bg-meta-4 @error('sede_id') border-meta-1 @enderror">
                            <option value="" class="text-body dark:text-bodydark">— Sin sede —</option>
                            <template x-for="sede in sedesFiltradas" :key="sede.id">
                                <option :value="sede.id" x-text="sede.nombre" :selected="sede.id == sedeSeleccionada" class="text-body dark:text-bodydark"></option>
                            </template>
                        </select>
                        <span class="absolute top-1/2 right-2.5 -translate-y-1/2 z-30 pointer-events-none">
                            <i class="fas fa-chevron-down text-[10px] text-bodydark2"></i>
                        </span>
                    </div>
                    @error('sede_id')<p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $message }}</p>@enderror
                </div>

                {{-- Asignar Técnico --}}
                <div>
                    <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">
                        Asignar Técnico
                    </label>
                    <div class="relative z-20">
                        <select id="asignado_a" name="asignado_a"
                                x-model="asignadoSeleccionado"
                                :disabled="!empresaSeleccionada"
                                class="w-full appearance-none rounded border border-stroke bg-transparent py-1.5 px-3 text-xs font-medium outline-none transition focus:border-primary active:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white disabled:bg-whiten disabled:cursor-not-allowed dark:disabled:bg-meta-4 @error('asignado_a') border-meta-1 @enderror">
                            <option value="" class="text-body dark:text-bodydark">— Pendiente —</option>
                            <template x-for="t in tecnicosDisponibles" :key="t.id">
                                <option :value="t.id" x-text="t.name" :selected="t.id == asignadoSeleccionado" class="text-body dark:text-bodydark"></option>
                            </template>
                        </select>
                        <span class="absolute top-1/2 right-2.5 -translate-y-1/2 z-30 pointer-events-none">
                            <i class="fas fa-chevron-down text-[10px] text-bodydark2"></i>
                        </span>
                    </div>
                    @error('asignado_a')<p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $message }}</p>@enderror
                </div>
            </div>

            <p class="mt-1 text-[10px] text-meta-6 font-medium" x-show="empresaSeleccionada && tecnicosDisponibles.length === 0" x-cloak>
                <i class="fas fa-exclamation-triangle mr-1"></i> Ningún técnico tiene cobertura para esta sede/empresa.
            </p>

            {{-- Equipo o impresora vinculada (el ticket queda en su historial) --}}
            <div class="mt-3">
                <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">
                    <i class="fas fa-desktop text-primary mr-0.5"></i> Equipo vinculado (Opcional)
                </label>
                <div class="relative z-20">
                    <select id="vinculo" name="vinculo"
                            x-model="vinculo"
                            :disabled="!empresaSeleccionada"
                            class="w-full appearance-none rounded border border-stroke bg-transparent py-1.5 px-3 text-xs font-medium outline-none transition focus:border-primary active:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white disabled:bg-whiten disabled:cursor-not-allowed dark:disabled:bg-meta-4 @error('vinculo') border-meta-1 @enderror">
                        <option value="" class="text-body dark:text-bodydark">— Ninguno —</option>
                        <template x-if="equiposFiltrados.length">
                            <optgroup label="Equipos">
                                <template x-for="a in equiposFiltrados" :key="a.valor">
                                    <option :value="a.valor" :selected="a.valor === vinculo" x-text="a.nombre + (a.detalle ? ' — ' + a.detalle : '')" class="text-body dark:text-bodydark"></option>
                                </template>
                            </optgroup>
                        </template>
                        <template x-if="impresorasFiltradas.length">
                            <optgroup label="Impresoras">
                                <template x-for="a in impresorasFiltradas" :key="a.valor">
                                    <option :value="a.valor" :selected="a.valor === vinculo" x-text="a.nombre + (a.detalle ? ' — ' + a.detalle : '')" class="text-body dark:text-bodydark"></option>
                                </template>
                            </optgroup>
                        </template>
                    </select>
                    <span class="absolute top-1/2 right-2.5 -translate-y-1/2 z-30 pointer-events-none">
                        <i class="fas fa-chevron-down text-[10px] text-bodydark2"></i>
                    </span>
                </div>
                <p class="mt-0.5 text-[10px] text-bodydark2" x-show="empresaSeleccionada" x-cloak>
                    Se listan los equipos e impresoras de la empresa<span x-show="sedeSeleccionada"> en la sede elegida</span>. Al elegir uno, el ticket toma su sede.
                </p>
                @error('vinculo')<p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- SECCIÓN 2: Detalles del Ticket --}}
        <div>
            <div class="mb-3 pb-2 border-b border-stroke dark:border-strokedark">
                <h3 class="font-bold text-black dark:text-white uppercase tracking-wider text-xs flex items-center gap-1.5">
                    <i class="fas fa-align-left text-primary"></i> Información de la Solicitud
                </h3>
            </div>

            <div class="space-y-3">
                {{-- Título, Categoría y Prioridad en la misma fila para pantallas medianas --}}
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                    <div class="md:col-span-6">
                        <label for="titulo" class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">
                            Título del Requerimiento <span class="text-meta-1">*</span>
                        </label>
                        <input type="text" id="titulo" name="titulo" maxlength="150" required
                               value="{{ old('titulo') }}"
                               placeholder="Ej: Falla en impresora o red local"
                               class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs font-medium outline-none transition focus:border-primary active:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white @error('titulo') border-meta-1 @enderror">
                        @error('titulo')<p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-3">
                        <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">
                            Categoría <span class="text-meta-1">*</span>
                        </label>
                        <div class="relative z-20">
                            <select id="categoria" name="categoria" required 
                                    class="w-full appearance-none rounded border border-stroke bg-transparent py-1.5 px-3 text-xs font-medium outline-none transition focus:border-primary active:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white @error('categoria') border-meta-1 @enderror">
                                <option value="" class="text-body dark:text-bodydark">— Seleccionar —</option>
                                @foreach (\App\Models\Ticket::CATEGORIAS as $key => $label)
                                    <option value="{{ $key }}" @selected(old('categoria', $vinculoInicial['categoria'] ?? null) === $key) class="text-body dark:text-bodydark">{{ $label }}</option>
                                @endforeach
                            </select>
                            <span class="absolute top-1/2 right-2.5 -translate-y-1/2 z-30 pointer-events-none">
                                <i class="fas fa-chevron-down text-[10px] text-bodydark2"></i>
                            </span>
                        </div>
                        @error('categoria')<p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-3">
                        <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">
                            Prioridad <span class="text-meta-1">*</span>
                        </label>
                        <div class="relative z-20">
                            <select id="prioridad" name="prioridad" required 
                                    class="w-full appearance-none rounded border border-stroke bg-transparent py-1.5 px-3 text-xs font-medium outline-none transition focus:border-primary active:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white @error('prioridad') border-meta-1 @enderror">
                                @foreach (\App\Models\Ticket::PRIORIDADES as $key => $label)
                                    <option value="{{ $key }}" @selected(old('prioridad', 'media') === $key) class="text-body dark:text-bodydark">{{ $label }}</option>
                                @endforeach
                            </select>
                            <span class="absolute top-1/2 right-2.5 -translate-y-1/2 z-30 pointer-events-none">
                                <i class="fas fa-chevron-down text-[10px] text-bodydark2"></i>
                            </span>
                        </div>
                        @error('prioridad')<p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Descripción --}}
                <div>
                    <label for="descripcion" class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">
                        Descripción Detallada <span class="text-meta-1">*</span>
                    </label>
                    <textarea id="descripcion" name="descripcion" rows="3" required
                              placeholder="Describe las observaciones o detalles del problema..."
                              class="w-full rounded border border-stroke bg-transparent py-2 px-3 text-xs font-medium outline-none transition focus:border-primary active:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white @error('descripcion') border-meta-1 @enderror">{{ old('descripcion') }}</textarea>
                    @error('descripcion')<p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- SECCIÓN 3: Adjunto --}}
        <div>
            <div class="mb-2 pb-2 border-b border-stroke dark:border-strokedark">
                <h3 class="font-bold text-black dark:text-white uppercase tracking-wider text-xs flex items-center gap-1.5">
                    <i class="fas fa-paperclip text-primary"></i> Evidencia (Opcional)
                </h3>
            </div>

            <div x-data="{ nombre: '' }">
                <input type="file" name="foto_solicitud" id="foto_solicitud" accept="image/*"
                       @change="nombre = $event.target.files[0]?.name ?? ''"
                       class="w-full cursor-pointer rounded border border-stroke bg-transparent text-xs text-bodydark2 outline-none transition file:mr-4 file:cursor-pointer file:border-0 file:border-r file:border-solid file:border-stroke file:bg-gray-2 file:py-1.5 file:px-3 file:text-xs file:font-semibold file:text-black hover:file:bg-primary hover:file:text-white dark:border-form-strokedark dark:file:border-form-strokedark dark:file:bg-meta-4 dark:file:text-white @error('foto_solicitud') border-meta-1 @enderror">
                <p class="mt-1 text-[10px] text-bodydark2">JPG, PNG, WEBP — Máximo 5MB</p>
                @error('foto_solicitud')<p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $message }}</p>@enderror
            </div>
        </div>

    </div>

    {{-- Pie de Acciones Compacto --}}
    <div class="flex items-center justify-end gap-2 bg-gray-2 dark:bg-meta-4/40 px-5 py-3 border-t border-stroke dark:border-strokedark">
        <a href="{{ route('dashboard') }}" 
           class="inline-flex items-center justify-center rounded border border-stroke py-1.5 px-4 text-center text-xs font-medium text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition">
            Cancelar
        </a>
        <button type="submit" 
                class="inline-flex items-center justify-center gap-1.5 rounded bg-primary py-1.5 px-5 text-center text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm">
            <i class="fas fa-check text-[10px]"></i> Crear Ticket
        </button>
    </div>

</form>

@push('scripts')
<script>
    function ticketAdminForm({
        sedesPorEmpresa,
        usuariosPorEmpresa,
        tecnicos,
        coberturas,
        activosPorEmpresa,
        empresaInicial,
        solicitanteInicial,
        solicitantePorDefecto,
        sedeInicial,
        asignadoInicial,
        vinculoInicial,
    }) {
        return {
            sedesPorEmpresa,
            usuariosPorEmpresa,
            tecnicos,
            coberturas,
            activosPorEmpresa,
            solicitantePorDefecto,

            empresaSeleccionada:    empresaInicial    || '',
            solicitanteSeleccionado: solicitanteInicial || '',
            sedeSeleccionada:       sedeInicial       || '',
            asignadoSeleccionado:   asignadoInicial   || '',
            vinculo:                vinculoInicial    || '',

            get sedesFiltradas() {
                return this.sedesPorEmpresa[this.empresaSeleccionada] ?? [];
            },
            get usuariosFiltrados() {
                // Quien crea ya aparece como "(yo)"
                return (this.usuariosPorEmpresa[this.empresaSeleccionada] ?? [])
                    .filter(u => String(u.id) !== String(this.solicitantePorDefecto));
            },

            // Equipos e impresoras de la empresa (y de la sede elegida, o sin sede)
            get activosFiltrados() {
                const lista = this.activosPorEmpresa[this.empresaSeleccionada] ?? [];
                if (!this.sedeSeleccionada) return lista;
                const sedeId = parseInt(this.sedeSeleccionada);
                return lista.filter(a => a.sede_id === null || parseInt(a.sede_id) === sedeId);
            },
            get equiposFiltrados()    { return this.activosFiltrados.filter(a => a.tipo === 'equipo'); },
            get impresorasFiltradas() { return this.activosFiltrados.filter(a => a.tipo === 'impresora'); },

            get tecnicosDisponibles() {
                if (!this.empresaSeleccionada) return [];

                const empresaId = parseInt(this.empresaSeleccionada);
                const sedeId = this.sedeSeleccionada ? parseInt(this.sedeSeleccionada) : null;

                const idsConCobertura = new Set(
                    this.coberturas
                        .filter(c => {
                            if (parseInt(c.empresa_id) !== empresaId) return false;
                            if (sedeId === null) return true;
                            return c.sede_id === null || parseInt(c.sede_id) === sedeId;
                        })
                        .map(c => parseInt(c.user_id))
                );

                return this.tecnicos.filter(t => idsConCobertura.has(parseInt(t.id)));
            },

            init() {
                this.$watch('empresaSeleccionada', () => {
                    this.solicitanteSeleccionado = this.solicitantePorDefecto;
                    this.sedeSeleccionada = '';
                    this.asignadoSeleccionado = '';
                    this.vinculo = '';
                });

                // El ticket toma la sede del equipo elegido
                this.$watch('vinculo', (valor) => {
                    const activo = this.activosFiltrados.find(a => a.valor === valor);
                    if (activo && activo.sede_id && String(activo.sede_id) !== String(this.sedeSeleccionada)) {
                        this.sedeSeleccionada = String(activo.sede_id);
                    }
                });

                this.$watch('sedeSeleccionada', () => {
                    if (this.asignadoSeleccionado) {
                        const sigueDisponible = this.tecnicosDisponibles
                            .some(t => parseInt(t.id) === parseInt(this.asignadoSeleccionado));
                        if (!sigueDisponible) {
                            this.asignadoSeleccionado = '';
                        }
                    }
                    // El equipo elegido debe seguir en la lista de la sede
                    if (this.vinculo && !this.activosFiltrados.some(a => a.valor === this.vinculo)) {
                        this.vinculo = '';
                    }
                });
            },
        };
    }
</script>
@endpush