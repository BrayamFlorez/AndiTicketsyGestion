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
            'modo'                      => $s->modo,
            'politica_rechazo'          => $s->politica_rechazo,
            'etapa_destino_rechazo_idx' => null, // se resuelve tras hidratar (ver JS)
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

<form action="{{ $actionUrl }}" method="POST" class="db-card"
      x-data="editorPlantilla({{ Js::from($estadoInicial) }}, {{ Js::from($roles) }}, '{{ route('flujos.plantillas.api.usuarios') }}', {{ $bloqueada ? 'true' : 'false' }})"
      x-init="init()"
      @submit="serializar()">
    @csrf
    @if ($modo === 'edit')
        @method('PUT')
    @endif

    {{-- Errores de validación --}}
    @if ($errors->any())
        <div class="mx-6 mt-6 px-4 py-3 rounded-lg bg-red-500/10 border border-red-500/30 text-red-300 text-sm">
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

    {{-- ── Datos generales ─────────────────────────────────────────── --}}
    <div class="form-card-body" style="padding: 1.5rem;">
        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">
            <i class="fas fa-info-circle mr-1"></i> Datos generales
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="form-group md:col-span-2">
                <label class="form-label" for="nombre">Nombre <span class="text-red-400">*</span></label>
                <input type="text" id="nombre" name="nombre" class="form-input"
                    value="{{ old('nombre', $plantilla->nombre) }}"
                    placeholder="Ej: Aprobación de Cotización" required maxlength="150" autofocus>
            </div>

            <div class="form-group flex items-end">
                <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                    <input type="hidden" name="activo" value="0">
                    <input type="checkbox" name="activo" value="1" class="form-checkbox"
                        @checked(old('activo', $plantilla->activo ?? true))>
                    <span class="text-sm text-white font-semibold">Plantilla activa</span>
                </label>
            </div>

            <div class="form-group md:col-span-3">
                <label class="form-label" for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="2" class="form-input"
                    placeholder="Cuándo se usa, qué documentos cubre, etc." maxlength="2000">{{ old('descripcion', $plantilla->descripcion) }}</textarea>
            </div>
        </div>
    </div>

    {{-- ── Editor de etapas ────────────────────────────────────────── --}}
    <div class="border-t border-gray-800" x-show="!bloqueada" style="display: none;">
        <div class="px-6 py-4 flex items-center justify-between">
            <div>
                <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest">
                    <i class="fas fa-stream mr-1"></i> Etapas del flujo
                </h3>
                <p class="text-[11px] text-gray-500 mt-1">
                    Ordenadas de arriba hacia abajo. Arrastrá desde el ícono
                    <i class="fas fa-grip-vertical text-gray-600"></i> para reordenar.
                </p>
            </div>
            <button type="button" class="db-btn outline" @click="agregarEtapa()">
                <i class="fas fa-plus mr-2"></i> Agregar Etapa
            </button>
        </div>

        <div class="px-6 pb-6 space-y-3" x-ref="etapasContainer">
            <template x-for="(etapa, idx) in etapas" :key="etapa._key">
                <div class="rounded-lg border border-gray-800 bg-[#1a1a1b] overflow-hidden">

                    {{-- Cabecera de etapa --}}
                    <div class="flex items-center gap-3 px-4 py-3 bg-[#232324] border-b border-gray-800">
                        <span class="drag-handle text-gray-600 hover:text-white cursor-move" title="Arrastrar">
                            <i class="fas fa-grip-vertical"></i>
                        </span>
                        <span class="w-7 h-7 rounded-full bg-[#007acc] text-white text-xs font-black flex items-center justify-center">
                            <span x-text="idx + 1"></span>
                        </span>
                        <input type="text" x-model="etapa.nombre" class="form-input flex-1"
                            placeholder="Nombre de la etapa (ej: Revisión Financiera)" required maxlength="150">
                        <button type="button" class="db-btn danger-outline" @click="eliminarEtapa(idx)" title="Eliminar etapa">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>

                    {{-- Cuerpo --}}
                    <div class="p-4 space-y-4">

                        {{-- Configuración de la etapa --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div class="form-group">
                                <label class="form-label text-[11px]">Modo</label>
                                <select x-model="etapa.modo" class="form-select">
                                    <option value="secuencial">Secuencial (un actor)</option>
                                    <option value="paralelo_todos">Paralelo — todos aprueban</option>
                                    <option value="paralelo_cualquiera">Paralelo — cualquiera aprueba</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label text-[11px]">Política de rechazo</label>
                                <select x-model="etapa.politica_rechazo" class="form-select">
                                    <option value="devolver_iniciador">Devolver al iniciador</option>
                                    <option value="devolver_etapa_anterior">Devolver a etapa anterior</option>
                                    <option value="devolver_etapa_especifica">Devolver a etapa específica</option>
                                    <option value="finalizar">Finalizar flujo</option>
                                </select>
                            </div>

                            <div class="form-group" x-show="etapa.politica_rechazo === 'devolver_etapa_especifica'">
                                <label class="form-label text-[11px]">Etapa destino</label>
                                <select x-model.number="etapa.etapa_destino_rechazo_idx" class="form-select">
                                    <option :value="null">— Seleccionar —</option>
                                    <template x-for="(otra, otraIdx) in etapas" :key="otra._key">
                                        <option :value="otraIdx" x-show="otraIdx !== idx"
                                            x-text="`${otraIdx + 1}. ${otra.nombre || 'Sin nombre'}`"></option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div class="form-group md:col-span-2">
                                <label class="form-label text-[11px]">Descripción de la etapa</label>
                                <input type="text" x-model="etapa.descripcion" class="form-input"
                                    placeholder="Qué se debe validar aquí" maxlength="1000">
                            </div>

                            <div class="form-group">
                                <label class="form-label text-[11px]">SLA (horas)</label>
                                <input type="number" x-model.number="etapa.sla_horas" class="form-input"
                                    min="1" max="8760" placeholder="Opcional">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="inline-flex items-center gap-2 cursor-pointer select-none text-xs text-gray-400">
                                <input type="checkbox" x-model="etapa.requiere_adjunto" class="form-checkbox">
                                <span>Requiere adjunto para poder aprobar esta etapa</span>
                            </label>
                        </div>

                        {{-- Actores --}}
                        <div class="rounded-md bg-[#232324] border border-gray-800 p-3">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[11px] font-black text-gray-400 uppercase tracking-wider">
                                    <i class="fas fa-users mr-1"></i> Actores autorizados
                                </span>
                                <span class="text-[10px] text-gray-500" x-show="etapa.modo === 'paralelo_todos'">
                                    Todos deben aprobar
                                </span>
                            </div>

                            {{-- Lista de actores actuales --}}
                            <div class="space-y-2 mb-3">
                                <template x-for="(actor, aIdx) in etapa.actores" :key="aIdx">
                                    <div class="flex items-center gap-2 px-3 py-2 rounded bg-[#1a1a1b] border border-gray-800">
                                        <span class="w-6 h-6 rounded flex items-center justify-center text-xs"
                                            :class="actor.actor_type === 'role' ? 'bg-purple-500/20 text-purple-300' : 'bg-blue-500/20 text-blue-300'">
                                            <i class="fas" :class="actor.actor_type === 'role' ? 'fa-user-tag' : 'fa-user'"></i>
                                        </span>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-semibold text-white truncate" x-text="actor.actor_label"></p>
                                            <p class="text-[10px] text-gray-500 uppercase tracking-wider" x-text="actor.actor_type === 'role' ? 'Rol' : 'Usuario'"></p>
                                        </div>
                                        <input type="text" x-model="actor.etiqueta" class="form-input text-xs"
                                            style="max-width: 180px;" placeholder="Etiqueta (opcional)" maxlength="120">
                                        <button type="button" class="db-btn danger-outline text-xs" @click="eliminarActor(idx, aIdx)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </template>

                                <div x-show="etapa.actores.length === 0" class="text-center py-4 text-xs text-gray-500 italic">
                                    Sin actores. Agregá al menos uno.
                                </div>
                            </div>

                            {{-- Picker de actores --}}
                            <div class="rounded bg-[#1a1a1b] border border-gray-800 p-3">
                                <div class="flex items-center gap-2 mb-2">
                                    <button type="button" class="text-[11px] px-3 py-1 rounded font-bold uppercase tracking-wider transition-colors"
                                        :class="etapa._pickerTipo === 'role' ? 'bg-[#007acc] text-white' : 'bg-transparent text-gray-500 hover:text-white'"
                                        @click="etapa._pickerTipo = 'role'">
                                        Por rol
                                    </button>
                                    <button type="button" class="text-[11px] px-3 py-1 rounded font-bold uppercase tracking-wider transition-colors"
                                        :class="etapa._pickerTipo === 'user' ? 'bg-[#007acc] text-white' : 'bg-transparent text-gray-500 hover:text-white'"
                                        @click="etapa._pickerTipo = 'user'">
                                        Por usuario
                                    </button>
                                </div>

                                {{-- Modo rol --}}
                                <div x-show="etapa._pickerTipo === 'role'" class="flex gap-2">
                                    <select x-model.number="etapa._rolElegido" class="form-select flex-1">
                                        <option :value="null">— Seleccionar rol —</option>
                                        <template x-for="rol in roles" :key="rol.id">
                                            <option :value="rol.id" x-text="rol.name"></option>
                                        </template>
                                    </select>
                                    <button type="button" class="db-btn solid" @click="agregarActorRol(idx)"
                                        :disabled="!etapa._rolElegido">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>

                                {{-- Modo usuario --}}
                                <div x-show="etapa._pickerTipo === 'user'" class="relative">
                                    <input type="text" x-model="etapa._userQuery" class="form-input"
                                        placeholder="Buscar por nombre o correo (mín. 2 caracteres)..."
                                        @input.debounce.400ms="buscarUsuarios(etapa)">

                                    <div x-show="etapa._userResultados.length > 0"
                                        class="mt-2 max-h-48 overflow-y-auto rounded border border-gray-800 bg-[#232324]">
                                        <template x-for="user in etapa._userResultados" :key="user.id">
                                            <button type="button"
                                                class="w-full flex items-center gap-2 px-3 py-2 hover:bg-[#2d2d2d] transition-colors text-left"
                                                @click="agregarActorUsuario(idx, user)">
                                                <span class="w-6 h-6 rounded-full bg-[#007acc] text-white text-[10px] font-black flex items-center justify-center flex-shrink-0"
                                                    x-text="user.name.charAt(0)"></span>
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-xs font-semibold text-white truncate" x-text="user.name"></p>
                                                    <p class="text-[10px] text-gray-500 truncate" x-text="user.email"></p>
                                                </div>
                                                <i class="fas fa-plus text-[#007acc] text-xs"></i>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </template>

            <div x-show="etapas.length === 0" class="text-center py-8 rounded-lg border-2 border-dashed border-gray-800">
                <i class="fas fa-stream text-3xl text-gray-700 mb-2"></i>
                <p class="text-sm text-gray-500 mb-3">Esta plantilla aún no tiene etapas.</p>
                <button type="button" class="db-btn solid" @click="agregarEtapa()">
                    <i class="fas fa-plus mr-2"></i> Agregar primera etapa
                </button>
            </div>
        </div>
    </div>

    {{-- Payload JSON (relleno en submit) --}}
    <textarea name="etapas_json" id="etapas_json" class="hidden"></textarea>

    <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-gray-800">
        <a href="{{ route('flujos.plantillas.index') }}" class="db-btn ghost">Cancelar</a>
        <button type="submit" class="db-btn solid">
            <i class="fas fa-save mr-2"></i>
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
            // Hidratación: mapear etapa_destino_rechazo_id → índice
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
                        // Al mover se invalidan las políticas de "etapa específica"
                        // porque los índices ya no coinciden.
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
            // Reindexar destinos de rechazo específicos
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