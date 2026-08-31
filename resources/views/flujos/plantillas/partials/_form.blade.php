{{-- resources/views/flujos/plantillas/partials/_form.blade.php --}}

@php
    $actionUrl = $modo === 'create'
        ? route('flujos.plantillas.store')
        : route('flujos.plantillas.update', $plantilla);

    // Estado inicial del editor (hidratación desde la BD)
    $estadoInicial = [
        'etapas' => $plantilla->stages->map(fn ($s) => [
            'id'                        => $s->id,
            'nombre'                    => $s->nombre,
            'descripcion'               => $s->descripcion,
            'codigo_externo'            => $s->codigo_externo,
            'modo'                      => $s->modo,
            'politica_rechazo'          => $s->politica_rechazo,
            'etapa_destino_rechazo_idx' => null,
            'etapa_destino_rechazo_id'  => $s->etapa_destino_rechazo_id,
            'requiere_adjunto'          => (bool) $s->requiere_adjunto,
            'sla_horas'                 => $s->sla_horas,
            'actores'                   => $s->actors->map(fn ($a) => [
                'actor_type'  => $a->actor_type,
                'actor_id'    => $a->actor_id,
                'actor_label' => $a->esRol()
                    ? optional($a->resolverActor())->name
                    : optional($a->resolverActor())->name,
                'etiqueta'    => $a->etiqueta,
            ])->values(),
        ])->values(),
    ];
@endphp

<form action="{{ $actionUrl }}" method="POST"
      class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark"
      x-data="editorPlantilla({{ Js::from($estadoInicial) }}, {{ Js::from($roles) }}, '{{ route('flujos.plantillas.api.usuarios') }}', {{ $bloqueada ? 'true' : 'false' }})"
      x-init="init()"
      @submit="serializar()">
    @csrf
    @if ($modo === 'edit')
        @method('PUT')
    @endif

    {{-- Errores de validación --}}
    @if ($errors->any())
        <div class="mx-6 mt-6 flex w-full border-l-6 border-meta-1 bg-meta-1/10 px-6 py-4 shadow-md dark:bg-meta-1/20 dark:border-meta-1 rounded-sm">
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

    {{-- ── Datos generales ─────────────────────────────────────────── --}}
    <div class="p-6 space-y-4">
        <div class="mb-2 pb-2 border-b border-stroke dark:border-strokedark">
            <h3 class="font-bold text-black dark:text-white uppercase tracking-wider text-xs flex items-center gap-1.5">
                <i class="fas fa-info-circle text-primary"></i> Datos Generales
            </h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Nombre --}}
            <div class="md:col-span-2">
                <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white" for="nombre">
                    Nombre de la Plantilla <span class="text-meta-1">*</span>
                </label>
                <input type="text" id="nombre" name="nombre"
                       value="{{ old('nombre', $plantilla->nombre) }}"
                       placeholder="Ej: Aprobación de Cotización de Servicios" required maxlength="150" autofocus
                       class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white @error('nombre') border-meta-1 @enderror">
                @error('nombre')<p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $message }}</p>@enderror
            </div>

            {{-- Toggle Activo --}}
            <div class="flex items-end pb-2">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="hidden" name="activo" value="0">
                    <input type="checkbox" name="activo" value="1"
                           @checked(old('activo', $plantilla->activo ?? true))
                           class="rounded border-stroke text-primary focus:ring-primary dark:border-form-strokedark dark:bg-form-input">
                    <span class="text-xs font-bold text-black dark:text-white">Plantilla Activa</span>
                </label>
            </div>

            {{-- Descripción --}}
            <div class="md:col-span-3">
                <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white" for="descripcion">
                    Descripción (Opcional)
                </label>
                <textarea id="descripcion" name="descripcion" rows="2" maxlength="2000"
                          placeholder="Propósito del flujo, normativas o condiciones de uso..."
                          class="w-full rounded border border-stroke bg-transparent py-2 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white @error('descripcion') border-meta-1 @enderror">{{ old('descripcion', $plantilla->descripcion) }}</textarea>
                @error('descripcion')<p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    {{-- ── Editor de etapas ────────────────────────────────────────── --}}
    <div class="border-t border-stroke dark:border-strokedark" x-show="!bloqueada" x-cloak>
        <div class="p-6 pb-3 flex items-center justify-between flex-wrap gap-2">
            <div>
                <h3 class="font-bold text-black dark:text-white uppercase tracking-wider text-xs flex items-center gap-1.5">
                    <i class="fas fa-stream text-primary"></i> Secuencia de Etapas
                </h3>
                <p class="text-[10px] text-bodydark2 mt-0.5">
                    Ordenadas de arriba hacia abajo. Arrastra desde el ícono <i class="fas fa-grip-vertical text-primary"></i> para reordenar la secuencia.
                </p>
            </div>
            <button type="button" 
                    class="inline-flex items-center gap-1.5 rounded border border-stroke py-1.5 px-3 text-xs font-bold uppercase text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition"
                    @click="agregarEtapa()">
                <i class="fas fa-plus text-[10px]"></i> Agregar Etapa
            </button>
        </div>

        <div class="px-6 pb-6 space-y-4" x-ref="etapasContainer">
            <template x-for="(etapa, idx) in etapas" :key="etapa._key">
                <div class="rounded-sm border border-stroke bg-gray-2 shadow-sm dark:border-strokedark dark:bg-meta-4/20 overflow-hidden">

                    {{-- Cabecera de etapa --}}
                    <div class="flex items-center gap-3 px-4 py-3 bg-white dark:bg-boxdark border-b border-stroke dark:border-strokedark">
                        <span class="drag-handle text-bodydark2 hover:text-primary cursor-move" title="Arrastrar para cambiar orden">
                            <i class="fas fa-grip-vertical"></i>
                        </span>
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-primary text-white text-[10px] font-bold">
                            <span x-text="idx + 1"></span>
                        </span>
                        <input type="text" x-model="etapa.codigo_externo" 
                               placeholder="COD-EXT" maxlength="50"
                               title="Código externo opcional (ej: APR-FIN, REV-TEC-01)"
                               class="w-28 rounded border border-stroke bg-transparent py-1 px-2.5 text-xs font-mono uppercase outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                        <input type="text" x-model="etapa.nombre" required maxlength="150"
                               placeholder="Nombre de la etapa (ej: Revisión Financiera)"
                               class="flex-1 rounded border border-stroke bg-transparent py-1 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                        <button type="button" 
                                class="flex h-7 w-7 items-center justify-center rounded border border-stroke text-meta-1 hover:border-meta-1 dark:border-strokedark transition" 
                                @click="eliminarEtapa(idx)" title="Eliminar etapa">
                            <i class="fas fa-trash-alt text-[10px]"></i>
                        </button>
                    </div>

                    {{-- Cuerpo de configuración de la etapa --}}
                    <div class="p-4 space-y-4">

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            {{-- Modo --}}
                            <div>
                                <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Modo de Aprobación</label>
                                <div class="relative z-20">
                                    <select x-model="etapa.modo" 
                                            class="w-full appearance-none rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white cursor-pointer">
                                        <option value="secuencial" class="bg-[#1c2434] text-white">Secuencial (un actor)</option>
                                        <option value="paralelo_todos" class="bg-[#1c2434] text-white">Paralelo — todos aprueban</option>
                                        <option value="paralelo_cualquiera" class="bg-[#1c2434] text-white">Paralelo — cualquiera aprueba</option>
                                    </select>
                                    <span class="absolute top-1/2 right-3 -translate-y-1/2 z-30 pointer-events-none">
                                        <i class="fas fa-chevron-down text-[10px] text-bodydark2"></i>
                                    </span>
                                </div>
                            </div>

                            {{-- Política de rechazo --}}
                            <div>
                                <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Política de Rechazo</label>
                                <div class="relative z-20">
                                    <select x-model="etapa.politica_rechazo" 
                                            class="w-full appearance-none rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white cursor-pointer">
                                        <option value="devolver_iniciador" class="bg-[#1c2434] text-white">Devolver al iniciador</option>
                                        <option value="devolver_etapa_anterior" class="bg-[#1c2434] text-white">Devolver a etapa anterior</option>
                                        <option value="devolver_etapa_especifica" class="bg-[#1c2434] text-white">Devolver a etapa específica</option>
                                        <option value="finalizar" class="bg-[#1c2434] text-white">Finalizar flujo (rechazo definitivo)</option>
                                    </select>
                                    <span class="absolute top-1/2 right-3 -translate-y-1/2 z-30 pointer-events-none">
                                        <i class="fas fa-chevron-down text-[10px] text-bodydark2"></i>
                                    </span>
                                </div>
                            </div>

                            {{-- Etapa destino específica --}}
                            <div x-show="etapa.politica_rechazo === 'devolver_etapa_especifica'">
                                <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Etapa Destino</label>
                                <div class="relative z-20">
                                    <select x-model.number="etapa.etapa_destino_rechazo_idx" 
                                            class="w-full appearance-none rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white cursor-pointer">
                                        <option :value="null" class="bg-[#1c2434] text-gray-400">— Seleccionar —</option>
                                        <template x-for="(otra, otraIdx) in etapas" :key="otra._key">
                                            <option :value="otraIdx" x-show="otraIdx !== idx"
                                                    x-text="`${otraIdx + 1}. ${otra.nombre || 'Sin nombre'}`" class="bg-[#1c2434] text-white"></option>
                                        </template>
                                    </select>
                                    <span class="absolute top-1/2 right-3 -translate-y-1/2 z-30 pointer-events-none">
                                        <i class="fas fa-chevron-down text-[10px] text-bodydark2"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            {{-- Descripción de etapa --}}
                            <div class="md:col-span-2">
                                <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Descripción de la Etapa</label>
                                <input type="text" x-model="etapa.descripcion" maxlength="1000"
                                       placeholder="Criterios que deben ser validados o revisados en esta instancia"
                                       class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                            </div>

                            {{-- SLA --}}
                            <div>
                                <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">SLA Máximo (Horas)</label>
                                <input type="number" x-model.number="etapa.sla_horas" min="1" max="8760" placeholder="Ej: 24"
                                       class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                            </div>
                        </div>

                        <div>
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" x-model="etapa.requiere_adjunto" 
                                       class="rounded border-stroke text-primary focus:ring-primary dark:border-form-strokedark dark:bg-form-input">
                                <span class="text-xs font-medium text-black dark:text-white">Requiere adjuntar documento/soporte obligatoriamente para aprobar</span>
                            </label>
                        </div>

                        {{-- Panel de Actores --}}
                        <div class="rounded border border-stroke bg-white p-3.5 dark:border-strokedark dark:bg-boxdark space-y-3">
                            <div class="flex items-center justify-between pb-1 border-b border-stroke dark:border-strokedark">
                                <span class="text-[10px] font-bold text-black dark:text-white uppercase tracking-wider flex items-center gap-1.5">
                                    <i class="fas fa-users text-primary"></i> Actores Autorizados
                                </span>
                                <span class="text-[9px] font-bold text-meta-6 uppercase" x-show="etapa.modo === 'paralelo_todos'">
                                    Aprobación Unánime
                                </span>
                            </div>

                            {{-- Lista de actores agregados --}}
                            <div class="space-y-2">
                                <template x-for="(actor, aIdx) in etapa.actores" :key="aIdx">
                                    <div class="flex items-center gap-2 p-2 rounded border border-stroke bg-gray-2 dark:border-strokedark dark:bg-meta-4/30">
                                        <span class="flex h-6 w-6 items-center justify-center rounded text-[10px] font-bold shrink-0"
                                              :class="actor.actor_type === 'role' ? 'bg-primary/20 text-primary' : 'bg-meta-3/20 text-meta-3'">
                                            <i class="fas" :class="actor.actor_type === 'role' ? 'fa-user-tag' : 'fa-user'"></i>
                                        </span>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-bold text-black dark:text-white truncate" x-text="actor.actor_label"></p>
                                            <p class="text-[9px] text-bodydark2 uppercase tracking-wider" x-text="actor.actor_type === 'role' ? 'Rol' : 'Usuario Especifico'"></p>
                                        </div>
                                        <input type="text" x-model="actor.etiqueta" maxlength="120"
                                               placeholder="Etiqueta opcional"
                                               class="w-36 rounded border border-stroke bg-transparent py-1 px-2 text-[10px] outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                                        <button type="button" 
                                                class="flex h-6 w-6 items-center justify-center rounded text-meta-1 hover:bg-meta-1/10 transition shrink-0" 
                                                @click="eliminarActor(idx, aIdx)" title="Quitar actor">
                                            <i class="fas fa-times text-xs"></i>
                                        </button>
                                    </div>
                                </template>

                                <div x-show="etapa.actores.length === 0" class="text-center py-3 text-xs text-bodydark2 italic">
                                    Sin actores definidos. Agrega al menos un rol o usuario para operar esta etapa.
                                </div>
                            </div>

                            {{-- Selector de tipo de actor --}}
                            <div class="pt-2 border-t border-stroke dark:border-strokedark">
                                <div class="flex items-center gap-2 mb-2">
                                    <button type="button" 
                                            class="py-1 px-3 rounded text-[10px] font-bold uppercase tracking-wider transition"
                                            :class="etapa._pickerTipo === 'role' ? 'bg-primary text-white' : 'bg-gray-2 text-black hover:bg-stroke dark:bg-meta-4 dark:text-white'"
                                            @click="etapa._pickerTipo = 'role'">
                                        Por Rol
                                    </button>
                                    <button type="button" 
                                            class="py-1 px-3 rounded text-[10px] font-bold uppercase tracking-wider transition"
                                            :class="etapa._pickerTipo === 'user' ? 'bg-primary text-white' : 'bg-gray-2 text-black hover:bg-stroke dark:bg-meta-4 dark:text-white'"
                                            @click="etapa._pickerTipo = 'user'">
                                        Por Usuario
                                    </button>
                                </div>

                                {{-- Selector Rol --}}
                                <div x-show="etapa._pickerTipo === 'role'" class="flex gap-2">
                                    <div class="relative flex-1 z-20">
                                        <select x-model.number="etapa._rolElegido" 
                                                class="w-full appearance-none rounded border border-stroke bg-transparent py-1 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white cursor-pointer">
                                            <option :value="null" class="bg-[#1c2434] text-gray-400">— Seleccionar Rol —</option>
                                            <template x-for="rol in roles" :key="rol.id">
                                                <option :value="rol.id" x-text="rol.name" class="bg-[#1c2434] text-white"></option>
                                            </template>
                                        </select>
                                        <span class="absolute top-1/2 right-3 -translate-y-1/2 z-30 pointer-events-none">
                                            <i class="fas fa-chevron-down text-[10px] text-bodydark2"></i>
                                        </span>
                                    </div>
                                    <button type="button" 
                                            class="inline-flex items-center justify-center rounded bg-primary py-1 px-3 text-xs font-bold text-white hover:bg-opacity-90 transition disabled:opacity-50"
                                            @click="agregarActorRol(idx)"
                                            :disabled="!etapa._rolElegido">
                                        <i class="fas fa-plus text-[10px]"></i>
                                    </button>
                                </div>

                                {{-- Búsqueda Usuario --}}
                                <div x-show="etapa._pickerTipo === 'user'" class="relative">
                                    <input type="text" x-model="etapa._userQuery"
                                           placeholder="Buscar por nombre o correo (mín. 2 caracteres)..."
                                           @input.debounce.400ms="buscarUsuarios(etapa)"
                                           class="w-full rounded border border-stroke bg-transparent py-1 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">

                                    <div x-show="etapa._userResultados.length > 0" x-cloak
                                         class="absolute left-0 right-0 top-full mt-1 z-50 max-h-48 overflow-y-auto rounded border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark py-1">
                                        <template x-for="user in etapa._userResultados" :key="user.id">
                                            <button type="button"
                                                    class="w-full flex items-center gap-2 px-3 py-1.5 hover:bg-gray-2 dark:hover:bg-meta-4 transition text-left"
                                                    @click="agregarActorUsuario(idx, user)">
                                                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-primary text-white text-[9px] font-bold shrink-0"
                                                      x-text="user.name.charAt(0)"></span>
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-xs font-bold text-black dark:text-white truncate" x-text="user.name"></p>
                                                    <p class="text-[10px] text-bodydark2 truncate" x-text="user.email"></p>
                                                </div>
                                                <i class="fas fa-plus text-primary text-xs"></i>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </template>

            <div x-show="etapas.length === 0" class="text-center py-8 rounded border-2 border-dashed border-stroke dark:border-strokedark">
                <i class="fas fa-stream text-3xl text-bodydark2 mb-2"></i>
                <p class="text-xs text-bodydark2 mb-3">Esta plantilla aún no tiene etapas configuradas.</p>
                <button type="button" 
                        class="inline-flex items-center gap-1.5 rounded bg-primary py-1.5 px-4 text-xs font-bold uppercase text-white hover:bg-opacity-90 transition" 
                        @click="agregarEtapa()">
                    <i class="fas fa-plus text-[10px]"></i> Agregar Primera Etapa
                </button>
            </div>
        </div>
    </div>

    {{-- Payload JSON (relleno en submit) --}}
    <textarea name="etapas_json" id="etapas_json" class="hidden"></textarea>

    {{-- Footer de Acciones --}}
    <div class="flex items-center justify-end gap-2 bg-gray-2 dark:bg-meta-4/40 px-6 py-3 border-t border-stroke dark:border-strokedark">
        <a href="{{ route('flujos.plantillas.index') }}" 
           class="inline-flex items-center justify-center rounded border border-stroke py-1.5 px-4 text-center text-xs font-medium text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition">
            Cancelar
        </a>
        <button type="submit" 
                class="inline-flex items-center gap-1.5 rounded bg-primary py-1.5 px-5 text-center text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm cursor-pointer">
            <i class="fas fa-save text-[10px]"></i>
            {{ $modo === 'create' ? 'Crear Plantilla' : 'Guardar Cambios' }}
        </button>
    </div>
</form>

@push('scripts')
<script>
function editorPlantilla(estadoInicial, roles, urlBusquedaUsuarios, bloqueada) {
    return {
        etapas: [],
        roles: roles,
        urlBusquedaUsuarios: urlBusquedaUsuarios,
        bloqueada: bloqueada,
        _nextKey: 1,

        init() {
            const idAIndice = {};
            estadoInicial.etapas.forEach((e, i) => {
                if (e.id) idAIndice[e.id] = i;
            });

            this.etapas = estadoInicial.etapas.map(e => ({
                _key: `e${this._nextKey++}`,
                _pickerTipo: 'role',
                _rolElegido: null,
                _userQuery: '',
                _userResultados: [],
                id: e.id,
                nombre: e.nombre || '',
                descripcion: e.descripcion || '',
                codigo_externo: e.codigo_externo || '',
                modo: e.modo || 'secuencial',
                politica_rechazo: e.politica_rechazo || 'devolver_iniciador',
                etapa_destino_rechazo_idx: e.etapa_destino_rechazo_id
                    ? (idAIndice[e.etapa_destino_rechazo_id] ?? null)
                    : null,
                requiere_adjunto: !!e.requiere_adjunto,
                sla_horas: e.sla_horas ?? null,
                actores: (e.actores || []).map(a => ({
                    actor_type: a.actor_type,
                    actor_id: a.actor_id,
                    actor_label: a.actor_label,
                    etiqueta: a.etiqueta || '',
                })),
            }));

            this.$nextTick(() => {
                if (this.bloqueada) return;
                new Sortable(this.$refs.etapasContainer, {
                    handle: '.drag-handle',
                    animation: 150,
                    onEnd: (evt) => {
                        const item = this.etapas.splice(evt.oldIndex, 1)[0];
                        this.etapas.splice(evt.newIndex, 0, item);
                        this.etapas.forEach(e => {
                            if (e.politica_rechazo === 'devolver_etapa_especifica') {
                                e.etapa_destino_rechazo_idx = null;
                            }
                        });
                    }
                });
            });
        },

        agregarEtapa() {
            this.etapas.push({
                _key: `e${this._nextKey++}`,
                _pickerTipo: 'role',
                _rolElegido: null,
                _userQuery: '',
                _userResultados: [],
                id: null,
                nombre: '',
                descripcion: '',
                codigo_externo: '',
                modo: 'secuencial',
                politica_rechazo: 'devolver_iniciador',
                etapa_destino_rechazo_idx: null,
                requiere_adjunto: false,
                sla_horas: null,
                actores: [],
            });
        },

        eliminarEtapa(idx) {
            if (!confirm('¿Eliminar esta etapa?')) return;
            this.etapas.splice(idx, 1);
            this.etapas.forEach(e => {
                if (e.etapa_destino_rechazo_idx === null) return;
                if (e.etapa_destino_rechazo_idx === idx) {
                    e.etapa_destino_rechazo_idx = null;
                } else if (e.etapa_destino_rechazo_idx > idx) {
                    e.etapa_destino_rechazo_idx -= 1;
                }
            });
        },

        agregarActorRol(etapaIdx) {
            const etapa = this.etapas[etapaIdx];
            if (!etapa._rolElegido) return;

            const rol = this.roles.find(r => r.id === etapa._rolElegido);
            if (!rol) return;

            const duplicado = etapa.actores.some(a =>
                a.actor_type === 'role' && a.actor_id === rol.id
            );
            if (duplicado) {
                alert('Ese rol ya está agregado en esta etapa.');
                return;
            }

            etapa.actores.push({
                actor_type: 'role',
                actor_id: rol.id,
                actor_label: rol.name,
                etiqueta: '',
            });
            etapa._rolElegido = null;
        },

        agregarActorUsuario(etapaIdx, user) {
            const etapa = this.etapas[etapaIdx];

            const duplicado = etapa.actores.some(a =>
                a.actor_type === 'user' && a.actor_id === user.id
            );
            if (duplicado) {
                alert('Ese usuario ya está agregado en esta etapa.');
                return;
            }

            etapa.actores.push({
                actor_type: 'user',
                actor_id: user.id,
                actor_label: `${user.name} · ${user.email}`,
                etiqueta: '',
            });
            etapa._userQuery = '';
            etapa._userResultados = [];
        },

        eliminarActor(etapaIdx, actorIdx) {
            this.etapas[etapaIdx].actores.splice(actorIdx, 1);
        },

        async buscarUsuarios(etapa) {
            const q = etapa._userQuery.trim();
            if (q.length < 2) {
                etapa._userResultados = [];
                return;
            }
            try {
                const res = await fetch(`${this.urlBusquedaUsuarios}?q=${encodeURIComponent(q)}`, {
                    headers: { 'Accept': 'application/json' }
                });
                etapa._userResultados = await res.json();
            } catch (e) {
                console.error('Error buscando usuarios:', e);
                etapa._userResultados = [];
            }
        },

        serializar() {
            const payload = this.etapas.map((e, i) => ({
                id: e.id,
                nombre: e.nombre,
                descripcion: e.descripcion,
                codigo_externo: e.codigo_externo?.toUpperCase() || null,
                orden: i + 1,
                modo: e.modo,
                politica_rechazo: e.politica_rechazo,
                etapa_destino_rechazo_idx: e.etapa_destino_rechazo_idx,
                requiere_adjunto: e.requiere_adjunto,
                sla_horas: e.sla_horas,
                actores: e.actores.map(a => ({
                    actor_type: a.actor_type,
                    actor_id: a.actor_id,
                    etiqueta: a.etiqueta || null,
                })),
            }));
            document.getElementById('etapas_json').value = JSON.stringify(payload);
        }
    };
}
</script>
@endpush