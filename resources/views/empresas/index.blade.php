{{-- resources/views/empresas/index.blade.php --}}
<x-app-layout>
    <x-slot name="title">Empresas</x-slot>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
        <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
        <style>
            /* Fondo oscuro con desenfoque para los modales */
            .modal-backdrop {
                background-color: rgba(0, 0, 0, 0.6);
                backdrop-filter: blur(4px);
            }
        </style>
    @endpush

    {{-- Estado global de Alpine para el modal de Creación --}}
    <div x-data="{ createOpen: {{ $errors->any() && !old('_method') ? 'true' : 'false' }} }">

        {{-- ── Cabecera ────────────────────────────────────────────────────── --}}
        <div class="db-header mb-6 flex items-center justify-between flex-wrap gap-3">
            <div>
                <h4>Empresas</h4>
                <p>{{ $totales['activas'] }} activas de {{ $totales['total'] }} registradas
                    @if ($totales['sinSedes'] > 0)
                        · <span style="color: #fbbf24;">{{ $totales['sinSedes'] }} sin sedes</span>
                    @endif
                </p>
            </div>

            @can('empresas.gestionar')
                <button @click="createOpen = true" class="db-btn solid">
                    <i class="fas fa-plus"></i> Nueva empresa
                </button>
            @endcan
        </div>

        {{-- ── Barra de filtros ───────────────────────────────────────────── --}}
        <form method="GET" action="{{ route('empresas.index') }}" class="db-card" style="margin-bottom: 1.25rem;">
            <div class="form-card-body" style="padding: 1rem 1.25rem;">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="form-group sm:col-span-2">
                        <label class="form-label" for="q">Buscar</label>
                        <input type="text" id="q" name="q" value="{{ $filtros['q'] ?? '' }}"
                            placeholder="Nombre, NIT o email..." class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Estado</label>
                        <label class="flex items-center gap-2 mt-2" style="color: var(--db-text); font-size: 0.85rem;">
                            <input type="checkbox" name="solo_activas" value="1" @checked(!empty($filtros['solo_activas']))>
                            <span>Solo activas</span>
                        </label>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 mt-3">
                    @if (!empty($filtros))
                        <a href="{{ route('empresas.index') }}" class="db-btn ghost">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                    @endif
                    <button type="submit" class="db-btn outline">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                </div>
            </div>
        </form>

        {{-- ── Tabla de empresas ──────────────────────────────────────────── --}}
        <div class="db-card">
            <div class="overflow-x-auto">
                <table class="db-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;"></th> {{-- Espacio para la flecha de expandir --}}
                            <th>Empresa</th>
                            <th>NIT</th>
                            <th>Contacto</th>
                            <th style="text-align: center;">Sedes</th>
                            <th style="text-align: center;">Usuarios</th>
                            <th style="text-align: center;">Tickets activos</th>
                            <th>Estado</th>
                            @can('empresas.gestionar')
                                <th></th>
                            @endcan
                        </tr>
                    </thead>
                    
                    @forelse ($empresas as $empresa)
                        {{-- ATENCIÓN AQUÍ: Usamos un tbody por empresa para compartir el estado Alpine --}}
                        <tbody x-data="{ 
                            expanded: false,
                            editOpen: {{ $errors->any() && old('empresa_id') == $empresa->id ? 'true' : 'false' }}, 
                            createOpen: false 
                        }">
                            {{-- FILA PRINCIPAL DE LA EMPRESA --}}
                            <tr class="transition-colors hover:bg-[var(--db-surface-2)]">
                                <td class="text-center">
                                    @if ($empresa->sedes->count() > 0)
                                        <button @click="expanded = !expanded" class="text-gray-400 hover:text-white transition-transform duration-200" :class="expanded ? 'rotate-90' : ''">
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="db-avatar"><i class="fas fa-building" style="font-size: 0.7rem;"></i></div>
                                        <div>
                                            <div style="color: var(--db-text-bright); font-weight: 600; font-size: 0.85rem;">
                                                {{ $empresa->nombre }}
                                            </div>
                                            @if ($empresa->email)
                                                <div class="db-sub">{{ $empresa->email }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="db-sub" style="color: var(--db-text);">{{ $empresa->nit ?? '—' }}</td>
                                <td class="db-sub" style="color: var(--db-text);">{{ $empresa->telefono ?? '—' }}</td>
                                <td style="text-align: center; color: var(--db-text); font-weight: 600;">{{ $empresa->sedes_count }}</td>
                                <td style="text-align: center; color: var(--db-text); font-weight: 600;">{{ $empresa->usuarios_count }}</td>
                                <td style="text-align: center;">
                                    @if ($empresa->tickets_activos_count > 0)
                                        <span class="db-badge en_progreso">{{ $empresa->tickets_activos_count }}</span>
                                    @else
                                        <span class="db-sub">0</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($empresa->activo)
                                        <span class="db-badge resuelto">Activa</span>
                                    @else
                                        <span class="db-badge cerrado">Inactiva</span>
                                    @endif
                                </td>
                                
                                @can('empresas.gestionar')
                                    <td>
                                        <div class="flex items-center gap-1 justify-end">
                                            {{-- Botón VER EMPRESA --}}
                                            <a href="{{ route('empresas.show', $empresa) }}" class="db-btn outline" title="Ver perfil de empresa">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            {{-- Botón NUEVA SEDE --}}
                                            <button @click="createOpen = true" class="db-btn ghost" title="Agregar Sede" style="color: #22d3ee;">
                                                <i class="fas fa-map-pin"></i>
                                            </button>

                                            {{-- Botón EDITAR EMPRESA --}}
                                            <button @click="editOpen = true" class="db-btn ghost" title="Editar">
                                                <i class="fas fa-pen"></i>
                                            </button>

                                            {{-- Botón ELIMINAR EMPRESA --}}
                                            <button type="button" class="db-btn danger-outline" title="Eliminar"
                                                    onclick="confirmDelete({{ $empresa->id }}, '{{ $empresa->nombre }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>

                                            <form id="delete-form-{{ $empresa->id }}" action="{{ route('empresas.destroy', $empresa) }}" method="POST" class="hidden">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                        
                                        @include('empresas.partials.edit')
                                        @include('sedes.partials.create', ['empresaDefecto' => $empresa])
                                    </td>
                                @endcan
                            </tr>

                            {{-- FILA EXPANDIBLE DE SEDES --}}
                            @if ($empresa->sedes->count() > 0)
                                <tr x-show="expanded" x-cloak style="background-color: var(--db-bg);">
                                    <td colspan="9" class="p-0 border-0">
                                        <div x-show="expanded" x-collapse>
                                            <div class="p-4 border-l-4 border-[#007acc] my-2 mx-4 bg-[#1a1a1b] rounded-r-lg shadow-inner">
                                                <h5 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Sedes registradas</h5>
                                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                                    @foreach ($empresa->sedes as $sede)
                                                        <div class="bg-[var(--db-surface)] p-3 rounded-lg border border-[var(--db-border)] flex flex-col justify-between">
                                                            <div class="flex justify-between items-start mb-2">
                                                                <div>
                                                                    <div class="font-bold text-sm text-gray-200">
                                                                        <i class="fas fa-map-marker-alt text-[#007acc] mr-1"></i> {{ $sede->nombre }}
                                                                    </div>
                                                                    <div class="text-xs text-gray-500 mt-1">{{ $sede->ciudad ?? 'Sin ciudad' }}</div>
                                                                </div>
                                                                <div>
                                                                    @if ($sede->activo)
                                                                        <span class="db-badge resuelto" style="font-size: 0.55rem; padding: 0.2rem 0.5rem;">Activa</span>
                                                                    @else
                                                                        <span class="db-badge cerrado" style="font-size: 0.55rem; padding: 0.2rem 0.5rem;">Inactiva</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            @if ($sede->direccion)
                                                                <div class="text-xs text-gray-400 truncate"><i class="fas fa-location-arrow mr-1"></i> {{ $sede->direccion }}</div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    @empty
                        <tbody>
                            <tr>
                                <td colspan="9">
                                    <div class="db-empty">
                                        <i class="fas fa-building"></i>
                                        <p>
                                            @if (!empty($filtros))
                                                No hay empresas que coincidan con los filtros aplicados.
                                            @else
                                                Aún no hay empresas registradas.
                                            @endif
                                        </p>
                                        @if (!empty($filtros))
                                            <a href="{{ route('empresas.index') }}" class="db-btn outline">Limpiar filtros</a>
                                        @elseif(auth()->user()->can('empresas.gestionar'))
                                            <button @click="createOpen = true" class="db-btn solid">
                                                <i class="fas fa-plus"></i> Registrar primera empresa
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    @endforelse
                </table>

        {{-- Modal global de Creación --}}
        @include('empresas.partials.create')

    </div>

    @push('scripts')
        <script>
            // Función JavaScript para el SweetAlert de confirmación de eliminación
            function confirmDelete(id, nombre) {
                Swal.fire({
                    title: '¿Eliminar empresa?',
                    text: `Estás a punto de eliminar "${nombre}". Esta acción es irreversible.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#2d2d2d',
                    confirmButtonText: '<i class="fas fa-trash"></i> Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    background: '#1e1e1e', // Tema oscuro
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
