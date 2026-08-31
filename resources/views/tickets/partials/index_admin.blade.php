{{-- resources/views/tickets/partials/index_admin.blade.php --}}

{{-- ── Cabecera y Acción Principal (TailAdmin Style) ────────────────── --}}
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-title-md2 font-bold text-black dark:text-white flex items-center gap-2">
            <i class="fas fa-ticket-alt text-primary"></i> Gestión de Tickets
        </h2>
        <p class="text-sm text-bodydark2 mt-1">
            {{ $tickets->total() }} resultado(s) · Registro global de incidentes y solicitudes
        </p>
    </div>

    @can('tickets.crear')
        <a href="{{ route('tickets.create') }}" 
           class="inline-flex items-center justify-center gap-2.5 rounded-md bg-primary py-2 px-4 text-center font-medium text-white hover:bg-opacity-90 transition shadow-sm">
            <i class="fas fa-plus"></i> Nuevo Ticket
        </a>
    @endcan
</div>

{{-- ── Chips de Filtro Rápido ────────────────────────────────────────── --}}
<div class="flex flex-wrap items-center gap-2 mb-6">
    <a href="{{ route('tickets.index') }}" 
       class="inline-flex rounded-full py-1.5 px-4 text-xs font-medium transition {{ empty($filtros) ? 'bg-primary text-white' : 'bg-gray hover:bg-opacity-80 dark:bg-meta-4 dark:text-white' }}">
        <i class="fas fa-list mr-1.5"></i> Todos
    </a>
    
    <a href="{{ route('tickets.index', ['sin_asignar' => 1]) }}" 
       class="inline-flex rounded-full py-1.5 px-4 text-xs font-medium transition {{ !empty($filtros['sin_asignar']) ? 'bg-primary text-white' : 'bg-gray hover:bg-opacity-80 dark:bg-meta-4 dark:text-white' }}">
        <i class="fas fa-inbox mr-1.5 {{ !empty($filtros['sin_asignar']) ? 'text-white' : 'text-primary' }}"></i> Sin Asignar
    </a>
    
    <a href="{{ route('tickets.index', ['prioridad' => 'critica']) }}" 
       class="inline-flex rounded-full py-1.5 px-4 text-xs font-medium transition {{ ($filtros['prioridad'] ?? '') === 'critica' ? 'bg-meta-1 text-white' : 'bg-gray hover:bg-opacity-80 dark:bg-meta-4 dark:text-white' }}">
        <i class="fas fa-exclamation-circle mr-1.5 {{ ($filtros['prioridad'] ?? '') === 'critica' ? 'text-white' : 'text-meta-1' }}"></i> Críticas
    </a>
    
    <a href="{{ route('tickets.index', ['estado' => 'en_progreso']) }}" 
       class="inline-flex rounded-full py-1.5 px-4 text-xs font-medium transition {{ ($filtros['estado'] ?? '') === 'en_progreso' ? 'bg-meta-6 text-white' : 'bg-gray hover:bg-opacity-80 dark:bg-meta-4 dark:text-white' }}">
        <i class="fas fa-sync-alt mr-1.5 {{ ($filtros['estado'] ?? '') === 'en_progreso' ? 'text-white' : 'text-meta-6' }}"></i> En Progreso
    </a>
</div>

{{-- ── Formulario de Filtros Avanzados (Card TailAdmin) ─────────────── --}}
<div class="rounded-sm border border-stroke bg-white px-5 pt-6 pb-4 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 mb-6">
    <form method="GET" action="{{ route('tickets.index') }}">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            
            {{-- Búsqueda --}}
            <div>
                <label class="mb-2.5 block text-xs font-bold uppercase text-black dark:text-white">Buscar</label>
                <input type="text" name="q" value="{{ $filtros['q'] ?? '' }}" placeholder="Título o ID..." 
                       class="w-full rounded border-[1.5px] border-stroke bg-transparent py-2 px-3 text-sm font-medium outline-none transition focus:border-primary active:border-primary dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary text-black dark:text-white">
            </div>

            {{-- Estado --}}
            <div>
                <label class="mb-2.5 block text-xs font-bold uppercase text-black dark:text-white">Estado</label>
                <div class="relative z-20 bg-transparent dark:bg-form-input">
                    <select name="estado" class="relative z-20 w-full appearance-none rounded border border-stroke bg-transparent py-2 px-3 text-sm font-medium outline-none transition focus:border-primary active:border-primary dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary text-black dark:text-white">
                        <option value="" class="text-body dark:text-bodydark">Todos</option>
                        @foreach (\App\Models\Ticket::ESTADOS as $key => $label)
                            <option value="{{ $key }}" @selected(($filtros['estado'] ?? '') === $key) class="text-body dark:text-bodydark">{{ $label }}</option>
                        @endforeach
                    </select>
                    <span class="absolute top-1/2 right-3 -translate-y-1/2 z-30">
                        <i class="fas fa-chevron-down text-xs text-bodydark2"></i>
                    </span>
                </div>
            </div>

            {{-- Prioridad --}}
            <div>
                <label class="mb-2.5 block text-xs font-bold uppercase text-black dark:text-white">Prioridad</label>
                <div class="relative z-20 bg-transparent dark:bg-form-input">
                    <select name="prioridad" class="relative z-20 w-full appearance-none rounded border border-stroke bg-transparent py-2 px-3 text-sm font-medium outline-none transition focus:border-primary active:border-primary dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary text-black dark:text-white">
                        <option value="" class="text-body dark:text-bodydark">Todas</option>
                        @foreach (\App\Models\Ticket::PRIORIDADES as $key => $label)
                            <option value="{{ $key }}" @selected(($filtros['prioridad'] ?? '') === $key) class="text-body dark:text-bodydark">{{ $label }}</option>
                        @endforeach
                    </select>
                    <span class="absolute top-1/2 right-3 -translate-y-1/2 z-30">
                        <i class="fas fa-chevron-down text-xs text-bodydark2"></i>
                    </span>
                </div>
            </div>

            {{-- Categoría --}}
            <div>
                <label class="mb-2.5 block text-xs font-bold uppercase text-black dark:text-white">Categoría</label>
                <div class="relative z-20 bg-transparent dark:bg-form-input">
                    <select name="categoria" class="relative z-20 w-full appearance-none rounded border border-stroke bg-transparent py-2 px-3 text-sm font-medium outline-none transition focus:border-primary active:border-primary dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary text-black dark:text-white">
                        <option value="" class="text-body dark:text-bodydark">Todas</option>
                        @foreach (\App\Models\Ticket::CATEGORIAS as $key => $label)
                            <option value="{{ $key }}" @selected(($filtros['categoria'] ?? '') === $key) class="text-body dark:text-bodydark">{{ $label }}</option>
                        @endforeach
                    </select>
                    <span class="absolute top-1/2 right-3 -translate-y-1/2 z-30">
                        <i class="fas fa-chevron-down text-xs text-bodydark2"></i>
                    </span>
                </div>
            </div>

            {{-- Empresa --}}
            <div>
                <label class="mb-2.5 block text-xs font-bold uppercase text-black dark:text-white">Empresa</label>
                <div class="relative z-20 bg-transparent dark:bg-form-input">
                    <select name="empresa_id" class="relative z-20 w-full appearance-none rounded border border-stroke bg-transparent py-2 px-3 text-sm font-medium outline-none transition focus:border-primary active:border-primary dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary text-black dark:text-white">
                        <option value="" class="text-body dark:text-bodydark">Todas</option>
                        @foreach ($empresas as $empresa)
                            <option value="{{ $empresa->id }}" @selected(($filtros['empresa_id'] ?? '') == $empresa->id) class="text-body dark:text-bodydark">{{ $empresa->nombre }}</option>
                        @endforeach
                    </select>
                    <span class="absolute top-1/2 right-3 -translate-y-1/2 z-30">
                        <i class="fas fa-chevron-down text-xs text-bodydark2"></i>
                    </span>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between flex-wrap gap-4 mt-6 border-t border-stroke pt-4 dark:border-strokedark">
            <label class="flex cursor-pointer select-none items-center">
                <div class="relative">
                    <input type="checkbox" name="sin_asignar" value="1" @checked(!empty($filtros['sin_asignar'])) class="sr-only">
                    <div class="box mr-3 flex h-4 w-4 items-center justify-center rounded border border-stroke dark:border-strokedark {{ !empty($filtros['sin_asignar']) ? 'bg-primary border-primary' : 'bg-transparent' }}">
                        <span class="opacity-100 {{ !empty($filtros['sin_asignar']) ? 'text-white' : 'hidden' }}">
                            <i class="fas fa-check text-[10px]"></i>
                        </span>
                    </div>
                </div>
                <span class="text-sm font-medium text-black dark:text-white">Solo pendientes de asignación</span>
            </label>

            <div class="flex items-center gap-3">
                @if (!empty($filtros))
                    <a href="{{ route('tickets.index') }}" class="inline-flex items-center justify-center gap-2 rounded-md border border-stroke py-1.5 px-4 text-center font-medium text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition">
                        Limpiar
                    </a>
                @endif
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-md bg-primary py-1.5 px-4 text-center font-medium text-white hover:bg-opacity-90 transition">
                    <i class="fas fa-filter text-xs"></i> Aplicar Filtros
                </button>
            </div>
        </div>
    </form>
</div>

{{-- ── Tabla de Datos Fina y Protegida (Table Three TailAdmin) ───────── --}}
<div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
    <div class="max-w-full overflow-x-auto">
        <table class="w-full table-auto">
            <thead>
                <tr class="bg-gray-2 text-left dark:bg-meta-4">
                    <th class="py-4 px-4 font-bold text-black dark:text-white text-xs uppercase w-12">#</th>
                    <th class="py-4 px-4 font-bold text-black dark:text-white text-xs uppercase min-w-[200px]">Título / Categoría</th>
                    <th class="py-4 px-4 font-bold text-black dark:text-white text-xs uppercase">Solicitante</th>
                    <th class="py-4 px-4 font-bold text-black dark:text-white text-xs uppercase">Empresa / Sede</th>
                    <th class="py-4 px-4 font-bold text-black dark:text-white text-xs uppercase">Técnico</th>
                    <th class="py-4 px-4 font-bold text-black dark:text-white text-xs uppercase text-center w-24">Prioridad</th>
                    <th class="py-4 px-4 font-bold text-black dark:text-white text-xs uppercase text-center w-28">Estado</th>
                    <th class="py-4 px-4 font-bold text-black dark:text-white text-xs uppercase w-24">Fecha</th>
                    <th class="py-4 px-4 font-bold text-black dark:text-white text-xs uppercase text-center w-16">Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tickets as $ticket)
                    <tr class="border-b border-[#eee] dark:border-strokedark hover:bg-gray-2 dark:hover:bg-meta-4 transition">
                        
                        <td class="py-3 px-4">
                            <span class="text-sm font-mono text-bodydark2">#{{ $ticket->id }}</span>
                        </td>

                        {{-- Título --}}
                        <td class="py-3 px-4">
                            <a href="{{ route('tickets.show', $ticket) }}" class="text-sm font-bold text-black dark:text-white hover:text-primary dark:hover:text-primary transition block truncate max-w-[240px]">
                                {{ $ticket->titulo }}
                            </a>
                            <span class="text-xs text-bodydark2 block mt-0.5">
                                <i class="fas {{ $ticket->icono_categoria }} text-[10px] mr-1"></i>{{ $ticket->label_categoria }}
                            </span>
                        </td>

                        {{-- Solicitante --}}
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary/20 text-primary text-xs font-bold shrink-0">
                                    {{ substr($ticket->solicitante?->name ?? 'U', 0, 1) }}
                                </div>
                                <span class="text-sm text-black dark:text-white truncate max-w-[120px]">
                                    {{ $ticket->solicitante?->name ?? 'Usuario Eliminado' }}
                                </span>
                            </div>
                        </td>

                        {{-- Empresa y Sede --}}
                        <td class="py-3 px-4">
                            <div class="text-sm text-black dark:text-white truncate max-w-[130px]">
                                {{ $ticket->empresa?->nombre ?? '—' }}
                            </div>
                            @if ($ticket->sede)
                                <span class="text-xs text-bodydark2 block truncate max-w-[130px] mt-0.5">
                                    <i class="fas fa-map-marker-alt text-[10px] mr-1"></i>{{ $ticket->sede->nombre }}
                                </span>
                            @endif
                        </td>

                        {{-- Asignado --}}
                        <td class="py-3 px-4">
                            @if ($ticket->asignado)
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-meta-3/20 text-meta-3 text-xs font-bold shrink-0">
                                        {{ substr($ticket->asignado->name, 0, 1) }}
                                    </div>
                                    <span class="text-sm text-black dark:text-white truncate max-w-[120px]">
                                        {{ $ticket->asignado->name }}
                                    </span>
                                </div>
                            @else
                                <span class="inline-flex rounded-full bg-meta-1/10 py-1 px-2.5 text-[10px] font-medium text-meta-1">
                                    Sin asignar
                                </span>
                            @endif
                        </td>

                        {{-- Prioridad --}}
                        <td class="py-3 px-4 text-center">
                            @php
                                $colorPrio = match($ticket->prioridad) {
                                    'critica' => 'bg-meta-1/10 text-meta-1',
                                    'alta' => 'bg-meta-6/10 text-meta-6',
                                    default => 'bg-primary/10 text-primary',
                                };
                            @endphp
                            <span class="inline-flex rounded-full py-1 px-3 text-[10px] font-bold uppercase {{ $colorPrio }}">
                                {{ $ticket->label_prioridad }}
                            </span>
                        </td>

                        {{-- Estado --}}
                        <td class="py-3 px-4 text-center">
                            @php
                                $colorEst = match($ticket->estado) {
                                    'resuelto' => 'bg-meta-3/10 text-meta-3',
                                    'en_progreso' => 'bg-meta-6/10 text-meta-6',
                                    'cerrado','cancelado' => 'bg-bodydark1 text-bodydark2 dark:bg-meta-4',
                                    default => 'bg-primary/10 text-primary',
                                };
                            @endphp
                            <span class="inline-flex rounded-full py-1 px-3 text-[10px] font-bold uppercase {{ $colorEst }}">
                                {{ $ticket->label_estado }}
                            </span>
                        </td>

                        {{-- Fecha --}}
                        <td class="py-3 px-4 text-xs font-mono text-bodydark2">
                            {{ $ticket->created_at->format('d/m/Y') }}
                        </td>

                        {{-- Dropdown de Acciones --}}
                        <td class="py-3 px-4 text-center">
                            <div x-data="dropdownMenu()" class="relative inline-block">
                                <button type="button" x-ref="trigger" @click="toggle()" class="hover:text-primary transition" title="Opciones">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>

                                <template x-teleport="body">
                                    <div x-show="open" x-cloak @click.outside="open = false"
                                         :style="`top: ${top}px; left: ${left}px;`"
                                         class="absolute w-48 rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark z-[9999] py-2">

                                        <a href="{{ route('tickets.show', $ticket) }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-black dark:text-white hover:bg-gray dark:hover:bg-meta-4 transition">
                                            <i class="fas fa-eye w-4 text-center text-bodydark2"></i> Ver detalle
                                        </a>

                                        @can('tickets.editar')
                                            @if (Route::has('tickets.edit') && ! in_array($ticket->estado, ['cerrado','cancelado']))
                                                <a href="{{ route('tickets.edit', $ticket) }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-black dark:text-white hover:bg-gray dark:hover:bg-meta-4 transition">
                                                    <i class="fas fa-pen w-4 text-center text-bodydark2"></i> Editar ticket
                                                </a>
                                            @endif
                                        @endcan

                                        @can('tickets.asignar')
                                            @if (! in_array($ticket->estado, ['cerrado','cancelado']))
                                                @php $tecnicosDisponibles = $ticket->tecnicosDisponibles(); @endphp
                                                <div class="my-1 border-t border-stroke dark:border-strokedark"></div>
                                                <div class="px-4 py-1 text-[10px] font-bold uppercase text-bodydark2">
                                                    {{ $tecnicosDisponibles->isEmpty() ? 'Sin cobertura técnica' : 'Reasignar / Asignar' }}
                                                </div>

                                                @if ($tecnicosDisponibles->isNotEmpty())
                                                    <form method="POST" action="{{ route('tickets.asignar', $ticket) }}" class="px-2 pb-1">
                                                        @csrf
                                                        <select name="tecnico_id"
                                                                onchange="if(this.value && this.value != '{{ $ticket->asignado_a }}') this.form.submit()"
                                                                class="w-full rounded border border-stroke bg-transparent py-1.5 px-2 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white cursor-pointer mt-1">
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
                                                      onsubmit="return confirm('¿Cancelar el ticket #{{ $ticket->id }}?')">
                                                    @csrf
                                                    <button type="submit" class="w-full text-left flex items-center gap-2.5 px-4 py-2 text-sm text-black dark:text-white hover:bg-gray dark:hover:bg-meta-4 transition">
                                                        <i class="fas fa-ban w-4 text-center text-bodydark2"></i> Cancelar
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan

                                        @can('tickets.eliminar')
                                            <div class="my-1 border-t border-stroke dark:border-strokedark"></div>
                                            <form method="POST" action="{{ route('tickets.destroy', $ticket) }}"
                                                  onsubmit="return confirm('¿Eliminar permanentemente el ticket #{{ $ticket->id }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-full text-left flex items-center gap-2.5 px-4 py-2 text-sm text-meta-1 hover:bg-meta-1/10 transition">
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
                        <td colspan="9" class="py-8 text-center">
                            <div class="flex flex-col items-center text-bodydark2">
                                <i class="fas fa-inbox text-3xl mb-2"></i>
                                <span class="text-sm">
                                    {{ !empty($filtros) ? 'No coinciden resultados con los filtros.' : 'No existen tickets en el sistema.' }}
                                </span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación (Se aprovecha la paginación base de Laravel que está inyectada o editamos luego la clase de Laravel) --}}
    @if ($tickets->hasPages())
        <div class="border-t border-stroke py-4 px-4 dark:border-strokedark sm:px-6">
            {{ $tickets->onEachSide(1)->links() }}
        </div>
    @endif
</div>