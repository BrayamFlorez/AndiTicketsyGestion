{{-- resources/views/sedes/index.blade.php --}}
<x-app-layout>
    <x-slot name="title">Gestión de Sedes</x-slot>

    @php
        $filtrosCount = collect($filtros ?? [])
            ->filter(fn($v) => $v !== null && $v !== '')
            ->count();
    @endphp

    <div x-data="sedesManager({
        empresasData: {{ Js::from($empresas) }},
        createOpenInit: {{ $errors->any() && !old('_method') ? 'true' : 'false' }}
    })">
        
        {{-- Flash Alerts Compactos --}}
        @if (session('success'))
            <div class="mb-3 flex w-full border-l-4 border-meta-3 bg-meta-3/10 px-3 py-2 shadow-sm rounded-sm">
                <i class="fas fa-check-circle text-meta-3 mr-2 mt-0.5 text-xs"></i>
                <p class="text-xs text-meta-3 font-medium">{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-3 flex w-full border-l-4 border-meta-1 bg-meta-1/10 px-3 py-2 shadow-sm rounded-sm">
                <i class="fas fa-exclamation-triangle text-meta-1 mr-2 mt-0.5 text-xs"></i>
                <p class="text-xs text-meta-1 font-medium">{{ session('error') }}</p>
            </div>
        @endif

        {{-- ── Header + Buscador + Filtros Integrados (Ultracompacto) ───────────── --}}
        <div class="rounded-sm border border-stroke bg-white p-3 shadow-default dark:border-strokedark dark:bg-boxdark mb-3"
             x-data="{ filtrosAbiertos: {{ $filtrosCount > 0 ? 'true' : 'false' }} }">

            <form method="GET" action="{{ route('sedes.index') }}">
                <div class="flex items-center gap-2 flex-wrap lg:flex-nowrap">
                    
                    <div class="flex items-center gap-1.5 shrink-0 mr-1">
                        <i class="fas fa-map-marker-alt text-primary text-sm"></i>
                        <h2 class="text-xs font-bold text-black dark:text-white uppercase tracking-wider whitespace-nowrap">
                            Sedes
                        </h2>
                        <span class="font-mono text-[10px] text-bodydark2">
                            ({{ $totales['activas'] }}/{{ $totales['total'] }})
                        </span>
                        @if ($totales['sinUsuarios'] > 0)
                            <span class="rounded bg-meta-6/10 px-1.5 py-0.2 text-[9px] font-bold text-meta-6" title="Sedes sin usuarios asignados">
                                {{ $totales['sinUsuarios'] }} sin usuarios
                            </span>
                        @endif
                    </div>

                    <div class="relative flex-1 min-w-[200px]">
                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-bodydark2 pointer-events-none">
                            <i class="fas fa-search text-[11px]"></i>
                        </span>
                        <input type="text" name="q" value="{{ $filtros['q'] ?? '' }}"
                               placeholder="Buscar por sede, ciudad, dirección..."
                               class="w-full rounded border border-stroke bg-transparent py-1 pl-8 pr-7 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                        @if (!empty($filtros['q']))
                            <a href="{{ route('sedes.index') }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-bodydark2 hover:text-meta-1">
                                <i class="fas fa-times text-[10px]"></i>
                            </a>
                        @endif
                    </div>

                    <div class="flex items-center gap-1.5 shrink-0">
                        <button type="button" @click="filtrosAbiertos = !filtrosAbiertos"
                                class="relative inline-flex items-center gap-1 rounded border border-stroke py-1 px-2.5 text-xs font-medium text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition">
                            <i class="fas fa-sliders-h text-[10px]"></i> Filtros
                        </button>
                        <button type="submit" class="inline-flex items-center gap-1 rounded bg-primary py-1 px-3 text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm cursor-pointer">
                            <i class="fas fa-search text-[10px]"></i> Buscar
                        </button>

                        @can('sedes.gestionar')
                            <button type="button" @click="createOpen = true"
                                    class="inline-flex items-center gap-1 rounded bg-meta-3 py-1 px-3 text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm whitespace-nowrap cursor-pointer ml-1">
                                <i class="fas fa-plus-circle text-[10px]"></i> Nueva Sede
                            </button>
                        @endcan
                    </div>
                </div>

                <div x-show="filtrosAbiertos" x-cloak x-transition class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5 mt-2.5 pt-2.5 border-t border-stroke dark:border-strokedark">
                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-[9px] font-bold uppercase text-black dark:text-white">Empresa</label>
                        <select name="empresa_id" class="w-full rounded border border-stroke bg-transparent py-1 px-2 text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                            <option value="" class="bg-[#1c2434] text-gray-400">Todas las empresas</option>
                            @foreach ($empresas as $empresa)
                                <option value="{{ $empresa->id }}" @selected(($filtros['empresa_id'] ?? '') == $empresa->id) class="bg-[#1c2434] text-white">{{ $empresa->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <label class="flex items-center gap-2 cursor-pointer select-none py-1.5">
                            <input type="checkbox" name="solo_activas" value="1" @checked(!empty($filtros['solo_activas'])) class="rounded border-stroke text-primary focus:ring-primary dark:border-form-strokedark dark:bg-form-input">
                            <span class="text-xs text-black dark:text-white font-bold">Mostrar solo activas</span>
                        </label>
                    </div>
                    <div class="flex items-end justify-end">
                        @if (!empty($filtros))
                            <a href="{{ route('sedes.index') }}" class="inline-flex items-center gap-1 rounded border border-stroke py-1 px-2.5 text-xs font-medium text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition">
                                <i class="fas fa-times text-[10px]"></i> Limpiar Filtros
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        {{-- ── Tabla de Sedes de Alta Densidad ──────────────────────────────── --}}
        <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark mb-6">
            <div class="max-w-full overflow-x-auto">
                <table class="w-full table-auto border-collapse">
                    <thead>
                        <tr class="bg-gray-2 text-left dark:bg-meta-4">
                            <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white min-w-[180px]">Sede</th>
                            <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white min-w-[160px]">Empresa</th>
                            <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white min-w-[180px]">Ubicación</th>
                            <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white text-center w-20">Usuarios</th>
                            <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white text-center w-20">Equipos</th>
                            <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white text-center w-24">Estado</th>
                            @can('sedes.gestionar')
                                <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white text-right w-20">Acción</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stroke dark:divide-strokedark">
                        @forelse ($sedes as $sede)
                            <tr x-data="{ editOpen: {{ $errors->any() && old('sede_id') == $sede->id ? 'true' : 'false' }} }"
                                class="hover:bg-gray-2 dark:hover:bg-meta-4/40 transition">
                                
                                <td class="py-1.5 px-3 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="flex h-6 w-6 items-center justify-center rounded bg-primary/10 text-primary font-bold text-[10px] shrink-0">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <span class="text-xs font-bold text-black dark:text-white block truncate max-w-[200px]" title="{{ $sede->nombre }}">{{ $sede->nombre }}</span>
                                            @if ($sede->telefono)
                                                <span class="text-[9px] text-bodydark2 block font-mono"><i class="fas fa-phone-alt text-[8px] mr-0.5"></i>{{ $sede->telefono }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td class="py-1.5 px-3 whitespace-nowrap text-xs font-bold text-black dark:text-white">
                                    {{ $sede->empresa->nombre ?? '—' }}
                                </td>

                                <td class="py-1.5 px-3">
                                    <span class="text-xs text-black dark:text-white font-medium block truncate max-w-[180px]">{{ $sede->ciudad ?? '—' }}</span>
                                    @if ($sede->direccion)
                                        <span class="text-[9px] text-bodydark2 block truncate max-w-[220px]" title="{{ $sede->direccion }}">{{ $sede->direccion }}</span>
                                    @endif
                                </td>

                                <td class="py-1.5 px-3 text-center whitespace-nowrap font-mono text-xs font-bold text-black dark:text-white">
                                    {{ $sede->usuarios_count }}
                                </td>
                                
                                <td class="py-1.5 px-3 text-center whitespace-nowrap font-mono text-xs font-bold text-black dark:text-white">
                                    {{ $sede->equipos_count ?? 0 }}
                                </td>

                                <td class="py-1.5 px-3 text-center whitespace-nowrap">
                                    @if ($sede->activo)
                                        <span class="inline-flex rounded-full bg-meta-3/10 px-2 py-0.5 text-[9px] font-bold text-meta-3 border border-meta-3/20 uppercase">Activa</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-meta-1/10 px-2 py-0.5 text-[9px] font-bold text-meta-1 border border-meta-1/20 uppercase">Inactiva</span>
                                    @endif
                                </td>

                                @can('sedes.gestionar')
                                    <td class="py-1.5 px-3 text-right whitespace-nowrap">
                                        <div class="flex items-center gap-1 justify-end">
                                            <button @click="editOpen = true" class="flex h-6 w-6 items-center justify-center rounded border border-stroke text-bodydark2 hover:border-primary hover:text-primary dark:border-strokedark transition" title="Editar">
                                                <i class="fas fa-pen text-[9px]"></i>
                                            </button>

                                            {{-- Cálculo Completo de Dependencias (Incluye Impresoras) --}}
                                            @php
                                                $dependencias = $sede->usuarios_count 
                                                    + ($sede->equipos_count ?? 0) 
                                                    + ($sede->impresoras_count ?? 0) 
                                                    + $sede->tickets_activos_count;
                                            @endphp
                                            <button type="button" 
                                                    @click="openDeleteModal({{ $sede->id }}, '{{ addslashes($sede->nombre) }}', {{ $dependencias }}, {{ $sede->empresa_id }})"
                                                    class="flex h-6 w-6 items-center justify-center rounded border border-stroke text-meta-1 hover:border-meta-1 dark:border-strokedark transition" 
                                                    title="Eliminar Sede">
                                                <i class="fas fa-trash text-[9px]"></i>
                                            </button>
                                        </div>

                                        @include('sedes.partials.edit')
                                    </td>
                                @endcan
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->can('sedes.gestionar') ? 7 : 6 }}" class="py-6 text-center">
                                    <div class="flex flex-col items-center text-bodydark2">
                                        <i class="fas fa-map-marked-alt text-xl mb-1"></i>
                                        <p class="text-xs">Sin sedes registradas.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($sedes->hasPages())
                <div class="border-t border-stroke px-4 py-2 dark:border-strokedark">
                    {{ $sedes->onEachSide(1)->links() }}
                </div>
            @endif
        </div>

        {{-- Modal de Creación nativo --}}
        @include('sedes.partials.create')

        {{-- ── MODAL DE ELIMINACIÓN CON MIGRACIÓN ── --}}
        <template x-teleport="body">
            <div x-show="deleteOpen" x-cloak class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
                <div @click.outside="deleteOpen = false" class="w-full max-w-md rounded-sm border border-stroke bg-white shadow-2xl dark:border-strokedark dark:bg-boxdark">
                    <div class="flex items-center justify-between px-4 py-3 border-b border-stroke dark:border-strokedark">
                        <h4 class="font-bold text-xs text-meta-1 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fas fa-exclamation-triangle"></i> Eliminar Nodo/Sede
                        </h4>
                        <button type="button" @click="deleteOpen = false" class="text-bodydark2 hover:text-black dark:hover:text-white">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>

                    <div class="p-4">
                        <p class="text-xs text-black dark:text-white mb-4">
                            Estás a punto de eliminar la sede <strong x-text="delNombre" class="text-meta-1"></strong>.
                        </p>

                        <form id="form-delete-modal" method="POST" :action="`/sedes/${delId}`">
                            @csrf
                            @method('DELETE')
                            
                            {{-- Panel si hay dependencias --}}
                            <template x-if="delDeps > 0">
                                <div class="rounded border border-meta-6/30 bg-meta-6/10 p-3 mb-4">
                                    <p class="text-[10px] font-bold text-meta-6 uppercase mb-1.5 flex items-center gap-1">
                                        <i class="fas fa-link"></i> Migración Requerida (<span x-text="delDeps"></span> registros)
                                    </p>
                                    <p class="text-[10px] text-black dark:text-white mb-3 leading-relaxed">
                                        Esta sede tiene equipos, impresoras o usuarios vinculados. Selecciona una <strong>sede de destino</strong> para transferir los registros.
                                    </p>
                                    
                                    <label class="block text-[9px] font-bold uppercase text-black dark:text-white mb-1">Migrar datos a:</label>
                                    <select name="migrar_a_sede_id" x-model="migrarASedeId" required
                                            class="w-full rounded border border-stroke bg-transparent py-1.5 px-2 text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                                        <option value="">— Selecciona destino —</option>
                                        <template x-for="s in sedesDisponibles" :key="s.id">
                                            <option :value="s.id" x-text="s.nombre" class="bg-[#1c2434] text-white"></option>
                                        </template>
                                    </select>

                                    <template x-if="sedesDisponibles.length === 0">
                                        <p class="text-[9px] text-meta-1 mt-1 font-bold">No hay otras sedes en esta empresa. Debes crear una antes de eliminar esta.</p>
                                    </template>
                                </div>
                            </template>

                            <div class="flex justify-end gap-2 pt-2 border-t border-stroke dark:border-strokedark">
                                <button type="button" @click="deleteOpen = false" class="rounded border border-stroke py-1.5 px-3 text-[10px] font-bold uppercase hover:bg-gray dark:border-strokedark dark:hover:bg-meta-4 transition text-black dark:text-white">
                                    Cancelar
                                </button>
                                <button type="submit" 
                                        :disabled="delDeps > 0 && (!migrarASedeId || sedesDisponibles.length === 0)"
                                        class="rounded bg-meta-1 py-1.5 px-3 text-[10px] font-bold uppercase text-white hover:bg-opacity-90 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                    <i class="fas fa-trash mr-1 text-[8px]"></i> Confirmar Eliminación
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('sedesManager', (config) => ({
                    createOpen: config.createOpenInit,
                    empresasData: config.empresasData || [],
                    
                    deleteOpen: false,
                    delId: null,
                    delNombre: '',
                    delDeps: 0,
                    delEmpresaId: null,
                    migrarASedeId: '',

                    get sedesDisponibles() {
                        if (!this.delEmpresaId) return [];
                        const emp = this.empresasData.find(e => e.id == this.delEmpresaId);
                        if (!emp || !emp.sedes) return [];
                        return emp.sedes.filter(s => s.id != this.delId);
                    },

                    openDeleteModal(id, nombre, deps, empresaId) {
                        this.delId = id;
                        this.delNombre = nombre;
                        this.delDeps = deps;
                        this.delEmpresaId = empresaId;
                        this.migrarASedeId = '';
                        this.deleteOpen = true;
                    }
                }));
            });
        </script>
    @endpush
</x-app-layout>