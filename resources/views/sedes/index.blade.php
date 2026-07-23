{{-- resources/views/sedes/index.blade.php --}}
<x-app-layout>
    <x-slot name="title">Sedes</x-slot>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
        <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
        <style>
            .modal-backdrop {
                background-color: rgba(0, 0, 0, 0.6);
                backdrop-filter: blur(4px);
            }
        </style>
    @endpush

    <div x-data="{ createOpen: {{ $errors->any() && !old('_method') ? 'true' : 'false' }} }">
        
        {{-- ── Cabecera ────────────────────────────────────────────────────── --}}
        <div class="db-header mb-6 flex items-center justify-between flex-wrap gap-3">
            <div>
                <h4>Sedes</h4>
                <p>{{ $totales['activas'] }} activas de {{ $totales['total'] }} registradas
                    @if ($totales['sinUsuarios'] > 0)
                        · <span style="color: #fbbf24;">{{ $totales['sinUsuarios'] }} sin usuarios asignados</span>
                    @endif
                </p>
            </div>

            @can('sedes.gestionar')
                <button @click="createOpen = true" class="db-btn solid">
                    <i class="fas fa-plus"></i> Nueva sede
                </button>
            @endcan
        </div>

        {{-- ── Barra de filtros ───────────────────────────────────────────── --}}
        <form method="GET" action="{{ route('sedes.index') }}" class="db-card" style="margin-bottom: 1.25rem;">
            <div class="form-card-body" style="padding: 1rem 1.25rem;">
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                    
                    <div class="form-group sm:col-span-2">
                        <label class="form-label" for="q">Buscar</label>
                        <input type="text" id="q" name="q" value="{{ $filtros['q'] ?? '' }}"
                               placeholder="Nombre, ciudad o dirección..."
                               class="form-input">
                    </div>

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

                    <div class="form-group">
                        <label class="form-label">Estado</label>
                        <label class="flex items-center gap-2 mt-2" style="color: var(--db-text); font-size: 0.85rem;">
                            <input type="checkbox" name="solo_activas" value="1"
                                   @checked(!empty($filtros['solo_activas']))>
                            <span>Solo activas</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 mt-3">
                    @if (!empty($filtros))
                        <a href="{{ route('sedes.index') }}" class="db-btn ghost">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                    @endif
                    <button type="submit" class="db-btn outline">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                </div>
            </div>
        </form>

        {{-- ── Tabla de Sedes ─────────────────────────────────────────────── --}}
        <div class="db-card">
            <div class="overflow-x-auto">
                <table class="db-table">
                    <thead>
                        <tr>
                            <th>Sede</th>
                            <th>Empresa</th>
                            <th>Ubicación</th>
                            <th style="text-align: center;">Usuarios</th>
                            <th style="text-align: center;">Tickets activos</th>
                            <th>Estado</th>
                            @can('sedes.gestionar')
                                <th></th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sedes as $sede)
                            <tr x-data="{ editOpen: {{ $errors->any() && old('sede_id') == $sede->id ? 'true' : 'false' }} }">
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="db-avatar"><i class="fas fa-map-marker-alt" style="font-size: 0.7rem;"></i></div>
                                        <div>
                                            <div style="color: var(--db-text-bright); font-weight: 600; font-size: 0.85rem;">
                                                {{ $sede->nombre }}
                                            </div>
                                            @if ($sede->telefono)
                                                <div class="db-sub"><i class="fas fa-phone-alt"></i> {{ $sede->telefono }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                
                                <td>
                                    <div style="color: var(--db-text-bright); font-weight: 600; font-size: 0.8rem;">
                                        {{ $sede->empresa->nombre ?? '—' }}
                                    </div>
                                </td>
                                
                                <td>
                                    <div style="color: var(--db-text); font-size: 0.8rem;">{{ $sede->ciudad ?? '—' }}</div>
                                    <div class="db-sub">{{ $sede->direccion ?? '' }}</div>
                                </td>
                                
                                <td style="text-align: center; color: var(--db-text); font-weight: 600;">{{ $sede->usuarios_count }}</td>
                                
                                <td style="text-align: center;">
                                    @if ($sede->tickets_activos_count > 0)
                                        <span class="db-badge en_progreso">{{ $sede->tickets_activos_count }}</span>
                                    @else
                                        <span class="db-sub">0</span>
                                    @endif
                                </td>
                                
                                <td>
                                    @if ($sede->activo)
                                        <span class="db-badge resuelto">Activa</span>
                                    @else
                                        <span class="db-badge cerrado">Inactiva</span>
                                    @endif
                                </td>
                                
                                @can('sedes.gestionar')
                                    <td>
                                        <div class="flex items-center gap-1 justify-end">
                                            <button @click="editOpen = true" class="db-btn ghost" title="Editar">
                                                <i class="fas fa-pen"></i>
                                            </button>

                                            <button type="button" class="db-btn danger-outline" title="Eliminar"
                                                    onclick="confirmDelete({{ $sede->id }}, '{{ $sede->nombre }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>

                                            <form id="delete-form-{{ $sede->id }}" action="{{ route('sedes.destroy', $sede) }}" method="POST" class="hidden">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                        
                                        @include('sedes.partials.edit')
                                    </td>
                                @endcan
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="db-empty">
                                        <i class="fas fa-map-marked-alt"></i>
                                        <p>
                                            @if (!empty($filtros))
                                                No hay sedes que coincidan con los filtros aplicados.
                                            @else
                                                Aún no hay sedes registradas.
                                            @endif
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($sedes->hasPages())
                <div class="px-4 py-3 border-t" style="border-color: var(--db-border)">
                    {{ $sedes->onEachSide(1)->links() }}
                </div>
            @endif
        </div>

        {{-- Modal global de Creación --}}
        @include('sedes.partials.create')

    </div>

    @push('scripts')
        <script>
            function confirmDelete(id, nombre) {
                Swal.fire({
                    title: '¿Eliminar sede?',
                    text: `Estás a punto de eliminar "${nombre}". Esta acción es irreversible.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#2d2d2d',
                    confirmButtonText: '<i class="fas fa-trash"></i> Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    background: '#1e1e1e',
                    color: '#fff'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('delete-form-' + id).submit();
                    }
                })
            }
        </script>
    @endpush
</x-app-layout>