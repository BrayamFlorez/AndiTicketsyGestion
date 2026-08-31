{{-- resources/views/usuarios/partials/form.blade.php --}}

@php
    $editing = isset($usuario) && $usuario !== null;
    $action  = $editing ? route('usuarios.update', $usuario) : route('usuarios.store');

    $rolSeleccionado     = old('rol', $rolActual ?? '');
    $empresaSeleccionada = old('empresa_id', $editing ? $usuario->empresa_id : '');
    $sedeSeleccionada    = old('sede_id', $editing ? $usuario->sede_id : '');
@endphp

<form action="{{ $action }}"
      method="POST"
      x-data="usuarioForm({
          sedesPorEmpresa: {{ Js::from($sedesPorEmpresa) }},
          empresas: {{ Js::from($empresas) }},
          rolesConCobertura: {{ Js::from($rolesConCobertura) }},
          rolesConEmpresa: {{ Js::from($rolesConEmpresa) }},
          rolesTransversales: {{ Js::from($rolesTransversales) }},
          rolInicial: {{ Js::from($rolSeleccionado) }},
          empresaInicial: {{ Js::from($empresaSeleccionada) }},
          sedeInicial: {{ Js::from($sedeSeleccionada) }},
          coberturasIniciales: {{ Js::from(old('coberturas', $coberturasActuales ?? [])) }},
      })"
      class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
    @csrf
    @if ($editing) @method('PUT') @endif

    <div class="p-6 space-y-6">

        {{-- ── SECCIÓN 1: Datos básicos ─────────────────────────────────────── --}}
        <div>
            <div class="mb-4 pb-2 border-b border-stroke dark:border-strokedark">
                <h3 class="font-bold text-black dark:text-white uppercase tracking-wider text-xs flex items-center gap-1.5">
                    <i class="fas fa-id-card text-primary"></i> Datos Básicos y Credenciales
                </h3>
                <p class="text-[10px] text-bodydark2 mt-0.5">Información personal y datos para inicio de sesión en el sistema</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Nombre completo --}}
                <div>
                    <label for="name" class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">
                        Nombre completo <span class="text-meta-1">*</span>
                    </label>
                    <input type="text" id="name" name="name" required maxlength="150"
                           value="{{ old('name', $editing ? $usuario->name : '') }}"
                           placeholder="Ej: Laura Ramírez"
                           class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white @error('name') border-meta-1 @enderror">
                    @error('name')<p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $message }}</p>@enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">
                        Email / Usuario <span class="text-meta-1">*</span>
                    </label>
                    <input type="email" id="email" name="email" required maxlength="255"
                           value="{{ old('email', $editing ? $usuario->email : '') }}"
                           placeholder="usuario@empresa.com"
                           class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white @error('email') border-meta-1 @enderror">
                    @error('email')<p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $message }}</p>@enderror
                </div>

                {{-- Contraseña --}}
                <div>
                    <label for="password" class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">
                        Contraseña
                        @if (! $editing)
                            <span class="text-meta-1">*</span>
                        @else
                            <span class="text-bodydark2 font-normal text-[9px]">(Dejar vacío para no cambiar)</span>
                        @endif
                    </label>
                    <input type="password" id="password" name="password" {{ $editing ? '' : 'required' }} minlength="8"
                           placeholder="••••••••"
                           class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white @error('password') border-meta-1 @enderror">
                    @error('password')<p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $message }}</p>@enderror
                </div>

                {{-- Confirmar contraseña --}}
                <div>
                    <label for="password_confirmation" class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">
                        Confirmar contraseña
                    </label>
                    <input type="password" id="password_confirmation" name="password_confirmation" minlength="8"
                           placeholder="••••••••"
                           class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                </div>
            </div>

            {{-- Activo Toggle --}}
            <div class="mt-4 pt-1">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="hidden" name="activo" value="0">
                    <input type="checkbox" name="activo" value="1"
                           @checked(old('activo', $editing ? $usuario->activo : true))
                           {{ $editing && $usuario->id === auth()->id() ? 'disabled' : '' }}
                           class="rounded border-stroke text-primary focus:ring-primary dark:border-form-strokedark dark:bg-form-input">
                    <span class="text-xs font-medium text-black dark:text-white">Usuario activo (puede iniciar sesión en la plataforma)</span>
                </label>
                @if ($editing && $usuario->id === auth()->id())
                    <p class="mt-0.5 text-[10px] text-meta-6 font-medium"><i class="fas fa-info-circle"></i> No puedes desactivar tu propia cuenta activa.</p>
                @endif
            </div>
        </div>

        {{-- ── SECCIÓN 2: Rol del sistema ───────────────────────────────── --}}
        <div>
            <div class="mb-3 pb-2 border-b border-stroke dark:border-strokedark">
                <h3 class="font-bold text-black dark:text-white uppercase tracking-wider text-xs flex items-center gap-1.5">
                    <i class="fas fa-user-tag text-primary"></i> Rol del Sistema
                </h3>
            </div>

            <div>
                <label for="rol" class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">
                    Rol Asignado <span class="text-meta-1">*</span>
                </label>
                <div class="relative z-20">
                    <select id="rol" name="rol" required
                            x-model="rol"
                            class="w-full appearance-none rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white cursor-pointer @error('rol') border-meta-1 @enderror">
                        <option value="" class="bg-[#1c2434] text-gray-400">— Selecciona un rol —</option>
                        @foreach ($roles as $r)
                            <option value="{{ $r }}" @selected($rolSeleccionado === $r) class="bg-[#1c2434] text-white">
                                {{ ucwords(str_replace('-', ' ', $r)) }}
                            </option>
                        @endforeach
                    </select>
                    <span class="absolute top-1/2 right-3 -translate-y-1/2 z-30 pointer-events-none">
                        <i class="fas fa-chevron-down text-[10px] text-bodydark2"></i>
                    </span>
                </div>
                <p class="mt-1 text-[10px] text-bodydark2">El rol determina los permisos, módulos habilitados y la vista principal del usuario.</p>
                @error('rol')<p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- ── SECCIÓN 3a: Vinculación a empresa ────────────────────────── --}}
        <div x-show="necesitaEmpresa" x-cloak class="space-y-3">
            <div class="mb-2 pb-2 border-b border-stroke dark:border-strokedark">
                <h3 class="font-bold text-black dark:text-white uppercase tracking-wider text-xs flex items-center gap-1.5">
                    <i class="fas fa-building text-primary"></i> Vinculación de Empresa y Sede
                </h3>
                <p class="text-[10px] text-bodydark2 mt-0.5">Determina la ubicación del usuario. Solo visualizará la información asociada a su entidad base.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Empresa --}}
                <div>
                    <label for="empresa_id" class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">
                        Empresa <span class="text-meta-1">*</span>
                    </label>
                    <div class="relative z-20">
                        <select id="empresa_id" name="empresa_id"
                                x-model="empresaSeleccionada"
                                :required="necesitaEmpresa"
                                class="w-full appearance-none rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white cursor-pointer @error('empresa_id') border-meta-1 @enderror">
                            <option value="" class="bg-[#1c2434] text-gray-400">— Selecciona empresa —</option>
                            @foreach ($empresas as $empresa)
                                <option value="{{ $empresa->id }}" @selected((string) $empresaSeleccionada === (string) $empresa->id) class="bg-[#1c2434] text-white">
                                    {{ $empresa->nombre }}
                                </option>
                            @endforeach
                        </select>
                        <span class="absolute top-1/2 right-3 -translate-y-1/2 z-30 pointer-events-none">
                            <i class="fas fa-chevron-down text-[10px] text-bodydark2"></i>
                        </span>
                    </div>
                    @error('empresa_id')<p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $message }}</p>@enderror
                </div>

                {{-- Sede --}}
                <div>
                    <label for="sede_id" class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">
                        Sede (Opcional)
                    </label>
                    <div class="relative z-20">
                        <select id="sede_id" name="sede_id"
                                x-model="sedeSeleccionada"
                                :disabled="!empresaSeleccionada"
                                class="w-full appearance-none rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white disabled:bg-whiten disabled:cursor-not-allowed dark:disabled:bg-meta-4 @error('sede_id') border-meta-1 @enderror">
                            <option value="" class="bg-[#1c2434] text-gray-400">— Sin sede específica —</option>
                            <template x-for="sede in sedesFiltradas" :key="sede.id">
                                <option :value="sede.id" x-text="sede.nombre" class="bg-[#1c2434] text-white"></option>
                            </template>
                        </select>
                        <span class="absolute top-1/2 right-3 -translate-y-1/2 z-30 pointer-events-none">
                            <i class="fas fa-chevron-down text-[10px] text-bodydark2"></i>
                        </span>
                    </div>
                    @error('sede_id')<p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- ── SECCIÓN 3b: Coberturas ───────────────────────────────────── --}}
        <div x-show="necesitaCobertura" x-cloak class="rounded border border-stroke bg-gray-2 p-4 dark:border-strokedark dark:bg-meta-4/30">
            <div class="mb-3 pb-1 border-b border-stroke dark:border-strokedark">
                <h3 class="font-bold text-black dark:text-white uppercase tracking-wider text-xs flex items-center gap-1.5">
                    <i class="fas fa-globe text-primary"></i> Coberturas Asignadas (Multi-Empresa)
                </h3>
                <p class="text-[10px] text-bodydark2 mt-0.5">
                    Define las empresas o sedes que este usuario tiene autorizadas para gestionar. Si dejas la sede en blanco, cubrirá <strong>toda la empresa</strong>.
                </p>
            </div>

            <div class="space-y-2">
                <template x-for="(cob, i) in coberturas" :key="i">
                    <div class="flex items-center gap-2">
                        {{-- Select Empresa --}}
                        <div class="relative flex-1 z-20">
                            <select x-model.number="cob.empresa_id"
                                    @change="cob.sede_id = ''"
                                    class="w-full appearance-none rounded border border-stroke bg-white py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-boxdark text-black dark:text-white cursor-pointer">
                                <option value="" class="bg-[#1c2434] text-gray-400">— Seleccionar Empresa —</option>
                                @foreach ($empresas as $emp)
                                    <option value="{{ $emp->id }}" class="bg-[#1c2434] text-white">{{ $emp->nombre }}</option>
                                @endforeach
                            </select>
                            <span class="absolute top-1/2 right-3 -translate-y-1/2 z-30 pointer-events-none">
                                <i class="fas fa-chevron-down text-[10px] text-bodydark2"></i>
                            </span>
                        </div>

                        {{-- Select Sede --}}
                        <div class="relative flex-1 z-20">
                            <select x-model.number="cob.sede_id"
                                    :disabled="!cob.empresa_id"
                                    x-init="$nextTick(() => { if (cob.sede_id) $el.value = cob.sede_id })"
                                    class="w-full appearance-none rounded border border-stroke bg-white py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-boxdark text-black dark:text-white disabled:bg-whiten disabled:cursor-not-allowed dark:disabled:bg-meta-4 cursor-pointer">
                                <option value="" class="bg-[#1c2434] text-gray-400">— Toda la Empresa —</option>
                                <template x-for="sede in sedesDeEmpresa(cob.empresa_id)" :key="sede.id">
                                    <option :value="sede.id" x-text="sede.nombre" class="bg-[#1c2434] text-white"></option>
                                </template>
                            </select>
                            <span class="absolute top-1/2 right-3 -translate-y-1/2 z-30 pointer-events-none">
                                <i class="fas fa-chevron-down text-[10px] text-bodydark2"></i>
                            </span>
                        </div>

                        {{-- Botón Quitar --}}
                        <button type="button" @click="quitar(i)" 
                                class="flex h-8 w-8 items-center justify-center rounded border border-stroke text-meta-1 hover:bg-meta-1/10 dark:border-strokedark transition shrink-0" 
                                title="Quitar regla">
                            <i class="fas fa-times text-xs"></i>
                        </button>

                        {{-- Inputs Hidden --}}
                        <input type="hidden" :name="`coberturas[${i}][empresa_id]`" :value="cob.empresa_id || ''">
                        <input type="hidden" :name="`coberturas[${i}][sede_id]`" :value="cob.sede_id || ''">
                    </div>
                </template>
            </div>

            <div class="mt-3">
                <button type="button" @click="agregar()" 
                        class="inline-flex items-center gap-1.5 rounded border border-stroke py-1.5 px-3 text-xs font-bold uppercase text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition">
                    <i class="fas fa-plus text-[10px]"></i> Agregar Cobertura
                </button>

                <p class="mt-2 text-[10px] font-medium text-meta-6" x-show="coberturas.length === 0" x-cloak>
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Sin coberturas asignadas, este usuario no podrá visualizar ni atender solicitudes/equipos.
                </p>
            </div>

            @error('coberturas')<p class="mt-1 text-[10px] text-meta-1 font-medium">{{ $message }}</p>@enderror
        </div>

        {{-- ── Sección informativa: roles transversales ─────────────────── --}}
        <div x-show="esTransversal" x-cloak>
            <div class="rounded-sm border border-dashed border-stroke bg-gray-2 p-4 dark:border-strokedark dark:bg-meta-4/20">
                <p class="text-xs text-bodydark2 flex items-center gap-2">
                    <i class="fas fa-info-circle text-primary text-sm"></i>
                    Este rol posee permisos globales/transversales a nivel de sistema, por lo que no requiere vinculación ni coberturas de empresa específicas.
                </p>
            </div>
        </div>

    </div>

    {{-- Footer de Acciones --}}
    <div class="flex items-center justify-end gap-2 bg-gray-2 dark:bg-meta-4/40 px-6 py-3 border-t border-stroke dark:border-strokedark">
        <a href="{{ route('usuarios.index') }}" 
           class="inline-flex items-center justify-center rounded border border-stroke py-1.5 px-4 text-center text-xs font-medium text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition">
            Cancelar
        </a>
        <button type="submit" 
                class="inline-flex items-center gap-1.5 rounded bg-primary py-1.5 px-5 text-center text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm cursor-pointer">
            <i class="fas fa-{{ $editing ? 'save' : 'check' }} text-[10px]"></i>
            {{ $editing ? 'Guardar Cambios' : 'Crear Usuario' }}
        </button>
    </div>

</form>

@push('scripts')
<script>
    function usuarioForm(config) {
        return {
            sedesPorEmpresa:    config.sedesPorEmpresa || {},
            empresas:           config.empresas || [],
            rolesConCobertura:  config.rolesConCobertura || [],
            rolesConEmpresa:    config.rolesConEmpresa || [],
            rolesTransversales: config.rolesTransversales || [],

            rol:                 config.rolInicial || '',
            empresaSeleccionada: config.empresaInicial ? String(config.empresaInicial) : '',
            sedeSeleccionada:    config.sedeInicial ? String(config.sedeInicial) : '',

            coberturas: (config.coberturasIniciales || []).map(c => ({
                empresa_id: c.empresa_id ? parseInt(c.empresa_id) : '',
                sede_id:    c.sede_id ? parseInt(c.sede_id) : '',
            })),

            get necesitaCobertura() {
                return this.rolesConCobertura.includes(this.rol);
            },
            get necesitaEmpresa() {
                return this.rolesConEmpresa.includes(this.rol);
            },
            get esTransversal() {
                return this.rolesTransversales.includes(this.rol);
            },

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
                let primeraVez = true;
                this.$watch('empresaSeleccionada', () => {
                    if (primeraVez) { primeraVez = false; return; }
                    this.sedeSeleccionada = '';
                });

                this.$watch('rol', () => {
                    if (this.necesitaCobertura && this.coberturas.length === 0) {
                        this.agregar();
                    }
                });

                if (this.necesitaCobertura && this.coberturas.length === 0) {
                    this.agregar();
                }
            },
        };
    }
</script>
@endpush