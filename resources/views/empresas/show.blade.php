{{-- resources/views/empresas/show.blade.php --}}
<x-app-layout>
    <x-slot name="title">{{ $empresa->nombre }}</x-slot>

    {{-- Estado Alpine global para controlar la creación de una nueva sede --}}
    <div x-data="{ createOpen: false }">

        {{-- ── Cabecera ────────────────────────────────────────────────────── --}}
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-title-md2 font-bold text-black dark:text-white flex items-center gap-2">
                    <i class="fas fa-building text-primary"></i> {{ $empresa->nombre }}
                </h2>
                <p class="text-xs text-bodydark2 mt-1">
                    Perfil corporativo, fichas de contacto e inventario de sedes
                </p>
            </div>
            
            <div class="flex items-center gap-2">
                <a href="{{ route('empresas.index') }}" 
                   class="inline-flex items-center justify-center gap-2 rounded border border-stroke py-2 px-4 text-center text-xs font-medium text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition shadow-sm">
                    <i class="fas fa-arrow-left text-[10px]"></i> Volver
                </a>
                
                @can('sedes.gestionar')
                    <button @click="createOpen = true" 
                            class="inline-flex items-center justify-center gap-2 rounded bg-primary py-2 px-4 text-center text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm cursor-pointer">
                        <i class="fas fa-plus text-[10px]"></i> Añadir Sede
                    </button>
                @endcan
            </div>
        </div>

        {{-- ── Contenido en Dos Columnas ───────────────────────────────────── --}}
        <div class="grid grid-cols-12 gap-4 md:gap-6 2xl:gap-7.5">

            {{-- COLUMNA PRINCIPAL: LISTADO DE SEDES (8 cols) --}}
            <div class="col-span-12 lg:col-span-8 space-y-4">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-xs font-bold text-bodydark2 uppercase tracking-wider">
                        Sedes Registradas ({{ $empresa->sedes_count }})
                    </h3>
                </div>

                @if($empresa->sedes->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach ($empresa->sedes as $sede)
                            {{-- Scope Alpine individual por sede --}}
                            <div x-data="{ editOpen: false }" 
                                 class="rounded-sm border border-stroke bg-white p-5 shadow-default dark:border-strokedark dark:bg-boxdark flex flex-col justify-between transition hover:border-primary dark:hover:border-primary">
                                
                                <div>
                                    {{-- Encabezado Sede --}}
                                    <div class="flex justify-between items-start gap-2 mb-3">
                                        <div>
                                            <h4 class="font-bold text-sm text-black dark:text-white flex items-center gap-1.5">
                                                <i class="fas fa-map-marker-alt text-primary"></i> {{ $sede->nombre }}
                                            </h4>
                                            <span class="text-[11px] text-bodydark2 block mt-0.5">
                                                <i class="fas fa-city mr-1"></i>{{ $sede->ciudad ?? 'Sin ciudad' }}
                                            </span>
                                        </div>
                                        <div>
                                            @if ($sede->activo)
                                                <span class="inline-flex rounded-full bg-meta-3/10 px-2.5 py-0.5 text-[9px] font-bold text-meta-3 uppercase">
                                                    Activa
                                                </span>
                                            @else
                                                <span class="inline-flex rounded-full bg-meta-1/10 px-2.5 py-0.5 text-[9px] font-bold text-meta-1 uppercase">
                                                    Inactiva
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Dirección y Teléfono --}}
                                    <div class="space-y-1.5 my-3 border-y border-stroke dark:border-strokedark py-3 text-xs text-bodydark2">
                                        <div class="truncate">
                                            <i class="fas fa-location-arrow w-4 text-primary"></i> {{ $sede->direccion ?? 'Sin dirección' }}
                                        </div>
                                        <div>
                                            <i class="fas fa-phone-alt w-4 text-primary"></i> {{ $sede->telefono ?? 'Sin teléfono' }}
                                        </div>
                                    </div>
                                </div>

                                {{-- Métricas Rápidas & Acciones --}}
                                <div class="flex justify-between items-center pt-2 border-t border-stroke dark:border-strokedark">
                                    <div class="flex items-center gap-3 text-[11px] font-bold text-black dark:text-white">
                                        <div>
                                            <i class="fas fa-users text-bodydark2 mr-1"></i>{{ $sede->usuarios_count }} <span class="font-normal text-bodydark2">Uis</span>
                                        </div>
                                        <div>
                                            <i class="fas fa-ticket-alt text-bodydark2 mr-1"></i>
                                            <span class="{{ $sede->tickets_activos_count > 0 ? 'text-meta-6' : 'text-bodydark2' }}">
                                                {{ $sede->tickets_activos_count }} Activos
                                            </span>
                                        </div>
                                    </div>

                                    @can('sedes.gestionar')
                                        <div class="flex items-center gap-1">
                                            {{-- Editar Sede --}}
                                            <button @click="editOpen = true" 
                                                    class="flex h-7 w-7 items-center justify-center rounded border border-stroke text-bodydark2 hover:border-primary hover:text-primary dark:border-strokedark transition" 
                                                    title="Editar sede">
                                                <i class="fas fa-pen text-[10px]"></i>
                                            </button>
                                            
                                            {{-- Eliminar Sede --}}
                                            <button type="button" 
                                                    class="flex h-7 w-7 items-center justify-center rounded border border-stroke text-meta-1 hover:border-meta-1 dark:border-strokedark transition" 
                                                    title="Eliminar sede"
                                                    onclick="confirmDeleteSede({{ $sede->id }}, '{{ $sede->nombre }}')">
                                                <i class="fas fa-trash text-[10px]"></i>
                                            </button>

                                            {{-- Formulario oculto de eliminación --}}
                                            <form id="delete-sede-form-{{ $sede->id }}" action="{{ route('sedes.destroy', $sede) }}" method="POST" class="hidden">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>

                                        @include('sedes.partials.edit')
                                    @endcan
                                </div>

                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-sm border border-stroke bg-white p-8 text-center shadow-default dark:border-strokedark dark:bg-boxdark">
                        <div class="flex flex-col items-center text-bodydark2">
                            <i class="fas fa-map-marked-alt text-3xl mb-2"></i>
                            <p class="text-xs">Esta empresa no tiene sedes registradas todavía.</p>
                            @can('sedes.gestionar')
                                <button @click="createOpen = true" 
                                        class="mt-3 inline-flex items-center gap-1.5 rounded border border-stroke py-1.5 px-4 text-xs font-bold uppercase text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition">
                                    Registrar primera sede
                                </button>
                            @endcan
                        </div>
                    </div>
                @endif
            </div>

            {{-- COLUMNA LATERAL: METADATOS Y FICHA TÉCNICA (4 cols) --}}
            <div class="col-span-12 lg:col-span-4 space-y-6">
                
                {{-- Ficha Informativa --}}
                <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                    <div class="border-b border-stroke py-3.5 px-5 dark:border-strokedark">
                        <h3 class="font-bold text-black dark:text-white text-xs uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-info-circle text-primary"></i> Datos de la Empresa
                        </h3>
                    </div>
                    <div class="p-5 space-y-3.5 text-xs">
                        <div>
                            <span class="block text-[10px] font-bold text-bodydark2 uppercase">Número NIT</span>
                            <span class="text-black dark:text-white font-mono mt-0.5 text-sm font-semibold block">{{ $empresa->nit ?? 'No registrado' }}</span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-bold text-bodydark2 uppercase">Correo Principal</span>
                            <span class="text-black dark:text-white mt-0.5 font-medium block truncate">{{ $empresa->email ?? 'Sin correo de contacto' }}</span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-bold text-bodydark2 uppercase">Teléfono Corporativo</span>
                            <span class="text-black dark:text-white mt-0.5 font-medium block">{{ $empresa->telefono ?? 'Sin teléfono registrado' }}</span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-bold text-bodydark2 uppercase mb-1">Estado Operativo</span>
                            <div>
                                @if($empresa->activo)
                                    <span class="inline-flex rounded-full bg-meta-3/10 px-2.5 py-0.5 text-[10px] font-bold text-meta-3 uppercase">
                                        Empresa Activa
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-meta-1/10 px-2.5 py-0.5 text-[10px] font-bold text-meta-1 uppercase">
                                        Inactiva / Bloqueada
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Métricas Globales --}}
                <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                    <div class="border-b border-stroke py-3.5 px-5 dark:border-strokedark">
                        <h3 class="font-bold text-black dark:text-white text-xs uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-chart-pie text-primary"></i> Métricas Globales
                        </h3>
                    </div>
                    <div class="p-5">
                        <ul class="divide-y divide-stroke dark:divide-strokedark text-xs">
                            <li class="flex justify-between py-2">
                                <span class="text-bodydark2">Total de Sedes</span>
                                <span class="text-black dark:text-white font-bold">{{ $empresa->sedes_count }}</span>
                            </li>
                            <li class="flex justify-between py-2">
                                <span class="text-bodydark2">Usuarios Vinculados</span>
                                <span class="text-black dark:text-white font-bold">{{ $empresa->usuarios_count }}</span>
                            </li>
                            <li class="flex justify-between py-2">
                                <span class="text-bodydark2">Histórico de Tickets</span>
                                <span class="text-primary font-bold">{{ $empresa->tickets_count }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>

        </div>

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
                    text: `Estás a punto de eliminar permanentemente la sede "${nombre}".`,
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