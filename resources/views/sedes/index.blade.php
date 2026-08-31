{{-- resources/views/sedes/index.blade.php --}}
<x-app-layout>
    <x-slot name="title">Sedes</x-slot>

    <div x-data="{ createOpen: {{ $errors->any() && !old('_method') ? 'true' : 'false' }} }">
        
        {{-- ── Cabecera y Acción Principal ────────────────────────────────── --}}
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-title-md2 font-bold text-black dark:text-white flex items-center gap-2">
                    <i class="fas fa-map-marker-alt text-primary"></i> Gestión de Sedes
                </h2>
                <p class="text-xs text-bodydark2 mt-1">
                    {{ $totales['activas'] }} activas de {{ $totales['total'] }} registradas
                    @if ($totales['sinUsuarios'] > 0)
                        · <span class="text-meta-6 font-semibold">{{ $totales['sinUsuarios'] }} sin usuarios asignados</span>
                    @endif
                </p>
            </div>

            @can('sedes.gestionar')
                <button @click="createOpen = true" 
                        class="inline-flex items-center justify-center gap-2 rounded bg-primary py-2 px-4 text-center text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm cursor-pointer">
                    <i class="fas fa-plus text-[10px]"></i> Nueva Sede
                </button>
            @endcan
        </div>

        {{-- ── Formulario de Filtros (Card TailAdmin) ─────────────────────── --}}
        <div class="rounded-sm border border-stroke bg-white px-5 pt-5 pb-4 shadow-default dark:border-strokedark dark:bg-boxdark mb-6">
            <form method="GET" action="{{ route('sedes.index') }}">
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    
                    {{-- Búsqueda --}}
                    <div class="sm:col-span-2">
                        <label for="q" class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Buscar</label>
                        <input type="text" id="q" name="q" value="{{ $filtros['q'] ?? '' }}"
                               placeholder="Nombre, ciudad o dirección..."
                               class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                    </div>

                    {{-- Empresa --}}
                    <div>
                        <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Empresa</label>
                        <div class="relative z-20">
                            <select name="empresa_id" class="w-full appearance-none rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white cursor-pointer">
                                <option value="" class="bg-[#1c2434] text-gray-400">Todas</option>
                                @foreach ($empresas as $empresa)
                                    <option value="{{ $empresa->id }}" @selected(($filtros['empresa_id'] ?? '') == $empresa->id) class="bg-[#1c2434] text-white">
                                        {{ $empresa->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="absolute top-1/2 right-3 -translate-y-1/2 z-30 pointer-events-none">
                                <i class="fas fa-chevron-down text-[10px] text-bodydark2"></i>
                            </span>
                        </div>
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
                        <a href="{{ route('sedes.index') }}" 
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

        {{-- ── Tabla de Sedes ─────────────────────────────────────────────── --}}
        <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark mb-6">
            <div class="max-w-full overflow-x-auto">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-2 text-left dark:bg-meta-4">
                            <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase">Sede</th>
                            <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase">Empresa</th>
                            <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase">Ubicación</th>
                            <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase text-center">Usuarios</th>
                            <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase text-center">Tickets Activos</th>
                            <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase text-center">Estado</th>
                            @can('sedes.gestionar')
                                <th class="py-3.5 px-4 text-right"></th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stroke dark:divide-strokedark">
                        @forelse ($sedes as $sede)
                            <tr x-data="{ editOpen: {{ $errors->any() && old('sede_id') == $sede->id ? 'true' : 'false' }} }"
                                class="hover:bg-gray-2 dark:hover:bg-meta-4/50 transition">
                                
                                {{-- Sede & Teléfono --}}
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded bg-primary/10 text-primary font-bold text-xs shrink-0">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </div>
                                        <div>
                                            <span class="text-xs font-bold text-black dark:text-white block">
                                                {{ $sede->nombre }}
                                            </span>
                                            @if ($sede->telefono)
                                                <span class="text-[10px] text-bodydark2 block">
                                                    <i class="fas fa-phone-alt mr-1"></i>{{ $sede->telefono }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                
                                {{-- Empresa --}}
                                <td class="py-3.5 px-4">
                                    <span class="text-xs font-bold text-black dark:text-white block">
                                        {{ $sede->empresa->nombre ?? '—' }}
                                    </span>
                                </td>
                                
                                {{-- Ubicación --}}
                                <td class="py-3.5 px-4">
                                    <span class="text-xs text-black dark:text-white font-medium block">
                                        {{ $sede->ciudad ?? '—' }}
                                    </span>
                                    @if ($sede->direccion)
                                        <span class="text-[10px] text-bodydark2 block truncate max-w-[200px]">
                                            {{ $sede->direccion }}
                                        </span>
                                    @endif
                                </td>
                                
                                {{-- Usuarios --}}
                                <td class="py-3.5 px-4 text-center text-xs font-bold text-black dark:text-white">
                                    {{ $sede->usuarios_count }}
                                </td>
                                
                                {{-- Tickets Activos --}}
                                <td class="py-3.5 px-4 text-center">
                                    @if ($sede->tickets_activos_count > 0)
                                        <span class="inline-flex rounded-full bg-meta-6/10 px-2.5 py-0.5 text-[10px] font-bold text-meta-6">
                                            {{ $sede->tickets_activos_count }}
                                        </span>
                                    @else
                                        <span class="text-xs text-bodydark2">0</span>
                                    @endif
                                </td>
                                
                                {{-- Estado --}}
                                <td class="py-3.5 px-4 text-center">
                                    @if ($sede->activo)
                                        <span class="inline-flex rounded-full bg-meta-3/10 px-2.5 py-0.5 text-[10px] font-bold text-meta-3 uppercase">
                                            Activa
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full bg-meta-1/10 px-2.5 py-0.5 text-[10px] font-bold text-meta-1 uppercase">
                                            Inactiva
                                        </span>
                                    @endif
                                </td>
                                
                                {{-- Acciones --}}
                                @can('sedes.gestionar')
                                    <td class="py-3.5 px-4 text-right">
                                        <div class="flex items-center gap-1 justify-end">
                                            {{-- EDITAR --}}
                                            <button @click="editOpen = true" 
                                                    class="flex h-7 w-7 items-center justify-center rounded border border-stroke text-bodydark2 hover:border-primary hover:text-primary dark:border-strokedark transition" 
                                                    title="Editar">
                                                <i class="fas fa-pen text-[10px]"></i>
                                            </button>

                                            {{-- ELIMINAR --}}
                                            <button type="button" 
                                                    class="flex h-7 w-7 items-center justify-center rounded border border-stroke text-meta-1 hover:border-meta-1 dark:border-strokedark transition" 
                                                    title="Eliminar"
                                                    onclick="confirmDelete({{ $sede->id }}, '{{ $sede->nombre }}')">
                                                <i class="fas fa-trash text-[10px]"></i>
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
                                <td colspan="7" class="py-8 text-center">
                                    <div class="flex flex-col items-center text-bodydark2">
                                        <i class="fas fa-map-marked-alt text-3xl mb-2"></i>
                                        <p class="text-xs">
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
                <div class="border-t border-stroke py-3 px-4 dark:border-strokedark">
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