{{-- resources/views/tickets/partials/index_usuario.blade.php --}}

{{-- ── Cabecera y Acción Principal ────────────────────────────────────── --}}
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-title-md2 font-bold text-black dark:text-white flex items-center gap-2">
            <i class="fas fa-ticket-alt text-primary"></i> Mis Solicitudes de Soporte
        </h2>
        <p class="text-xs text-bodydark2 mt-1">
            {{ $tickets->total() }} ticket(s) reportado(s) · Sigue el estado y evolución de tus requerimientos
        </p>
    </div>

    @can('tickets.crear')
        <a href="{{ route('tickets.create') }}" 
           class="inline-flex items-center justify-center gap-2 rounded bg-primary py-2 px-4 text-center text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm">
            <i class="fas fa-plus text-[10px]"></i> Nuevo Ticket
        </a>
    @endcan
</div>

{{-- ── Chips de Filtro Rápido ────────────────────────────────────────── --}}
<div class="flex flex-wrap items-center gap-2 mb-6">
    <a href="{{ route('tickets.index') }}" 
       class="inline-flex rounded-full py-1.5 px-4 text-xs font-medium transition {{ empty($filtros) ? 'bg-primary text-white' : 'bg-gray hover:bg-opacity-80 dark:bg-meta-4 dark:text-white' }}">
        <i class="fas fa-list mr-1.5"></i> Todos
    </a>
    
    <a href="{{ route('tickets.index', ['estado' => 'abierto']) }}" 
       class="inline-flex rounded-full py-1.5 px-4 text-xs font-medium transition {{ ($filtros['estado'] ?? '') === 'abierto' ? 'bg-primary text-white' : 'bg-gray hover:bg-opacity-80 dark:bg-meta-4 dark:text-white' }}">
        <i class="fas fa-hourglass-half mr-1.5 {{ ($filtros['estado'] ?? '') === 'abierto' ? 'text-white' : 'text-primary' }}"></i> Pendientes
    </a>
    
    <a href="{{ route('tickets.index', ['estado' => 'en_progreso']) }}" 
       class="inline-flex rounded-full py-1.5 px-4 text-xs font-medium transition {{ ($filtros['estado'] ?? '') === 'en_progreso' ? 'bg-meta-6 text-white' : 'bg-gray hover:bg-opacity-80 dark:bg-meta-4 dark:text-white' }}">
        <i class="fas fa-sync-alt mr-1.5 {{ ($filtros['estado'] ?? '') === 'en_progreso' ? 'text-white' : 'text-meta-6' }}"></i> En Atención
    </a>
    
    <a href="{{ route('tickets.index', ['estado' => 'resuelto']) }}" 
       class="inline-flex rounded-full py-1.5 px-4 text-xs font-medium transition {{ ($filtros['estado'] ?? '') === 'resuelto' ? 'bg-meta-3 text-white' : 'bg-gray hover:bg-opacity-80 dark:bg-meta-4 dark:text-white' }}">
        <i class="fas fa-check-circle mr-1.5 {{ ($filtros['estado'] ?? '') === 'resuelto' ? 'text-white' : 'text-meta-3' }}"></i> Resueltos
    </a>
</div>

{{-- ── Formulario de Búsqueda y Filtros ──────────────────────────────── --}}
<div class="rounded-sm border border-stroke bg-white px-5 pt-5 pb-4 shadow-default dark:border-strokedark dark:bg-boxdark mb-6">
    <form method="GET" action="{{ route('tickets.index') }}">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            
            {{-- Búsqueda --}}
            <div>
                <label for="q" class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Buscar</label>
                <input type="text" id="q" name="q" value="{{ $filtros['q'] ?? '' }}" 
                       placeholder="Buscar en mis tickets..." 
                       class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
            </div>

            {{-- Estado --}}
            <div>
                <label for="estado" class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Estado</label>
                <div class="relative z-20">
                    <select id="estado" name="estado" class="w-full appearance-none rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white cursor-pointer">
                        <option value="" class="bg-[#1c2434] text-gray-400">Todos</option>
                        @foreach (\App\Models\Ticket::ESTADOS as $key => $label)
                            <option value="{{ $key }}" @selected(($filtros['estado'] ?? '') === $key) class="bg-[#1c2434] text-white">{{ $label }}</option>
                        @endforeach
                    </select>
                    <span class="absolute top-1/2 right-3 -translate-y-1/2 z-30 pointer-events-none">
                        <i class="fas fa-chevron-down text-[10px] text-bodydark2"></i>
                    </span>
                </div>
            </div>

            {{-- Categoría --}}
            <div>
                <label for="categoria" class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Categoría</label>
                <div class="relative z-20">
                    <select id="categoria" name="categoria" class="w-full appearance-none rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white cursor-pointer">
                        <option value="" class="bg-[#1c2434] text-gray-400">Todas</option>
                        @foreach (\App\Models\Ticket::CATEGORIAS as $key => $label)
                            <option value="{{ $key }}" @selected(($filtros['categoria'] ?? '') === $key) class="bg-[#1c2434] text-white">{{ $label }}</option>
                        @endforeach
                    </select>
                    <span class="absolute top-1/2 right-3 -translate-y-1/2 z-30 pointer-events-none">
                        <i class="fas fa-chevron-down text-[10px] text-bodydark2"></i>
                    </span>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2 mt-4 border-t border-stroke pt-3 dark:border-strokedark">
            @if (!empty($filtros))
                <a href="{{ route('tickets.index') }}" 
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

{{-- ── Tabla de Datos TailAdmin ───────────────────────────────────────── --}}
<div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark mb-6">
    <div class="max-w-full overflow-x-auto">
        <table class="w-full table-auto">
            <thead>
                <tr class="bg-gray-2 text-left dark:bg-meta-4">
                    <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase w-12">#</th>
                    <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase min-w-[200px]">Título / Categoría</th>
                    <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase text-center w-24">Prioridad</th>
                    <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase text-center w-28">Estado</th>
                    <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase">Técnico Asignado</th>
                    <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase w-24">Fecha</th>
                    <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase text-center w-16">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stroke dark:divide-strokedark">
                @forelse ($tickets as $ticket)
                    @php
                        $colorEst = match($ticket->estado) {
                            'resuelto' => 'bg-meta-3/10 text-meta-3',
                            'en_progreso' => 'bg-meta-6/10 text-meta-6',
                            'cerrado','cancelado' => 'bg-meta-1/10 text-meta-1',
                            default => 'bg-primary/10 text-primary',
                        };
                        $colorPrio = match($ticket->prioridad) {
                            'critica' => 'bg-meta-1/10 text-meta-1',
                            'alta' => 'bg-meta-6/10 text-meta-6',
                            default => 'bg-primary/10 text-primary',
                        };
                    @endphp
                    <tr class="hover:bg-gray-2 dark:hover:bg-meta-4/50 transition">
                        
                        {{-- ID --}}
                        <td class="py-3.5 px-4 font-mono text-xs text-bodydark2">
                            #{{ $ticket->id }}
                        </td>

                        {{-- Título y Categoría --}}
                        <td class="py-3.5 px-4">
                            <a href="{{ route('tickets.show', $ticket) }}" class="font-bold text-xs text-black dark:text-white hover:text-primary transition truncate block max-w-[280px]">
                                {{ $ticket->titulo }}
                            </a>
                            <span class="text-[10px] text-bodydark2 block mt-0.5">
                                <i class="fas {{ $ticket->icono_categoria }} text-[9px] mr-1 text-primary"></i>{{ $ticket->label_categoria }}
                            </span>
                        </td>

                        {{-- Prioridad --}}
                        <td class="py-3.5 px-4 text-center">
                            <span class="inline-flex rounded-full py-1 px-3 text-[10px] font-bold uppercase {{ $colorPrio }}">
                                {{ $ticket->label_prioridad }}
                            </span>
                        </td>

                        {{-- Estado --}}
                        <td class="py-3.5 px-4 text-center">
                            <span class="inline-flex rounded-full py-1 px-3 text-[10px] font-bold uppercase {{ $colorEst }}">
                                {{ $ticket->label_estado }}
                            </span>
                        </td>

                        {{-- Técnico Asignado --}}
                        <td class="py-3.5 px-4">
                            @if ($ticket->asignado)
                                <div class="flex items-center gap-2">
                                    <div class="flex h-6 w-6 items-center justify-center rounded-full bg-meta-3/20 text-meta-3 font-bold text-[10px] shrink-0">
                                        {{ substr($ticket->asignado->name, 0, 1) }}
                                    </div>
                                    <span class="text-xs text-black dark:text-white truncate max-w-[140px]">
                                        {{ $ticket->asignado->name }}
                                    </span>
                                </div>
                            @else
                                <span class="text-[10px] text-bodydark2 font-mono">Pendiente de asignación</span>
                            @endif
                        </td>

                        {{-- Fecha --}}
                        <td class="py-3.5 px-4 text-xs font-mono text-bodydark2">
                            {{ $ticket->created_at->format('d/m/Y') }}
                        </td>

                        {{-- Acciones (Dropdown) --}}
                        <td class="py-3.5 px-4 text-center">
                            <div x-data="dropdownMenu()" class="relative inline-block">
                                <button type="button" x-ref="trigger" @click="toggle()" class="hover:text-primary transition" title="Opciones">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>

                                <template x-teleport="body">
                                    <div x-show="open" x-cloak @click.outside="open = false"
                                         :style="`top: ${top}px; left: ${left}px;`"
                                         class="absolute w-48 rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark z-[9999] py-2 text-left">

                                        <a href="{{ route('tickets.show', $ticket) }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-black dark:text-white hover:bg-gray dark:hover:bg-meta-4 transition">
                                            <i class="fas fa-eye w-4 text-center text-bodydark2"></i> Ver detalle
                                        </a>

                                        @can('tickets.editar')
                                            @if (Route::has('tickets.edit') && ! in_array($ticket->estado, ['cerrado','cancelado','resuelto']))
                                                <a href="{{ route('tickets.edit', $ticket) }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-black dark:text-white hover:bg-gray dark:hover:bg-meta-4 transition">
                                                    <i class="fas fa-pen w-4 text-center text-bodydark2"></i> Editar
                                                </a>
                                            @endif
                                        @endcan

                                        @can('tickets.cancelar')
                                            @if ($ticket->puedeCancelar())
                                                <div class="my-1 border-t border-stroke dark:border-strokedark"></div>
                                                <form method="POST" action="{{ route('tickets.cancelar', $ticket) }}"
                                                      onsubmit="return confirm('¿Estás seguro de cancelar tu ticket #{{ $ticket->id }}?')">
                                                    @csrf
                                                    <button type="submit" class="w-full text-left flex items-center gap-2.5 px-4 py-2 text-xs text-meta-1 hover:bg-meta-1/10 transition">
                                                        <i class="fas fa-ban w-4 text-center"></i> Cancelar ticket
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan

                                    </div>
                                </template>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center">
                            <div class="flex flex-col items-center text-bodydark2">
                                <i class="fas fa-ticket-alt text-3xl mb-2"></i>
                                <p class="text-xs mb-3">
                                    @if (!empty($filtros))
                                        No hay tickets que coincidan con los filtros aplicados.
                                    @else
                                        Aún no has reportado tickets de soporte.
                                    @endif
                                </p>
                                @if (!empty($filtros))
                                    <a href="{{ route('tickets.index') }}" class="inline-flex items-center gap-1 rounded border border-stroke py-1 px-3 text-xs font-medium text-black dark:border-strokedark dark:text-white">
                                        Limpiar filtros
                                    </a>
                                @elseif ($tickets->total() === 0)
                                    @can('tickets.crear')
                                        <a href="{{ route('tickets.create') }}" class="inline-flex items-center gap-1.5 rounded bg-primary py-1.5 px-4 text-xs font-bold uppercase text-white hover:bg-opacity-90">
                                            <i class="fas fa-plus"></i> Crear mi primer ticket
                                        </a>
                                    @endcan
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    @if ($tickets->hasPages())
        <div class="border-t border-stroke py-3 px-4 dark:border-strokedark">
            {{ $tickets->onEachSide(1)->links() }}
        </div>
    @endif
</div>