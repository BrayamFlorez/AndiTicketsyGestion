{{-- resources/views/flujos/instancias/index.blade.php --}}
<x-app-layout>
    <x-slot name="title">Mi Bandeja de Flujos</x-slot>

    @php
        $instancias = $instancias ?? $flujos ?? collect();
        $mostrarCodigoInterno = \App\Models\Setting::get('flujos.mostrar_codigo_interno', true);
        $filtrosActivos = collect($filtros ?? [])->filter(fn ($v) => $v !== null && $v !== '')->count();
        
        $tabsDisponibles = $tabsDisponibles ?? [
            'pendientes' => ['label' => 'Mis Pendientes',       'icono' => 'fa-user'],
            'iniciados'  => ['label' => 'Iniciados por Mí',   'icono' => 'fa-user'],
            'correccion' => ['label' => 'Requieren Corrección','icono' => 'fa-exclamation-triangle'],
            'cerrados'   => ['label' => 'Cerrados',            'icono' => 'fa-check'],
        ];
        if (auth()->user()->can('flujos.ver-todos')) {
            $tabsDisponibles['todos'] = ['label' => 'Todos', 'icono' => 'fa-layer-group'];
        }
        $tabActivo = $tabActivo ?? 'pendientes';
    @endphp

    {{-- Alert de éxito --}}
    @if (session('exito'))
        <div class="mb-3 flex w-full border-l-4 border-meta-3 bg-meta-3/10 px-4 py-2 shadow-sm dark:bg-meta-3/20 rounded-sm">
            <div class="mr-2 flex h-5 w-5 items-center justify-center rounded-full bg-meta-3 text-white shrink-0">
                <i class="fas fa-check text-[10px]"></i>
            </div>
            <p class="text-xs text-meta-3 font-medium">{{ session('exito') }}</p>
        </div>
    @endif

    {{-- ── Header + Búsqueda + Pestañas Integradas (Ultracompacto) ───────────── --}}
    <div class="rounded-sm border border-stroke bg-white p-3 shadow-default dark:border-strokedark dark:bg-boxdark mb-3"
        x-data="{ expandido: {{ $filtrosActivos > 1 ? 'true' : 'false' }} }">

        <form method="GET" action="{{ route('flujos.instancias.index') }}">
            <input type="hidden" name="tab" value="{{ $tabActivo }}">

            {{-- Fila Superior: Título + Buscador + Botón Crear --}}
            <div class="flex items-center gap-2 flex-wrap lg:flex-nowrap">
                
                <div class="flex items-center gap-1.5 shrink-0 mr-1">
                    <i class="fas fa-project-diagram text-primary text-sm"></i>
                    <h2 class="text-xs font-bold text-black dark:text-white uppercase tracking-wider whitespace-nowrap">
                        Mi Bandeja
                    </h2>
                </div>

                <div class="relative flex-1 min-w-[200px]">
                    <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-bodydark2 pointer-events-none">
                        <i class="fas fa-search text-[11px]"></i>
                    </span>
                    <input type="text" name="q" value="{{ $filtros['q'] ?? '' }}"
                        placeholder="Buscar por código, referencia, título o archivo..."
                        class="w-full rounded border border-stroke bg-transparent py-1 pl-8 pr-2 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                </div>

                <div class="flex items-center gap-1.5 shrink-0">
                    <button type="button" @click="expandido = !expandido"
                        class="relative inline-flex items-center gap-1 rounded border border-stroke py-1 px-2.5 text-xs font-medium text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition">
                        <i class="fas fa-sliders-h text-[10px]"></i>
                        <span x-text="expandido ? 'Menos' : 'Filtros'"></span>
                        @if ($filtrosActivos > 1)
                            <span class="ml-0.5 rounded-full bg-meta-1 px-1 py-0.2 text-[9px] font-bold text-white leading-none">
                                {{ $filtrosActivos - 1 }}
                            </span>
                        @endif
                    </button>

                    <button type="submit"
                        class="inline-flex items-center gap-1 rounded bg-primary py-1 px-3 text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm cursor-pointer">
                        <i class="fas fa-search text-[10px]"></i> Buscar
                    </button>

                    @if (!empty($filtrosActivos))
                        <a href="{{ route('flujos.instancias.index', ['tab' => $tabActivo]) }}"
                            class="inline-flex items-center justify-center rounded border border-stroke p-1 text-bodydark2 hover:border-primary hover:text-primary dark:border-strokedark transition"
                            title="Limpiar filtros">
                            <i class="fas fa-times text-xs"></i>
                        </a>
                    @endif

                    @can('flujos.iniciar')
                        <a href="{{ route('flujos.instancias.create') }}"
                            class="inline-flex items-center gap-1 rounded bg-primary py-1 px-3 text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm whitespace-nowrap ml-1">
                            <i class="fas fa-play-circle text-[10px]"></i> Iniciar Flujo
                        </a>
                    @endcan
                </div>
            </div>

            {{-- Panel Desplegable de Filtros --}}
            <div x-show="expandido" x-cloak x-transition
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5 mt-2.5 pt-2.5 border-t border-stroke dark:border-strokedark">
                <div>
                    <label class="mb-1 block text-[9px] font-bold uppercase text-black dark:text-white">Plantilla</label>
                    <select name="template_id" class="w-full rounded border border-stroke bg-transparent py-1 px-2 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                        <option value="" class="bg-[#1c2434] text-gray-400">Todas las plantillas</option>
                        @foreach ($plantillas ?? [] as $p)
                            <option value="{{ $p->id }}" @selected(($filtros['template_id'] ?? '') == $p->id) class="bg-[#1c2434] text-white">{{ $p->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-[9px] font-bold uppercase text-black dark:text-white">Tipo Documento</label>
                    <select name="document_type_id" class="w-full rounded border border-stroke bg-transparent py-1 px-2 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                        <option value="" class="bg-[#1c2434] text-gray-400">Todos los tipos</option>
                        @foreach ($tiposDocumento ?? [] as $t)
                            <option value="{{ $t->id }}" @selected(($filtros['document_type_id'] ?? '') == $t->id) class="bg-[#1c2434] text-white">{{ $t->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-[9px] font-bold uppercase text-black dark:text-white">Desde</label>
                    <input type="date" name="desde" value="{{ $filtros['desde'] ?? '' }}" class="w-full rounded border border-stroke bg-transparent py-1 px-2 text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                </div>
                <div>
                    <label class="mb-1 block text-[9px] font-bold uppercase text-black dark:text-white">Hasta</label>
                    <input type="date" name="hasta" value="{{ $filtros['hasta'] ?? '' }}" class="w-full rounded border border-stroke bg-transparent py-1 px-2 text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                </div>
            </div>
        </form>

        {{-- Pestañas Integradas (Pills Compactos) --}}
        <div class="mt-2.5 pt-2 border-t border-stroke dark:border-strokedark flex items-center gap-1 overflow-x-auto">
            @foreach ($tabsDisponibles as $key => $tab)
                @php 
                    $activa = $key === $tabActivo;
                    $count = $contadores[$key] ?? null;
                @endphp
                <a href="{{ route('flujos.instancias.index', array_merge($filtros ?? [], ['tab' => $key])) }}"
                    class="inline-flex items-center gap-1 rounded-sm py-1 px-2.5 text-[10px] font-bold uppercase tracking-wider transition whitespace-nowrap {{ $activa ? 'bg-primary text-white shadow-sm' : 'bg-gray-2 text-black hover:bg-stroke dark:bg-meta-4/50 dark:text-white dark:hover:bg-meta-4' }}">
                    <i class="fas {{ $tab['icono'] }} text-[9px]"></i> 
                    {{ $tab['label'] }}
                    @if ($count)
                        <span class="ml-0.5 rounded-full bg-meta-1 px-1.5 py-0.2 text-[8px] font-extrabold text-white leading-none">
                            {{ $count }}
                        </span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>

    {{-- ── Tabla Sincronizada (Mismo Número de Columnas que las Filas) ────── --}}
    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark mb-6">
        <div class="max-w-full overflow-x-auto">
            <table class="w-full table-auto border-collapse">
                <thead>
                    <tr class="bg-gray-2 text-left dark:bg-meta-4">
                        @if ($mostrarCodigoInterno)
                            <th class="py-2 px-3 font-bold text-black dark:text-white text-[11px] uppercase w-28">Código</th>
                        @endif
                        <th class="py-2 px-3 font-bold text-black dark:text-white text-[11px] uppercase min-w-[320px]">Trámite / Iniciador</th>
                        <th class="py-2 px-3 font-bold text-black dark:text-white text-[11px] uppercase min-w-[180px]">Etapa / SLA</th>
                        <th class="py-2 px-3 font-bold text-black dark:text-white text-[11px] uppercase text-center w-28">Estado</th>
                        <th class="py-2 px-3 font-bold text-black dark:text-white text-[11px] uppercase text-right w-20">Fecha</th>
                        <th class="py-2 px-3 font-bold text-black dark:text-white text-[11px] uppercase text-center w-12">Ver</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke dark:divide-strokedark">
                    @forelse ($instancias as $instancia)
                        @include('dashboard.partials._flujo_row', [
                            'flujo' => $instancia,
                            'mostrarCodigoInterno' => $mostrarCodigoInterno
                        ])
                    @empty
                        <tr>
                            <td colspan="{{ $mostrarCodigoInterno ? 6 : 5 }}" class="py-6 text-center">
                                <div class="flex flex-col items-center text-bodydark2">
                                    <i class="fas fa-inbox text-xl mb-1"></i>
                                    <p class="text-xs">
                                        @if (!empty($filtros['q']))
                                            Sin resultados para "<strong>{{ $filtros['q'] }}</strong>".
                                        @else
                                            No hay trámites en esta bandeja.
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if (method_exists($instancias, 'hasPages') && $instancias->hasPages())
            <div class="border-t border-stroke px-4 py-2 dark:border-strokedark">
                {{ $instancias->links() }}
            </div>
        @endif
    </div>
</x-app-layout>