{{-- resources/views/empresas/show.blade.php --}}
<x-app-layout>
    <x-slot name="title">{{ $empresa->nombre }}</x-slot>

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

    {{-- Estado Alpine global para controlar la creación de una nueva sede aquí mismo --}}
    <div x-data="{ createOpen: false }">

        {{-- ── Cabecera ────────────────────────────────────────────────────── --}}
        <div class="db-header mb-6 flex items-start justify-between flex-wrap gap-3">
            <div>
                <h4 class="text-white text-xl font-bold flex items-center gap-2">
                    <span style="color: var(--db-text-muted)"><i class="fas fa-building"></i></span>
                    {{ $empresa->nombre }}
                </h4>
                <p class="text-gray-400 text-sm">Perfil corporativo y administración de sedes</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('empresas.index') }}" class="db-btn ghost">
                    <i class="fas fa-arrow-left"></i> Volver al listado
                </a>
                @can('sedes.gestionar')
                    <button @click="createOpen = true" class="db-btn solid">
                        <i class="fas fa-plus"></i> Añadir sede
                    </button>
                @endcan
            </div>
        </div>

        {{-- ── Contenido en Dos Columnas ───────────────────────────────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- COLUMNA PRINCIPAL: LISTADO DETALLADO DE SEDES (GRILLA) --}}
            <div class="lg:col-span-2 space-y-4">
                <div class="flex items-center justify-between">
                    <h5 class="text-[11px] font-black text-gray-400 uppercase tracking-[2px]">
                        Sedes Registradas ({{ $empresa->sedes_count }})
                    </h5>
                </div>

                @if($empresa->sedes->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach ($empresa->sedes as $sede)
                            {{-- Alcance Alpine individual por sede para controlar su modal de edición --}}
                            <div x-data="{ editOpen: false }" 
                                 class="db-card flex flex-col justify-between transition-all hover:border-[var(--db-accent)] bg-[var(--db-surface)] p-4 border border-[var(--db-border)] rounded-xl shadow-lg relative">
                                
                                <div>
                                    {{-- Nombre y Estado de la sede --}}
                                    <div class="flex justify-between items-start gap-2 mb-3">
                                        <div>
                                            <h3 class="font-bold text-base text-white tracking-tight">
                                                <i class="fas fa-map-marker-alt text-[#007acc] mr-1.5"></i>{{ $sede->nombre }}
                                            </h3>
                                            <span class="text-xs text-gray-500 font-semibold"><i class="fas fa-city mr-1"></i>{{ $sede->ciudad ?? 'Sin ciudad' }}</span>
                                        </div>
                                        <div>
                                            @if ($sede->activo)
                                                <span class="db-badge resuelto" style="font-size: 0.55rem; padding: 0.2rem 0.55rem;">Activa</span>
                                            @else
                                                <span class="db-badge cerrado" style="font-size: 0.55rem; padding: 0.2rem 0.55rem;">Inactiva</span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Dirección y Teléfono --}}
                                    <div class="space-y-1.5 my-4 border-t border-b border-gray-800/50 py-3 text-xs text-gray-400">
                                        <div class="truncate"><i class="fas fa-location-arrow w-4 text-gray-600"></i> {{ $sede->direccion ?? 'Sin dirección registrada' }}</div>
                                        <div><i class="fas fa-phone-alt w-4 text-gray-600"></i> {{ $sede->telefono ?? 'Sin teléfono' }}</div>
                                    </div>
                                </div>

                                {{-- Estadísticas de la sede e interactivos --}}
                                <div class="flex justify-between items-center mt-2 pt-2 border-t border-gray-800/30">
                                    <div class="flex items-center gap-3 text-[11px] font-bold text-gray-400">
                                        <div><i class="fas fa-users text-gray-600 mr-1"></i>{{ $sede->usuarios_count }} <span class="font-normal text-gray-500">Uis</span></div>
                                        <div>
                                            <i class="fas fa-ticket-alt text-gray-600 mr-1"></i>
                                            <span class="{{ $sede->tickets_activos_count > 0 ? 'text-amber-500' : '' }}">{{ $sede->tickets_activos_count }} Activos</span>
                                        </div>
                                    </div>

                                    @can('sedes.gestionar')
                                        <div class="flex items-center gap-1">
                                            {{-- Editar Sede --}}
                                            <button @click="editOpen = true" class="p-1.5 text-gray-500 hover:text-white transition" title="Editar sede">
                                                <i class="fas fa-pen text-xs"></i>
                                            </button>
                                            
                                            {{-- Eliminar Sede --}}
                                            <button type="button" class="p-1.5 text-gray-500 hover:text-red-400 transition" title="Eliminar sede"
                                                    onclick="confirmDeleteSede({{ $sede->id }}, '{{ $sede->nombre }}')">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>

                                            {{-- Formulario oculto de eliminación --}}
                                            <form id="delete-sede-form-{{ $sede->id }}" action="{{ route('sedes.destroy', $sede) }}" method="POST" class="hidden">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>

                                        {{-- Inyección automática de tu partial de edición --}}
                                        @include('sedes.partials.edit')
                                    @endcan
                                </div>

                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="db-card p-8 text-center bg-[var(--db-surface)] border border-[var(--db-border)] rounded-xl">
                        <div class="db-empty py-4">
                            <i class="fas fa-map-marked-alt text-gray-700 text-3xl mb-2"></i>
                            <p class="text-sm text-gray-400">Esta empresa no tiene sedes registradas todavía.</p>
                            @can('sedes.gestionar')
                                <button @click="createOpen = true" class="db-btn outline mt-2">Registrar primera sede</button>
                            @endcan
                        </div>
                    </div>
                @endif
            </div>

            {{-- COLUMNA LATERAL: METADATOS Y CARD DE LA EMPRESA --}}
            <div class="space-y-4">
                <h5 class="text-[11px] font-black text-gray-400 uppercase tracking-[2px]">Ficha Informativa</h5>
                
                {{-- Datos Técnicos --}}
                <div class="db-card bg-[var(--db-surface)] border border-[var(--db-border)] rounded-xl overflow-hidden">
                    <div class="db-card-header bg-black/10">
                        <span>Detalles de Contacto</span>
                    </div>
                    <div class="form-card-body p-4 space-y-3 text-xs">
                        <div>
                            <div class="form-label text-[10px] text-gray-500">Número NIT</div>
                            <div class="text-gray-200 font-mono mt-0.5 text-sm font-semibold">{{ $empresa->nit ?? 'No registrado' }}</div>
                        </div>
                        <div>
                            <div class="form-label text-[10px] text-gray-500">Correo Principal</div>
                            <div class="text-gray-200 mt-0.5 font-medium truncate">{{ $empresa->email ?? 'Sin correo de contacto' }}</div>
                        </div>
                        <div>
                            <div class="form-label text-[10px] text-gray-500">Teléfono Corporativo</div>
                            <div class="text-gray-200 mt-0.5 font-medium">{{ $empresa->telefono ?? 'Sin teléfono registrado' }}</div>
                        </div>
                        <div>
                            <div class="form-label text-[10px] text-gray-500">Estado de Operación</div>
                            <div class="mt-1">
                                @if($empresa->activo)
                                    <span class="db-badge resuelto" style="font-size:0.6rem;">Empresa Activa</span>
                                @else
                                    <span class="db-badge cerrado" style="font-size:0.6rem;">Bloqueada / Inactiva</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Métricas Globales de la Empresa --}}
                <div class="db-card bg-[var(--db-surface)] border border-[var(--db-border)] rounded-xl overflow-hidden">
                    <div class="db-card-header bg-black/10">
                        <span>Carga Global Estimada</span>
                    </div>
                    <div class="form-card-body p-4">
                        <ul class="space-y-2 text-xs" style="list-style: none; padding: 0; margin: 0;">
                            <li class="flex justify-between py-1.5 border-b border-gray-800/40">
                                <span class="text-gray-400">Total de Sedes</span>
                                <span class="text-white font-bold">{{ $empresa->sedes_count }}</span>
                            </li>
                            <li class="flex justify-between py-1.5 border-b border-gray-800/40">
                                <span class="text-gray-400">Usuarios Vinculados</span>
                                <span class="text-white font-bold">{{ $empresa->usuarios_count }}</span>
                            </li>
                            <li class="flex justify-between py-1.5">
                                <span class="text-gray-400">Histórico de Tickets</span>
                                <span class="text-[#007acc] font-bold">{{ $empresa->tickets_count }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>

        {{-- Inyección automática de tu partial de Creación de Sede --}}
        {{-- Le inyectamos '$empresa' como 'empresaDefecto' para bloquear el select --}}
        @can('sedes.gestionar')
            @include('sedes.partials.create', ['empresaDefecto' => $empresa])
        @endcan

    </div>

    @push('scripts')
        <script>
            // Alerta SweetAlert personalizada para eliminar sedes de forma segura
            function confirmDeleteSede(id, nombre) {
                Swal.fire({
                    title: '¿Eliminar sede?',
                    text: `Estás a punto de eliminar permanentemente la sede "${nombre}".`,
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
                        document.getElementById('delete-sede-form-' + id).submit();
                    }
                })
            }
        </script>
    @endpush
</x-app-layout>