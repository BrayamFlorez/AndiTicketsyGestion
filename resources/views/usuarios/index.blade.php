<x-app-layout>
    <x-slot name="title">Control de Personal</x-slot>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
        <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
    @endpush

    {{-- ── Cabecera ────────────────────────────────────────────────────── --}}
    <div class="db-header mb-6 flex items-center justify-between flex-wrap gap-3">
        <div>
            <h2 class="text-2xl font-black text-white uppercase tracking-tighter">Control de Personal</h2>
            <p class="text-xs text-gray-400 font-medium uppercase tracking-widest">
                Gestión de acceso a {{ config('app.name', env('APP_NAME')) }}
            </p>
        </div>

        @can('usuarios.crear')
            <a href="{{ route('usuarios.create') }}" class="db-btn solid py-2.5 px-4 shadow-lg shadow-[#007acc]/20">
                <i class="fas fa-user-plus mr-2 text-sm"></i> Nuevo Usuario
            </a>
        @endcan
    </div>

    {{-- ── Barra de filtros ───────────────────────────────────────────── --}}
    <form method="GET" action="{{ route('usuarios.index') }}" class="db-card" style="margin-bottom: 1.25rem;">
        <div class="form-card-body" style="padding: 1rem 1.25rem;">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">

                {{-- Búsqueda --}}
                <div class="form-group lg:col-span-2">
                    <label class="form-label" for="q">Buscar</label>
                    <input type="text" id="q" name="q" value="{{ $filtros['q'] ?? '' }}"
                        placeholder="Nombre o correo..." class="form-input">
                </div>

                {{-- Filtro por Rol --}}
                <div class="form-group">
                    <label class="form-label">Rol</label>
                    <select name="rol_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach ($roles as $rol)
                            {{-- Como $rol es un texto, lo usamos directamente en el value y lo imprimimos --}}
                            <option value="{{ $rol }}" @selected(($filtros['rol_id'] ?? '') == $rol)>
                                {{ ucfirst($rol) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filtro por Empresa --}}
                <div class="form-group">
                    <label class="form-label">Empresa</label>
                    <select name="empresa_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach ($empresas as $empresa)
                            <option value="{{ $empresa->id }}" @selected(($filtros['empresa_id'] ?? '') == $empresa->id)>
                                {{ $empresa->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 mt-3">
                @if (!empty($filtros))
                    <a href="{{ route('usuarios.index') }}" class="db-btn ghost">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                @endif
                <button type="submit" class="db-btn outline">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
            </div>
        </div>
    </form>

    {{-- ── Tabla de Usuarios ──────────────────────────────────────────── --}}
    <div class="db-card">
        <div class="overflow-x-auto">
            <table class="db-table w-full">
                <thead>
                    <tr>
                        <th class="px-6 py-4">Usuario / Identidad</th>
                        <th class="px-6 py-4">Rol Asignado</th>
                        <th class="px-6 py-4">Ubicación Base</th>
                        <th class="px-6 py-4 text-center">Cobertura Técnica</th>
                        <th class="px-6 py-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $usuario)
                        <tr class="group transition-colors hover:bg-[var(--db-surface-2)]">

                            {{-- Columna 1: Usuario --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="db-avatar text-lg w-10 h-10 shadow-inner">
                                        {{ substr($usuario->name, 0, 1) }}
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-bold text-white">{{ $usuario->name }}</p>
                                        <p class="text-xs text-gray-400">{{ $usuario->email }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Columna 2: Rol --}}
                            <td class="px-6 py-4">
                                @forelse($usuario->roles as $role)
                                    <span
                                        class="db-badge {{ $role->name === 'tecnico' ? 'asignado' : ($role->name === 'admin' ? 'critica' : 'abierto') }}"
                                        style="font-size: 0.65rem;">
                                        {{ $role->name }}
                                    </span>
                                @empty
                                    <span class="db-badge abierto" style="font-size: 0.65rem;">Sin rol</span>
                                @endforelse
                            </td>

                            {{-- Columna 3: Empresa y Sede --}}
                            <td class="px-6 py-4">
                                @if ($usuario->empresa)
                                    <div class="text-[0.8rem] text-white font-semibold flex items-center gap-1.5">
                                        <i class="fas fa-building text-gray-500"></i> {{ $usuario->empresa->nombre }}
                                    </div>
                                    <div class="db-sub mt-1 flex items-center gap-1.5">
                                        <i class="fas fa-map-marker-alt text-gray-600"></i>
                                        {{ $usuario->sede->nombre ?? 'Sin sede asignada' }}
                                    </div>
                                @else
                                    <span class="db-sub">—</span>
                                @endif
                            </td>

                            {{-- Columna 4: Cobertura (Solo aplica a técnicos) --}}
                            <td class="px-6 py-4 text-center">
                                @if ($usuario->hasRole('tecnico'))
                                    @php
                                        $cobertura = $coberturasPorTecnico[$usuario->id] ?? 0;
                                    @endphp
                                    <span class="db-badge {{ $cobertura > 0 ? 'en_progreso' : 'cancelado' }}"
                                        style="font-size: 0.65rem;">
                                        {{ $cobertura }} Empresa(s)
                                    </span>
                                @else
                                    <span class="db-sub">—</span>
                                @endif
                            </td>

                            {{-- Columna 5: Acciones --}}
                            <td class="px-6 py-4">
                                <div
                                    class="flex justify-center items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                    @can('usuarios.editar')
                                        <a href="{{ route('usuarios.edit', $usuario) }}" class="db-btn ghost"
                                            title="Editar">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                    @endcan

                                    @can('usuarios.eliminar')
                                        <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST"
                                            class="inline"
                                            onsubmit="return confirm('¿Eliminar acceso del usuario {{ $usuario->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="db-btn danger-outline" title="Eliminar acceso">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="db-empty py-8 text-center">
                                    <i class="fas fa-users text-4xl mb-3 text-gray-700"></i>
                                    <p class="text-gray-400">
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
            <div class="px-4 py-3 border-t border-gray-800">
                {{ $usuarios->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
