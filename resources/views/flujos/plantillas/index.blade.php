<x-app-layout>
    <x-slot name="title">Plantillas de Flujo</x-slot>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
        <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
    @endpush

    <div class="db-header mb-6 flex items-center justify-between flex-wrap gap-3">
        <div>
            <h2 class="text-2xl font-black text-white uppercase tracking-tighter">Plantillas de Flujo</h2>
            <p class="text-xs text-gray-400 font-medium uppercase tracking-widest">
                Diseño de procesos de aprobación reutilizables
            </p>
        </div>
        <a href="{{ route('flujos.plantillas.create') }}" class="db-btn solid py-2.5 px-4 shadow-lg shadow-[#007acc]/20">
            <i class="fas fa-plus mr-2 text-sm"></i> Nueva Plantilla
        </a>
    </div>

    @if (session('exito'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-green-500/10 border border-green-500/30 text-green-300 text-sm">
            <i class="fas fa-check-circle mr-2"></i> {{ session('exito') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-red-500/10 border border-red-500/30 text-red-300 text-sm">
            <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
        </div>
    @endif

    <form method="GET" action="{{ route('flujos.plantillas.index') }}" class="db-card" style="margin-bottom: 1.25rem;">
        <div class="form-card-body" style="padding: 1rem 1.25rem;">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="form-group sm:col-span-2">
                    <label class="form-label" for="q">Buscar</label>
                    <input type="text" id="q" name="q" value="{{ $filtros['q'] ?? '' }}"
                        placeholder="Nombre o descripción..." class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Estado</label>
                    <select name="activo" class="form-select">
                        <option value="">Todos</option>
                        <option value="1" @selected(($filtros['activo'] ?? '') === '1')>Activas</option>
                        <option value="0" @selected(($filtros['activo'] ?? '') === '0')>Inactivas</option>
                    </select>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 mt-3">
                @if (!empty(array_filter($filtros, fn ($v) => $v !== null && $v !== '')))
                    <a href="{{ route('flujos.plantillas.index') }}" class="db-btn ghost">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                @endif
                <button type="submit" class="db-btn outline">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
            </div>
        </div>
    </form>

    <div class="db-card">
        <div class="overflow-x-auto">
            <table class="db-table w-full">
                <thead>
                    <tr>
                        <th class="px-6 py-4">Plantilla</th>
                        <th class="px-6 py-4 text-center">Etapas</th>
                        <th class="px-6 py-4 text-center">Tipos Doc.</th>
                        <th class="px-6 py-4 text-center">Instancias</th>
                        <th class="px-6 py-4 text-center">Estado</th>
                        <th class="px-6 py-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($plantillas as $plantilla)
                        <tr class="group transition-colors hover:bg-[var(--db-surface-2)]">
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-white">{{ $plantilla->nombre }}</p>
                                @if ($plantilla->descripcion)
                                    <p class="text-xs text-gray-500 mt-0.5 line-clamp-1">{{ $plantilla->descripcion }}</p>
                                @endif
                                <p class="text-[10px] text-gray-600 mt-1 uppercase tracking-wider">
                                    v{{ $plantilla->version }} · creada {{ $plantilla->created_at?->diffForHumans() }}
                                </p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="db-badge asignado" style="font-size: 0.65rem;">
                                    {{ $plantilla->stages_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="db-badge abierto" style="font-size: 0.65rem;">
                                    {{ $plantilla->document_types_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="db-badge {{ $plantilla->instances_count > 0 ? 'en_progreso' : 'abierto' }}" style="font-size: 0.65rem;">
                                    {{ $plantilla->instances_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="db-badge {{ $plantilla->activo ? 'en_progreso' : 'cancelado' }}" style="font-size: 0.65rem;">
                                    {{ $plantilla->activo ? 'Activa' : 'Inactiva' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                    <a href="{{ route('flujos.plantillas.edit', $plantilla) }}" class="db-btn ghost" title="Editar">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form action="{{ route('flujos.plantillas.toggle-activo', $plantilla) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="db-btn ghost" title="Activar/Desactivar">
                                            <i class="fas {{ $plantilla->activo ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                        </button>
                                    </form>
                                    @if ($plantilla->instances_count === 0)
                                        <form action="{{ route('flujos.plantillas.destroy', $plantilla) }}" method="POST" class="inline"
                                            onsubmit="return confirm('¿Eliminar la plantilla {{ $plantilla->nombre }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="db-btn danger-outline" title="Eliminar">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="db-empty py-8 text-center">
                                    <i class="fas fa-project-diagram text-4xl mb-3 text-gray-700"></i>
                                    <p class="text-gray-400">Aún no hay plantillas de flujo configuradas.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($plantillas->hasPages())
            <div class="px-4 py-3 border-t border-gray-800">
                {{ $plantillas->links() }}
            </div>
        @endif
    </div>
</x-app-layout>