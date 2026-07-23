{{-- resources/views/usuarios/partials/form.blade.php --}}

@php
    $editing = isset($usuario) && $usuario !== null;
    $action  = $editing ? route('usuarios.update', $usuario) : route('usuarios.store');
@endphp

<form
    action="{{ $action }}"
    method="POST"
    class="form-card"
    x-data="usuarioForm({
        sedesPorEmpresa: {{ Js::from($sedesPorEmpresa) }},
        empresas: {{ Js::from($empresas) }},
        rolInicial: '{{ old('rol', $rolActual ?? '') }}',
        empresaInicial: '{{ old('empresa_id', $editing ? $usuario->empresa_id : '') }}',
        sedeInicial: '{{ old('sede_id', $editing ? $usuario->sede_id : '') }}',
        coberturasIniciales: {{ Js::from(old('coberturas', $coberturasActuales ?? [])) }},
    })"
>
    @csrf
    @if ($editing) @method('PUT') @endif

    {{-- ── SECCIÓN 1: Datos básicos ─────────────────────────────────────── --}}
    <div class="form-card-header">
        <h3>Datos básicos</h3>
        <p>Información de acceso y contacto</p>
    </div>

    <div class="form-card-body">
        <div class="form-section">
            <div class="form-grid form-grid-2">

                <div class="form-group">
                    <label class="form-label" for="name">Nombre completo <span class="required">*</span></label>
                    <input type="text" id="name" name="name" required maxlength="150"
                           value="{{ old('name', $editing ? $usuario->name : '') }}"
                           class="form-input @error('name') is-invalid @enderror">
                    @error('name')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email <span class="required">*</span></label>
                    <input type="email" id="email" name="email" required maxlength="255"
                           value="{{ old('email', $editing ? $usuario->email : '') }}"
                           class="form-input @error('email') is-invalid @enderror">
                    @error('email')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">
                        Contraseña
                        @if (! $editing) <span class="required">*</span>
                        @else <span style="font-weight: 500; color: var(--db-text-muted);">(dejar vacío para no cambiar)</span>
                        @endif
                    </label>
                    <input type="password" id="password" name="password" {{ $editing ? '' : 'required' }} minlength="8"
                           class="form-input @error('password') is-invalid @enderror">
                    @error('password')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Confirmar contraseña</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" minlength="8"
                           class="form-input">
                </div>

            </div>

            {{-- Activo toggle --}}
            <div class="form-group" style="margin-top: 1rem;">
                <label class="flex items-center gap-2" style="color: var(--db-text); cursor: pointer;">
                    <input type="hidden" name="activo" value="0">
                    <input type="checkbox" name="activo" value="1"
                           @checked(old('activo', $editing ? $usuario->activo : true))
                           {{ $editing && $usuario->id === auth()->id() ? 'disabled' : '' }}>
                    <span style="font-size: 0.85rem;">Usuario activo (puede iniciar sesión)</span>
                </label>
                @if ($editing && $usuario->id === auth()->id())
                    <p class="form-help">No puedes desactivar tu propia cuenta.</p>
                @endif
            </div>
        </div>

        {{-- ── SECCIÓN 2: Rol del sistema ───────────────────────────────── --}}
        <div class="form-section">
            <div class="form-section-title">Rol y permisos</div>

            <div class="form-group">
                <label class="form-label" for="rol">Rol <span class="required">*</span></label>
                <select id="rol" name="rol" required
                        x-model="rol"
                        class="form-select @error('rol') is-invalid @enderror">
                    <option value="">— Selecciona un rol —</option>
                    @foreach ($roles as $r)
                        <option value="{{ $r }}">{{ ucwords(str_replace('-', ' ', $r)) }}</option>
                    @endforeach
                </select>
                <p class="form-help">
                    El rol determina qué puede ver y hacer este usuario en el sistema.
                </p>
                @error('rol')<p class="form-error">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- ── SECCIÓN 3a: Vinculación (solo para usuario final) ─────────── --}}
        <div class="form-section" x-show="rol === 'usuario'" x-cloak>
            <div class="form-section-title">Vinculación a empresa</div>
            <p class="form-help" style="margin-top: -0.75rem; margin-bottom: 1rem;">
                ¿Dónde trabaja este usuario? Solo verá tickets de su empresa/sede.
            </p>

            <div class="form-grid form-grid-2">
                <div class="form-group">
                    <label class="form-label" for="empresa_id">Empresa <span class="required">*</span></label>
                    <select id="empresa_id" name="empresa_id"
                            x-model="empresaSeleccionada"
                            :required="rol === 'usuario'"
                            class="form-select @error('empresa_id') is-invalid @enderror">
                        <option value="">— Selecciona —</option>
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
            </div>
        </div>

        {{-- ── SECCIÓN 3b: Coberturas (solo para técnico) ────────────────── --}}
        <div class="form-section" x-show="rol === 'tecnico'" x-cloak>
            <div class="form-section-title">Coberturas del técnico</div>
            <p class="form-help" style="margin-top: -0.75rem; margin-bottom: 1rem;">
                ¿Qué empresas y sedes cubre este técnico? Cada fila es una asignación.
                Si dejas la sede vacía, el técnico cubre <strong>toda la empresa</strong>.
            </p>

            <template x-for="(cob, i) in coberturas" :key="i">
                <div class="cobertura-row">
                    <select x-model.number="cob.empresa_id"
                            @change="cob.sede_id = ''"
                            class="form-select">
                        <option value="">— Empresa —</option>
                        <template x-for="emp in empresas" :key="emp.id">
                            <option :value="emp.id" x-text="emp.nombre"></option>
                        </template>
                    </select>

                    <select x-model.number="cob.sede_id"
                            :disabled="!cob.empresa_id"
                            class="form-select">
                        <option value="">— Toda la empresa —</option>
                        <template x-for="sede in sedesDeEmpresa(cob.empresa_id)" :key="sede.id">
                            <option :value="sede.id" x-text="sede.nombre"></option>
                        </template>
                    </select>

                    <button type="button" @click="quitar(i)" class="db-btn ghost" title="Quitar">
                        <i class="fas fa-times"></i>
                    </button>

                    {{-- Inputs hidden para enviar al backend --}}
                    <input type="hidden" :name="`coberturas[${i}][empresa_id]`" :value="cob.empresa_id || ''">
                    <input type="hidden" :name="`coberturas[${i}][sede_id]`" :value="cob.sede_id || ''">
                </div>
            </template>

            <button type="button" @click="agregar()" class="db-btn outline" style="margin-top: 0.5rem;">
                <i class="fas fa-plus"></i> Agregar cobertura
            </button>

            <p class="form-help" x-show="coberturas.length === 0" x-cloak style="margin-top: 0.75rem; color: #fbbf24;">
                <i class="fas fa-exclamation-triangle"></i>
                Sin coberturas, el técnico no podrá ver ni gestionar tickets ni equipos.
            </p>

            @error('coberturas')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        {{-- Sección informativa para admin/super-admin --}}
        <div class="form-section" x-show="rol === 'admin' || rol === 'super-admin'" x-cloak>
            <div style="background-color: var(--db-bg); border: 1px dashed var(--db-border);
                        border-radius: 0.5rem; padding: 1rem;">
                <p style="color: var(--db-text-muted); font-size: 0.82rem; margin: 0;">
                    <i class="fas fa-info-circle"></i>
                    Los administradores tienen acceso a todas las empresas del sistema y no requieren
                    asignación específica.
                </p>
            </div>
        </div>

    </div>

    <div class="form-card-footer">
        <a href="{{ route('usuarios.index') }}" class="db-btn ghost lg">Cancelar</a>
        <button type="submit" class="db-btn solid lg">
            <i class="fas fa-save"></i> {{ $editing ? 'Guardar cambios' : 'Crear usuario' }}
        </button>
    </div>

</form>

<style>
    .cobertura-row {
        display: grid;
        grid-template-columns: 1fr 1fr auto;
        gap: 0.5rem;
        align-items: center;
        margin-bottom: 0.5rem;
    }
</style>

@push('scripts')
<script>
    function usuarioForm({ sedesPorEmpresa, empresas, rolInicial, empresaInicial, sedeInicial, coberturasIniciales }) {
        return {
            sedesPorEmpresa,
            empresas,

            rol: rolInicial || '',
            empresaSeleccionada: empresaInicial || '',
            sedeSeleccionada: sedeInicial || '',
            // Normalizar coberturas iniciales (vienen del backend o del old())
            coberturas: (coberturasIniciales || []).map(c => ({
                empresa_id: c.empresa_id ? parseInt(c.empresa_id) : '',
                sede_id: c.sede_id ? parseInt(c.sede_id) : '',
            })),

            get sedesFiltradas() {
                return this.sedesPorEmpresa[this.empresaSeleccionada] ?? [];
            },

            sedesDeEmpresa(empresaId) {
                if (!empresaId) return [];
                return this.sedesPorEmpresa[empresaId] ?? [];
            },

            agregar() {
                this.coberturas.push({ empresa_id: '', sede_id: '' });
            },

            quitar(index) {
                this.coberturas.splice(index, 1);
            },

            init() {
                // Al cambiar empresa principal (usuario final), limpiar sede
                this.$watch('empresaSeleccionada', () => {
                    this.sedeSeleccionada = '';
                });

                // Si entra al form con rol técnico y sin coberturas, agregar fila vacía
                this.$watch('rol', (nuevo) => {
                    if (nuevo === 'tecnico' && this.coberturas.length === 0) {
                        this.agregar();
                    }
                });

                // Si ya inició como técnico sin coberturas, agregar una vacía
                if (this.rol === 'tecnico' && this.coberturas.length === 0) {
                    this.agregar();
                }
            },
        };
    }
</script>
@endpush
