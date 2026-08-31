{{-- resources/views/empresas/index.blade.php --}}
<x-app-layout>
    <x-slot name="title">Empresas</x-slot>

    {{-- Estado global de Alpine para el modal de Creación --}}
    <div x-data="{ createOpen: {{ $errors->any() && !old('_method') ? 'true' : 'false' }} }">

        {{-- ── Cabecera y Acción Principal ────────────────────────────────── --}}
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-title-md2 font-bold text-black dark:text-white flex items-center gap-2">
                    <i class="fas fa-building text-primary"></i> Gestión de Empresas
                </h2>
                <p class="text-xs text-bodydark2 mt-1">
                    {{ $totales['activas'] }} activas de {{ $totales['total'] }} registradas
                    @if ($totales['sinSedes'] > 0)
                        · <span class="text-meta-6 font-semibold">{{ $totales['sinSedes'] }} sin sedes</span>
                    @endif
                </p>
            </div>

            @can('empresas.gestionar')
                <button @click="createOpen = true" 
                        class="inline-flex items-center justify-center gap-2 rounded bg-primary py-2 px-4 text-center text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm cursor-pointer">
                    <i class="fas fa-plus text-[10px]"></i> Nueva Empresa
                </button>
            @endcan
        </div>

        {{-- ── Formulario de Filtros (Card TailAdmin) ─────────────────────── --}}
        <div class="rounded-sm border border-stroke bg-white px-5 pt-5 pb-4 shadow-default dark:border-strokedark dark:bg-boxdark mb-6">
            <form method="GET" action="{{ route('empresas.index') }}">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    {{-- Búsqueda --}}
                    <div class="sm:col-span-2">
                        <label for="q" class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Buscar</label>
                        <input type="text" id="q" name="q" value="{{ $filtros['q'] ?? '' }}"
                               placeholder="Nombre, NIT o email de la empresa..."
                               class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                    </div>

                    {{-- Estado --}}
                    <div>
                        <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Estado</label>
                        <label class="flex items-center gap-2 mt-2 cursor-pointer select-none">
                            <input type="checkbox" name="solo_activas" value="1" @checked(!empty($filtros['solo_activas']))
                                   class="rounded border-stroke text-primary focus:ring-primary dark:border-form-strokedark dark:bg-form-input">
                            <span class="text-xs text-black dark:text-white font-medium">Solo activas</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 mt-4 border-t border-stroke pt-3 dark:border-strokedark">
                    @if (!empty($filtros))
                        <a href="{{ route('empresas.index') }}" 
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

        {{-- ── Tabla de Empresas ──────────────────────────────────────────── --}}
        <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark mb-6">
            <div class="max-w-full overflow-x-auto">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-2 text-left dark:bg-meta-4">
                            <th class="py-3.5 px-4 w-10"></th>
                            <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase">Empresa</th>
                            <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase">NIT</th>
                            <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase">Contacto</th>
                            <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase text-center">Sedes</th>
                            <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase text-center">Usuarios</th>
                            <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase text-center">Tickets Activos</th>
                            <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase text-center">Estado</th>
                            @can('empresas.gestionar')
                                <th class="py-3.5 px-4 text-right"></th>
                            @endcan
                        </tr>
                    </thead>
                    
                    @forelse ($empresas as $empresa)
                        <tbody x-data="{ 
                            expanded: false,
                            editOpen: {{ $errors->any() && old('empresa_id') == $empresa->id ? 'true' : 'false' }}, 
                            createOpen: false 
                        }}" class="border-b border-stroke dark:border-strokedark">
                            
                            {{-- FILA PRINCIPAL DE LA EMPRESA --}}
                            <tr class="hover:bg-gray-2 dark:hover:bg-meta-4/50 transition">
                                <td class="py-3.5 px-4 text-center">
                                    @if ($empresa->sedes->count() > 0)
                                        <button @click="expanded = !expanded" 
                                                class="text-bodydark2 hover:text-primary transition-transform duration-200" 
                                                :class="expanded ? 'rotate-90 text-primary' : ''">
                                            <i class="fas fa-chevron-right text-xs"></i>
                                        </button>
                                    @endif
                                </td>
                                
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded bg-primary/10 text-primary font-bold text-xs shrink-0">
                                            <i class="fas fa-building"></i>
                                        </div>
                                        <div>
                                            <a href="{{ route('empresas.show', $empresa) }}" class="text-xs font-bold text-black dark:text-white hover:text-primary transition block">
                                                {{ $empresa->nombre }}
                                            </a>
                                            @if ($empresa->email)
                                                <span class="text-[10px] text-bodydark2 block">{{ $empresa->email }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3.5 px-4 text-xs font-mono text-bodydark2">
                                    {{ $empresa->nit ?? '—' }}
                                </td>

                                <td class="py-3.5 px-4 text-xs text-bodydark2 font-mono">
                                    {{ $empresa->telefono ?? '—' }}
                                </td>

                                <td class="py-3.5 px-4 text-center text-xs font-bold text-black dark:text-white">
                                    {{ $empresa->sedes_count }}
                                </td>

                                <td class="py-3.5 px-4 text-center text-xs font-bold text-black dark:text-white">
                                    {{ $empresa->usuarios_count }}
                                </td>

                                <td class="py-3.5 px-4 text-center">
                                    @if ($empresa->tickets_activos_count > 0)
                                        <span class="inline-flex rounded-full bg-meta-6/10 px-2.5 py-0.5 text-[10px] font-bold text-meta-6">
                                            {{ $empresa->tickets_activos_count }}
                                        </span>
                                    @else
                                        <span class="text-xs text-bodydark2">0</span>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 text-center">
                                    @if ($empresa->activo)
                                        <span class="inline-flex rounded-full bg-meta-3/10 px-2.5 py-0.5 text-[10px] font-bold text-meta-3 uppercase">
                                            Activa
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full bg-meta-1/10 px-2.5 py-0.5 text-[10px] font-bold text-meta-1 uppercase">
                                            Inactiva
                                        </span>
                                    @endif
                                </td>
                                
                                @can('empresas.gestionar')
                                    <td class="py-3.5 px-4 text-right">
                                        <div class="flex items-center gap-1 justify-end">
                                            {{-- VER EMPRESA --}}
                                            <a href="{{ route('empresas.show', $empresa) }}" 
                                               class="flex h-7 w-7 items-center justify-center rounded border border-stroke text-bodydark2 hover:border-primary hover:text-primary dark:border-strokedark transition" 
                                               title="Ver perfil">
                                                <i class="fas fa-eye text-xs"></i>
                                            </a>

                                            {{-- NUEVA SEDE --}}
                                            <button @click="createOpen = true" 
                                                    class="flex h-7 w-7 items-center justify-center rounded border border-stroke text-meta-5 hover:border-meta-5 dark:border-strokedark transition" 
                                                    title="Agregar Sede">
                                                <i class="fas fa-map-pin text-xs"></i>
                                            </button>

                                            {{-- EDITAR --}}
                                            <button @click="editOpen = true" 
                                                    class="flex h-7 w-7 items-center justify-center rounded border border-stroke text-bodydark2 hover:border-primary hover:text-primary dark:border-strokedark transition" 
                                                    title="Editar">
                                                <i class="fas fa-pen text-xs"></i>
                                            </button>

                                            {{-- ELIMINAR --}}
                                            <button type="button" 
                                                    class="flex h-7 w-7 items-center justify-center rounded border border-stroke text-meta-1 hover:border-meta-1 dark:border-strokedark transition" 
                                                    title="Eliminar"
                                                    onclick="confirmDelete({{ $empresa->id }}, '{{ $empresa->nombre }}')">
                                                <i class="fas fa-trash text-xs"></i>
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
                                <tr x-show="expanded" x-cloak class="bg-gray-2 dark:bg-meta-4/20">
                                    <td colspan="9" class="p-4">
                                        <div x-show="expanded" x-collapse>
                                            <div class="border-l-2 border-primary pl-4">
                                                <h5 class="text-[10px] font-bold text-bodydark2 uppercase tracking-wider mb-3">Sedes Registradas</h5>
                                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                                    @foreach ($empresa->sedes as $sede)
                                                        <div class="rounded border border-stroke bg-white p-3 dark:border-strokedark dark:bg-boxdark flex flex-col justify-between">
                                                            <div class="flex justify-between items-start mb-2">
                                                                <div>
                                                                    <div class="font-bold text-xs text-black dark:text-white">
                                                                        <i class="fas fa-map-marker-alt text-primary mr-1"></i> {{ $sede->nombre }}
                                                                    </div>
                                                                    <div class="text-[10px] text-bodydark2 mt-0.5">{{ $sede->ciudad ?? 'Sin ciudad' }}</div>
                                                                </div>
                                                                <div>
                                                                    @if ($sede->activo)
                                                                        <span class="inline-flex rounded-full bg-meta-3/10 px-2 py-0.5 text-[9px] font-bold text-meta-3 uppercase">Activa</span>
                                                                    @else
                                                                        <span class="inline-flex rounded-full bg-meta-1/10 px-2 py-0.5 text-[9px] font-bold text-meta-1 uppercase">Inactiva</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            @if ($sede->direccion)
                                                                <div class="text-[10px] text-bodydark2 truncate mt-1">
                                                                    <i class="fas fa-location-arrow mr-1 text-primary"></i> {{ $sede->direccion }}
                                                                </div>
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
                                <td colspan="9" class="py-8 text-center">
                                    <div class="flex flex-col items-center text-bodydark2">
                                        <i class="fas fa-building text-3xl mb-2"></i>
                                        <p class="text-xs">
                                            @if (!empty($filtros))
                                                No hay empresas que coincidan con los filtros aplicados.
                                            @else
                                                Aún no hay empresas registradas.
                                            @endif
                                        </p>
                                        @if (!empty($filtros))
                                            <a href="{{ route('empresas.index') }}" class="mt-2 inline-flex items-center gap-1 rounded border border-stroke py-1 px-3 text-xs font-medium text-black dark:border-strokedark dark:text-white">Limpiar Filtros</a>
                                        @elseif(auth()->user()->can('empresas.gestionar'))
                                            <button @click="createOpen = true" class="mt-3 inline-flex items-center gap-1.5 rounded bg-primary py-1.5 px-4 text-xs font-bold uppercase text-white hover:bg-opacity-90">
                                                <i class="fas fa-plus"></i> Registrar Primera Empresa
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    @endforelse
                </table>
            </div>
        </div>

        {{-- Modal global de Creación --}}
        @include('empresas.partials.create')

    </div>

    @push('scripts')
        <script>
            function confirmDelete(id, nombre) {
                Swal.fire({
                    title: '¿Eliminar empresa?',
                    text: `Estás a punto de eliminar "${nombre}". Esta acción es irreversible.`,
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
                        document.getElementById('delete-form-' + id).submit();
                    }
                })
            }
        </script>
    @endpush
</x-app-layout>