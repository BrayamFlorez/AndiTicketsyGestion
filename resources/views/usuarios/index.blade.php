{{-- resources/views/usuarios/index.blade.php --}}
<x-app-layout>
    <x-slot name="title">Control de Personal</x-slot>

    {{-- ── Cabecera y Acción Principal ────────────────────────────────── --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-title-md2 font-bold text-black dark:text-white flex items-center gap-2">
                <i class="fas fa-users-cog text-primary"></i> Control de Personal
            </h2>
            <p class="text-xs text-bodydark2 mt-1">
                Gestión de acceso y credenciales de {{ config('app.name', env('APP_NAME')) }}
            </p>
        </div>

        @can('usuarios.crear')
            <a href="{{ route('usuarios.create') }}" 
               class="inline-flex items-center justify-center gap-2 rounded bg-primary py-2 px-4 text-center text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm">
                <i class="fas fa-user-plus text-[10px]"></i> Nuevo Usuario
            </a>
        @endcan
    </div>

    {{-- ── Formulario de Filtros (Card TailAdmin) ─────────────────────── --}}
    <div class="rounded-sm border border-stroke bg-white px-5 pt-5 pb-4 shadow-default dark:border-strokedark dark:bg-boxdark mb-6">
        <form method="GET" action="{{ route('usuarios.index') }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                {{-- Búsqueda --}}
                <div class="lg:col-span-2">
                    <label for="q" class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Buscar</label>
                    <input type="text" id="q" name="q" value="{{ $filtros['q'] ?? '' }}"
                           placeholder="Nombre o correo electrónico..."
                           class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                </div>

                {{-- Filtro por Rol --}}
                <div>
                    <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Rol</label>
                    <div class="relative z-20">
                        <select name="rol_id" class="w-full appearance-none rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white cursor-pointer">
                            <option value="" class="bg-[#1c2434] text-gray-400">Todos</option>
                            @foreach ($roles as $rol)
                                <option value="{{ $rol }}" @selected(($filtros['rol_id'] ?? '') == $rol) class="bg-[#1c2434] text-white">
                                    {{ ucfirst($rol) }}
                                </option>
                            @endforeach
                        </select>
                        <span class="absolute top-1/2 right-3 -translate-y-1/2 z-30 pointer-events-none">
                            <i class="fas fa-chevron-down text-[10px] text-bodydark2"></i>
                        </span>
                    </div>
                </div>

                {{-- Filtro por Empresa --}}
                <div>
                    <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Empresa</label>
                    <div class="relative z-20">
                        <select name="empresa_id" class="w-full appearance-none rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white cursor-pointer">
                            <option value="" class="bg-[#1c2434] text-gray-400">Todas</option>
                            @foreach ($empresas as $empresa)
                                <option value="{{ $empresa->id }}" @selected(($filtros['empresa_id'] ?? '') == $empresa->id) class="bg-[#1c2434] text-white">
                                    {{ $empresa->nombre }}
                                </option>
                            @endforeach
                        </select>
                        <span class="absolute top-1/2 right-3 -translate-y-1/2 z-30 pointer-events-none">
                            <i class="fas fa-chevron-down text-[10px] text-bodydark2"></i>
                        </span>
                    </div>
                </div>

            </div>

            <div class="flex items-center justify-end gap-2 mt-4 border-t border-stroke pt-3 dark:border-strokedark">
                @if (!empty($filtros))
                    <a href="{{ route('usuarios.index') }}" 
                       class="inline-flex items-center gap-1.5 rounded border border-stroke py-1.5 px-3 text-xs font-medium text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition">
                        <i class="fas fa-times text-[10px]"></i> Limpiar
                    </a>
                @endif
                <button type="submit" 
                        class="inline-flex items-center gap-1.5 rounded bg-primary py-1.5 px-4 text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm cursor-pointer">
                    <i class="fas fa-filter text-[10px]"></i> Filtrar
                </button>
            </div>
        </form>
    </div>

    {{-- ── Tabla de Usuarios ──────────────────────────────────────────── --}}
    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark mb-6">
        <div class="max-w-full overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-gray-2 text-left dark:bg-meta-4">
                        <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase">Usuario / Identidad</th>
                        <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase">Rol Asignado</th>
                        <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase">Ubicación Base</th>
                        <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase text-center">Cobertura Técnica</th>
                        <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase text-center w-24">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke dark:divide-strokedark">
                    @forelse($usuarios as $usuario)
                        <tr class="hover:bg-gray-2 dark:hover:bg-meta-4/50 transition">

                            {{-- Columna 1: Usuario & Avatar --}}
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-primary/20 text-primary font-bold text-xs shrink-0">
                                        {{ substr($usuario->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <span class="text-xs font-bold text-black dark:text-white block">{{ $usuario->name }}</span>
                                        <span class="text-[10px] text-bodydark2 block font-mono">{{ $usuario->email }}</span>
                                    </div>
                                </div>
                            </td>

                            {{-- Columna 2: Rol Asignado --}}
                            <td class="py-3.5 px-4">
                                @forelse($usuario->roles as $role)
                                    @php
                                        $colorRol = match($role->name) {
                                            'tecnico' => 'bg-meta-6/10 text-meta-6',
                                            'admin','super-admin' => 'bg-meta-1/10 text-meta-1',
                                            default => 'bg-primary/10 text-primary',
                                        };
                                    @endphp
                                    <span class="inline-flex rounded-full py-0.5 px-2.5 text-[10px] font-bold uppercase {{ $colorRol }}">
                                        {{ $role->name }}
                                    </span>
                                @empty
                                    <span class="inline-flex rounded-full bg-gray-2 py-0.5 px-2.5 text-[10px] font-bold uppercase text-bodydark2 dark:bg-meta-4">
                                        Sin rol
                                    </span>
                                @endforelse
                            </td>

                            {{-- Columna 3: Empresa y Sede --}}
                            <td class="py-3.5 px-4">
                                @if ($usuario->empresa)
                                    <span class="text-xs font-semibold text-black dark:text-white block">
                                        <i class="fas fa-building text-primary mr-1 text-[10px]"></i> {{ $usuario->empresa->nombre }}
                                    </span>
                                    <span class="text-[10px] text-bodydark2 block mt-0.5">
                                        <i class="fas fa-map-marker-alt mr-1 text-[9px]"></i> {{ $usuario->sede->nombre ?? 'Sin sede asignada' }}
                                    </span>
                                @else
                                    <span class="text-xs text-bodydark2">—</span>
                                @endif
                            </td>

                            {{-- Columna 4: Cobertura Técnica --}}
                            <td class="py-3.5 px-4 text-center">
                                @if ($usuario->hasRole('tecnico'))
                                    @php
                                        $cobertura = $coberturasPorTecnico[$usuario->id] ?? 0;
                                    @endphp
                                    <span class="inline-flex rounded-full py-0.5 px-2.5 text-[10px] font-bold uppercase {{ $cobertura > 0 ? 'bg-meta-3/10 text-meta-3' : 'bg-meta-1/10 text-meta-1' }}">
                                        {{ $cobertura }} Empresa(s)
                                    </span>
                                @else
                                    <span class="text-xs text-bodydark2">—</span>
                                @endif
                            </td>

                            {{-- Columna 5: Acciones --}}
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    @can('usuarios.editar')
                                        <a href="{{ route('usuarios.edit', $usuario) }}" 
                                           class="flex h-7 w-7 items-center justify-center rounded border border-stroke text-bodydark2 hover:border-primary hover:text-primary dark:border-strokedark transition"
                                           title="Editar">
                                            <i class="fas fa-pen text-[10px]"></i>
                                        </a>
                                    @endcan

                                    @can('usuarios.eliminar')
                                        <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST"
                                              onsubmit="return confirm('¿Eliminar acceso del usuario {{ $usuario->name }}?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="flex h-7 w-7 items-center justify-center rounded border border-stroke text-meta-1 hover:border-meta-1 dark:border-strokedark transition"
                                                    title="Eliminar acceso">
                                                <i class="fas fa-trash-alt text-[10px]"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center">
                                <div class="flex flex-col items-center text-bodydark2">
                                    <i class="fas fa-users text-3xl mb-2"></i>
                                    <p class="text-xs">
                                        @if (!empty($filtros))
                                            No hay usuarios que coincidan con los filtros de búsqueda.
                                        @else
                                            Aún no hay personal registrado en el sistema.
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if ($usuarios->hasPages())
            <div class="border-t border-stroke py-3 px-4 dark:border-strokedark">
                {{ $usuarios->links() }}
            </div>
        @endif
    </div>
</x-app-layout>