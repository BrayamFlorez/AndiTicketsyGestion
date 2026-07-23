{{-- resources/views/equipos/partials/form.blade.php --}}

@php
    $editing = isset($equipo) && $equipo !== null;
    $action  = $editing ? route('equipos.update', $equipo) : route('equipos.store');

    // Valores iniciales (old() tiene prioridad para preservar input tras error)
    $valEmpresa     = old('empresa_id',     $editing ? $equipo->empresa_id : '');
    $valSede        = old('sede_id',        $editing ? $equipo->sede_id : '');
    $valResponsable = old('responsable_id', $editing ? $equipo->responsable_id : '');
    $valCategoria   = old('categoria',      $editing ? $equipo->categoria : '');
    $valEspecs      = old('especificaciones', $editing ? ($equipo->especificaciones ?? []) : []);
@endphp

<form
    action="{{ $action }}"
    method="POST"
    class="form-card"
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
>
    @csrf
    @if ($editing) @method('PUT') @endif

    <div class="form-card-header">
        <h3>{{ $editing ? 'Modificar equipo' : 'Datos del equipo' }}</h3>
        <p>Define a qué empresa pertenece, su categoría y especificaciones</p>
    </div>

    <div class="form-card-body">

        {{-- ── SECCIÓN 1: Contexto ──────────────────────────────────────── --}}
        <div class="form-section">
            <div class="form-section-title">Vinculación</div>

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

                <div class="form-group">
                    <label class="form-label" for="responsable_id">Responsable (opcional)</label>
                    <select id="responsable_id" name="responsable_id"
                            x-model="responsableSeleccionado"
                            :disabled="!empresaSeleccionada"
                            class="form-select @error('responsable_id') is-invalid @enderror">
                        <option value="">— Sin responsable —</option>
                        <template x-for="u in usuariosFiltrados" :key="u.id">
                            <option :value="u.id" x-text="u.name"></option>
                        </template>
                    </select>
                    @error('responsable_id')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="ubicacion">Ubicación física (opcional)</label>
                    <input type="text" id="ubicacion" name="ubicacion" maxlength="255"
                           value="{{ old('ubicacion', $editing ? $equipo->ubicacion : '') }}"
                           placeholder="Ej: Recepción, Oficina 203, Bodega"
                           class="form-input @error('ubicacion') is-invalid @enderror">
                    @error('ubicacion')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- ── SECCIÓN 2: Identificación ──────────────────────────────────── --}}
        <div class="form-section">
            <div class="form-section-title">Identificación del equipo</div>

            <div class="form-grid form-grid-2">

                <div class="form-group">
                    <label class="form-label" for="categoria">Categoría <span class="required">*</span></label>
                    <select id="categoria" name="categoria" required
                            x-model="categoria"
                            class="form-select @error('categoria') is-invalid @enderror">
                        <option value="">— Selecciona categoría —</option>
                        @foreach (\App\Models\Equipo::CATEGORIAS as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @if ($editing)
                        <p class="form-help" x-show="categoria !== categoriaInicial" x-cloak
                           style="color: #fbbf24;">
                            <i class="fas fa-exclamation-triangle"></i>
                            Cambiar de categoría borrará los campos específicos del equipo.
                        </p>
                    @endif
                    @error('categoria')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="nombre">Nombre identificador <span class="required">*</span></label>
                    <input type="text" id="nombre" name="nombre" required maxlength="150"
                           value="{{ old('nombre', $editing ? $equipo->nombre : '') }}"
                           placeholder="Ej: PC-Recepción-01"
                           class="form-input @error('nombre') is-invalid @enderror">
                    @error('nombre')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="marca">Marca</label>
                    <input type="text" id="marca" name="marca" maxlength="100"
                           value="{{ old('marca', $editing ? $equipo->marca : '') }}"
                           placeholder="Ej: HP, Lenovo, Epson"
                           class="form-input @error('marca') is-invalid @enderror">
                    @error('marca')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="modelo">Modelo</label>
                    <input type="text" id="modelo" name="modelo" maxlength="100"
                           value="{{ old('modelo', $editing ? $equipo->modelo : '') }}"
                           class="form-input @error('modelo') is-invalid @enderror">
                    @error('modelo')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="numero_serie">Número de serie</label>
                    <input type="text" id="numero_serie" name="numero_serie" maxlength="100"
                           value="{{ old('numero_serie', $editing ? $equipo->numero_serie : '') }}"
                           class="form-input @error('numero_serie') is-invalid @enderror"
                           style="font-family: monospace;">
                    <p class="form-help">Debe ser único dentro de la empresa.</p>
                    @error('numero_serie')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="estado">Estado</label>
                    <select id="estado" name="estado" class="form-select @error('estado') is-invalid @enderror">
                        @foreach (\App\Models\Equipo::ESTADOS as $key => $label)
                            <option value="{{ $key }}" @selected(old('estado', $editing ? $equipo->estado : 'activo') === $key)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('estado')<p class="form-error">{{ $message }}</p>@enderror
                </div>

            </div>
        </div>

        {{-- ── SECCIÓN 3: Especificaciones dinámicas ─────────────────────── --}}
        <div class="form-section" x-show="categoria" x-cloak>
            <div class="form-section-title">
                Especificaciones técnicas
                <span x-show="categoria" x-text="`(${labelCategoria})`" style="font-weight: 500; color: var(--db-text-muted)"></span>
            </div>

            <div class="form-grid form-grid-2">
                <template x-for="(config, campo) in camposDelEsquema" :key="campo">
                    <div class="form-group">
                        <label class="form-label" :for="`espec_${campo}`">
                            <span x-text="config.label"></span>
                            <span x-show="config.required !== false" class="required">*</span>
                        </label>

                        {{-- Input según tipo --}}
                        <template x-if="config.type === 'select'">
                            <select :id="`espec_${campo}`"
                                    :name="`especificaciones[${campo}]`"
                                    :required="config.required !== false"
                                    x-model="valores[campo]"
                                    class="form-select">
                                <option value="">— Selecciona —</option>
                                <template x-for="op in config.options" :key="op">
                                    <option :value="op" x-text="op"></option>
                                </template>
                            </select>
                        </template>

                        <template x-if="config.type === 'number'">
                            <input type="number" min="0" step="any"
                                   :id="`espec_${campo}`"
                                   :name="`especificaciones[${campo}]`"
                                   :required="config.required !== false"
                                   x-model="valores[campo]"
                                   :placeholder="config.placeholder ?? ''"
                                   class="form-input">
                        </template>

                        <template x-if="config.type === 'text' || !config.type">
                            <input type="text" maxlength="200"
                                   :id="`espec_${campo}`"
                                   :name="`especificaciones[${campo}]`"
                                   :required="config.required !== false"
                                   x-model="valores[campo]"
                                   :placeholder="config.placeholder ?? ''"
                                   class="form-input">
                        </template>
                    </div>
                </template>
            </div>
        </div>

        {{-- ── SECCIÓN 4: Notas ──────────────────────────────────────────── --}}
        <div class="form-section">
            <div class="form-group">
                <label class="form-label" for="notas">Notas internas (opcional)</label>
                <textarea id="notas" name="notas" rows="3" maxlength="2000"
                          placeholder="Observaciones, garantía, particularidades..."
                          class="form-textarea @error('notas') is-invalid @enderror">{{ old('notas', $editing ? $equipo->notas : '') }}</textarea>
                @error('notas')<p class="form-error">{{ $message }}</p>@enderror
            </div>
        </div>

    </div>

    <div class="form-card-footer">
        <a href="{{ route('equipos.index') }}" class="db-btn ghost lg">Cancelar</a>
        <button type="submit" class="db-btn solid lg">
            <i class="fas fa-{{ $editing ? 'save' : 'check' }}"></i>
            {{ $editing ? 'Guardar cambios' : 'Registrar equipo' }}
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
            categoriaInicial:        categoriaInicial || '',  // Para detectar cambios
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
                // Al cambiar empresa: limpiar sede y responsable
                this.$watch('empresaSeleccionada', () => {
                    this.sedeSeleccionada = '';
                    this.responsableSeleccionado = '';
                });

                // Al cambiar categoria: limpiar valores (campos incompatibles)
                this.$watch('categoria', (nueva, anterior) => {
                    // Solo limpiar si efectivamente cambió (no en init)
                    if (anterior && nueva !== anterior) {
                        this.valores = {};
                    }
                });
            },
        };
    }
</script>
@endpush
