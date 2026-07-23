<x-app-layout>
    <x-slot name="title">Detalle de Equipo: {{ $equipo->nombre }}</x-slot>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @endpush

    {{-- ── Cabecera ────────────────────────────────────────────────────── --}}
    <div class="db-header mb-5 flex items-center justify-between flex-wrap gap-3">
        <div>
            <div class="flex items-center gap-2 mb-2" style="font-size: 0.85rem;">
                <a href="{{ route('equipos.index') }}" style="color: var(--db-text-muted); text-decoration: none;">Equipos</a>
                <i class="fas fa-chevron-right" style="font-size: 0.6rem; color: var(--db-text-muted);"></i>
                <span style="color: var(--db-text-bright); font-weight: 500;">{{ $equipo->nombre }}</span>
            </div>
            <div class="flex items-center gap-3">
                <h4 class="mb-0">{{ $equipo->nombre }}</h4>
                <span class="db-badge {{ $equipo->color_estado }}">{{ $equipo->label_estado }}</span>
            </div>
            <p class="mt-1">Registrado el {{ $equipo->created_at->format('d/m/Y') }} por {{ $equipo->creador->name ?? 'Sistema' }}</p>
        </div>
        
        <div class="flex items-center gap-2">
            @can('mpreventivo.crear')
                <a href="{{ route('mpreventivo.create', $equipo) }}" class="db-btn solid" style="background-color: #198754; border-color: #198754; color: white;">
                    <i class="fas fa-tools"></i> Programar Mantenimiento
                </a>
            @endcan
            
            @can('equipos.editar')
                <a href="{{ route('equipos.edit', $equipo) }}" class="db-btn outline">
                    <i class="fas fa-edit"></i> Editar Equipo
                </a>
            @endcan
        </div>
    </div>

    @if (session('success'))
        <div class="form-alert success mb-4"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif

    {{-- ── Detalles del Equipo ──────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-5">
        
        {{-- Bloque 1: Identificación y Ubicación --}}
        <div class="db-card lg:col-span-1 p-4">
            <h5 style="color: var(--db-text-bright); font-size: 1rem; font-weight: 600; border-bottom: 1px solid var(--db-border); padding-bottom: 0.75rem; margin-bottom: 1rem;">
                <i class="fas fa-info-circle me-2"></i> Información General
            </h5>
            
            <div class="flex flex-col gap-3">
                <div>
                    <span class="db-sub text-uppercase tracking-wider" style="font-size: 0.7rem;">Categoría</span>
                    <div style="font-weight: 500; color: var(--db-text);">{{ $equipo->label_categoria }}</div>
                </div>
                <div>
                    <span class="db-sub text-uppercase tracking-wider" style="font-size: 0.7rem;">Marca y Modelo</span>
                    <div style="font-weight: 500; color: var(--db-text);">{{ $equipo->marca ?? 'N/A' }} {{ $equipo->modelo ? ' - ' . $equipo->modelo : '' }}</div>
                </div>
                <div>
                    <span class="db-sub text-uppercase tracking-wider" style="font-size: 0.7rem;">Número de Serie</span>
                    <div style="font-family: monospace; font-size: 0.95rem; font-weight: 600; color: var(--db-text-bright);">{{ $equipo->numero_serie ?? 'Sin registro' }}</div>
                </div>
                <div class="mt-2 pt-3" style="border-top: 1px dashed var(--db-border);">
                    <span class="db-sub text-uppercase tracking-wider" style="font-size: 0.7rem;">Ubicación</span>
                    <div style="font-weight: 500; color: var(--db-text);">
                        <i class="fas fa-building me-1 text-muted"></i> {{ $equipo->empresa->nombre }} <br>
                        @if($equipo->sede) <i class="fas fa-map-marker-alt me-1 text-muted ms-2"></i> {{ $equipo->sede->nombre }} <br> @endif
                        @if($equipo->ubicacion) <span class="ms-2 text-muted text-sm"><i class="fas fa-door-open me-1"></i> {{ $equipo->ubicacion }}</span> @endif
                    </div>
                </div>
                <div>
                    <span class="db-sub text-uppercase tracking-wider" style="font-size: 0.7rem;">Responsable (Usuario)</span>
                    <div style="font-weight: 500; color: var(--db-text);">
                        @if($equipo->responsable)
                            <i class="fas fa-user me-1 text-muted"></i> {{ $equipo->responsable->name }}
                        @else
                            <span class="text-muted italic">Sin asignar</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Bloque 2: Especificaciones Técnicas --}}
        <div class="db-card lg:col-span-2 p-4">
            <h5 style="color: var(--db-text-bright); font-size: 1rem; font-weight: 600; border-bottom: 1px solid var(--db-border); padding-bottom: 0.75rem; margin-bottom: 1rem;">
                <i class="fas fa-microchip me-2"></i> Especificaciones Técnicas
            </h5>
            
            @if(!empty($equipo->especificaciones))
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($equipo->schema_especificaciones as $key => $schema)
                        @if(isset($equipo->especificaciones[$key]) && $equipo->especificaciones[$key] !== null && $equipo->especificaciones[$key] !== '')
                            <div style="background-color: var(--db-bg); padding: 0.75rem 1rem; border-radius: 0.5rem; border: 1px solid var(--db-border);">
                                <span class="db-sub text-uppercase tracking-wider block mb-1" style="font-size: 0.7rem;">{{ $schema['label'] }}</span>
                                <div style="font-weight: 600; color: var(--db-text-bright);">{{ $equipo->especificaciones[$key] }}</div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-box-open text-3xl mb-2 opacity-50"></i>
                    <p>No hay especificaciones técnicas registradas para este equipo.</p>
                </div>
            @endif

            @if($equipo->notas)
                <div class="mt-4 pt-3" style="border-top: 1px dashed var(--db-border);">
                    <span class="db-sub text-uppercase tracking-wider" style="font-size: 0.7rem;">Notas Adicionales</span>
                    <p style="color: var(--db-text); font-size: 0.9rem; margin-top: 0.5rem; white-space: pre-line;">{{ $equipo->notas }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ── Historial de Mantenimientos ──────────────────────────────────── --}}
    <div class="db-card">
        <div class="p-4 border-b" style="border-color: var(--db-border);">
            <h5 style="color: var(--db-text-bright); font-size: 1rem; font-weight: 600; margin: 0;">
                <i class="fas fa-history me-2"></i> Historial de Mantenimientos Preventivos
            </h5>
        </div>
        
        <div class="overflow-x-auto">
            <table class="db-table w-full">
                <thead>
                    <tr>
                        <th>ID / Fecha</th>
                        <th>Técnico</th>
                        <th>Estado</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($equipo->mantenimientosPreventivos as $maint)
                        <tr>
                            <td>
                                <div style="color: var(--db-text-bright); font-weight: 600;">#{{ $maint->id }}</div>
                                <div class="db-sub">
                                    {{ $maint->fecha_inicio ? $maint->fecha_inicio->format('d/m/Y h:i A') : 'Sin iniciar' }}
                                </div>
                            </td>
                            <td>
                                @if($maint->tecnico)
                                    <div class="flex items-center gap-2">
                                        <div class="db-avatar">{{ substr($maint->tecnico->name, 0, 1) }}</div>
                                        <span class="db-sub" style="color: var(--db-text)">{{ $maint->tecnico->name }}</span>
                                    </div>
                                @else
                                    <span class="db-sub text-muted">No asignado</span>
                                @endif
                            </td>
                            <td>
                                <span class="db-badge 
                                    @if($maint->estado == 'completado') resuelto 
                                    @elseif($maint->estado == 'en_progreso') en_progreso 
                                    @else cerrado @endif">
                                    {{ ucfirst($maint->estado) }}
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center justify-end gap-2">
                                    @can('mpreventivo.ver')
                                        <a href="{{ route('mpreventivo.show', $maint) }}" class="db-btn outline" title="Ver detalle">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endcan
                                    @can('mpreventivo.editar')
                                        <a href="{{ route('mpreventivo.edit', $maint) }}" class="db-btn outline" title="Editar">
                                            <i class="fas fa-edit text-warning"></i>
                                        </a>
                                    @endcan
                                    @can('mpreventivo.eliminar')
                                        <form action="{{ route('mpreventivo.destroy', $maint) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este registro de mantenimiento? Esta acción no se puede deshacer.')" style="display:inline-block;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="db-btn outline" title="Eliminar">
                                                <i class="fas fa-trash text-danger"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="db-empty p-5 text-center">
                                    <i class="fas fa-clipboard-check text-4xl mb-3" style="color: var(--db-border);"></i>
                                    <p style="color: var(--db-text-muted);">Este equipo no tiene mantenimientos registrados en su historial.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>