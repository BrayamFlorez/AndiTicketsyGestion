<x-app-layout>
    <x-slot name="title">Tipos de Documento</x-slot>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
        <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
    @endpush

    {{-- Cabecera --}}
    <div class="db-header mb-6 flex items-center justify-between flex-wrap gap-3">
        <div>
            <h2 class="text-2xl font-black text-white uppercase tracking-tighter">Tipos de Documento</h2>
            <p class="text-xs text-gray-400 font-medium uppercase tracking-widest">
                Catálogo de documentos que circulan por flujos de trabajo
            </p>
        </div>

        @can('flujos.tipos-documento.gestionar')
            <a href="{{ route('flujos.tipos-documento.create') }}" class="db-btn solid py-2.5 px-4 shadow-lg shadow-[#007acc]/20">
                <i class="fas fa-plus mr-2 text-sm"></i> Nuevo Tipo
            </a>
        @endcan
    </div>

    {{-- Toast de éxito --}}
    @if (session('exito'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-green-500/10 border border-green-500/30 text-green-300 text-sm">
            <i class="fas fa-check-circle mr-2"></i> {{ session('exito') }}
        </div>
    @endif

    {{-- Filtros --}}
    <form method="GET" action="{{ route('flujos.tipos-documento.index') }}" class="db-card" style="margin-bottom: 1.25rem;">
        <div class="form-card-body" style="padding: 1rem 1.25rem;">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="form-group sm:col-span-2">
                    <label class="form-label" for="q">Buscar</label>
                    <input type="text" id="q" name="q" value="{{ $filtros['q'] ?? '' }}"
                        placeholder="Nombre o identificador..." class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Estado</label>
                    <select name="activo" class="form-select">
                        <option value="">Todos</option>
                        <option value="1" @selected(($filtros['activo'] ?? '') === '1')>Activos</option>
                        <option value="0" @selected(($filtros['activo'] ?? '') === '0')>Inactivos</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 mt-3">
                @if (!empty(array_filter($filtros, fn ($v) => $v !== null && $v !== '')))
                    <a href="{{ route('flujos.tipos-documento.index') }}" class="db-btn ghost">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                @endif
                <button type="submit" class="db-btn outline">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
            </div>
        </div>
    </form>

    {{-- Tabla --}}
    <div class="db-card">
        <div class="overflow-x-auto">
            <table class="db-table w-full">
                <thead>
                    <tr>
                        <th class="px-6 py-4">Tipo</th>
                        <th class="px-6 py-4">Plantilla Asociada</th>
                        <th class="px-6 py-4 text-center">Estado</th>
                        <th class="px-6 py-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tipos as $tipo)
                        <tr class="group transition-colors hover:bg-[var(--db-surface-2)]">

                            {{-- Columna 1: Tipo --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-[#007acc]/10 flex items-center justify-center text-[#007acc] shadow-inner">
                                        <i class="fas {{ $tipo->icono ?: 'fa-file-alt' }}"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-white">{{ $tipo->nombre }}</p>
                                        <p class="text-[10px] text-gray-500 font-mono uppercase tracking-wider">{{ $tipo->slug }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Columna 2: Plantilla --}}
                            <td class="px-6 py-4">
                                @if ($tipo->template)
                                    <div class="text-[0.8rem] text-white font-semibold flex items-center gap-1.5">
                                        <i class="fas fa-project-diagram text-gray-500"></i>
                                        {{ $tipo->template->nombre }}
                                    </div>
                                @else
                                    <span class="db-badge abierto" style="font-size: 0.65rem;">Sin plantilla</span>
                                @endif
                            </td>

                            {{-- Columna 3: Estado --}}
                            <td class="px-6 py-4 text-center">
                                <span class="db-badge {{ $tipo->activo ? 'en_progreso' : 'cancelado' }}" style="font-size: 0.65rem;">
                                    {{ $tipo->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>

                            {{-- Columna 4: Acciones --}}
                            <td class="px-6 py-4">
                                <div class="flex justify-center items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                    @can('flujos.tipos-documento.gestionar')
                                        <a href="{{ route('flujos.tipos-documento.edit', $tipo) }}" class="db-btn ghost" title="Editar">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <form action="{{ route('flujos.tipos-documento.destroy', $tipo) }}" method="POST" class="inline"
                                            onsubmit="return confirm('¿Eliminar el tipo {{ $tipo->nombre }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="db-btn danger-outline" title="Eliminar">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="db-empty py-8 text-center">
                                    <i class="fas fa-folder-open text-4xl mb-3 text-gray-700"></i>
                                    <p class="text-gray-400">
                                        @if (!empty(array_filter($filtros, fn ($v) => $v !== null && $v !== '')))
                                            No hay tipos que coincidan con los filtros.
                                        @else
                                            Aún no hay tipos de documento registrados.
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($tipos->hasPages())
            <div class="px-4 py-3 border-t border-gray-800">
                {{ $tipos->links() }}
            </div>
        @endif
    </div>
</x-app-layout>