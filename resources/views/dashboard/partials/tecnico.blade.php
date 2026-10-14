{{-- resources/views/dashboard/partials/tecnico.blade.php --}}
@php
    $mostrarCodigoInterno = \App\Models\Setting::get('flujos.mostrar_codigo_interno', true);
@endphp

<div x-data="{ tab: 'tickets' }">

    {{-- ── Header Compacto: Saludo + Selector de Pestañas + KPIs Inline ──────────────── --}}
    <div class="mb-5 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between border-b border-stroke pb-3.5 dark:border-strokedark">
        
        {{-- Saludo y Selector de Pestañas --}}
        <div class="flex items-center gap-3 flex-wrap">
            <div class="shrink-0 mr-2">
                <h2 class="text-xs font-bold text-black dark:text-white flex items-center gap-1.5 uppercase tracking-wider">
                    <i class="fas fa-user-cog text-primary"></i> Mi Jornada Técnica
                </h2>
                <p class="text-[10px] text-bodydark2">
                    Hola, <strong class="text-black dark:text-white">{{ auth()->user()->name }}</strong>
                </p>
            </div>

            <button type="button" @click="tab = 'tickets'"
                :class="tab === 'tickets' ? 'bg-primary text-white font-bold shadow-sm' : 'bg-white text-black hover:bg-stroke border border-stroke dark:bg-meta-4 dark:text-white dark:border-strokedark'"
                class="rounded-sm py-1.5 px-3 text-xs uppercase tracking-wider transition cursor-pointer">
                <i class="fas fa-ticket-alt mr-1.5"></i> Tickets Activos
            </button>
            <button type="button" @click="tab = 'flujos'"
                :class="tab === 'flujos' ? 'bg-primary text-white font-bold shadow-sm' : 'bg-white text-black hover:bg-stroke border border-stroke dark:bg-meta-4 dark:text-white dark:border-strokedark'"
                class="rounded-sm py-1.5 px-3 text-xs uppercase tracking-wider transition cursor-pointer">
                <i class="fas fa-check-double mr-1.5"></i> Aprobaciones & Flujos
            </button>
        </div>

        {{-- KPIs Compactos Inline --}}
        <div class="flex items-center gap-2 flex-wrap">
            
            {{-- Métricas Compactas de Tickets Técnico --}}
            <div x-show="tab === 'tickets'" class="flex items-center gap-1.5 flex-wrap text-xs">
                <span class="inline-flex items-center gap-1 rounded bg-gray-2 dark:bg-meta-4/60 px-2.5 py-1 text-[11px] font-medium text-black dark:text-white border border-stroke dark:border-strokedark">
                    <span class="font-bold text-primary">{{ $totalAsignados }}</span> Asignados
                </span>
                <span class="inline-flex items-center gap-1 rounded bg-primary/10 px-2.5 py-1 text-[11px] font-medium text-primary border border-primary/20">
                    <i class="fas fa-user-check text-[10px]"></i>
                    <span class="font-bold">{{ $ticketsPorEstado['asignado'] ?? 0 }}</span> Por Iniciar
                </span>
                <span class="inline-flex items-center gap-1 rounded bg-meta-6/10 px-2.5 py-1 text-[11px] font-medium text-meta-6 border border-meta-6/20">
                    <i class="fas fa-sync-alt text-[10px]"></i>
                    <span class="font-bold">{{ $ticketsPorEstado['en_progreso'] ?? 0 }}</span> En Progreso
                </span>
                <span class="inline-flex items-center gap-1 rounded bg-meta-3/10 px-2.5 py-1 text-[11px] font-medium text-meta-3 border border-meta-3/20">
                    <i class="fas fa-check-circle text-[10px]"></i>
                    <span class="font-bold">{{ $resueltos }}</span> Resueltos
                </span>
            </div>

            {{-- Métricas Compactas de Flujos Técnico --}}
            <div x-show="tab === 'flujos'" class="flex items-center gap-1.5 flex-wrap text-xs">
                <span class="inline-flex items-center gap-1 rounded bg-meta-1/10 px-2.5 py-1 text-[11px] font-medium text-meta-1 border border-meta-1/20">
                    <i class="fas fa-hourglass-half text-[10px]"></i>
                    <span class="font-bold">{{ $flujosPendientesCount }}</span> Requieren Mi Decisión
                </span>
                <span class="inline-flex items-center gap-1 rounded bg-primary/10 px-2.5 py-1 text-[11px] font-medium text-primary border border-primary/20">
                    <i class="fas fa-user-friends text-[10px]"></i>
                    <span class="font-bold">{{ $flujosParticipandoCount }}</span> Participando
                </span>
            </div>
        </div>
    </div>

    {{-- 🟢 PESTAÑA 1: TICKETS ASIGNADOS --}}
    <div x-show="tab === 'tickets'" x-cloak class="space-y-5">
        <div class="rounded-sm border border-stroke bg-white px-4 pt-4 pb-2 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-6">
            <div class="mb-3 flex items-center justify-between flex-wrap gap-2">
                <h3 class="text-xs font-bold uppercase tracking-wider text-black dark:text-white">Tickets Activos — Ordenados por Prioridad</h3>
                <a href="{{ route('tickets.index') }}" class="text-[11px] font-bold text-primary hover:underline uppercase">
                    Ver Todos <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>

            <div class="max-w-full overflow-x-auto">
                <table class="w-full table-auto border-collapse">
                    <thead>
                        <tr class="bg-gray-2 text-left dark:bg-meta-4">
                            <th class="py-2.5 px-3 font-bold text-black dark:text-white text-[11px] uppercase w-12">#</th>
                            <th class="py-2.5 px-3 font-bold text-black dark:text-white text-[11px] uppercase min-w-[220px]">Título / Solicitante</th>
                            <th class="py-2.5 px-3 font-bold text-black dark:text-white text-[11px] uppercase min-w-[180px]">Empresa / Sede</th>
                            <th class="py-2.5 px-3 font-bold text-black dark:text-white text-[11px] uppercase text-center w-24">Prioridad</th>
                            <th class="py-2.5 px-3 font-bold text-black dark:text-white text-[11px] uppercase text-center w-28">Estado</th>
                            <th class="py-2.5 px-3 font-bold text-black dark:text-white text-[11px] uppercase text-center w-16">Acción</th>
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
                                <td class="py-2 px-3 font-mono text-xs text-bodydark2 whitespace-nowrap">#{{ $ticket->id }}</td>
                                
                                <td class="py-2 px-3">
                                    <div class="flex items-center gap-2 whitespace-nowrap">
                                        <a href="{{ route('tickets.show', $ticket) }}" class="font-bold text-xs text-black dark:text-white hover:text-primary transition truncate max-w-[280px]">
                                            {{ $ticket->titulo }}
                                        </a>
                                        <span class="text-[10px] text-bodydark2 shrink-0">
                                            · {{ $ticket->solicitante?->name ?? 'Usuario Eliminado' }}
                                        </span>
                                    </div>
                                </td>

                                <td class="py-2 px-3 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-xs font-medium text-black dark:text-white truncate max-w-[140px]">
                                            {{ $ticket->empresa?->nombre ?? '—' }}
                                        </span>
                                        @if ($ticket->sede)
                                            <span class="text-[10px] text-bodydark2 shrink-0">
                                                ({{ $ticket->sede->nombre }})
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <td class="py-2 px-3 text-center whitespace-nowrap">
                                    <span class="inline-flex rounded-full py-0.5 px-2 text-[9px] font-bold uppercase {{ $colorPrio }}">
                                        {{ $ticket->label_prioridad }}
                                    </span>
                                </td>

                                <td class="py-2 px-3 text-center whitespace-nowrap">
                                    <span class="inline-flex rounded-full py-0.5 px-2 text-[9px] font-bold uppercase {{ $colorEst }}">
                                        {{ $ticket->label_estado }}
                                    </span>
                                </td>

                                <td class="py-2 px-3 text-center whitespace-nowrap">
                                    <a href="{{ route('tickets.show', $ticket) }}" 
                                       class="inline-flex h-6 w-6 items-center justify-center rounded border border-stroke text-bodydark2 hover:border-primary hover:text-primary dark:border-strokedark transition mx-auto"
                                       title="Ver Detalle">
                                        <i class="fas fa-eye text-[10px]"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center">
                                    <div class="flex flex-col items-center text-bodydark2">
                                        <i class="fas fa-check-double text-2xl mb-1.5 text-meta-3"></i>
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
    <div x-show="tab === 'flujos'" x-cloak class="space-y-5">
        <div class="rounded-sm border border-stroke bg-white px-4 pt-4 pb-2 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-6">
            <div class="mb-3 flex items-center justify-between flex-wrap gap-2">
                <h3 class="text-xs font-bold uppercase tracking-wider text-black dark:text-white">Aprobaciones y Trámites Pendientes</h3>
                <a href="{{ route('flujos.instancias.index', ['tab' => 'pendientes']) }}" class="text-[11px] font-bold text-primary hover:underline uppercase">
                    Ir a Bandeja <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>

            <div class="max-w-full overflow-x-auto">
                <table class="w-full table-auto border-collapse">
                    <thead>
                        <tr class="bg-gray-2 text-left dark:bg-meta-4">
                            @if($mostrarCodigoInterno)
                                <th class="py-2.5 px-3 font-bold text-black dark:text-white text-[11px] uppercase w-32">Código</th>
                            @endif
                            <th class="py-2.5 px-3 font-bold text-black dark:text-white text-[11px] uppercase min-w-[260px]">Trámite / Documento</th>
                            <th class="py-2.5 px-3 font-bold text-black dark:text-white text-[11px] uppercase min-w-[200px]">Etapa / Progreso</th>
                            <th class="py-2.5 px-3 font-bold text-black dark:text-white text-[11px] uppercase text-center w-28">Estado</th>
                            <th class="py-2.5 px-3 font-bold text-black dark:text-white text-[11px] uppercase text-right w-24">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stroke dark:divide-strokedark">
                        @forelse ($flujosPendientes as $flujo)
                            @include('dashboard.partials._flujo_row', [
                                'flujo' => $flujo, 
                                'mostrarCodigoInterno' => $mostrarCodigoInterno
                            ])
                        @empty
                            <tr>
                                <td colspan="{{ $mostrarCodigoInterno ? 5 : 4 }}" class="py-6 text-center">
                                    <div class="flex flex-col items-center text-bodydark2">
                                        <i class="fas fa-check-circle text-2xl mb-1.5 text-meta-3"></i>
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