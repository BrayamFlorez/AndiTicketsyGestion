{{-- resources/views/empresas/show.blade.php --}}

<x-app-layout>
    <x-slot name="title">{{ $empresa->nombre }}</x-slot>

    {{-- Estado Alpine global para controlar la creación de una nueva sede --}}
    <div x-data="{ createOpen: false }">

        {{-- ── Cabecera Compacta & Acciones ────────────────────────────────────── --}}
        <div class="mb-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-stroke pb-2.5 dark:border-strokedark">
            <div class="flex items-center gap-2.5 flex-wrap min-w-0">
                <span class="font-mono text-xs font-bold text-primary bg-primary/10 px-2 py-0.5 rounded border border-primary/20">
                    {{ $empresa->nit ?? 'SIN-NIT' }}
                </span>

                <h2 class="text-title-md font-bold text-black dark:text-white truncate" title="{{ $empresa->nombre }}">
                    {{ $empresa->nombre }}
                </h2>

                @if($empresa->activo)
                    <span class="inline-flex rounded-full bg-meta-3/10 px-2 py-0.5 text-[9px] font-bold text-meta-3 border border-meta-3/20 uppercase">Empresa Activa</span>
                @else
                    <span class="inline-flex rounded-full bg-meta-1/10 px-2 py-0.5 text-[9px] font-bold text-meta-1 border border-meta-1/20 uppercase">Inactiva</span>
                @endif
            </div>

            {{-- Acciones --}}
            <div class="flex items-center gap-1.5 shrink-0">
                @can('sedes.gestionar')
                    <button @click="createOpen = true" 
                            class="inline-flex items-center gap-1 rounded bg-primary py-1.5 px-3 text-xs font-bold uppercase text-white hover:bg-opacity-90 transition shadow-sm cursor-pointer">
                        <i class="fas fa-plus-circle text-[10px]"></i> Añadir Sede
                    </button>
                @endcan

                <a href="{{ route('empresas.index') }}" 
                   class="inline-flex items-center gap-1 rounded border border-stroke py-1.5 px-3 text-xs font-bold text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition shadow-sm">
                    <i class="fas fa-arrow-left text-[10px]"></i> Volver
                </a>
            </div>
        </div>

        {{-- ── Strip de Métricas Rápidas Corporativas (4 KPIs) ─────────────────────── --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-3">
            <div class="rounded-sm border border-stroke bg-white p-2 shadow-default dark:border-strokedark dark:bg-boxdark flex items-center gap-2">
                <div class="flex h-7 w-7 items-center justify-center rounded bg-gray-2 text-bodydark2 dark:bg-meta-4 shrink-0">
                    <i class="fas fa-id-card text-xs"></i>
                </div>
                <div class="min-w-0">
                    <span class="text-[8px] font-bold text-bodydark2 uppercase block">NIT / Identificación</span>
                    <span class="text-[11px] font-bold font-mono text-black dark:text-white truncate block">
                        {{ $empresa->nit ?? '—' }}
                    </span>
                </div>
            </div>

            <div class="rounded-sm border border-stroke bg-white p-2 shadow-default dark:border-strokedark dark:bg-boxdark flex items-center gap-2">
                <div class="flex h-7 w-7 items-center justify-center rounded bg-primary/10 text-primary shrink-0">
                    <i class="fas fa-map-marked-alt text-xs"></i>
                </div>
                <div class="min-w-0">
                    <span class="text-[8px] font-bold text-bodydark2 uppercase block">Total Sedes</span>
                    <span class="text-[11px] font-bold font-mono text-black dark:text-white block">
                        {{ $empresa->sedes_count ?? $empresa->sedes->count() }} Nodos Registrados
                    </span>
                </div>
            </div>

            <div class="rounded-sm border border-stroke bg-white p-2 shadow-default dark:border-strokedark dark:bg-boxdark flex items-center gap-2">
                <div class="flex h-7 w-7 items-center justify-center rounded bg-meta-3/10 text-meta-3 shrink-0">
                    <i class="fas fa-users text-xs"></i>
                </div>
                <div class="min-w-0">
                    <span class="text-[8px] font-bold text-bodydark2 uppercase block">Usuarios Globales</span>
                    <span class="text-[11px] font-bold font-mono text-black dark:text-white truncate block">
                        {{ $empresa->usuarios_count ?? 0 }} Vinculados
                    </span>
                </div>
            </div>

            <div class="rounded-sm border border-stroke bg-white p-2 shadow-default dark:border-strokedark dark:bg-boxdark flex items-center gap-2">
                <div class="flex h-7 w-7 items-center justify-center rounded bg-meta-6/10 text-meta-6 shrink-0">
                    <i class="fas fa-ticket-alt text-xs"></i>
                </div>
                <div class="min-w-0">
                    <span class="text-[8px] font-bold text-bodydark2 uppercase block">Soporte y Tickets</span>
                    <span class="text-[11px] font-bold font-mono text-black dark:text-white truncate block">
                        {{ $empresa->tickets_count ?? 0 }} Históricos
                    </span>
                </div>
            </div>
        </div>

        {{-- ── Grid Principal (8 Cols Izq / 4 Cols Der) ─────────────── --}}
        <div class="grid grid-cols-12 gap-3">

            {{-- COLUMNA IZQUIERDA: Sedes y Sub-Nodos (8 cols) --}}
            <div class="col-span-12 lg:col-span-8 space-y-3">
                
                <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                    <div class="flex items-center justify-between border-b border-stroke py-2.5 px-4 dark:border-strokedark">
                        <h3 class="font-bold text-[11px] uppercase tracking-wider text-black dark:text-white flex items-center gap-1.5">
                            <i class="fas fa-sitemap text-primary"></i>
                            Sedes / Nodos Registrados ({{ $empresa->sedes->count() }})
                        </h3>
                    </div>

                    <div class="p-3.5">
                        @if($empresa->sedes->count() > 0)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach ($empresa->sedes as $sede)
                                    <div x-data="{ editOpen: {{ $errors->any() && old('sede_id') == $sede->id ? 'true' : 'false' }} }" 
                                         class="rounded border border-stroke bg-gray-2 p-3 dark:border-strokedark dark:bg-meta-4/20 flex flex-col justify-between group hover:border-primary/50 transition">
                                        
                                        {{-- Header Sede --}}
                                        <div class="flex justify-between items-start gap-2 mb-2">
                                            <div class="min-w-0">
                                                <h4 class="font-bold text-xs text-black dark:text-white flex items-center gap-1.5 truncate" title="{{ $sede->nombre }}">
                                                    <i class="fas fa-map-marker-alt text-primary text-[10px]"></i> {{ $sede->nombre }}
                                                </h4>
                                                <span class="text-[9px] text-bodydark2 block mt-0.5 truncate font-mono">
                                                    <i class="fas fa-city mr-1 text-[8px]"></i>{{ $sede->ciudad ?? 'Sin ciudad' }}
                                                </span>
                                            </div>
                                            <div>
                                                @if ($sede->activo)
                                                    <span class="inline-flex rounded-full bg-meta-3/10 px-1.5 py-0.2 text-[8px] font-bold text-meta-3 uppercase border border-meta-3/20">Activa</span>
                                                @else
                                                    <span class="inline-flex rounded-full bg-meta-1/10 px-1.5 py-0.2 text-[8px] font-bold text-meta-1 uppercase border border-meta-1/20">Inactiva</span>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Info Secundaria --}}
                                        <div class="space-y-1 my-2 py-2 border-y border-stroke dark:border-strokedark text-[10px] text-bodydark2">
                                            <div class="truncate font-mono" title="{{ $sede->direccion }}">
                                                <i class="fas fa-location-arrow w-3 text-primary/70"></i> {{ $sede->direccion ?? 'Sin dirección' }}
                                            </div>
                                            @if($sede->telefono)
                                                <div class="font-mono">
                                                    <i class="fas fa-phone-alt w-3 text-primary/70"></i> {{ $sede->telefono }}
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Métricas internas y Botones --}}
                                        <div class="flex justify-between items-center pt-1 text-[10px] font-bold text-black dark:text-white">
                                            <div class="flex items-center gap-2.5">
                                                <span title="Usuarios Asignados">
                                                    <i class="fas fa-users text-bodydark2 mr-1"></i>{{ $sede->usuarios_count ?? 0 }}
                                                </span>
                                                <span class="{{ ($sede->tickets_activos_count ?? 0) > 0 ? 'text-meta-6' : 'text-bodydark2' }}" title="Tickets Activos">
                                                    <i class="fas fa-ticket-alt mr-1"></i>{{ $sede->tickets_activos_count ?? 0 }}
                                                </span>
                                            </div>

                                            @can('sedes.gestionar')
                                                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <button @click="editOpen = true" class="flex h-6 w-6 items-center justify-center rounded border border-stroke text-bodydark2 hover:text-primary dark:border-strokedark transition" title="Editar">
                                                        <i class="fas fa-pen text-[9px]"></i>
                                                    </button>
                                                    <button type="button" onclick="confirmDeleteSede({{ $sede->id }}, '{{ $sede->nombre }}')" class="flex h-6 w-6 items-center justify-center rounded border border-stroke text-meta-1 dark:border-strokedark transition" title="Eliminar">
                                                        <i class="fas fa-trash text-[9px]"></i>
                                                    </button>
                                                    <form id="delete-sede-form-{{ $sede->id }}" action="{{ route('sedes.destroy', $sede) }}" method="POST" class="hidden">
                                                        @csrf @method('DELETE')
                                                    </form>
                                                </div>
                                                @include('sedes.partials.edit')
                                            @endcan
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-6 border border-dashed border-stroke dark:border-strokedark rounded-sm">
                                <i class="fas fa-map-marked-alt text-xl text-bodydark2 mb-1 block"></i>
                                <p class="text-xs text-bodydark2">Esta corporación no tiene sedes vinculadas.</p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- COLUMNA DERECHA: Ficha Técnica & HUD Corporativo (4 cols) --}}
            <div class="col-span-12 lg:col-span-4 space-y-3">
                
                {{-- Card Diagnóstico / Vectorial Sede HQ --}}
                <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                    <div class="flex items-center justify-between border-b border-stroke py-2.5 px-3 dark:border-strokedark">
                        <h3 class="font-bold text-[10px] uppercase tracking-wider text-black dark:text-white flex items-center gap-1.5">
                            <i class="fas fa-building text-primary"></i> Estructura Corporativa
                        </h3>
                        <span class="font-mono text-[8px] font-bold text-primary bg-primary/10 px-1.5 py-0.5 rounded">HQ-NODE</span>
                    </div>

                    <div class="p-4 flex flex-col items-center justify-center border-b border-stroke dark:border-strokedark">
                        {{-- Esquema Vectorial Headquarters --}}
                        <div class="relative w-36 h-28 flex items-center justify-center">
                            <svg class="w-full h-full drop-shadow-sm" viewBox="0 0 200 160" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <!-- Piso/Base -->
                                <path d="M20 135 L180 135" class="stroke-stroke dark:stroke-strokedark" stroke-width="2" stroke-dasharray="4 4"/>
                                
                                <!-- Edificio Central HQ -->
                                <path d="M75 35 L125 35 L125 135 L75 135 Z" class="fill-gray-2 dark:fill-meta-4 stroke-primary" stroke-width="1.5"/>
                                <path d="M85 55 L115 55 M85 75 L115 75 M85 95 L115 95 M85 115 L115 115" class="stroke-primary/40" stroke-width="2"/>
                                
                                <!-- Edificio Izquierdo -->
                                <path d="M45 80 L75 80 L75 135 L45 135 Z" class="fill-white dark:fill-boxdark stroke-primary/70" stroke-width="1.5"/>
                                <path d="M55 95 L65 95 M55 115 L65 115" class="stroke-primary/40" stroke-width="2"/>
                                
                                <!-- Edificio Derecho -->
                                <path d="M125 60 L160 60 L160 135 L125 135 Z" class="fill-white dark:fill-boxdark stroke-primary/70" stroke-width="1.5"/>
                                <path d="M135 75 L150 75 M135 95 L150 95 M135 115 L150 115" class="stroke-primary/40" stroke-width="2"/>
                                
                                <!-- Nodos de Conectividad / Antena -->
                                <circle cx="100" cy="20" r="3" class="fill-meta-3"/>
                                <line x1="100" y1="23" x2="100" y2="35" class="stroke-primary" stroke-width="1"/>
                                <path d="M100 20 L135 10" class="stroke-primary/40" stroke-width="1" stroke-dasharray="2 2"/>
                                <circle cx="135" cy="10" r="2" class="fill-primary"/>
                            </svg>
                        </div>
                        <span class="font-mono text-[9px] text-primary tracking-wider mt-2 uppercase font-bold text-center">
                            RED EMPRESARIAL <br> <span class="text-bodydark2 text-[8px]">{{ $empresa->nombre }}</span>
                        </span>
                    </div>

                    {{-- Datos de Contacto --}}
                    <div class="p-3.5 space-y-2 text-xs">
                        <div>
                            <span class="text-[9px] font-bold text-bodydark2 uppercase block">Correo Principal</span>
                            <div class="text-black dark:text-white font-mono mt-0.5 truncate" title="{{ $empresa->email }}">
                                {{ $empresa->email ?? 'Sin registro' }}
                            </div>
                        </div>
                        <div>
                            <span class="text-[9px] font-bold text-bodydark2 uppercase block">Teléfono Corporativo</span>
                            <div class="text-black dark:text-white font-mono mt-0.5">
                                {{ $empresa->telefono ?? 'Sin registro' }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            

        </div><br>
            @include('empresas.partials._llave_agente', ['empresa' => $empresa])
        {{-- Partial Creación de Sede --}}
        @can('sedes.gestionar')
            @include('sedes.partials.create', ['empresaDefecto' => $empresa])
        @endcan

    </div>

    @push('scripts')
        <script>
            function confirmDeleteSede(id, nombre) {
                Swal.fire({
                    title: '¿Eliminar sede?',
                    text: `Estás a punto de eliminar permanentemente el nodo "${nombre}".`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#DC3545',
                    cancelButtonColor: '#24303F',
                    confirmButtonText: '<i class="fas fa-trash"></i> Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    background: '#1C2434',
                    color: '#FFF'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('delete-sede-form-' + id).submit();
                    }
                })
            }
        </script>
    @endpush
</x-app-layout>