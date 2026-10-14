{{-- resources/views/flujos/plantillas/index.blade.php --}}
<x-app-layout>
    <x-slot name="title">Plantillas de Flujo</x-slot>

    @php
        $filtrosCount = collect($filtros ?? [])
            ->filter(fn($v) => $v !== null && $v !== '')
            ->count();
    @endphp

    {{-- Flash Alerts Compactos --}}
    @if (session('exito'))
        <div class="mb-3 flex w-full border-l-4 border-meta-3 bg-meta-3/10 px-3 py-2 shadow-sm rounded-sm">
            <i class="fas fa-check-circle text-meta-3 mr-2 mt-0.5 text-xs"></i>
            <p class="text-xs text-meta-3 font-medium">{{ session('exito') }}</p>
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

        <form method="GET" action="{{ route('flujos.plantillas.index') }}">
            {{-- Fila Superior: Título + Buscador + Botones --}}
            <div class="flex items-center gap-2 flex-wrap lg:flex-nowrap">
                
                {{-- Título y Métricas --}}
                <div class="flex items-center gap-1.5 shrink-0 mr-1">
                    <i class="fas fa-project-diagram text-primary text-sm"></i>
                    <h2 class="text-xs font-bold text-black dark:text-white uppercase tracking-wider whitespace-nowrap">
                        Plantillas de Flujo
                    </h2>
                    <span class="font-mono text-[10px] text-bodydark2">({{ $plantillas->total() }})</span>
                </div>

                {{-- Input Buscador --}}
                <div class="relative flex-1 min-w-[200px]">
                    <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-bodydark2 pointer-events-none">
                        <i class="fas fa-search text-[11px]"></i>
                    </span>
                    <input type="text" name="q" value="{{ $filtros['q'] ?? '' }}"
                           placeholder="Buscar por nombre o descripción de plantilla..."
                           class="w-full rounded border border-stroke bg-transparent py-1 pl-8 pr-7 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                    @if (!empty($filtros['q']))
                        <a href="{{ route('flujos.plantillas.index') }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-bodydark2 hover:text-meta-1">
                            <i class="fas fa-times text-[10px]"></i>
                        </a>
                    @endif
                </div>

                {{-- Acciones --}}
                <div class="flex items-center gap-1.5 shrink-0">
                    <button type="button" @click="filtrosAbiertos = !filtrosAbiertos"
                            class="relative inline-flex items-center gap-1 rounded border border-stroke py-1 px-2.5 text-xs font-medium text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition">
                        <i class="fas fa-sliders-h text-[10px]"></i>
                        <span x-text="filtrosAbiertos ? 'Menos' : 'Filtros'"></span>
                        @if ($filtrosCount > 0)
                            <span class="ml-0.5 rounded-full bg-meta-1 px-1 py-0.2 text-[9px] font-bold text-white leading-none">
                                {{ $filtrosCount }}
                            </span>
                        @endif
                    </button>

                    <button type="submit"
                            class="inline-flex items-center gap-1 rounded bg-primary py-1 px-3 text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm cursor-pointer">
                        <i class="fas fa-search text-[10px]"></i> Buscar
                    </button>

                    <a href="{{ route('flujos.plantillas.create') }}"
                       class="inline-flex items-center gap-1 rounded bg-meta-3 py-1 px-3 text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm whitespace-nowrap cursor-pointer ml-1">
                        <i class="fas fa-plus-circle text-[10px]"></i> Nueva Plantilla
                    </a>
                </div>
            </div>

            {{-- Panel Desplegable de Filtros Avanzados --}}
            <div x-show="filtrosAbiertos" x-cloak x-transition
                 class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 mt-2.5 pt-2.5 border-t border-stroke dark:border-strokedark">
                <div>
                    <label class="mb-1 block text-[9px] font-bold uppercase text-black dark:text-white">Estado de Plantilla</label>
                    <select name="activo" class="w-full rounded border border-stroke bg-transparent py-1 px-2 text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                        <option value="" class="bg-[#1c2434] text-gray-400">Todas las plantillas</option>
                        <option value="1" @selected(($filtros['activo'] ?? '') === '1') class="bg-[#1c2434] text-white">Activas</option>
                        <option value="0" @selected(($filtros['activo'] ?? '') === '0') class="bg-[#1c2434] text-white">Inactivas</option>
                    </select>
                </div>

                <div class="flex items-end justify-end">
                    @if (!empty(array_filter($filtros, fn ($v) => $v !== null && $v !== '')))
                        <a href="{{ route('flujos.plantillas.index') }}" 
                           class="inline-flex items-center gap-1 rounded border border-stroke py-1 px-2.5 text-xs font-medium text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition">
                            <i class="fas fa-times text-[10px]"></i> Limpiar Filtros
                        </a>
                    @endif
                </div>
            </div>
        </form>

        {{-- Chips Rápidos de Estado --}}
        <div class="mt-2 pt-2 border-t border-stroke dark:border-strokedark flex items-center gap-1 overflow-x-auto">
            <a href="{{ route('flujos.plantillas.index') }}" 
               class="inline-flex items-center gap-1 rounded-sm py-0.5 px-2 text-[10px] font-bold uppercase tracking-wider transition whitespace-nowrap {{ !isset($filtros['activo']) || $filtros['activo'] === '' ? 'bg-primary text-white shadow-sm' : 'bg-gray-2 text-black hover:bg-stroke dark:bg-meta-4/50 dark:text-white dark:hover:bg-meta-4' }}">
                <i class="fas fa-list text-[9px]"></i> Todas
            </a>
            <a href="{{ route('flujos.plantillas.index', ['activo' => '1']) }}" 
               class="inline-flex items-center gap-1 rounded-sm py-0.5 px-2 text-[10px] font-bold uppercase tracking-wider transition whitespace-nowrap {{ ($filtros['activo'] ?? '') === '1' ? 'bg-meta-3 text-white shadow-sm' : 'bg-gray-2 text-black hover:bg-stroke dark:bg-meta-4/50 dark:text-white dark:hover:bg-meta-4' }}">
                <i class="fas fa-check-circle text-[9px]"></i> Activas
            </a>
            <a href="{{ route('flujos.plantillas.index', ['activo' => '0']) }}" 
               class="inline-flex items-center gap-1 rounded-sm py-0.5 px-2 text-[10px] font-bold uppercase tracking-wider transition whitespace-nowrap {{ ($filtros['activo'] ?? '') === '0' ? 'bg-meta-1 text-white shadow-sm' : 'bg-gray-2 text-black hover:bg-stroke dark:bg-meta-4/50 dark:text-white dark:hover:bg-meta-4' }}">
                <i class="fas fa-ban text-[9px]"></i> Inactivas
            </a>
        </div>
    </div>

    {{-- ── Tabla de Plantillas de Alta Densidad ─────────────────────────── --}}
    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark mb-6">
        <div class="max-w-full overflow-x-auto">
            <table class="w-full table-auto border-collapse">
                <thead>
                    <tr class="bg-gray-2 text-left dark:bg-meta-4">
                        <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white min-w-[200px]">Plantilla / Descripción</th>
                        <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white text-center w-20">Etapas</th>
                        <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white text-center w-24">Tipos Doc.</th>
                        <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white text-center w-24">Instancias</th>
                        <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white text-center w-24">Estado</th>
                        <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white text-right w-24">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke dark:divide-strokedark">
                    @forelse ($plantillas as $plantilla)
                        <tr class="hover:bg-gray-2 dark:hover:bg-meta-4/40 transition">
                            
                            {{-- Columna 1: Nombre, Descripción y Metadata --}}
                            <td class="py-1.5 px-3">
                                <span class="text-xs font-bold text-black dark:text-white block truncate max-w-md" title="{{ $plantilla->nombre }}">
                                    {{ $plantilla->nombre }}
                                </span>
                                @if ($plantilla->descripcion)
                                    <span class="text-[9px] text-bodydark2 block truncate max-w-md" title="{{ $plantilla->descripcion }}">
                                        {{ $plantilla->descripcion }}
                                    </span>
                                @endif
                                <span class="text-[9px] text-bodydark2 block font-mono uppercase mt-0.5">
                                    v{{ $plantilla->version }} · Creada {{ $plantilla->created_at?->diffForHumans() }}
                                </span>
                            </td>

                            {{-- Columna 2: Etapas --}}
                            <td class="py-1.5 px-3 text-center whitespace-nowrap font-mono">
                                <span class="inline-flex rounded-full bg-primary/10 px-2 py-0.5 text-[9px] font-bold text-primary border border-primary/20">
                                    {{ $plantilla->stages_count }} etapas
                                </span>
                            </td>

                            {{-- Columna 3: Tipos Documento --}}
                            <td class="py-1.5 px-3 text-center whitespace-nowrap font-mono">
                                <span class="inline-flex rounded-full bg-gray-2 px-2 py-0.5 text-[9px] font-bold text-black dark:bg-meta-4 dark:text-white border border-stroke dark:border-strokedark">
                                    {{ $plantilla->document_types_count }} tipos
                                </span>
                            </td>

                            {{-- Columna 4: Instancias --}}
                            <td class="py-1.5 px-3 text-center whitespace-nowrap font-mono">
                                <span class="inline-flex rounded-full py-0.5 px-2 text-[9px] font-bold {{ $plantilla->instances_count > 0 ? 'bg-meta-6/10 text-meta-6 border border-meta-6/20' : 'text-bodydark2' }}">
                                    {{ $plantilla->instances_count }} activas
                                </span>
                            </td>

                            {{-- Columna 5: Estado --}}
                            <td class="py-1.5 px-3 text-center whitespace-nowrap">
                                @if ($plantilla->activo)
                                    <span class="inline-flex rounded-full bg-meta-3/10 px-2 py-0.5 text-[9px] font-bold uppercase text-meta-3 border border-meta-3/20">
                                        Activa
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-meta-1/10 px-2 py-0.5 text-[9px] font-bold uppercase text-meta-1 border border-meta-1/20">
                                        Inactiva
                                    </span>
                                @endif
                            </td>

                            {{-- Columna 6: Acciones Inline --}}
                            <td class="py-1.5 px-3 text-right whitespace-nowrap">
                                <div class="flex items-center gap-1 justify-end">
                                    
                                    {{-- Editar --}}
                                    <a href="{{ route('flujos.plantillas.edit', $plantilla) }}" 
                                       class="flex h-6 w-6 items-center justify-center rounded border border-stroke text-bodydark2 hover:border-primary hover:text-primary dark:border-strokedark transition"
                                       title="Editar estructura / datos">
                                        <i class="fas fa-pen text-[9px]"></i>
                                    </a>

                                    {{-- Toggle Activo --}}
                                    <form action="{{ route('flujos.plantillas.toggle-activo', $plantilla) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="flex h-6 w-6 items-center justify-center rounded border border-stroke {{ $plantilla->activo ? 'text-meta-3 hover:border-meta-3' : 'text-bodydark2 hover:border-primary' }} dark:border-strokedark transition"
                                                title="{{ $plantilla->activo ? 'Desactivar plantilla' : 'Activar plantilla' }}">
                                            <i class="fas {{ $plantilla->activo ? 'fa-toggle-on' : 'fa-toggle-off' }} text-xs"></i>
                                        </button>
                                    </form>

                                    {{-- Eliminar (Solo si no tiene instancias) --}}
                                    @if ($plantilla->instances_count === 0)
                                        <form action="{{ route('flujos.plantillas.destroy', $plantilla) }}" method="POST"
                                              onsubmit="return confirm('¿Eliminar la plantilla {{ $plantilla->nombre }}?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="flex h-6 w-6 items-center justify-center rounded border border-stroke text-meta-1 hover:border-meta-1 dark:border-strokedark transition"
                                                    title="Eliminar plantilla">
                                                <i class="fas fa-trash-alt text-[9px]"></i>
                                            </button>
                                        </form>
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center">
                                <div class="flex flex-col items-center text-bodydark2">
                                    <i class="fas fa-project-diagram text-xl mb-1"></i>
                                    <p class="text-xs">
                                        {{ !empty($filtros) ? 'No coinciden plantillas con los filtros aplicados.' : 'Aún no hay plantillas de flujo configuradas.' }}
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if ($plantillas->hasPages())
            <div class="border-t border-stroke px-4 py-2 dark:border-strokedark">
                {{ $plantillas->links() }}
            </div>
        @endif
    </div>
</x-app-layout>