{{-- resources/views/tickets/partials/form_admin.blade.php --}}

<form
    action="{{ route('tickets.store') }}"
    method="POST"
    enctype="multipart/form-data"
    class="form-card"
    x-data="ticketAdminForm({
        sedesPorEmpresa: {{ Js::from($sedesPorEmpresa) }},
        usuariosPorEmpresa: {{ Js::from($usuariosPorEmpresa) }},
        tecnicos: {{ Js::from($tecnicos) }},
        coberturas: {{ Js::from($coberturas) }},
        empresaInicial: '{{ old('empresa_id') }}',
        solicitanteInicial: '{{ old('solicitante_id') }}',
        sedeInicial: '{{ old('sede_id') }}',
        asignadoInicial: '{{ old('asignado_a') }}',
    })"
>
    @csrf

    <div class="form-card-header">
        <h3>Contexto del ticket</h3>
        <p>¿De quién es el ticket y a qué empresa pertenece?</p>
    </div>

    <div class="form-card-body">

        <div class="form-section">
            <div class="form-grid form-grid-2">

                <div class="form-group">
                    <label class="form-label" for="empresa_id">Empresa <span class="required">*</span></label>
                    <select id="empresa_id" name="empresa_id" required
                            x-model="empresaSeleccionada"
                            class="form-select @error('empresa_id') is-invalid @enderror">
                        <option value="">— Selecciona empresa —</option>
                        @foreach ($empresas as $empresa)
                            <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                        @endforeach
                    </select>
                    @error('empresa_id')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="solicitante_id">Solicitante <span class="required">*</span></label>
                    <select id="solicitante_id" name="solicitante_id" required
                            x-model="solicitanteSeleccionado"
                            :disabled="!empresaSeleccionada"
                            class="form-select @error('solicitante_id') is-invalid @enderror">
                        <option value="">— Selecciona usuario —</option>
                        <template x-for="usuario in usuariosFiltrados" :key="usuario.id">
                            <option :value="usuario.id" x-text="usuario.name"></option>
                        </template>
                    </select>
                    <p class="form-help">Solo se muestran usuarios de la empresa seleccionada.</p>
                    @error('solicitante_id')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="sede_id">Sede (opcional)</label>
                    <select id="sede_id" name="sede_id"
                            x-model="sedeSeleccionada"
                            :disabled="!empresaSeleccionada"
                            class="form-select @error('sede_id') is-invalid @enderror">
                        <option value="">— Sin sede específica —</option>
                        <template x-for="sede in sedesFiltradas" :key="sede.id">
                            <option :value="sede.id" x-text="sede.nombre"></option>
                        </template>
                    </select>
                    @error('sede_id')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                {{-- ── Asignar técnico — filtrado dinámicamente por cobertura ── --}}
                <div class="form-group">
                    <label class="form-label" for="asignado_a">Asignar a técnico (opcional)</label>
                    <select id="asignado_a" name="asignado_a"
                            x-model="asignadoSeleccionado"
                            :disabled="!empresaSeleccionada"
                            class="form-select @error('asignado_a') is-invalid @enderror">
                        <option value="">— No asignar ahora —</option>
                        <template x-for="t in tecnicosDisponibles" :key="t.id">
                            <option :value="t.id" x-text="t.name"></option>
                        </template>
                    </select>
                    <p class="form-help" x-show="empresaSeleccionada && tecnicosDisponibles.length === 0" x-cloak
                       style="color: #fbbf24;">
                        <i class="fas fa-exclamation-triangle"></i>
                        Ningún técnico tiene cobertura para esta empresa/sede.
                    </p>
                    <p class="form-help" x-show="empresaSeleccionada && tecnicosDisponibles.length > 0" x-cloak>
                        Si asignas un técnico, el ticket nace con estado "Asignado".
                    </p>
                    @error('asignado_a')<p class="form-error">{{ $message }}</p>@enderror
                </div>

            </div>
        </div>

        {{-- ── Detalle del ticket ──────────────────────────────────────── --}}
        <div class="form-section">
            <div class="form-section-title">Detalle del ticket</div>

            <div class="form-group" style="margin-bottom: 1rem">
                <label class="form-label" for="titulo">Título <span class="required">*</span></label>
                <input type="text" id="titulo" name="titulo" maxlength="150" required
                       value="{{ old('titulo') }}"
                       placeholder="Ej: Falla en impresora de recepción"
                       class="form-input @error('titulo') is-invalid @enderror">
                @error('titulo')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-grid form-grid-2" style="margin-bottom: 1rem">
                <div class="form-group">
                    <label class="form-label" for="categoria">Categoría <span class="required">*</span></label>
                    <select id="categoria" name="categoria" required class="form-select @error('categoria') is-invalid @enderror">
                        <option value="">— Selecciona —</option>
                        @foreach (\App\Models\Ticket::CATEGORIAS as $key => $label)
                            <option value="{{ $key }}" @selected(old('categoria') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('categoria')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="prioridad">Prioridad <span class="required">*</span></label>
                    <select id="prioridad" name="prioridad" required class="form-select @error('prioridad') is-invalid @enderror">
                        @foreach (\App\Models\Ticket::PRIORIDADES as $key => $label)
                            <option value="{{ $key }}" @selected(old('prioridad', 'media') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('prioridad')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="descripcion">Descripción <span class="required">*</span></label>
                <textarea id="descripcion" name="descripcion" rows="5" required
                          placeholder="Describe el problema con todo el detalle disponible."
                          class="form-textarea @error('descripcion') is-invalid @enderror">{{ old('descripcion') }}</textarea>
                @error('descripcion')<p class="form-error">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- ── Foto adjunta ────────────────────────────────────────────── --}}
        <div class="form-section">
            <div class="form-group" x-data="{ nombre: '' }">
                <label class="form-label">Foto adjunta (opcional)</label>
                <label class="form-file-wrap @error('foto_solicitud') is-invalid @enderror">
                    <div class="form-file-icon"><i class="fas fa-image"></i></div>
                    <div>
                        <div class="form-file-text" x-text="nombre || 'Seleccionar imagen'"></div>
                        <div class="form-file-hint">JPG, PNG — máx. 5MB</div>
                    </div>
                    <input type="file" name="foto_solicitud" accept="image/*"
                           @change="nombre = $event.target.files[0]?.name ?? ''">
                </label>
                @error('foto_solicitud')<p class="form-error">{{ $message }}</p>@enderror
            </div>
        </div>

    </div>

    <div class="form-card-footer">
        <a href="{{ route('dashboard') }}" class="db-btn ghost lg">Cancelar</a>
        <button type="submit" class="db-btn solid lg">
            <i class="fas fa-check"></i> Crear ticket
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
        empresaInicial,
        solicitanteInicial,
        sedeInicial,
        asignadoInicial,
    }) {
        return {
            sedesPorEmpresa,
            usuariosPorEmpresa,
            tecnicos,
            coberturas,

            empresaSeleccionada:    empresaInicial    || '',
            solicitanteSeleccionado: solicitanteInicial || '',
            sedeSeleccionada:       sedeInicial       || '',
            asignadoSeleccionado:   asignadoInicial   || '',

            get sedesFiltradas() {
                return this.sedesPorEmpresa[this.empresaSeleccionada] ?? [];
            },
            get usuariosFiltrados() {
                return this.usuariosPorEmpresa[this.empresaSeleccionada] ?? [];
            },

            /**
             * Tecnicos cuya cobertura (en empresa_user) coincide con
             * la empresa+sede actualmente seleccionada.
             *
             * Regla:
             *  - empresa debe coincidir
             *  - si hay sede seleccionada: la cobertura debe ser NULL o la sede
             *  - si no hay sede: cualquier cobertura de la empresa sirve
             */
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
                // Al cambiar empresa, limpiar selecciones dependientes
                this.$watch('empresaSeleccionada', () => {
                    this.solicitanteSeleccionado = '';
                    this.sedeSeleccionada = '';
                    this.asignadoSeleccionado = '';
                });

                // Al cambiar sede, revalidar que el tecnico siga teniendo cobertura
                this.$watch('sedeSeleccionada', () => {
                    if (this.asignadoSeleccionado) {
                        const sigueDisponible = this.tecnicosDisponibles
                            .some(t => parseInt(t.id) === parseInt(this.asignadoSeleccionado));
                        if (!sigueDisponible) {
                            this.asignadoSeleccionado = '';
                        }
                    }
                });
            },
        };
    }
</script>
@endpush
