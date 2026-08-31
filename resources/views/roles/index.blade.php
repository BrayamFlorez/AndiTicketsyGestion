{{-- resources/views/roles/index.blade.php --}}
<x-app-layout>
    <x-slot name="title">Matriz de Roles</x-slot>

    @if (session('status') === 'rol-reasignado-y-eliminado')
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            class="mb-6 flex w-full border-l-6 border-meta-3 bg-meta-3/10 px-7 py-3 shadow-md dark:bg-meta-3/20 dark:border-meta-3 rounded-sm">
            <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-meta-3 text-white shrink-0">
                <i class="fas fa-check text-sm"></i>
            </div>
            <div class="w-full">
                <h5 class="mb-1 font-bold text-meta-3">Rol reasignado y eliminado</h5>
                <p class="text-xs text-meta-3">Los usuarios y plantillas se movieron al rol reemplazo correctamente.</p>
            </div>
        </div>
    @endif

    {{-- ── Notificaciones / Alertas ──────────────────────────────────────── --}}
    @if (session('status') === 'rol-eliminado')
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
            class="mb-6 flex w-full border-l-6 border-meta-1 bg-meta-1/10 px-7 py-3 shadow-md dark:bg-meta-1/20 dark:border-meta-1 rounded-sm">
            <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-meta-1 text-white shrink-0">
                <i class="fas fa-check text-sm"></i>
            </div>
            <div class="w-full">
                <h5 class="mb-1 font-bold text-meta-1">Rol Eliminado</h5>
                <p class="text-xs text-meta-1">El rol ha sido revocado y eliminado del sistema correctamente.</p>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
            class="mb-6 flex w-full border-l-6 border-meta-6 bg-meta-6/10 px-7 py-3 shadow-md dark:bg-meta-6/20 dark:border-meta-6 rounded-sm">
            <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-meta-6 text-white shrink-0">
                <i class="fas fa-exclamation-triangle text-sm"></i>
            </div>
            <div class="w-full">
                <h5 class="mb-1 font-bold text-meta-6">Atención</h5>
                <p class="text-xs text-meta-6">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    {{-- ── Cabecera y Acción Principal ────────────────────────────────── --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-title-md2 font-bold text-black dark:text-white flex items-center gap-2">
                <i class="fas fa-shield-alt text-primary"></i> Matriz de Roles y Permisos
            </h2>
            <p class="text-xs text-bodydark2 mt-1">
                Configuración y administración de niveles de seguridad del sistema
            </p>
        </div>

        @can('roles.gestionar')
            <a href="{{ route('roles.create') }}"
                class="inline-flex items-center justify-center gap-2 rounded bg-primary py-2 px-4 text-center text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm">
                <i class="fas fa-shield-alt text-[10px]"></i> Definir Nuevo Rol
            </a>
        @endcan
    </div>

    {{-- ── Tarjetas de Roles ───────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 2xl:gap-7.5">
        @foreach ($roles as $role)
            @php
                $badgeRol = match ($role->name) {
                    'admin', 'super-admin' => 'bg-meta-1/10 text-meta-1',
                    'tecnico' => 'bg-meta-6/10 text-meta-6',
                    default => 'bg-primary/10 text-primary',
                };
            @endphp

            <div
                class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark flex flex-col justify-between overflow-hidden">

                {{-- Cabecera de la tarjeta --}}
                <div
                    class="border-b border-stroke py-3 px-5 dark:border-strokedark flex justify-between items-center bg-gray-2 dark:bg-meta-4/40">
                    <h3 class="font-bold text-primary uppercase text-xs tracking-wider flex items-center gap-1.5">
                        <i class="fas fa-user-shield text-[10px]"></i> {{ $role->name }}
                    </h3>

                    <div class="flex items-center gap-1">
                        @can('roles.gestionar')
                            <a href="{{ route('roles.edit', $role) }}"
                                class="flex h-7 w-7 items-center justify-center rounded border border-stroke text-bodydark2 hover:border-primary hover:text-primary dark:border-strokedark transition"
                                title="Editar Configuración">
                                <i class="fas fa-cog text-[10px]"></i>
                            </a>
                        @endcan

                        @can('roles.gestionar')
                            <a href="{{ route('roles.confirmar-eliminacion', $role) }}"
                                class="flex h-7 w-7 items-center justify-center rounded border border-stroke text-meta-1 hover:border-meta-1 dark:border-strokedark transition"
                                title="Eliminar rol">
                                <i class="fas fa-trash-alt text-[10px]"></i>
                            </a>
                        @endcan
                    </div>
                </div>

                {{-- Cuerpo: Lista de permisos --}}
                <div class="p-5 flex-1 space-y-2">
                    <span class="text-[10px] font-bold text-bodydark2 uppercase tracking-wider block">Permisos
                        Habilitados</span>

                    <div class="flex flex-wrap gap-1.5">
                        @forelse($role->permissions as $permission)
                            <span
                                class="inline-flex rounded bg-gray border border-stroke px-2 py-0.5 text-[10px] font-mono font-medium text-black dark:border-strokedark dark:bg-meta-4 dark:text-white">
                                {{ str_replace('.', ':', $permission->name) }}
                            </span>
                        @empty
                            <span class="text-xs text-bodydark2 italic">Sin permisos asignados</span>
                        @endforelse
                    </div>
                </div>

                {{-- Pie: Usuarios vinculados --}}
                <div
                    class="px-5 py-3 border-t border-stroke dark:border-strokedark bg-gray-2 dark:bg-meta-4/20 flex items-center justify-between">
                    <span class="text-[10px] font-bold text-bodydark2 flex items-center gap-1.5">
                        <i class="fas fa-users text-primary"></i>
                        {{ $role->users->count() }} Usuario(s) vinculado(s)
                    </span>
                    <span
                        class="inline-flex rounded-full py-0.5 px-2.5 text-[9px] font-bold uppercase {{ $badgeRol }}">
                        {{ $role->name }}
                    </span>
                </div>

            </div>
        @endforeach
    </div>
</x-app-layout>
