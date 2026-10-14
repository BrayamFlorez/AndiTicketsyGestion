{{-- resources/views/tickets/partials/index_admin.blade.php --}}

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
                <i class="fas fa-ticket-alt text-primary text-sm"></i>
                <h2 class="text-xs font-bold text-black dark:text-white uppercase tracking-wider whitespace-nowrap">
                    Todos los Tickets
                </h2>
                <span class="font-mono text-[10px] text-bodydark2">({{ $tickets->total() }})</span>
            </div>

            {{-- Input Buscador --}}
            <div class="relative flex-1 min-w-[200px]">
                <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-bodydark2 pointer-events-none">
                    <i class="fas fa-search text-[11px]"></i>
                </span>
                <input type="text" name="q" value="{{ $filtros['q'] ?? '' }}"
                       placeholder="Buscar por ID, título, solicitante, empresa..."
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

                @can('tickets.crear')
                    <a href="{{ route('tickets.create') }}"
                       class="inline-flex items-center gap-1 rounded bg-meta-3 py-1 px-3 text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm whitespace-nowrap cursor-pointer ml-1">
                        <i class="fas fa-plus-circle text-[10px]"></i> Nuevo Ticket
                    </a>
                @endcan
            </div>
        </div>

        {{-- Panel Desplegable Filtros --}}
        <div x-show="filtrosAbiertos" x-cloak x-transition
             class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-2.5 mt-2.5 pt-2.5 border-t border-stroke dark:border-strokedark">
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

            <div>
                <label class="mb-1 block text-[9px] font-bold uppercase text-black dark:text-white">Empresa</label>
                <select name="empresa_id" class="w-full rounded border border-stroke bg-transparent py-1 px-2 text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                    <option value="" class="bg-[#1c2434] text-gray-400">Todas</option>
                    @foreach ($empresas as $empresa)
                        <option value="{{ $empresa->id }}" @selected(($filtros['empresa_id'] ?? '') == $empresa->id) class="bg-[#1c2434] text-white">{{ $empresa->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <label class="flex items-center gap-2 cursor-pointer select-none py-1.5">
                    <input type="checkbox" name="sin_asignar" value="1" @checked(!empty($filtros['sin_asignar'])) class="rounded border-stroke text-primary focus:ring-primary dark:border-form-strokedark dark:bg-form-input">
                    <span class="text-xs text-black dark:text-white font-bold">Sin Asignar</span>
                </label>
            </div>
        </div>
    </form>

    {{-- Chips Rápidos --}}
    <div class="mt-2 pt-2 border-t border-stroke dark:border-strokedark flex items-center gap-1 overflow-x-auto">
        <a href="{{ route('tickets.index') }}" 
           class="inline-flex items-center gap-1 rounded-sm py-0.5 px-2 text-[10px] font-bold uppercase tracking-wider transition whitespace-nowrap {{ empty($filtros) ? 'bg-primary text-white shadow-sm' : 'bg-gray-2 text-black hover:bg-stroke dark:bg-meta-4/50 dark:text-white dark:hover:bg-meta-4' }}">
            <i class="fas fa-list text-[9px]"></i> Todos
        </a>
        <a href="{{ route('tickets.index', ['sin_asignar' => 1]) }}" 
           class="inline-flex items-center gap-1 rounded-sm py-0.5 px-2 text-[10px] font-bold uppercase tracking-wider transition whitespace-nowrap {{ !empty($filtros['sin_asignar']) ? 'bg-primary text-white shadow-sm' : 'bg-gray-2 text-black hover:bg-stroke dark:bg-meta-4/50 dark:text-white dark:hover:bg-meta-4' }}">
            <i class="fas fa-inbox text-[9px]"></i> Sin Asignar
        </a>
        <a href="{{ route('tickets.index', ['prioridad' => 'critica']) }}" 
           class="inline-flex items-center gap-1 rounded-sm py-0.5 px-2 text-[10px] font-bold uppercase tracking-wider transition whitespace-nowrap {{ ($filtros['prioridad'] ?? '') === 'critica' ? 'bg-meta-1 text-white shadow-sm' : 'bg-gray-2 text-black hover:bg-stroke dark:bg-meta-4/50 dark:text-white dark:hover:bg-meta-4' }}">
            <i class="fas fa-exclamation-circle text-[9px]"></i> Críticas
        </a>
        <a href="{{ route('tickets.index', ['estado' => 'en_progreso']) }}" 
           class="inline-flex items-center gap-1 rounded-sm py-0.5 px-2 text-[10px] font-bold uppercase tracking-wider transition whitespace-nowrap {{ ($filtros['estado'] ?? '') === 'en_progreso' ? 'bg-meta-6 text-white shadow-sm' : 'bg-gray-2 text-black hover:bg-stroke dark:bg-meta-4/50 dark:text-white dark:hover:bg-meta-4' }}">
            <i class="fas fa-sync-alt text-[9px]"></i> En Progreso
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
                    <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white min-w-[130px]">Técnico</th>
                    <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white text-center w-24">Prioridad</th>
                    <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white text-center w-28">Estado</th>
                    <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white w-20">Fecha</th>
                    <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white text-center w-10">Opciones</th>
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

                        <td class="py-1.5 px-3 whitespace-nowrap">
                            @if ($ticket->asignado)
                                <div class="flex items-center gap-1.5">
                                    <div class="flex h-5 w-5 items-center justify-center rounded-full bg-meta-3/20 text-meta-3 font-bold text-[9px] shrink-0">
                                        {{ substr($ticket->asignado->name, 0, 1) }}
                                    </div>
                                    <span class="text-xs font-semibold text-black dark:text-white truncate max-w-[120px]">
                                        {{ $ticket->asignado->name }}
                                    </span>
                                </div>
                            @else
                                <span class="inline-flex rounded-full bg-meta-1/10 px-1.5 py-0.2 text-[8px] font-bold text-meta-1 uppercase">Sin asignar</span>
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
                            <div x-data="dropdownMenu()" class="relative inline-block">
                                <button type="button" x-ref="trigger" @click="toggle()" class="text-bodydark2 hover:text-primary transition p-1" title="Opciones">
                                    <i class="fas fa-ellipsis-v text-xs"></i>
                                </button>

                                <template x-teleport="body">
                                    <div x-show="open" x-cloak @click.outside="open = false"
                                         :style="`top: ${top}px; left: ${left}px;`"
                                         class="fixed w-48 rounded-sm border border-stroke bg-white shadow-xl dark:border-strokedark dark:bg-boxdark z-[99999] py-1 text-left text-xs">

                                        <a href="{{ route('tickets.show', $ticket) }}" class="flex items-center gap-2 px-3 py-1.5 text-black dark:text-white hover:bg-gray-2 dark:hover:bg-meta-4 transition">
                                            <i class="fas fa-eye w-4 text-center text-primary"></i> Ver detalle
                                        </a>

                                        @can('tickets.editar')
                                            @if (Route::has('tickets.edit') && !in_array($ticket->estado, ['cerrado','cancelado']))
                                                <a href="{{ route('tickets.edit', $ticket) }}" class="flex items-center gap-2 px-3 py-1.5 text-black dark:text-white hover:bg-gray-2 dark:hover:bg-meta-4 transition">
                                                    <i class="fas fa-pen w-4 text-center text-bodydark2"></i> Editar ticket
                                                </a>
                                            @endif
                                        @endcan

                                        @can('tickets.asignar')
                                            @if (!in_array($ticket->estado, ['cerrado','cancelado']))
                                                @php $tecnicosDisponibles = $ticket->tecnicosDisponibles(); @endphp
                                                <div class="my-1 border-t border-stroke dark:border-strokedark"></div>
                                                <div class="px-3 py-0.5 text-[9px] font-bold uppercase text-bodydark2">
                                                    {{ $tecnicosDisponibles->isEmpty() ? 'Sin cobertura' : 'Asignar Técnico' }}
                                                </div>

                                                @if ($tecnicosDisponibles->isNotEmpty())
                                                    <form method="POST" action="{{ route('tickets.asignar', $ticket) }}" class="px-2 pb-1">
                                                        @csrf
                                                        <select name="tecnico_id"
                                                                onchange="if(this.value && this.value != '{{ $ticket->asignado_a }}') this.form.submit()"
                                                                class="w-full rounded border border-stroke bg-transparent py-1 px-1.5 text-[10px] outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white cursor-pointer">
                                                            <option value="">— Seleccionar —</option>
                                                            @foreach ($tecnicosDisponibles as $t)
                                                                <option value="{{ $t->id }}" @selected($ticket->asignado_a == $t->id)>
                                                                    {{ $t->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </form>
                                                @endif
                                            @endif
                                        @endcan

                                        @can('tickets.cancelar')
                                            @if ($ticket->puedeCancelar())
                                                <div class="my-1 border-t border-stroke dark:border-strokedark"></div>
                                                <form method="POST" action="{{ route('tickets.cancelar', $ticket) }}"
                                                      onsubmit="return confirm('¿Cancelar ticket #{{ $ticket->id }}?')">
                                                    @csrf
                                                    <button type="submit" class="w-full text-left flex items-center gap-2 px-3 py-1.5 text-black dark:text-white hover:bg-gray-2 dark:hover:bg-meta-4 transition">
                                                        <i class="fas fa-ban w-4 text-center text-bodydark2"></i> Cancelar
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan

                                        @can('tickets.eliminar')
                                            <div class="my-1 border-t border-stroke dark:border-strokedark"></div>
                                            <form method="POST" action="{{ route('tickets.destroy', $ticket) }}"
                                                  onsubmit="return confirm('¿Eliminar permanentemente ticket #{{ $ticket->id }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-full text-left flex items-center gap-2 px-3 py-1.5 text-meta-1 hover:bg-meta-1/10 transition font-bold">
                                                    <i class="fas fa-trash-alt w-4 text-center"></i> Eliminar
                                                </button>
                                            </form>
                                        @endcan

                                    </div>
                                </template>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="py-6 text-center">
                            <div class="flex flex-col items-center text-bodydark2">
                                <i class="fas fa-inbox text-xl mb-1"></i>
                                <span class="text-xs">
                                    {{ !empty($filtros) ? 'No coinciden resultados con los filtros.' : 'No existen tickets en el sistema.' }}
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