<x-app-layout>
    <x-slot name="title">Mi Bandeja de Flujos</x-slot>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
        <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
    @endpush

    <div class="db-header mb-6 flex items-center justify-between flex-wrap gap-3">
        <div>
            <h2 class="text-2xl font-black text-white uppercase tracking-tighter">Mi Bandeja</h2>
            <p class="text-xs text-gray-400 font-medium uppercase tracking-widest">
                Flujos de trabajo · buscar por título, código, iniciador o archivo adjunto
            </p>
        </div>

        @can('flujos.iniciar')
            <a href="{{ route('flujos.instancias.create') }}" class="db-btn solid py-2.5 px-4 shadow-lg shadow-[#007acc]/20">
                <i class="fas fa-play-circle mr-2 text-sm"></i> Iniciar Flujo
            </a>
        @endcan
    </div>

    @if (session('exito'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-green-500/10 border border-green-500/30 text-green-300 text-sm">
            <i class="fas fa-check-circle mr-2"></i> {{ session('exito') }}
        </div>
    @endif

    @php
        $filtrosActivos = array_filter($filtros, fn($v) => $v !== null && $v !== '');
    @endphp

    {{-- Formulario de búsqueda + filtros --}}
    <form method="GET" action="{{ route('flujos.instancias.index') }}" class="db-card mb-4" x-data="{ expandido: {{ !empty($filtrosActivos) && (isset($filtrosActivos['template_id']) || isset($filtrosActivos['document_type_id']) || isset($filtrosActivos['desde']) || isset($filtrosActivos['hasta'])) ? 'true' : 'false' }} }">
        <input type="hidden" name="tab" value="{{ $tabActivo }}">

        <div class="form-card-body" style="padding: 1rem 1.25rem;">
            {{-- Barra principal --}}
            <div class="flex items-center gap-2 flex-wrap">
                <div class="relative flex-1 min-w-[240px]">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-xs"></i>
                    <input type="text" name="q" value="{{ $filtros['q'] ?? '' }}"
                        placeholder="Código, título, iniciador o nombre de archivo..." class="form-input"
                        style="padding-left: 2.25rem;">
                </div>
                <button type="button" class="db-btn ghost" @click="expandido = !expandido">
                    <i class="fas fa-sliders-h mr-1"></i>
                    <span x-text="expandido ? 'Menos' : 'Más filtros'"></span>
                </button>
                <button type="submit" class="db-btn solid">
                    <i class="fas fa-search"></i> Buscar
                </button>
                @if (!empty($filtrosActivos))
                    <a href="{{ route('flujos.instancias.index', ['tab' => $tabActivo]) }}" class="db-btn ghost"
                        title="Limpiar filtros">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>

            {{-- Filtros extendidos --}}
            <div x-show="expandido" x-transition
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mt-3 pt-3 border-t border-gray-800">
                <div class="form-group">
                    <label class="form-label text-[10px]">Plantilla</label>
                    <select name="template_id" class="form-select text-xs">
                        <option value="">Todas</option>
                        @foreach ($plantillas as $p)
                            <option value="{{ $p->id }}" @selected(($filtros['template_id'] ?? '') == $p->id)>{{ $p->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label text-[10px]">Tipo de documento</label>
                    <select name="document_type_id" class="form-select text-xs">
                        <option value="">Todos</option>
                        @foreach ($tiposDocumento as $t)
                            <option value="{{ $t->id }}" @selected(($filtros['document_type_id'] ?? '') == $t->id)>{{ $t->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label text-[10px]">Iniciado desde</label>
                    <input type="date" name="desde" value="{{ $filtros['desde'] ?? '' }}"
                        class="form-input text-xs">
                </div>
                <div class="form-group">
                    <label class="form-label text-[10px]">Iniciado hasta</label>
                    <input type="date" name="hasta" value="{{ $filtros['hasta'] ?? '' }}"
                        class="form-input text-xs">
                </div>
            </div>

            {{-- Chip indicador de filtros activos --}}
            @if (!empty($filtrosActivos))
                <div class="text-[10px] text-gray-500 mt-3 pt-3 border-t border-gray-800">
                    <i class="fas fa-info-circle mr-1"></i>
                    Mostrando resultados filtrados. {{ $instancias->total() }} resultado(s) encontrado(s).
                </div>
            @endif
        </div>
    </form>

    {{-- Tabs --}}
    <div class="mb-4 flex flex-wrap items-center gap-1 border-b border-gray-800">
        @foreach ($tabsDisponibles as $key => $tab)
            @php $activa = $key === $tabActivo; @endphp
            <a href="{{ route('flujos.instancias.index', array_merge($filtros, ['tab' => $key])) }}"
                class="px-4 py-2.5 text-[11px] font-black uppercase tracking-widest border-b-2 transition-colors
                      {{ $activa ? 'border-[#007acc] text-white' : 'border-transparent text-gray-500 hover:text-gray-300' }}">
                <i class="fas {{ $tab['icono'] }} mr-1.5"></i> {{ $tab['label'] }}
            </a>
        @endforeach
    </div>

    <div class="db-card">
        <div class="overflow-x-auto">
            <table class="db-table w-full">
                <thead>
                    <tr>
                        <th class="px-6 py-4">Código / Título</th>
                        <th class="px-6 py-4">Tipo</th>
                        <th class="px-6 py-4">Etapa Actual</th>
                        <th class="px-6 py-4">Iniciador</th>
                        <th class="px-6 py-4 text-center">Estado</th>
                        <th class="px-6 py-4 text-center">Iniciado</th>
                        <th class="px-6 py-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($instancias as $instancia)
                        <tr class="group transition-colors hover:bg-[var(--db-surface-2)]">
                            <td class="px-6 py-4">
                                <p class="text-[10px] text-gray-500 font-mono uppercase tracking-wider">{{ $instancia->codigo }}</p>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="text-sm font-bold text-white">{{ $instancia->titulo }}</p>
                                    @if ($instancia->attachments_count > 0)
                                        <span class="text-[10px] text-gray-400 flex items-center gap-1 px-1.5 py-0.5 rounded bg-[#1a1a1b] border border-gray-800" title="Adjuntos totales">
                                            <i class="fas fa-paperclip"></i> {{ $instancia->attachments_count }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-[10px] text-gray-600 mt-0.5 uppercase tracking-wider">
                                    Plantilla: {{ $instancia->template->nombre }} · Rev. {{ $instancia->revision }}
                                </p>
                            </td>
                            <td class="px-6 py-4">
                                @if ($instancia->documentType)
                                    <div class="text-xs text-white flex items-center gap-1.5">
                                        <i
                                            class="fas {{ $instancia->documentType->icono ?: 'fa-file-alt' }} text-gray-500"></i>
                                        {{ $instancia->documentType->nombre }}
                                    </div>
                                @else
                                    <span class="db-sub">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if ($instancia->etapaActual)
                                    <div class="text-xs text-white flex items-center gap-1.5">
                                        <i class="fas fa-map-marker-alt text-gray-500"></i>
                                        {{ $instancia->etapaActual->nombre }}
                                    </div>
                                @else
                                    <span class="db-sub">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="db-avatar text-xs w-7 h-7">
                                        {{ substr($instancia->iniciador->name, 0, 1) }}
                                    </div>
                                    <span class="text-xs text-white">{{ $instancia->iniciador->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @include('flujos.instancias.partials._badge_estado', [
                                    'estado' => $instancia->estado,
                                ])
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-[10px] text-gray-500">
                                    {{ $instancia->iniciado_en?->diffForHumans() }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div
                                    class="flex justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('flujos.instancias.show', $instancia) }}"
                                        class="db-btn outline" title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="db-empty py-8 text-center">
                                    <i class="fas fa-inbox text-4xl mb-3 text-gray-700"></i>
                                    <p class="text-gray-400">
                                        No hay flujos en esta bandeja.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($instancias->hasPages())
            <div class="px-4 py-3 border-t border-gray-800">
                {{ $instancias->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
