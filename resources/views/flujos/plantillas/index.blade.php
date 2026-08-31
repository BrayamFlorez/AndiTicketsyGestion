{{-- resources/views/flujos/plantillas/index.blade.php --}}
<x-app-layout>
    <x-slot name="title">Plantillas de Flujo</x-slot>

    {{-- ── Cabecera y Acción Principal ────────────────────────────────── --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-title-md2 font-bold text-black dark:text-white flex items-center gap-2">
                <i class="fas fa-project-diagram text-primary"></i> Plantillas de Flujo
            </h2>
            <p class="text-xs text-bodydark2 mt-1">
                Diseño de procesos de aprobación y secuencias de etapas reutilizables
            </p>
        </div>

        <a href="{{ route('flujos.plantillas.create') }}" 
           class="inline-flex items-center justify-center gap-2 rounded bg-primary py-2 px-4 text-center text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm">
            <i class="fas fa-plus text-[10px]"></i> Nueva Plantilla
        </a>
    </div>

    {{-- Alerts Flash --}}
    @if (session('exito'))
        <div class="mb-6 flex w-full border-l-6 border-meta-3 bg-meta-3/10 px-7 py-3 shadow-md dark:bg-meta-3/20 dark:border-meta-3 rounded-sm">
            <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-meta-3 text-white shrink-0">
                <i class="fas fa-check"></i>
            </div>
            <div class="w-full">
                <h5 class="mb-1 font-bold text-meta-3">Operación Exitosa</h5>
                <p class="text-sm text-meta-3">{{ session('exito') }}</p>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 flex w-full border-l-6 border-meta-1 bg-meta-1/10 px-7 py-3 shadow-md dark:bg-meta-1/20 dark:border-meta-1 rounded-sm">
            <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-meta-1 text-white shrink-0">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="w-full">
                <h5 class="mb-1 font-bold text-meta-1">Atención</h5>
                <p class="text-sm text-meta-1">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    {{-- ── Formulario de Filtros (Card TailAdmin) ─────────────────────── --}}
    <div class="rounded-sm border border-stroke bg-white px-5 pt-5 pb-4 shadow-default dark:border-strokedark dark:bg-boxdark mb-6">
        <form method="GET" action="{{ route('flujos.plantillas.index') }}">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                
                {{-- Búsqueda --}}
                <div class="sm:col-span-2">
                    <label for="q" class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Buscar</label>
                    <input type="text" id="q" name="q" value="{{ $filtros['q'] ?? '' }}"
                           placeholder="Nombre o descripción de la plantilla..."
                           class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                </div>

                {{-- Estado --}}
                <div>
                    <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Estado</label>
                    <div class="relative z-20">
                        <select name="activo" class="w-full appearance-none rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white cursor-pointer">
                            <option value="" class="bg-[#1c2434] text-gray-400">Todos</option>
                            <option value="1" @selected(($filtros['activo'] ?? '') === '1') class="bg-[#1c2434] text-white">Activas</option>
                            <option value="0" @selected(($filtros['activo'] ?? '') === '0') class="bg-[#1c2434] text-white">Inactivas</option>
                        </select>
                        <span class="absolute top-1/2 right-3 -translate-y-1/2 z-30 pointer-events-none">
                            <i class="fas fa-chevron-down text-[10px] text-bodydark2"></i>
                        </span>
                    </div>
                </div>

            </div>

            <div class="flex items-center justify-end gap-2 mt-4 border-t border-stroke pt-3 dark:border-strokedark">
                @if (!empty(array_filter($filtros, fn ($v) => $v !== null && $v !== '')))
                    <a href="{{ route('flujos.plantillas.index') }}" 
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

    {{-- ── Tabla de Plantillas ────────────────────────────────────────── --}}
    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark mb-6">
        <div class="max-w-full overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-gray-2 text-left dark:bg-meta-4">
                        <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase">Plantilla</th>
                        <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase text-center w-24">Etapas</th>
                        <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase text-center w-28">Tipos Doc.</th>
                        <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase text-center w-28">Instancias</th>
                        <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase text-center w-28">Estado</th>
                        <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase text-center w-28">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke dark:divide-strokedark">
                    @forelse ($plantillas as $plantilla)
                        <tr class="hover:bg-gray-2 dark:hover:bg-meta-4/50 transition">
                            
                            {{-- Columna 1: Plantilla --}}
                            <td class="py-3.5 px-4">
                                <span class="text-xs font-bold text-black dark:text-white block">{{ $plantilla->nombre }}</span>
                                @if ($plantilla->descripcion)
                                    <span class="text-[10px] text-bodydark2 block truncate max-w-sm mt-0.5">{{ $plantilla->descripcion }}</span>
                                @endif
                                <span class="text-[10px] text-bodydark2 block font-mono uppercase mt-0.5">
                                    v{{ $plantilla->version }} · Creada {{ $plantilla->created_at?->diffForHumans() }}
                                </span>
                            </td>

                            {{-- Columna 2: Etapas --}}
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex rounded-full bg-primary/10 px-2.5 py-0.5 text-[10px] font-bold text-primary">
                                    {{ $plantilla->stages_count }}
                                </span>
                            </td>

                            {{-- Columna 3: Tipos Documento --}}
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex rounded-full bg-meta-2 px-2.5 py-0.5 text-[10px] font-bold text-black dark:bg-meta-4 dark:text-white">
                                    {{ $plantilla->document_types_count }}
                                </span>
                            </td>

                            {{-- Columna 4: Instancias --}}
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex rounded-full py-0.5 px-2.5 text-[10px] font-bold {{ $plantilla->instances_count > 0 ? 'bg-meta-6/10 text-meta-6' : 'bg-gray-2 text-bodydark2 dark:bg-meta-4' }}">
                                    {{ $plantilla->instances_count }}
                                </span>
                            </td>

                            {{-- Columna 5: Estado --}}
                            <td class="py-3.5 px-4 text-center">
                                @if ($plantilla->activo)
                                    <span class="inline-flex rounded-full bg-meta-3/10 px-2.5 py-0.5 text-[10px] font-bold uppercase text-meta-3">
                                        Activa
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-meta-1/10 px-2.5 py-0.5 text-[10px] font-bold uppercase text-meta-1">
                                        Inactiva
                                    </span>
                                @endif
                            </td>

                            {{-- Columna 6: Acciones --}}
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    {{-- Editar --}}
                                    <a href="{{ route('flujos.plantillas.edit', $plantilla) }}" 
                                       class="flex h-7 w-7 items-center justify-center rounded border border-stroke text-bodydark2 hover:border-primary hover:text-primary dark:border-strokedark transition"
                                       title="Editar estructura/datos">
                                        <i class="fas fa-pen text-[10px]"></i>
                                    </a>

                                    {{-- Toggle Activo --}}
                                    <form action="{{ route('flujos.plantillas.toggle-activo', $plantilla) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="flex h-7 w-7 items-center justify-center rounded border border-stroke {{ $plantilla->activo ? 'text-meta-3 hover:border-meta-3' : 'text-bodydark2 hover:border-primary' }} dark:border-strokedark transition"
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
                                                    class="flex h-7 w-7 items-center justify-center rounded border border-stroke text-meta-1 hover:border-meta-1 dark:border-strokedark transition"
                                                    title="Eliminar plantilla">
                                                <i class="fas fa-trash-alt text-[10px]"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center">
                                <div class="flex flex-col items-center text-bodydark2">
                                    <i class="fas fa-project-diagram text-3xl mb-2"></i>
                                    <p class="text-xs">Aún no hay plantillas de flujo configuradas.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if ($plantillas->hasPages())
            <div class="border-t border-stroke py-3 px-4 dark:border-strokedark">
                {{ $plantillas->links() }}
            </div>
        @endif
    </div>
</x-app-layout>