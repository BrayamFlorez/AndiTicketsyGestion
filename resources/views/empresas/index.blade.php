{{-- resources/views/empresas/index.blade.php --}}
<x-app-layout>
    <x-slot name="title">Gestión de Empresas</x-slot>

    @php
        $filtrosCount = collect($filtros ?? [])
            ->filter(fn($v) => $v !== null && $v !== '')
            ->count();
    @endphp

    <div x-data="empresasManager({
        todasEmpresas: {{ Js::from($todasEmpresas) }},
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

        {{-- ── Header + Buscador + Filtros Integrados ───────────── --}}
        <div class="rounded-sm border border-stroke bg-white p-3 shadow-default dark:border-strokedark dark:bg-boxdark mb-3"
             x-data="{ filtrosAbiertos: {{ $filtrosCount > 0 ? 'true' : 'false' }} }">

            <form method="GET" action="{{ route('empresas.index') }}">
                <div class="flex items-center gap-2 flex-wrap lg:flex-nowrap">
                    
                    <div class="flex items-center gap-1.5 shrink-0 mr-1">
                        <i class="fas fa-building text-primary text-sm"></i>
                        <h2 class="text-xs font-bold text-black dark:text-white uppercase tracking-wider whitespace-nowrap">
                            Empresas
                        </h2>
                        <span class="font-mono text-[10px] text-bodydark2">
                            ({{ $totales['activas'] }}/{{ $totales['total'] }})
                        </span>
                        @if ($totales['sinSedes'] > 0)
                            <span class="rounded bg-meta-6/10 px-1.5 py-0.2 text-[9px] font-bold text-meta-6" title="Empresas sin sedes vinculadas">
                                {{ $totales['sinSedes'] }} sin sedes
                            </span>
                        @endif
                    </div>

                    <div class="relative flex-1 min-w-[200px]">
                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-bodydark2 pointer-events-none">
                            <i class="fas fa-search text-[11px]"></i>
                        </span>
                        <input type="text" name="q" value="{{ $filtros['q'] ?? '' }}"
                               placeholder="Buscar por nombre, NIT, email..."
                               class="w-full rounded border border-stroke bg-transparent py-1 pl-8 pr-7 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                        @if (!empty($filtros['q']))
                            <a href="{{ route('empresas.index') }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-bodydark2 hover:text-meta-1">
                                <i class="fas fa-times text-[10px]"></i>
                            </a>
                        @endif
                    </div>

                    <div class="flex items-center gap-1.5 shrink-0">
                        <button type="button" @click="filtrosAbiertos = !filtrosAbiertos"
                                class="relative inline-flex items-center gap-1 rounded border border-stroke py-1 px-2.5 text-xs font-medium text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition">
                            <i class="fas fa-sliders-h text-[10px]"></i>
                            <span x-text="filtrosAbiertos ? 'Menos' : 'Filtros'"></span>
                        </button>

                        <button type="submit" class="inline-flex items-center gap-1 rounded bg-primary py-1 px-3 text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm cursor-pointer">
                            <i class="fas fa-search text-[10px]"></i> Buscar
                        </button>

                        @can('empresas.gestionar')
                            <button type="button" @click="createOpen = true"
                                    class="inline-flex items-center gap-1 rounded bg-meta-3 py-1 px-3 text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm whitespace-nowrap cursor-pointer ml-1">
                                <i class="fas fa-plus-circle text-[10px]"></i> Nueva Empresa
                            </button>
                        @endcan
                    </div>
                </div>

                <div x-show="filtrosAbiertos" x-cloak x-transition class="flex items-center gap-4 mt-2.5 pt-2.5 border-t border-stroke dark:border-strokedark">
                    <label class="flex items-center gap-2 cursor-pointer select-none py-1">
                        <input type="checkbox" name="solo_activas" value="1" @checked(!empty($filtros['solo_activas'])) class="rounded border-stroke text-primary focus:ring-primary dark:border-form-strokedark dark:bg-form-input">
                        <span class="text-xs text-black dark:text-white font-bold">Mostrar solo empresas activas</span>
                    </label>

                    <div class="ml-auto">
                        @if (!empty($filtros))
                            <a href="{{ route('empresas.index') }}" class="inline-flex items-center gap-1 rounded border border-stroke py-1 px-2.5 text-xs font-medium text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition">
                                <i class="fas fa-times text-[10px]"></i> Limpiar Filtros
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        {{-- ── Tabla de Empresas de Alta Densidad ──────────────────────────────── --}}
        <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark mb-6">
            <div class="max-w-full overflow-x-auto">
                <table class="w-full table-auto border-collapse">
                    <thead>
                        <tr class="bg-gray-2 text-left dark:bg-meta-4 border-b border-stroke dark:border-strokedark">
                            <th class="py-2 px-3 w-8"></th>
                            <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white min-w-[200px]">Empresa / Contacto</th>
                            <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white w-32">NIT / RUC</th>
                            <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white text-center w-20">Sedes</th>
                            <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white text-center w-20">Usuarios</th>
                            <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white text-center w-28">Tickets Activos</th>
                            <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white text-center w-24">Estado</th>
                            @can('empresas.gestionar')
                                <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white text-right w-24">Acción</th>
                            @endcan
                        </tr>
                    </thead>
                    
                    @forelse ($empresas as $empresa)
                        <tbody x-data="{ 
                            expanded: false,
                            editOpen: {{ $errors->any() && old('empresa_id') == $empresa->id ? 'true' : 'false' }}
                        }" class="border-b border-stroke dark:border-strokedark group">
                            
                            <tr class="hover:bg-gray-2 dark:hover:bg-meta-4/40 transition">
                                <td class="py-1.5 px-3 text-center">
                                    @if ($empresa->sedes->count() > 0)
                                        <button @click="expanded = !expanded" class="text-bodydark2 hover:text-primary transition-transform duration-200 p-1" :class="expanded ? 'rotate-90 text-primary' : ''">
                                            <i class="fas fa-chevron-right text-[10px]"></i>
                                        </button>
                                    @endif
                                </td>
                                
                                <td class="py-1.5 px-3 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="flex h-6 w-6 items-center justify-center rounded bg-primary/10 text-primary font-bold text-[10px] shrink-0">
                                            <i class="fas fa-building"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <a href="{{ route('empresas.show', $empresa) }}" class="text-xs font-bold text-black dark:text-white hover:text-primary transition block truncate max-w-[200px]" title="{{ $empresa->nombre }}">
                                                {{ $empresa->nombre }}
                                            </a>
                                            <div class="flex items-center gap-1.5 text-[9px] text-bodydark2 font-mono mt-0.5">
                                                @if ($empresa->email)
                                                    <span class="truncate max-w-[120px]" title="{{ $empresa->email }}"><i class="fas fa-envelope text-[8px] mr-0.5"></i>{{ $empresa->email }}</span>
                                                @endif
                                                @if ($empresa->telefono)
                                                    <span><i class="fas fa-phone-alt text-[8px] mr-0.5"></i>{{ $empresa->telefono }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-1.5 px-3 text-xs font-mono font-bold text-bodydark2">
                                    {{ $empresa->nit ?? '—' }}
                                </td>

                                <td class="py-1.5 px-3 text-center text-xs font-bold text-black dark:text-white">
                                    {{ $empresa->sedes_count }}
                                </td>

                                <td class="py-1.5 px-3 text-center text-xs font-bold text-black dark:text-white">
                                    {{ $empresa->usuarios_count }}
                                </td>

                                <td class="py-1.5 px-3 text-center">
                                    @if ($empresa->tickets_activos_count > 0)
                                        <span class="inline-flex rounded-full bg-meta-6/10 px-2 py-0.5 text-[9px] font-bold text-meta-6 border border-meta-6/20">
                                            {{ $empresa->tickets_activos_count }} activos
                                        </span>
                                    @else
                                        <span class="text-xs text-bodydark2 font-mono">0</span>
                                    @endif
                                </td>

                                <td class="py-1.5 px-3 text-center">
                                    @if ($empresa->activo)
                                        <span class="inline-flex rounded-full bg-meta-3/10 px-2 py-0.5 text-[9px] font-bold text-meta-3 uppercase border border-meta-3/20">Activa</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-meta-1/10 px-2 py-0.5 text-[9px] font-bold text-meta-1 uppercase border border-meta-1/20">Inactiva</span>
                                    @endif
                                </td>
                                
                                @can('empresas.gestionar')
                                    <td class="py-1.5 px-3 text-right whitespace-nowrap">
                                        <div class="flex items-center gap-1 justify-end">
                                            <a href="{{ route('empresas.show', $empresa) }}" class="flex h-6 w-6 items-center justify-center rounded border border-stroke text-bodydark2 hover:border-primary hover:text-primary dark:border-strokedark transition" title="Ver Perfil">
                                                <i class="fas fa-eye text-[9px]"></i>
                                            </a>

                                            <button @click="editOpen = true" class="flex h-6 w-6 items-center justify-center rounded border border-stroke text-bodydark2 hover:border-primary hover:text-primary dark:border-strokedark transition" title="Editar Empresa">
                                                <i class="fas fa-pen text-[9px]"></i>
                                            </button>

                                            {{-- Conteo total de dependencias asociadas a la empresa --}}
                                            @php
                                                $depsTotal = $empresa->sedes_count 
                                                    + $empresa->usuarios_count 
                                                    + ($empresa->equipos_count ?? 0) 
                                                    + ($empresa->impresoras_count ?? 0) 
                                                    + $empresa->tickets_activos_count;
                                            @endphp
                                            <button type="button" 
                                                    @click="openDeleteModal({{ $empresa->id }}, '{{ addslashes($empresa->nombre) }}', {{ $depsTotal }})"
                                                    class="flex h-6 w-6 items-center justify-center rounded border border-stroke text-meta-1 hover:border-meta-1 dark:border-strokedark transition" 
                                                    title="Eliminar Empresa">
                                                <i class="fas fa-trash text-[9px]"></i>
                                            </button>
                                        </div>
                                        
                                        @include('empresas.partials.edit')
                                    </td>
                                @endcan
                            </tr>

                            {{-- FILA EXPANDIBLE DE SEDES --}}
                            @if ($empresa->sedes->count() > 0)
                                <tr x-show="expanded" x-cloak class="bg-gray-2/50 dark:bg-meta-4/20 border-t border-stroke dark:border-strokedark">
                                    <td colspan="8" class="p-3">
                                        <div x-show="expanded" x-collapse>
                                            <div class="border-l-2 border-primary pl-3 ml-2 my-1">
                                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2">
                                                    @foreach ($empresa->sedes as $sede)
                                                        <div class="rounded border border-stroke bg-white p-2 dark:border-strokedark dark:bg-boxdark flex flex-col justify-between shadow-sm">
                                                            <div class="flex justify-between items-start mb-1">
                                                                <div class="min-w-0">
                                                                    <div class="font-bold text-[11px] text-black dark:text-white truncate" title="{{ $sede->nombre }}">
                                                                        <i class="fas fa-map-marker-alt text-primary mr-1 text-[9px]"></i> {{ $sede->nombre }}
                                                                    </div>
                                                                    <div class="text-[9px] text-bodydark2 mt-0.5 truncate">{{ $sede->ciudad ?? 'Sin ciudad' }}</div>
                                                                </div>
                                                            </div>
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
                                <td colspan="{{ auth()->user()->can('empresas.gestionar') ? 8 : 7 }}" class="py-8 text-center">
                                    <div class="flex flex-col items-center text-bodydark2">
                                        <i class="fas fa-building text-2xl mb-2"></i>
                                        <p class="text-xs">No hay empresas registradas.</p>
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

        {{-- ── MODAL DE ELIMINACIÓN CON MIGRACIÓN DE EMPRESA ── --}}
        <template x-teleport="body">
            <div x-show="deleteOpen" x-cloak class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
                <div @click.outside="deleteOpen = false" class="w-full max-w-md rounded-sm border border-stroke bg-white shadow-2xl dark:border-strokedark dark:bg-boxdark">
                    <div class="flex items-center justify-between px-4 py-3 border-b border-stroke dark:border-strokedark">
                        <h4 class="font-bold text-xs text-meta-1 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fas fa-exclamation-triangle"></i> Eliminar Corporación
                        </h4>
                        <button type="button" @click="deleteOpen = false" class="text-bodydark2 hover:text-black dark:hover:text-white">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>

                    <div class="p-4">
                        <p class="text-xs text-black dark:text-white mb-4">
                            Estás a punto de eliminar la empresa <strong x-text="delNombre" class="text-meta-1"></strong>.
                        </p>

                        <form id="form-delete-empresa-modal" method="POST" :action="`/empresas/${delId}`">
                            @csrf
                            @method('DELETE')
                            
                            {{-- Si tiene recursos vinculados se solicita la migración --}}
                            <template x-if="delDeps > 0">
                                <div class="rounded border border-meta-6/30 bg-meta-6/10 p-3 mb-4">
                                    <p class="text-[10px] font-bold text-meta-6 uppercase mb-1.5 flex items-center gap-1">
                                        <i class="fas fa-link"></i> Reasignación Corporativa (<span x-text="delDeps"></span> recursos)
                                    </p>
                                    <p class="text-[10px] text-black dark:text-white mb-3 leading-relaxed">
                                        Esta empresa cuenta con sedes, usuarios, equipos o tickets activos. Selecciona una <strong>empresa receptora</strong> para transferir todos los recursos antes de la baja.
                                    </p>
                                    
                                    <label class="block text-[9px] font-bold uppercase text-black dark:text-white mb-1">Empresa Destino:</label>
                                    <select name="migrar_a_empresa_id" x-model="migrarAEmpresaId" required
                                            class="w-full rounded border border-stroke bg-transparent py-1.5 px-2 text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                                        <option value="">— Selecciona empresa destino —</option>
                                        <template x-for="e in empresasDisponibles" :key="e.id">
                                            <option :value="e.id" x-text="e.nombre" class="bg-[#1c2434] text-white"></option>
                                        </template>
                                    </select>

                                    <template x-if="empresasDisponibles.length === 0">
                                        <p class="text-[9px] text-meta-1 mt-1 font-bold">No existen otras empresas registradas para realizar la transferencia.</p>
                                    </template>
                                </div>
                            </template>

                            <div class="flex justify-end gap-2 pt-2 border-t border-stroke dark:border-strokedark">
                                <button type="button" @click="deleteOpen = false" class="rounded border border-stroke py-1.5 px-3 text-[10px] font-bold uppercase hover:bg-gray dark:border-strokedark dark:hover:bg-meta-4 transition text-black dark:text-white">
                                    Cancelar
                                </button>
                                <button type="submit" 
                                        :disabled="delDeps > 0 && (!migrarAEmpresaId || empresasDisponibles.length === 0)"
                                        class="rounded bg-meta-1 py-1.5 px-3 text-[10px] font-bold uppercase text-white hover:bg-opacity-90 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                    <i class="fas fa-trash mr-1 text-[8px]"></i> Eliminar Empresa
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
                Alpine.data('empresasManager', (config) => ({
                    createOpen: config.createOpenInit,
                    todasEmpresas: config.todasEmpresas || [],
                    
                    deleteOpen: false,
                    delId: null,
                    delNombre: '',
                    delDeps: 0,
                    migrarAEmpresaId: '',

                    get empresasDisponibles() {
                        return this.todasEmpresas.filter(e => e.id != this.delId);
                    },

                    openDeleteModal(id, nombre, deps) {
                        this.delId = id;
                        this.delNombre = nombre;
                        this.delDeps = deps;
                        this.migrarAEmpresaId = '';
                        this.deleteOpen = true;
                    }
                }));
            });
        </script>
    @endpush
</x-app-layout>