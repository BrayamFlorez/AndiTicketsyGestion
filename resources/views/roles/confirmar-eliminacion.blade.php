<x-app-layout>
    <x-slot name="title">Confirmar eliminación de rol</x-slot>

    <div class="mx-auto max-w-3xl">

        {{-- ── Cabecera ──────────────────────────────────────────── --}}
        <div class="mb-6">
            <a href="{{ route('roles.index') }}"
               class="inline-flex items-center gap-1 text-xs text-bodydark2 hover:text-primary mb-3">
                <i class="fas fa-arrow-left"></i> Volver a la matriz de roles
            </a>
            <h2 class="text-title-md2 font-bold text-black dark:text-white flex items-center gap-2">
                <i class="fas fa-trash-alt text-meta-1"></i>
                Eliminar rol: <span class="font-mono">{{ $role->name }}</span>
            </h2>
            <p class="text-xs text-bodydark2 mt-1">
                Revisá el impacto antes de continuar. La acción es irreversible.
            </p>
        </div>

        {{-- ── Sin dependencias: eliminar directo ───────────────── --}}
        @if (! $impacto['tiene_dependencias'])

            <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark p-6 mb-4">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-meta-3/10 text-meta-3 shrink-0">
                        <i class="fas fa-check"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-black dark:text-white mb-1">Sin dependencias detectadas</h3>
                        <p class="text-xs text-bodydark2">
                            Este rol no está asignado a ningún usuario, plantilla ni asignación activa.
                            Se puede eliminar de forma segura.
                        </p>
                    </div>
                </div>
            </div>

            <form action="{{ route('roles.destroy', $role->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex justify-end gap-2">
                    <a href="{{ route('roles.index') }}"
                       class="inline-flex items-center gap-1.5 rounded border border-stroke py-2 px-4 text-xs font-bold uppercase tracking-wider text-black dark:text-white hover:bg-gray-2 dark:border-strokedark dark:hover:bg-meta-4 transition">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 rounded bg-meta-1 py-2 px-4 text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition cursor-pointer">
                        <i class="fas fa-trash-alt text-[10px]"></i> Eliminar rol
                    </button>
                </div>
            </form>

        @else

            {{-- ── Con dependencias: análisis de impacto + reemplazo ── --}}

            <div class="rounded-sm border-l-4 border-meta-6 bg-meta-6/10 p-4 mb-6">
                <div class="flex gap-3">
                    <i class="fas fa-exclamation-triangle text-meta-6 mt-0.5"></i>
                    <div class="text-xs">
                        <p class="font-bold text-meta-6 mb-1">Este rol tiene dependencias activas</p>
                        <p class="text-bodydark2">
                            Para eliminarlo, debés elegir un rol al que <b>reasignar sus responsabilidades</b>.
                            Los usuarios cambiarán al rol reemplazo, y las plantillas de flujo que lo usan como actor
                            pasarán a usar el rol nuevo.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Impacto por tipo --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

                <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fas fa-users text-primary"></i>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-bodydark2">Usuarios</span>
                    </div>
                    <div class="text-2xl font-bold text-black dark:text-white">{{ $impacto['usuarios_count'] }}</div>
                    <p class="text-[10px] text-bodydark2 mt-1">con este rol asignado</p>
                </div>

                <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fas fa-project-diagram text-primary"></i>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-bodydark2">Plantillas</span>
                    </div>
                    <div class="text-2xl font-bold text-black dark:text-white">{{ $impacto['plantillas_count'] }}</div>
                    <p class="text-[10px] text-bodydark2 mt-1">de flujo lo usan como actor</p>
                </div>

                <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fas fa-hand-paper text-primary"></i>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-bodydark2">Asignaciones</span>
                    </div>
                    <div class="text-2xl font-bold text-black dark:text-white">{{ $impacto['asignaciones_activas'] }}</div>
                    <p class="text-[10px] text-bodydark2 mt-1">pendientes activas</p>
                </div>

            </div>

            {{-- Detalle expandible --}}
            <div x-data="{ tab: 'usuarios' }" class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark mb-6">
                <div class="flex border-b border-stroke dark:border-strokedark">
                    <button type="button" @click="tab = 'usuarios'"
                            :class="tab === 'usuarios' ? 'border-primary text-primary font-bold' : 'border-transparent text-bodydark2 hover:text-primary'"
                            class="flex-1 py-3 px-4 text-xs uppercase tracking-wider border-b-2 transition">
                        Usuarios ({{ $impacto['usuarios_count'] }})
                    </button>
                    <button type="button" @click="tab = 'plantillas'"
                            :class="tab === 'plantillas' ? 'border-primary text-primary font-bold' : 'border-transparent text-bodydark2 hover:text-primary'"
                            class="flex-1 py-3 px-4 text-xs uppercase tracking-wider border-b-2 transition">
                        Plantillas ({{ $impacto['plantillas_count'] }})
                    </button>
                </div>

                <div x-show="tab === 'usuarios'" class="p-4 space-y-1.5">
                    @forelse ($impacto['usuarios'] as $usuario)
                        <div class="flex items-center gap-2 text-xs">
                            <i class="fas fa-user text-bodydark2 text-[10px]"></i>
                            <span class="text-black dark:text-white font-medium">{{ $usuario->name }}</span>
                            <span class="text-bodydark2">·</span>
                            <span class="text-bodydark2 font-mono">{{ $usuario->email }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-bodydark2 italic">Ningún usuario tiene este rol.</p>
                    @endforelse
                </div>

                <div x-show="tab === 'plantillas'" x-cloak class="p-4 space-y-1.5">
                    @forelse ($impacto['plantillas'] as $plantilla)
                        <div class="flex items-center gap-2 text-xs">
                            <i class="fas fa-project-diagram text-bodydark2 text-[10px]"></i>
                            <span class="text-black dark:text-white font-medium">{{ $plantilla->nombre }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-bodydark2 italic">Ninguna plantilla usa este rol como actor.</p>
                    @endforelse
                </div>
            </div>

            {{-- Formulario de reasignación --}}
            <form action="{{ route('roles.destroy', $role->id) }}" method="POST"
                  onsubmit="return confirm('¿Confirmás que querés reasignar y eliminar el rol? Esta acción es irreversible.')">
                @csrf
                @method('DELETE')

                <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark p-6 mb-4">
                    <label for="rol_reemplazo_id" class="text-xs font-bold uppercase tracking-wider text-black dark:text-white block mb-2">
                        Rol al que reasignar las responsabilidades <span class="text-meta-1">*</span>
                    </label>
                    <p class="text-xs text-bodydark2 mb-3">
                        Los usuarios y actores de plantillas del rol <b class="font-mono text-black dark:text-white">{{ $role->name }}</b>
                        pasarán al rol que elijas acá.
                    </p>

                    {{-- Select Ajustado con Tema Oscuro --}}
                    <div class="relative z-20">
                        <select id="rol_reemplazo_id" name="rol_reemplazo_id" required
                                class="w-full appearance-none rounded border border-stroke bg-transparent py-2 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white cursor-pointer @error('rol_reemplazo_id') border-meta-1 @enderror">
                            <option value="" class="bg-[#1c2434] text-gray-400">— Elegí un rol —</option>
                            @foreach ($rolesAlternativos as $alt)
                                <option value="{{ $alt->id }}" @selected(old('rol_reemplazo_id') == $alt->id) class="bg-[#1c2434] text-white">
                                    {{ $alt->name }}
                                </option>
                            @endforeach
                        </select>
                        <span class="absolute top-1/2 right-3 -translate-y-1/2 z-30 pointer-events-none">
                            <i class="fas fa-chevron-down text-[10px] text-bodydark2"></i>
                        </span>
                    </div>

                    @error('rol_reemplazo_id')
                        <p class="text-xs text-meta-1 font-medium mt-1">{{ $message }}</p>
                    @enderror

                    <p class="text-[10px] text-bodydark2 mt-3 flex items-center gap-1">
                        <i class="fas fa-info-circle text-primary"></i>
                        Los permisos que reciban los usuarios serán los del rol reemplazo, no los del rol eliminado.
                        Si tiene menos permisos, perderán capacidades.
                    </p>
                </div>

                <div class="flex justify-end gap-2">
                    <a href="{{ route('roles.index') }}"
                       class="inline-flex items-center gap-1.5 rounded border border-stroke py-2 px-4 text-xs font-bold uppercase tracking-wider text-black dark:text-white hover:bg-gray-2 dark:border-strokedark dark:hover:bg-meta-4 transition">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 rounded bg-meta-1 py-2 px-4 text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition cursor-pointer">
                        <i class="fas fa-exchange-alt text-[10px]"></i> Reasignar y eliminar
                    </button>
                </div>
            </form>

        @endif

    </div>
</x-app-layout>