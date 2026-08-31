{{-- resources/views/flujos/instancias/index.blade.php --}}
<x-app-layout>
    <x-slot name="title">Mi Bandeja de Flujos</x-slot>

    {{-- ── Cabecera y Acción Principal ────────────────────────────────── --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-title-md2 font-bold text-black dark:text-white flex items-center gap-2">
                <i class="fas fa-project-diagram text-primary"></i> Mi Bandeja de Flujos
            </h2>
            <p class="text-xs text-bodydark2 mt-1">
                Flujos de trabajo · Búsqueda por título, código, iniciador o adjuntos
            </p>
        </div>

        @can('flujos.iniciar')
            <a href="{{ route('flujos.instancias.create') }}"
                class="inline-flex items-center justify-center gap-2 rounded bg-primary py-2 px-4 text-center text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm">
                <i class="fas fa-play-circle text-[10px]"></i> Iniciar Flujo
            </a>
        @endcan
    </div>

    {{-- Alert de éxito --}}
    @if (session('exito'))
        <div
            class="mb-6 flex w-full border-l-6 border-meta-3 bg-meta-3/10 px-7 py-3 shadow-md dark:bg-meta-3/20 dark:border-meta-3 rounded-sm">
            <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-meta-3 text-white shrink-0">
                <i class="fas fa-check"></i>
            </div>
            <div class="w-full">
                <h5 class="mb-1 font-bold text-meta-3">Operación Exitosa</h5>
                <p class="text-sm text-meta-3">{{ session('exito') }}</p>
            </div>
        </div>
    @endif

    @php
        $filtrosActivos = array_filter($filtros, fn($v) => $v !== null && $v !== '');
    @endphp

    {{-- ── Formulario de Búsqueda y Filtros Extendidos ──────────────────── --}}
    <div class="rounded-sm border border-stroke bg-white px-5 pt-5 pb-4 shadow-default dark:border-strokedark dark:bg-boxdark mb-6"
        x-data="{ expandido: {{ !empty($filtrosActivos) && (isset($filtrosActivos['template_id']) || isset($filtrosActivos['document_type_id']) || isset($filtrosActivos['desde']) || isset($filtrosActivos['hasta'])) ? 'true' : 'false' }} }">

        <form method="GET" action="{{ route('flujos.instancias.index') }}">
            <input type="hidden" name="tab" value="{{ $tabActivo }}">

            {{-- Barra principal --}}
            <div class="flex items-center gap-3 flex-wrap">
                <div class="relative flex-1 min-w-[240px]">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-bodydark2">
                        <i class="fas fa-search text-xs"></i>
                    </span>
                    <input type="text" name="q" value="{{ $filtros['q'] ?? '' }}"
                        placeholder="Código, referencia, título, iniciador, archivo o etiqueta..."
                        class="w-full rounded border border-stroke bg-transparent py-1.5 pl-9 pr-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                </div>

                <button type="button" @click="expandido = !expandido"
                    class="inline-flex items-center justify-center gap-1.5 rounded border border-stroke py-1.5 px-3 text-xs font-medium text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition">
                    <i class="fas fa-sliders-h text-[10px]"></i>
                    <span x-text="expandido ? 'Menos Filtros' : 'Más Filtros'"></span>
                </button>

                <button type="submit"
                    class="inline-flex items-center justify-center gap-1.5 rounded bg-primary py-1.5 px-4 text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm cursor-pointer">
                    <i class="fas fa-search text-[10px]"></i> Buscar
                </button>

                @if (!empty($filtrosActivos))
                    <a href="{{ route('flujos.instancias.index', ['tab' => $tabActivo]) }}"
                        class="inline-flex items-center justify-center rounded border border-stroke p-2 text-bodydark2 hover:border-primary hover:text-primary dark:border-strokedark transition"
                        title="Limpiar filtros">
                        <i class="fas fa-times text-xs"></i>
                    </a>
                @endif
            </div>

            {{-- Filtros extendidos --}}
            <div x-show="expandido" x-collapse
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-4 pt-4 border-t border-stroke dark:border-strokedark">
                <div>
                    <label
                        class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Plantilla</label>
                    <div class="relative z-20">
                        <select name="template_id"
                            class="w-full appearance-none rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white cursor-pointer">
                            <option value="" class="bg-[#1c2434] text-gray-400">Todas</option>
                            @foreach ($plantillas as $p)
                                <option value="{{ $p->id }}" @selected(($filtros['template_id'] ?? '') == $p->id)
                                    class="bg-[#1c2434] text-white">
                                    {{ $p->nombre }}
                                </option>
                            @endforeach
                        </select>
                        <span class="absolute top-1/2 right-3 -translate-y-1/2 z-30 pointer-events-none">
                            <i class="fas fa-chevron-down text-[10px] text-bodydark2"></i>
                        </span>
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Tipo de
                        Documento</label>
                    <div class="relative z-20">
                        <select name="document_type_id"
                            class="w-full appearance-none rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white cursor-pointer">
                            <option value="" class="bg-[#1c2434] text-gray-400">Todos</option>
                            @foreach ($tiposDocumento as $t)
                                <option value="{{ $t->id }}" @selected(($filtros['document_type_id'] ?? '') == $t->id)
                                    class="bg-[#1c2434] text-white">
                                    {{ $t->nombre }}
                                </option>
                            @endforeach
                        </select>
                        <span class="absolute top-1/2 right-3 -translate-y-1/2 z-30 pointer-events-none">
                            <i class="fas fa-chevron-down text-[10px] text-bodydark2"></i>
                        </span>
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Iniciado
                        Desde</label>
                    <input type="date" name="desde" value="{{ $filtros['desde'] ?? '' }}"
                        class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                </div>

                <div>
                    <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Iniciado
                        Hasta</label>
                    <input type="date" name="hasta" value="{{ $filtros['hasta'] ?? '' }}"
                        class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                </div>
            </div>

            @if (!empty($filtrosActivos))
                <div
                    class="text-[10px] text-bodydark2 mt-3 pt-2 border-t border-stroke dark:border-strokedark flex items-center gap-1">
                    <i class="fas fa-info-circle text-primary"></i>
                    Resultados filtrados. {{ $instancias->total() }} registro(s) encontrado(s).
                </div>
            @endif
        </form>
    </div>

    {{-- ── Pestañas de Navegación ───────────────────────────────────────── --}}
    <div class="mb-6 flex flex-wrap items-center gap-2 border-b border-stroke dark:border-strokedark pb-2">
        @foreach ($tabsDisponibles as $key => $tab)
            @php $activa = $key === $tabActivo; @endphp
            <a href="{{ route('flujos.instancias.index', array_merge($filtros, ['tab' => $key])) }}"
                class="inline-flex items-center gap-2 rounded-sm py-2 px-4 text-xs font-bold uppercase tracking-wider transition {{ $activa ? 'bg-primary text-white shadow-sm' : 'bg-white text-black hover:bg-gray border border-stroke dark:bg-meta-4 dark:text-white dark:border-strokedark dark:hover:bg-opacity-80' }}">
                <i class="fas {{ $tab['icono'] }}"></i> {{ $tab['label'] }}
            </a>
        @endforeach
    </div>

    {{-- ── Tabla de Instancias de Flujos ────────────────────────────────── --}}
    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark mb-6">
        <div class="max-w-full overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-gray-2 text-left dark:bg-meta-4">
                        <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase">Código / Título
                        </th>
                        <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase">Tipo</th>
                        <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase">Etapa Actual</th>
                        <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase">Iniciador</th>
                        <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase text-center">
                            Estado</th>
                        <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase text-center">
                            Iniciado</th>
                        <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase text-center w-16">
                            Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke dark:divide-strokedark">
                    @forelse ($instancias as $instancia)
                        <tr class="hover:bg-gray-2 dark:hover:bg-meta-4/50 transition">

                            {{-- Código, Título y Etiquetas --}}
                            <td class="py-3.5 px-4">
                                <span class="text-[10px] text-bodydark2 font-mono uppercase block">
                                    {{ $instancia->codigo }}
                                </span>

                                <div class="flex items-center gap-2 flex-wrap">
                                    <a href="{{ route('flujos.instancias.show', $instancia) }}"
                                        class="text-xs font-bold text-black dark:text-white hover:text-primary transition block">
                                        {{ $instancia->titulo }}
                                    </a>

                                    <div class="text-xs font-mono text-primary">{{ $instancia->codigo }}</div>
                                    @if ($instancia->referencia_externa)
                                        <div class="text-[10px] font-mono text-primary/70 mt-0.5" title="Referencia">
                                            <i class="fas fa-hashtag" style="font-size: 8px;"></i>
                                            {{ $instancia->referencia_externa }}
                                        </div>
                                    @endif

                                    @php
                                        $etiquetas = $instancia->steps->pluck('codigo_referencia')->filter()->unique();
                                    @endphp
                                    @if ($etiquetas->isNotEmpty())
                                        <div class="flex flex-wrap items-center gap-1">
                                            @foreach ($etiquetas->take(3) as $etiqueta)
                                                <span
                                                    class="inline-flex items-center rounded bg-primary/10 border border-primary/20 px-1.5 py-0.5 text-[9px] font-mono font-bold text-primary">
                                                    <i class="fas fa-tag text-[8px] mr-1"></i>{{ $etiqueta }}
                                                </span>
                                            @endforeach
                                            @if ($etiquetas->count() > 3)
                                                <span
                                                    class="text-[9px] text-bodydark2 font-mono">+{{ $etiquetas->count() - 3 }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <span class="text-[10px] text-bodydark2 block mt-0.5">
                                    Plantilla: {{ $instancia->template->nombre }} · Rev. {{ $instancia->revision }}
                                </span>
                            </td>

                            {{-- Tipo de Documento --}}
                            <td class="py-3.5 px-4">
                                @if ($instancia->documentType)
                                    <div class="text-xs text-black dark:text-white flex items-center gap-1.5">
                                        <i
                                            class="fas {{ $instancia->documentType->icono ?: 'fa-file-alt' }} text-bodydark2"></i>
                                        {{ $instancia->documentType->nombre }}
                                    </div>
                                @else
                                    <span class="text-xs text-bodydark2">—</span>
                                @endif
                            </td>

                            {{-- Etapa Actual & SLA --}}
                            <td class="py-3.5 px-4">
                                @if ($instancia->etapaActual)
                                    <div
                                        class="text-xs text-black dark:text-white font-medium flex items-center gap-1.5 flex-wrap">
                                        <i class="fas fa-map-marker-alt text-primary"></i>
                                        {{ $instancia->etapaActual->nombre }}
                                        @if ($instancia->etapaActual->codigo_externo)
                                            <span
                                                class="inline-flex rounded bg-gray px-1.5 py-0.5 text-[9px] font-mono text-black dark:bg-meta-4 dark:text-white">
                                                {{ $instancia->etapaActual->codigo_externo }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="mt-1">
                                        @include('flujos.instancias.partials._sla_badge', [
                                            'step' => $instancia->currentStep,
                                        ])
                                    </div>
                                @else
                                    <span class="text-xs text-bodydark2">—</span>
                                @endif
                            </td>

                            {{-- Iniciador --}}
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="flex h-6 w-6 items-center justify-center rounded-full bg-primary/20 text-primary font-bold text-[10px] shrink-0">
                                        {{ substr($instancia->iniciador->name, 0, 1) }}
                                    </div>
                                    <span class="text-xs text-black dark:text-white truncate max-w-[120px]">
                                        {{ $instancia->iniciador->name }}
                                    </span>
                                </div>
                            </td>

                            {{-- Estado --}}
                            <td class="py-3.5 px-4 text-center">
                                @include('flujos.instancias.partials._badge_estado', [
                                    'estado' => $instancia->estado,
                                ])
                            </td>

                            {{-- Fecha de Inicio --}}
                            <td class="py-3.5 px-4 text-center text-xs text-bodydark2 font-mono">
                                {{ $instancia->iniciado_en?->diffForHumans() }}
                            </td>

                            {{-- Acción --}}
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('flujos.instancias.show', $instancia) }}"
                                    class="inline-flex h-7 w-7 items-center justify-center rounded border border-stroke text-bodydark2 hover:border-primary hover:text-primary dark:border-strokedark transition"
                                    title="Ver detalle">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center">
                                <div class="flex flex-col items-center text-bodydark2">
                                    <i class="fas fa-inbox text-3xl mb-2"></i>
                                    <span class="text-xs">No hay flujos en esta bandeja.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($instancias->hasPages())
            <div class="border-t border-stroke py-3 px-4 dark:border-strokedark">
                {{ $instancias->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
