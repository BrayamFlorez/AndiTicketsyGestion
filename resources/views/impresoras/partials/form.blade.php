{{-- resources/views/impresoras/partials/_form.blade.php --}}
@php
    $editing = isset($impresora) && $impresora->exists;
    $action = $editing ? route('impresoras.update', $impresora) : route('impresoras.store');
    $hasRedData = old('ip_red', $editing ? $impresora->ip_red : '') || old('panel_web_usuario', $editing ? $impresora->panel_web_usuario : '');
    $hasEstadoData = old('estado', $editing ? $impresora->estado : 'activa') !== 'activa' || old('intervalo_mantenimiento_dias', $editing ? $impresora->intervalo_mantenimiento_dias : '');
@endphp

{{-- Header Compacto (Sin Fixed/Sticky para evitar desfasaje) --}}
<div class="mb-3 flex items-center justify-between border-b border-stroke pb-2.5 dark:border-strokedark">
    <div>
        <h2 class="text-title-md font-bold text-black dark:text-white flex items-center gap-1.5 uppercase tracking-wider text-xs">
            <i class="fas fa-print text-primary"></i>
            {{ $editing ? "Editar {$impresora->codigo}" : 'Nueva impresora' }}
        </h2>
        <p class="text-[10px] text-bodydark2 mt-0.5">
            {{ $editing ? 'Modificá los datos del equipo.' : 'Registrá una nueva impresora en el inventario.' }}
        </p>
    </div>
    <a href="{{ route('impresoras.index') }}"
        class="inline-flex items-center gap-1 rounded border border-stroke py-1 px-3 text-xs font-bold text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition">
        <i class="fas fa-arrow-left text-[10px]"></i> Cancelar
    </a>
</div>

{{-- ── FORMULARIO PRINCIPAL CON INTERCEPTOR PARA EL OSO ── --}}
<form action="{{ $action }}" method="POST" enctype="multipart/form-data"
    @submit.prevent="
        if ($el.checkValidity()) {
            estadoGuardado = 'guardando';
            setTimeout(() => {
                estadoGuardado = 'exito';
                setTimeout(() => $el.submit(), 500);
            }, 1200);
        } else {
            $el.reportValidity();
        }
    "
    x-data="impresoraForm({
        sedesPorEmpresa: {{ Js::from($sedesPorEmpresa) }},
        empresaInicial: '{{ old('empresa_id', $editing ? $impresora->empresa_id : '') }}',
        sedeInicial: '{{ old('sede_id', $editing ? $impresora->sede_id : '') }}',
        hasRedData: {{ $hasRedData ? 'true' : 'false' }},
        hasEstadoData: {{ $hasEstadoData ? 'true' : 'false' }}
    })">
    @csrf
    @if ($editing)
        @method('PUT')
    @endif

    {{-- Alert de Errores Comprimido --}}
    @if ($errors->any())
        <div class="mb-3 flex w-full border-l-4 border-meta-1 bg-meta-1/10 px-3 py-2 rounded-sm">
            <i class="fas fa-exclamation-triangle text-meta-1 mr-2 mt-0.5 text-xs"></i>
            <ul class="text-[11px] text-meta-1 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Layout Principal Grid (8 Cols Principal / 4 Cols Lateral) --}}
    <div class="grid grid-cols-12 gap-3 md:gap-4">

        {{-- COLUMNA IZQUIERDA: Datos Primarios e Identificación (8 Cols) --}}
        <div class="col-span-12 lg:col-span-8 space-y-3">

            {{-- Card: Vinculación e Identificación Principal --}}
            <div class="rounded-sm border border-stroke bg-white p-3.5 shadow-default dark:border-strokedark dark:bg-boxdark">
                <h3 class="text-[11px] font-bold uppercase tracking-wider text-primary mb-2.5 pb-1 border-b border-stroke dark:border-strokedark flex items-center gap-1.5">
                    <i class="fas fa-building"></i> Vinculación & Modelo
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                    <div>
                        <label class="block text-[9px] font-bold uppercase text-black dark:text-white mb-0.5">
                            Empresa <span class="text-meta-1">*</span>
                        </label>
                        <select name="empresa_id" x-model="empresaSeleccionada" required
                            class="w-full rounded border border-stroke bg-transparent py-1 px-2 text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                            <option value="" class="bg-[#1c2434] text-gray-400">— Seleccioná —</option>
                            @foreach ($empresas as $empresa)
                                <option value="{{ $empresa->id }}" @selected((string) old('empresa_id', $editing ? $impresora->empresa_id : '') === (string) $empresa->id) class="bg-[#1c2434] text-white">
                                    {{ $empresa->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[9px] font-bold uppercase text-black dark:text-white mb-0.5">
                            Sede (Opcional)
                        </label>
                        <select name="sede_id" x-model="sedeSeleccionada" :disabled="!empresaSeleccionada"
                            class="w-full rounded border border-stroke bg-transparent py-1 px-2 text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white disabled:opacity-50">
                            <option value="" class="bg-[#1c2434] text-gray-400">— Sin sede —</option>
                            <template x-for="sede in sedesFiltradas" :key="sede.id">
                                <option :value="sede.id" x-text="sede.nombre" class="bg-[#1c2434] text-white"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[9px] font-bold uppercase text-black dark:text-white mb-0.5">
                            Modelo (Catálogo)
                        </label>
                        <select name="modelo_id"
                            class="w-full rounded border border-stroke bg-transparent py-1 px-2 text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                            <option value="" class="bg-[#1c2434] text-gray-400">— Sin especificar —</option>
                            @foreach ($modelos as $m)
                                <option value="{{ $m->id }}" @selected(old('modelo_id', $editing ? $impresora->modelo_id : '') == $m->id) class="bg-[#1c2434] text-white">
                                    {{ $m->marca }} {{ $m->modelo }} {{ $m->color ? '(Color)' : '(Mono)' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 mt-2.5">
                    <div>
                        <label class="block text-[9px] font-bold uppercase text-black dark:text-white mb-0.5">
                            Nombre asignado
                        </label>
                        <input type="text" name="nombre_asignado" maxlength="120"
                            value="{{ old('nombre_asignado', $editing ? $impresora->nombre_asignado : '') }}"
                            placeholder="Ej: Impresora Recepción"
                            class="w-full rounded border border-stroke bg-transparent py-1 px-2.5 text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                    </div>

                    <div>
                        <label class="block text-[9px] font-bold uppercase text-black dark:text-white mb-0.5">
                            Ubicación física
                        </label>
                        <input type="text" name="ubicacion_fisica" maxlength="200"
                            value="{{ old('ubicacion_fisica', $editing ? $impresora->ubicacion_fisica : '') }}"
                            placeholder="Ej: Piso 3, Oficina 302"
                            class="w-full rounded border border-stroke bg-transparent py-1 px-2.5 text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                    </div>
                </div>
            </div>

            {{-- Card: Datos de Hardware / Ficha --}}
            <div class="rounded-sm border border-stroke bg-white p-3.5 shadow-default dark:border-strokedark dark:bg-boxdark">
                <h3 class="text-[11px] font-bold uppercase tracking-wider text-primary mb-2.5 pb-1 border-b border-stroke dark:border-strokedark flex items-center gap-1.5">
                    <i class="fas fa-barcode"></i> Ficha Técnica & Serie
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                    <div>
                        <label class="block text-[9px] font-bold uppercase text-black dark:text-white mb-0.5">
                            Código interno
                        </label>
                        <input type="text" name="codigo_interno" maxlength="50"
                            value="{{ old('codigo_interno', $editing ? $impresora->codigo_interno : '') }}"
                            placeholder="Ej: IMP-CONT-01"
                            class="w-full rounded border border-stroke bg-transparent py-1 px-2.5 font-mono text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                    </div>

                    <div>
                        <label class="block text-[9px] font-bold uppercase text-black dark:text-white mb-0.5">
                            Número de serie
                        </label>
                        <input type="text" name="numero_serie" maxlength="80"
                            value="{{ old('numero_serie', $editing ? $impresora->numero_serie : '') }}"
                            placeholder="S/N del fabricante"
                            class="w-full rounded border border-stroke bg-transparent py-1 px-2.5 font-mono text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                    </div>

                    <div>
                        <label class="block text-[9px] font-bold uppercase text-black dark:text-white mb-0.5">
                            Fecha adquisición
                        </label>
                        <input type="date" name="fecha_adquisicion"
                            value="{{ old('fecha_adquisicion', $editing ? $impresora->fecha_adquisicion?->format('Y-m-d') : '') }}"
                            class="w-full rounded border border-stroke bg-transparent py-1 px-2.5 text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                    </div>
                </div>

                <div class="mt-2.5">
                    <label class="block text-[9px] font-bold uppercase text-black dark:text-white mb-0.5">
                        Observaciones
                    </label>
                    <textarea name="observaciones" rows="2" maxlength="2000"
                        placeholder="Detalles adicionales, estado físico..."
                        class="w-full rounded border border-stroke bg-transparent py-1.5 px-2.5 text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">{{ old('observaciones', $editing ? $impresora->observaciones : '') }}</textarea>
                </div>
            </div>

        </div>

        {{-- COLUMNA DERECHA: Plegables Opcionales (Red, Acceso y Estado) (4 Cols) --}}
        <div class="col-span-12 lg:col-span-4 space-y-3">

            {{-- Plegable 1: Estado & Mantenimiento --}}
            <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="flex items-center justify-between p-3 cursor-pointer border-b border-stroke dark:border-strokedark select-none"
                    @click="openEstado = !openEstado">
                    <h3 class="text-[11px] font-bold uppercase tracking-wider text-black dark:text-white flex items-center gap-1.5">
                        <i class="fas fa-cog text-primary"></i> Estado & Mantenimiento
                    </h3>
                    <div class="flex items-center gap-1.5">
                        <span class="text-[9px] font-bold uppercase px-1.5 py-0.2 rounded bg-primary/10 text-primary">
                            {{ ucfirst(old('estado', $editing ? $impresora->estado : 'activa')) }}
                        </span>
                        <i class="fas text-xs text-bodydark2" :class="openEstado ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </div>
                </div>

                <div x-show="openEstado" x-cloak x-collapse class="p-3 space-y-2.5">
                    <div>
                        <label class="block text-[9px] font-bold uppercase text-black dark:text-white mb-0.5">
                            Estado Operativo <span class="text-meta-1">*</span>
                        </label>
                        <select name="estado" required
                            class="w-full rounded border border-stroke bg-transparent py-1 px-2 text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                            @foreach ($estados as $key => $label)
                                <option value="{{ $key }}" @selected(old('estado', $editing ? $impresora->estado : 'activa') === $key) class="bg-[#1c2434] text-white">
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[9px] font-bold uppercase text-black dark:text-white mb-0.5">
                            Intervalo Preventivo (Días)
                        </label>
                        <input type="number" name="intervalo_mantenimiento_dias" min="1" max="3650"
                            value="{{ old('intervalo_mantenimiento_dias', $editing ? $impresora->intervalo_mantenimiento_dias : '') }}"
                            placeholder="Ej: 90"
                            class="w-full rounded border border-stroke bg-transparent py-1 px-2.5 text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                    </div>
                </div>
            </div>

            {{-- Plegable 2: Red y Panel Web --}}
            <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="flex items-center justify-between p-3 cursor-pointer border-b border-stroke dark:border-strokedark select-none"
                    @click="openRed = !openRed">
                    <h3 class="text-[11px] font-bold uppercase tracking-wider text-black dark:text-white flex items-center gap-1.5">
                        <i class="fas fa-network-wired text-primary"></i> Red & Panel Web
                    </h3>
                    <i class="fas text-xs text-bodydark2" :class="openRed ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </div>

                <div x-show="openRed" x-cloak x-collapse class="p-3 space-y-2.5">
                    <div>
                        <label class="block text-[9px] font-bold uppercase text-black dark:text-white mb-0.5">
                            IP en red
                        </label>
                        <input type="text" name="ip_red" maxlength="45"
                            value="{{ old('ip_red', $editing ? $impresora->ip_red : '') }}" placeholder="192.168.1.100"
                            class="w-full rounded border border-stroke bg-transparent py-1 px-2.5 font-mono text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                    </div>

                    <div>
                        <label class="block text-[9px] font-bold uppercase text-black dark:text-white mb-0.5">
                            Hostname
                        </label>
                        <input type="text" name="hostname" maxlength="120"
                            value="{{ old('hostname', $editing ? $impresora->hostname : '') }}" placeholder="hp-contab-01"
                            class="w-full rounded border border-stroke bg-transparent py-1 px-2.5 font-mono text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                    </div>

                    <div>
                        <label class="block text-[9px] font-bold uppercase text-black dark:text-white mb-0.5">
                            MAC Address
                        </label>
                        <input type="text" name="mac_address" maxlength="17"
                            value="{{ old('mac_address', $editing ? $impresora->mac_address : '') }}"
                            placeholder="AA:BB:CC:DD:EE:FF" style="text-transform: uppercase;"
                            class="w-full rounded border border-stroke bg-transparent py-1 px-2.5 font-mono text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                    </div>

                    <div class="pt-2 border-t border-dashed border-stroke dark:border-strokedark">
                        <label class="block text-[9px] font-bold uppercase text-black dark:text-white mb-0.5">
                            Panel Web — Usuario
                        </label>
                        <input type="text" name="panel_web_usuario" maxlength="80" autocomplete="off"
                            value="{{ old('panel_web_usuario', $editing ? $impresora->panel_web_usuario : '') }}"
                            class="w-full rounded border border-stroke bg-transparent py-1 px-2.5 text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                    </div>

                    <div x-data="{ visible: false }">
                        <label class="block text-[9px] font-bold uppercase text-black dark:text-white mb-0.5">
                            Panel Web — Contraseña
                        </label>
                        <div class="relative">
                            <input :type="visible ? 'text' : 'password'" name="panel_web_password" maxlength="200"
                                autocomplete="new-password"
                                value="{{ old('panel_web_password', $editing ? $impresora->panel_web_password : '') }}"
                                class="w-full rounded border border-stroke bg-transparent py-1 px-2.5 pr-8 text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                            <button type="button" @click="visible = !visible"
                                class="absolute right-2 top-1/2 -translate-y-1/2 text-bodydark2 hover:text-primary">
                                <i class="fas text-[10px]" :class="visible ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Botón de Acción Principal --}}
            <div class="rounded-sm border border-stroke bg-white p-3 shadow-default dark:border-strokedark dark:bg-boxdark">
                <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-1.5 rounded bg-primary py-2 px-4 text-center text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm cursor-pointer">
                    <i class="fas fa-save text-[10px]"></i>
                    {{ $editing ? 'Guardar Cambios' : 'Crear Impresora' }}
                </button>
            </div>

        </div>
    </div>
</form>

@push('scripts')
    <script>
        function impresoraForm({
            sedesPorEmpresa,
            empresaInicial,
            sedeInicial,
            hasRedData,
            hasEstadoData
        }) {
            return {
                sedesPorEmpresa: sedesPorEmpresa || {},
                empresaSeleccionada: empresaInicial || '',
                sedeSeleccionada: sedeInicial || '',
                openRed: hasRedData || false,
                openEstado: hasEstadoData || false,

                get sedesFiltradas() {
                    return this.sedesPorEmpresa[this.empresaSeleccionada] ?? [];
                },

                init() {
                    let primeraVez = true;
                    this.$watch('empresaSeleccionada', () => {
                        if (primeraVez) {
                            primeraVez = false;
                            return;
                        }
                        this.sedeSeleccionada = '';
                    });
                },
            };
        }
    </script>
@endpush