{{-- resources/views/dashboard/partials/tecnico.blade.php --}}

<div x-data="{ tab: 'tickets' }">

    {{-- Breadcrumb & Selector de Pestañas Estilo TailAdmin --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b border-stroke pb-4 dark:border-strokedark">
        <div>
            <h2 class="text-title-md2 font-bold text-black dark:text-white flex items-center gap-2">
                <i class="fas fa-user-cog text-primary"></i> Mi Jornada Técnica
            </h2>
            <p class="text-xs text-bodydark2 mt-1">
                Hola, <strong class="text-black dark:text-white">{{ auth()->user()->name }}</strong> — Tickets asignados y decisiones pendientes
            </p>
        </div>

        {{-- Tabs de Navegación --}}
        <div class="flex items-center gap-2">
            <button type="button" 
                    @click="tab = 'tickets'" 
                    :class="tab === 'tickets' ? 'bg-primary text-white font-bold shadow-sm' : 'bg-white text-black hover:bg-stroke border border-stroke dark:bg-meta-4 dark:text-white dark:border-strokedark'" 
                    class="rounded-sm py-2 px-4 text-xs uppercase tracking-wider transition cursor-pointer">
                <i class="fas fa-ticket-alt mr-1.5"></i> Mis Tickets Activos
            </button>
            <button type="button" 
                    @click="tab = 'flujos'" 
                    :class="tab === 'flujos' ? 'bg-primary text-white font-bold shadow-sm' : 'bg-white text-black hover:bg-stroke border border-stroke dark:bg-meta-4 dark:text-white dark:border-strokedark'" 
                    class="rounded-sm py-2 px-4 text-xs uppercase tracking-wider transition cursor-pointer">
                <i class="fas fa-check-double mr-1.5"></i> Aprobaciones & Flujos
            </button>
        </div>
    </div>

    {{-- 🟢 PESTAÑA 1: TICKETS ASIGNADOS --}}
    <div x-show="tab === 'tickets'" x-cloak class="space-y-6">
        
        {{-- KPIs Tickets del Técnico --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-4 md:gap-6 2xl:gap-7.5">
            
            {{-- Asignados --}}
            <div class="rounded-sm border border-stroke bg-white py-5 px-6 shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-2 text-primary dark:bg-meta-4">
                    <i class="fas fa-ticket-alt text-base"></i>
                </div>
                <div class="mt-4 flex items-end justify-between">
                    <div>
                        <h4 class="text-title-md font-bold text-black dark:text-white">{{ $totalAsignados }}</h4>
                        <span class="text-xs font-semibold text-bodydark2 uppercase">Asignados</span>
                    </div>
                </div>
            </div>

            {{-- En Progreso --}}
            <div class="rounded-sm border border-stroke bg-white py-5 px-6 shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-6/10 text-meta-6">
                    <i class="fas fa-sync-alt text-base"></i>
                </div>
                <div class="mt-4 flex items-end justify-between">
                    <div>
                        <h4 class="text-title-md font-bold text-meta-6">{{ $ticketsPorEstado['en_progreso'] ?? 0 }}</h4>
                        <span class="text-xs font-semibold text-bodydark2 uppercase">En Progreso</span>
                    </div>
                </div>
            </div>

            {{-- Por Iniciar --}}
            <div class="rounded-sm border border-stroke bg-white py-5 px-6 shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-primary/10 text-primary">
                    <i class="fas fa-user-check text-base"></i>
                </div>
                <div class="mt-4 flex items-end justify-between">
                    <div>
                        <h4 class="text-title-md font-bold text-primary">{{ $ticketsPorEstado['asignado'] ?? 0 }}</h4>
                        <span class="text-xs font-semibold text-bodydark2 uppercase">Por Iniciar</span>
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
                        <h4 class="text-title-md font-bold text-meta-3">{{ $resueltos }}</h4>
                        <span class="text-xs font-semibold text-bodydark2 uppercase">Resueltos</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- Tabla Tickets Activos --}}
        <div class="rounded-sm border border-stroke bg-white px-5 pt-6 pb-2.5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5">
            <div class="mb-4 flex items-center justify-between flex-wrap gap-2">
                <h3 class="text-sm font-bold uppercase tracking-wider text-black dark:text-white">Tickets Activos — Ordenados por Prioridad</h3>
                <a href="{{ route('tickets.index') }}" class="text-xs font-bold text-primary hover:underline uppercase">
                    Ver Todos <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>

            <div class="max-w-full overflow-x-auto">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-2 text-left dark:bg-meta-4">
                            <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase w-12">#</th>
                            <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase min-w-[200px]">Título / Solicitante</th>
                            <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase">Empresa / Sede</th>
                            <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase text-center w-24">Prioridad</th>
                            <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase text-center w-28">Estado</th>
                            <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase text-center w-16">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stroke dark:divide-strokedark">
                        @forelse ($misTickets as $ticket)
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
                                <td class="py-3.5 px-4 font-mono text-xs text-bodydark2">#{{ $ticket->id }}</td>
                                
                                <td class="py-3.5 px-4">
                                    <a href="{{ route('tickets.show', $ticket) }}" class="font-bold text-xs text-black dark:text-white hover:text-primary transition truncate block max-w-[280px]">
                                        {{ $ticket->titulo }}
                                    </a>
                                    <span class="text-[10px] text-bodydark2 block mt-0.5">
                                        <i class="fas fa-user text-[9px] mr-1 text-primary"></i>{{ $ticket->solicitante?->name ?? 'Usuario Eliminado' }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-4">
                                    <span class="text-xs font-medium text-black dark:text-white block truncate max-w-[160px]">
                                        {{ $ticket->empresa?->nombre ?? '—' }}
                                    </span>
                                    @if ($ticket->sede)
                                        <span class="text-[10px] text-bodydark2 block truncate max-w-[160px]">
                                            <i class="fas fa-map-marker-alt text-[9px] mr-1 text-primary"></i>{{ $ticket->sede->nombre }}
                                        </span>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex rounded-full py-1 px-3 text-[10px] font-bold uppercase {{ $colorPrio }}">
                                        {{ $ticket->label_prioridad }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex rounded-full py-1 px-3 text-[10px] font-bold uppercase {{ $colorEst }}">
                                        {{ $ticket->label_estado }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-4 text-center">
                                    <a href="{{ route('tickets.show', $ticket) }}" 
                                       class="inline-flex h-7 w-7 items-center justify-center rounded border border-stroke text-bodydark2 hover:border-primary hover:text-primary dark:border-strokedark transition"
                                       title="Ver Detalle">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center">
                                    <div class="flex flex-col items-center text-bodydark2">
                                        <i class="fas fa-check-double text-3xl mb-2 text-meta-3"></i>
                                        <p class="text-xs">No tienes tickets activos asignados en este momento. ¡Todo al día!</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- 🟣 PESTAÑA 2: FLUJOS & APROBACIONES --}}
    <div x-show="tab === 'flujos'" x-cloak class="space-y-6">
        
        {{-- KPIs Flujos del Técnico --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:gap-6 2xl:gap-7.5">
            
            <div class="rounded-sm border border-stroke bg-white py-5 px-6 shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-1/10 text-meta-1">
                    <i class="fas fa-hourglass-half text-base"></i>
                </div>
                <div class="mt-4 flex items-end justify-between">
                    <div>
                        <h4 class="text-title-md font-bold text-meta-1">{{ $flujosPendientesCount }}</h4>
                        <span class="text-xs font-semibold text-bodydark2 uppercase">Requieren Mi Decisión</span>
                    </div>
                </div>
            </div>

            <div class="rounded-sm border border-stroke bg-white py-5 px-6 shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-primary/10 text-primary">
                    <i class="fas fa-user-friends text-base"></i>
                </div>
                <div class="mt-4 flex items-end justify-between">
                    <div>
                        <h4 class="text-title-md font-bold text-primary">{{ $flujosParticipandoCount }}</h4>
                        <span class="text-xs font-semibold text-bodydark2 uppercase">Participando</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- Tabla Aprobaciones Pendientes --}}
        <div class="rounded-sm border border-stroke bg-white px-5 pt-6 pb-2.5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5">
            <div class="mb-4 flex items-center justify-between flex-wrap gap-2">
                <h3 class="text-sm font-bold uppercase tracking-wider text-black dark:text-white">Aprobaciones y Trámites Pendientes</h3>
                <a href="{{ route('flujos.instancias.index', ['tab' => 'pendientes']) }}" class="text-xs font-bold text-primary hover:underline uppercase">
                    Ir a Bandeja <i class="fas fa-arrow-right ml-1"></i>
                </a>
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
                        @forelse ($flujosPendientes as $flujo)
                            @include('dashboard.partials._flujo_row', ['flujo' => $flujo])
                        @empty
                            <tr>
                                <td colspan="3" class="py-8 text-center">
                                    <div class="flex flex-col items-center text-bodydark2">
                                        <i class="fas fa-check-circle text-3xl mb-2 text-meta-3"></i>
                                        <p class="text-xs">Sin decisiones pendientes por el momento.</p>
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