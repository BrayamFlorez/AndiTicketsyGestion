{{-- resources/views/dashboard/partials/usuario.blade.php --}}

<div x-data="{ tab: 'tickets' }">

    {{-- Breadcrumb & Selector de Pestañas Estilo TailAdmin --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b border-stroke pb-4 dark:border-strokedark">
        <div>
            <h2 class="text-title-md2 font-bold text-black dark:text-white flex items-center gap-2">
                <i class="fas fa-user-circle text-primary"></i> Mi Actividad
            </h2>
            <p class="text-xs text-bodydark2 mt-1">
                Hola, <strong class="text-black dark:text-white">{{ auth()->user()->name }}</strong> — Seguimiento de tus tickets y trámites
            </p>
        </div>

        {{-- Tabs de Navegación --}}
        <div class="flex items-center gap-2">
            <button type="button" 
                    @click="tab = 'tickets'" 
                    :class="tab === 'tickets' ? 'bg-primary text-white font-bold shadow-sm' : 'bg-white text-black hover:bg-stroke border border-stroke dark:bg-meta-4 dark:text-white dark:border-strokedark'" 
                    class="rounded-sm py-2 px-4 text-xs uppercase tracking-wider transition cursor-pointer">
                <i class="fas fa-ticket-alt mr-1.5"></i> Mis Tickets
            </button>
            <button type="button" 
                    @click="tab = 'flujos'" 
                    :class="tab === 'flujos' ? 'bg-primary text-white font-bold shadow-sm' : 'bg-white text-black hover:bg-stroke border border-stroke dark:bg-meta-4 dark:text-white dark:border-strokedark'" 
                    class="rounded-sm py-2 px-4 text-xs uppercase tracking-wider transition cursor-pointer">
                <i class="fas fa-project-diagram mr-1.5"></i> Mis Flujos
            </button>
        </div>
    </div>

    {{-- 🟢 PESTAÑA 1: MIS TICKETS DE SOPORTE --}}
    <div x-show="tab === 'tickets'" x-cloak class="space-y-6">
        
        {{-- KPIs Tickets del Usuario --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 md:gap-6 2xl:gap-7.5">
            
            {{-- Mis Tickets --}}
            <div class="rounded-sm border border-stroke bg-white py-5 px-6 shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-2 text-primary dark:bg-meta-4">
                    <i class="fas fa-ticket-alt text-base"></i>
                </div>
                <div class="mt-4 flex items-end justify-between">
                    <div>
                        <h4 class="text-title-md font-bold text-black dark:text-white">{{ $totalMisTickets }}</h4>
                        <span class="text-xs font-semibold text-bodydark2 uppercase">Mis Tickets</span>
                    </div>
                </div>
            </div>

            {{-- En Proceso --}}
            <div class="rounded-sm border border-stroke bg-white py-5 px-6 shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-6/10 text-meta-6">
                    <i class="fas fa-hourglass-half text-base"></i>
                </div>
                <div class="mt-4 flex items-end justify-between">
                    <div>
                        <h4 class="text-title-md font-bold text-meta-6">{{ $abiertos }}</h4>
                        <span class="text-xs font-semibold text-bodydark2 uppercase">En Proceso</span>
                    </div>
                </div>
            </div>

            {{-- Resueltos --}}
            <div class="rounded-sm border border-stroke bg-white py-5 px-6 shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-3/10 text-meta-3">
                    <i class="fas fa-check-circle text-base"></i>
                </div>
                <div class="mt-4 flex items-end justify-between">
                    <div>
                        <h4 class="text-title-md font-bold text-meta-3">{{ $ticketsPorEstado['resuelto'] ?? 0 }}</h4>
                        <span class="text-xs font-semibold text-bodydark2 uppercase">Resueltos</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- Tabla Mis Tickets Recientes (TailAdmin Table) --}}
        <div class="rounded-sm border border-stroke bg-white px-5 pt-6 pb-2.5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5">
            <div class="mb-4 flex items-center justify-between flex-wrap gap-2">
                <h3 class="text-sm font-bold uppercase tracking-wider text-black dark:text-white">Mis Tickets Recientes</h3>
                <div class="flex items-center gap-2">
                    @can('tickets.crear')
                        <a href="{{ route('tickets.create') }}" 
                           class="inline-flex items-center justify-center gap-1.5 rounded bg-primary py-1.5 px-3 text-center text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm">
                            <i class="fas fa-plus text-[10px]"></i> Nuevo Ticket
                        </a>
                    @endcan
                    <a href="{{ route('tickets.index') }}" class="text-xs font-bold text-primary hover:underline uppercase">
                        Ver Todos <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            <div class="max-w-full overflow-x-auto">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-2 text-left dark:bg-meta-4">
                            <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase w-12">#</th>
                            <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase min-w-[200px]">Título</th>
                            <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase">Categoría</th>
                            <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase text-center w-28">Estado</th>
                            <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase">Técnico Asignado</th>
                            <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase w-24">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stroke dark:divide-strokedark">
                        @forelse ($misTickets as $ticket)
                            <tr class="hover:bg-gray-2 dark:hover:bg-meta-4/50 transition">
                                <td class="py-3 px-4 text-xs font-mono text-bodydark2">#{{ $ticket->id }}</td>
                                <td class="py-3 px-4">
                                    <a href="{{ route('tickets.show', $ticket) }}" class="font-bold text-xs text-black dark:text-white hover:text-primary transition truncate block max-w-[280px]">
                                        {{ $ticket->titulo }}
                                    </a>
                                </td>
                                <td class="py-3 px-4 text-xs text-black dark:text-white">
                                    <i class="fas {{ $ticket->icono_categoria }} text-[10px] mr-1 text-primary"></i>{{ $ticket->label_categoria }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @php
                                        $colorEst = match($ticket->estado) {
                                            'resuelto' => 'bg-meta-3/10 text-meta-3',
                                            'en_progreso' => 'bg-meta-6/10 text-meta-6',
                                            'cerrado','cancelado' => 'bg-meta-1/10 text-meta-1',
                                            default => 'bg-primary/10 text-primary',
                                        };
                                    @endphp
                                    <span class="inline-flex rounded-full py-1 px-3 text-[10px] font-bold uppercase {{ $colorEst }}">
                                        {{ $ticket->label_estado }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    @if ($ticket->asignado)
                                        <div class="flex items-center gap-2">
                                            <div class="flex h-6 w-6 items-center justify-center rounded-full bg-meta-3/20 text-meta-3 font-bold text-[10px] shrink-0">
                                                {{ substr($ticket->asignado->name, 0, 1) }}
                                            </div>
                                            <span class="text-xs text-black dark:text-white truncate max-w-[120px]">
                                                {{ $ticket->asignado->name }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-[10px] text-bodydark2 font-mono">Pendiente</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-xs font-mono text-bodydark2">
                                    {{ $ticket->created_at->format('d/m/Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center">
                                    <div class="flex flex-col items-center text-bodydark2">
                                        <i class="fas fa-ticket-alt text-3xl mb-2"></i>
                                        <p class="text-xs mb-3">Aún no has registrado ningún ticket de soporte.</p>
                                        @can('tickets.crear')
                                            <a href="{{ route('tickets.create') }}" 
                                               class="inline-flex items-center gap-1.5 rounded bg-primary py-1.5 px-4 text-xs font-bold uppercase text-white hover:bg-opacity-90 transition">
                                                <i class="fas fa-plus text-[10px]"></i> Crear mi primer ticket
                                            </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- 🟣 PESTAÑA 2: MIS FLUJOS DE TRABAJO --}}
    <div x-show="tab === 'flujos'" x-cloak class="space-y-6">
        
        {{-- KPIs Flujos del Usuario --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 md:gap-6 2xl:gap-7.5">
            
            <div class="rounded-sm border border-stroke bg-white py-5 px-6 shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-2 text-primary dark:bg-meta-4">
                    <i class="fas fa-play-circle text-base"></i>
                </div>
                <div class="mt-4 flex items-end justify-between">
                    <div>
                        <h4 class="text-title-md font-bold text-black dark:text-white">{{ $misFlujosAbiertos }}</h4>
                        <span class="text-xs font-semibold text-bodydark2 uppercase">Flujos Abiertos</span>
                    </div>
                </div>
            </div>

            <div class="rounded-sm border border-stroke bg-white py-5 px-6 shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-1/10 text-meta-1">
                    <i class="fas fa-exclamation-triangle text-base"></i>
                </div>
                <div class="mt-4 flex items-end justify-between">
                    <div>
                        <h4 class="text-title-md font-bold text-meta-1">{{ $misFlujosCorreccion }}</h4>
                        <span class="text-xs font-semibold text-bodydark2 uppercase">Requiere Corrección</span>
                    </div>
                </div>
            </div>

            <div class="rounded-sm border border-stroke bg-white py-5 px-6 shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-3/10 text-meta-3">
                    <i class="fas fa-check-double text-base"></i>
                </div>
                <div class="mt-4 flex items-end justify-between">
                    <div>
                        <h4 class="text-title-md font-bold text-meta-3">{{ $misFlujosAprobados }}</h4>
                        <span class="text-xs font-semibold text-bodydark2 uppercase">Aprobados</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- Tabla Mis Flujos Recientes --}}
        <div class="rounded-sm border border-stroke bg-white px-5 pt-6 pb-2.5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5">
            <div class="mb-4 flex items-center justify-between flex-wrap gap-2">
                <h3 class="text-sm font-bold uppercase tracking-wider text-black dark:text-white">Mis Flujos Recientes</h3>
                <div class="flex items-center gap-2">
                    @can('flujos.iniciar')
                        <a href="{{ route('flujos.instancias.create') }}" 
                           class="inline-flex items-center justify-center gap-1.5 rounded bg-primary py-1.5 px-3 text-center text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm">
                            <i class="fas fa-play-circle text-[10px]"></i> Iniciar Flujo
                        </a>
                    @endcan
                    <a href="{{ route('flujos.instancias.index', ['tab' => 'iniciados']) }}" class="text-xs font-bold text-primary hover:underline uppercase">
                        Ver Todos <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            <div class="max-w-full overflow-x-auto">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-2 text-left dark:bg-meta-4">
                            <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase">Código / Trámite</th>
                            <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase">Etapa Actual</th>
                            <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase text-center w-28">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stroke dark:divide-strokedark">
                        @forelse ($misFlujos as $flujo)
                            @include('dashboard.partials._flujo_row', ['flujo' => $flujo])
                        @empty
                            <tr>
                                <td colspan="3" class="py-8 text-center">
                                    <div class="flex flex-col items-center text-bodydark2">
                                        <i class="fas fa-project-diagram text-3xl mb-2"></i>
                                        <p class="text-xs mb-3">Aún no has iniciado ningún flujo de trabajo.</p>
                                        @can('flujos.iniciar')
                                            <a href="{{ route('flujos.instancias.create') }}" 
                                               class="inline-flex items-center gap-1.5 rounded bg-primary py-1.5 px-4 text-xs font-bold uppercase text-white hover:bg-opacity-90 transition">
                                                <i class="fas fa-play-circle text-[10px]"></i> Iniciar mi primer flujo
                                            </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>