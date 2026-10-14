{{-- resources/views/tickets/partials/index_tecnico.blade.php --}}

@php
    $filtrosCount = collect($filtros ?? [])->filter(fn($v) => $v !== null && $v !== '')->count();
@endphp

{{-- ── Header + Buscador + Filtros Integrados ───────────────────────────── --}}
<div class="rounded-sm border border-stroke bg-white p-3 shadow-default dark:border-strokedark dark:bg-boxdark mb-3"
     x-data="{ filtrosAbiertos: {{ $filtrosCount > 0 ? 'true' : 'false' }} }">

    <form method="GET" action="{{ route('tickets.index') }}">
        <div class="flex items-center gap-2 flex-wrap lg:flex-nowrap">
            
            {{-- Título --}}
            <div class="flex items-center gap-1.5 shrink-0 mr-1">
                <i class="fas fa-headset text-primary text-sm"></i>
                <h2 class="text-xs font-bold text-black dark:text-white uppercase tracking-wider whitespace-nowrap">
                    Mis Asignaciones
                </h2>
                <span class="font-mono text-[10px] text-bodydark2">({{ $tickets->total() }})</span>
            </div>

            {{-- Input Buscador --}}
            <div class="relative flex-1 min-w-[200px]">
                <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-bodydark2 pointer-events-none">
                    <i class="fas fa-search text-[11px]"></i>
                </span>
                <input type="text" name="q" value="{{ $filtros['q'] ?? '' }}"
                       placeholder="Buscar por ID, título o solicitante..."
                       class="w-full rounded border border-stroke bg-transparent py-1 pl-8 pr-7 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                @if (!empty($filtros['q']))
                    <a href="{{ route('tickets.index') }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-bodydark2 hover:text-meta-1">
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
            </div>
        </div>

        {{-- Panel Desplegable Filtros --}}
        <div x-show="filtrosAbiertos" x-cloak x-transition
             class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 mt-2.5 pt-2.5 border-t border-stroke dark:border-strokedark">
            <div>
                <label class="mb-1 block text-[9px] font-bold uppercase text-black dark:text-white">Estado</label>
                <select name="estado" class="w-full rounded border border-stroke bg-transparent py-1 px-2 text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                    <option value="" class="bg-[#1c2434] text-gray-400">Todos</option>
                    @foreach (\App\Models\Ticket::ESTADOS as $key => $label)
                        <option value="{{ $key }}" @selected(($filtros['estado'] ?? '') === $key) class="bg-[#1c2434] text-white">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1 block text-[9px] font-bold uppercase text-black dark:text-white">Prioridad</label>
                <select name="prioridad" class="w-full rounded border border-stroke bg-transparent py-1 px-2 text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                    <option value="" class="bg-[#1c2434] text-gray-400">Todas</option>
                    @foreach (\App\Models\Ticket::PRIORIDADES as $key => $label)
                        <option value="{{ $key }}" @selected(($filtros['prioridad'] ?? '') === $key) class="bg-[#1c2434] text-white">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1 block text-[9px] font-bold uppercase text-black dark:text-white">Categoría</label>
                <select name="categoria" class="w-full rounded border border-stroke bg-transparent py-1 px-2 text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                    <option value="" class="bg-[#1c2434] text-gray-400">Todas</option>
                    @foreach (\App\Models\Ticket::CATEGORIAS as $key => $label)
                        <option value="{{ $key }}" @selected(($filtros['categoria'] ?? '') === $key) class="bg-[#1c2434] text-white">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>

    {{-- Chips Rápidos --}}
    <div class="mt-2 pt-2 border-t border-stroke dark:border-strokedark flex items-center gap-1 overflow-x-auto">
        <a href="{{ route('tickets.index') }}" 
           class="inline-flex items-center gap-1 rounded-sm py-0.5 px-2 text-[10px] font-bold uppercase tracking-wider transition whitespace-nowrap {{ empty($filtros) ? 'bg-primary text-white shadow-sm' : 'bg-gray-2 text-black hover:bg-stroke dark:bg-meta-4/50 dark:text-white dark:hover:bg-meta-4' }}">
            <i class="fas fa-list text-[9px]"></i> Todos los Míos
        </a>
        <a href="{{ route('tickets.index', ['estado' => 'asignado']) }}" 
           class="inline-flex items-center gap-1 rounded-sm py-0.5 px-2 text-[10px] font-bold uppercase tracking-wider transition whitespace-nowrap {{ ($filtros['estado'] ?? '') === 'asignado' ? 'bg-primary text-white shadow-sm' : 'bg-gray-2 text-black hover:bg-stroke dark:bg-meta-4/50 dark:text-white dark:hover:bg-meta-4' }}">
            <i class="fas fa-clock text-[9px]"></i> Por Iniciar
        </a>
        <a href="{{ route('tickets.index', ['estado' => 'en_progreso']) }}" 
           class="inline-flex items-center gap-1 rounded-sm py-0.5 px-2 text-[10px] font-bold uppercase tracking-wider transition whitespace-nowrap {{ ($filtros['estado'] ?? '') === 'en_progreso' ? 'bg-meta-6 text-white shadow-sm' : 'bg-gray-2 text-black hover:bg-stroke dark:bg-meta-4/50 dark:text-white dark:hover:bg-meta-4' }}">
            <i class="fas fa-sync-alt text-[9px]"></i> En Progreso
        </a>
        <a href="{{ route('tickets.index', ['estado' => 'resuelto']) }}" 
           class="inline-flex items-center gap-1 rounded-sm py-0.5 px-2 text-[10px] font-bold uppercase tracking-wider transition whitespace-nowrap {{ ($filtros['estado'] ?? '') === 'resuelto' ? 'bg-meta-3 text-white shadow-sm' : 'bg-gray-2 text-black hover:bg-stroke dark:bg-meta-4/50 dark:text-white dark:hover:bg-meta-4' }}">
            <i class="fas fa-check text-[9px]"></i> Resueltos
        </a>
        <a href="{{ route('tickets.index', ['prioridad' => 'critica']) }}" 
           class="inline-flex items-center gap-1 rounded-sm py-0.5 px-2 text-[10px] font-bold uppercase tracking-wider transition whitespace-nowrap {{ ($filtros['prioridad'] ?? '') === 'critica' ? 'bg-meta-1 text-white shadow-sm' : 'bg-gray-2 text-black hover:bg-stroke dark:bg-meta-4/50 dark:text-white dark:hover:bg-meta-4' }}">
            <i class="fas fa-fire text-[9px]"></i> Críticas
        </a>
    </div>
</div>

{{-- ── Tabla de Datos Fina y Alta Densidad ────────────────────────────── --}}
<div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark mb-6">
    <div class="max-w-full overflow-x-auto">
        <table class="w-full table-auto border-collapse">
            <thead>
                <tr class="bg-gray-2 text-left dark:bg-meta-4">
                    <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white w-14">#</th>
                    <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white min-w-[200px]">Título / Categoría</th>
                    <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white min-w-[130px]">Solicitante</th>
                    <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white min-w-[140px]">Empresa / Sede</th>
                    <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white text-center w-24">Prioridad</th>
                    <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white text-center w-28">Estado</th>
                    <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white w-20">Fecha</th>
                    <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white text-center w-16">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stroke dark:divide-strokedark">
                @forelse ($tickets as $ticket)
                    @php
                        $colorEst = match($ticket->estado) {
                            'resuelto'            => 'bg-meta-3/10 text-meta-3 border-meta-3/20',
                            'en_progreso'         => 'bg-meta-6/10 text-meta-6 border-meta-6/20',
                            'cerrado','cancelado' => 'bg-meta-1/10 text-meta-1 border-meta-1/20',
                            default               => 'bg-primary/10 text-primary border-primary/20',
                        };
                        $colorPrio = match($ticket->prioridad) {
                            'critica' => 'bg-meta-1/10 text-meta-1 border-meta-1/20',
                            'alta'    => 'bg-meta-6/10 text-meta-6 border-meta-6/20',
                            default   => 'bg-primary/10 text-primary border-primary/20',
                        };
                    @endphp
                    <tr class="hover:bg-gray-2 dark:hover:bg-meta-4/40 transition">
                        
                        <td class="py-1.5 px-3 font-mono text-xs font-bold text-primary whitespace-nowrap">
                            #{{ $ticket->id }}
                        </td>

                        <td class="py-1.5 px-3">
                            <a href="{{ route('tickets.show', $ticket) }}" class="font-bold text-xs text-black dark:text-white hover:text-primary transition block truncate max-w-[240px]" title="{{ $ticket->titulo }}">
                                {{ $ticket->titulo }}
                            </a>
                            <span class="text-[9px] text-bodydark2 block font-mono">
                                <i class="fas {{ $ticket->icono_categoria }} text-[8px] mr-1 text-primary"></i>{{ $ticket->label_categoria }}
                            </span>
                        </td>

                        <td class="py-1.5 px-3 whitespace-nowrap">
                            <div class="flex items-center gap-1.5">
                                <div class="flex h-5 w-5 items-center justify-center rounded bg-primary/20 text-primary font-bold text-[9px] shrink-0">
                                    {{ substr($ticket->solicitante?->name ?? 'U', 0, 1) }}
                                </div>
                                <span class="text-xs text-black dark:text-white truncate max-w-[120px]">
                                    {{ $ticket->solicitante?->name ?? 'Eliminado' }}
                                </span>
                            </div>
                        </td>

                        <td class="py-1.5 px-3">
                            <span class="text-xs text-black dark:text-white font-bold truncate block max-w-[140px]">
                                {{ $ticket->empresa?->nombre ?? '—' }}
                            </span>
                            @if ($ticket->sede)
                                <span class="text-[9px] text-bodydark2 block truncate max-w-[140px]">
                                    <i class="fas fa-map-marker-alt text-primary text-[8px] mr-0.5"></i>{{ $ticket->sede->nombre }}
                                </span>
                            @endif
                        </td>

                        <td class="py-1.5 px-3 text-center whitespace-nowrap">
                            <span class="inline-flex rounded-full px-2 py-0.5 text-[8px] font-bold uppercase border {{ $colorPrio }}">
                                {{ $ticket->label_prioridad }}
                            </span>
                        </td>

                        <td class="py-1.5 px-3 text-center whitespace-nowrap">
                            <span class="inline-flex rounded-full px-2 py-0.5 text-[8px] font-bold uppercase border {{ $colorEst }}">
                                {{ $ticket->label_estado }}
                            </span>
                        </td>

                        <td class="py-1.5 px-3 text-[10px] font-mono text-bodydark2 whitespace-nowrap">
                            {{ $ticket->created_at->format('d/m/Y') }}
                        </td>

                        <td class="py-1.5 px-3 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('tickets.show', $ticket) }}" 
                                   class="flex h-6 w-6 items-center justify-center rounded border border-stroke text-bodydark2 hover:border-primary hover:text-primary dark:border-strokedark transition" 
                                   title="Ver Detalle">
                                    <i class="fas fa-eye text-[9px]"></i>
                                </a>

                                @can('tickets.iniciar')
                                    @if ($ticket->puedeIniciar() && $ticket->asignado_a === auth()->id())
                                        <form method="POST" action="{{ route('tickets.iniciar', $ticket) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="flex h-6 w-6 items-center justify-center rounded bg-primary text-white hover:bg-opacity-90 transition shadow-sm" title="Iniciar Atención">
                                                <i class="fas fa-play text-[8px]"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-6 text-center">
                            <div class="flex flex-col items-center text-bodydark2">
                                <i class="fas fa-check-double text-xl mb-1 text-meta-3"></i>
                                <span class="text-xs">
                                    {{ !empty($filtros) ? 'No hay asignaciones que coincidan con los filtros.' : 'No tienes tickets asignados activos — ¡todo al día!' }}
                                </span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($tickets->hasPages())
        <div class="border-t border-stroke px-4 py-2 dark:border-strokedark">
            {{ $tickets->onEachSide(1)->links() }}
        </div>
    @endif
</div>