{{-- resources/views/dashboard/partials/usuario.blade.php --}}
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
                    <i class="fas fa-user-circle text-primary"></i> Mi Actividad
                </h2>
                <p class="text-[10px] text-bodydark2">
                    Hola, <strong class="text-black dark:text-white">{{ auth()->user()->name }}</strong>
                </p>
            </div>

            <button type="button" @click="tab = 'tickets'"
                :class="tab === 'tickets' ? 'bg-primary text-white font-bold shadow-sm' : 'bg-white text-black hover:bg-stroke border border-stroke dark:bg-meta-4 dark:text-white dark:border-strokedark'"
                class="rounded-sm py-1.5 px-3 text-xs uppercase tracking-wider transition cursor-pointer">
                <i class="fas fa-ticket-alt mr-1.5"></i> Mis Tickets
            </button>
            <button type="button" @click="tab = 'flujos'"
                :class="tab === 'flujos' ? 'bg-primary text-white font-bold shadow-sm' : 'bg-white text-black hover:bg-stroke border border-stroke dark:bg-meta-4 dark:text-white dark:border-strokedark'"
                class="rounded-sm py-1.5 px-3 text-xs uppercase tracking-wider transition cursor-pointer">
                <i class="fas fa-project-diagram mr-1.5"></i> Mis Flujos
            </button>
        </div>

        {{-- KPIs Compactos Inline --}}
        <div class="flex items-center gap-2 flex-wrap">
            
            {{-- Métricas Compactas de Mis Tickets --}}
            <div x-show="tab === 'tickets'" class="flex items-center gap-1.5 flex-wrap text-xs">
                <span class="inline-flex items-center gap-1 rounded bg-gray-2 dark:bg-meta-4/60 px-2.5 py-1 text-[11px] font-medium text-black dark:text-white border border-stroke dark:border-strokedark">
                    <span class="font-bold text-primary">{{ $totalMisTickets }}</span> Totales
                </span>
                <span class="inline-flex items-center gap-1 rounded bg-meta-6/10 px-2.5 py-1 text-[11px] font-medium text-meta-6 border border-meta-6/20">
                    <i class="fas fa-hourglass-half text-[10px]"></i>
                    <span class="font-bold">{{ $abiertos }}</span> En Proceso
                </span>
                <span class="inline-flex items-center gap-1 rounded bg-meta-3/10 px-2.5 py-1 text-[11px] font-medium text-meta-3 border border-meta-3/20">
                    <i class="fas fa-check-circle text-[10px]"></i>
                    <span class="font-bold">{{ $ticketsPorEstado['resuelto'] ?? 0 }}</span> Resueltos
                </span>
            </div>

            {{-- Métricas Compactas de Mis Flujos --}}
            <div x-show="tab === 'flujos'" class="flex items-center gap-1.5 flex-wrap text-xs">
                <span class="inline-flex items-center gap-1 rounded bg-primary/10 px-2.5 py-1 text-[11px] font-medium text-primary border border-primary/20">
                    <i class="fas fa-play-circle text-[10px]"></i>
                    <span class="font-bold">{{ $misFlujosAbiertos }}</span> Abiertos
                </span>
                <span class="inline-flex items-center gap-1 rounded bg-meta-1/10 px-2.5 py-1 text-[11px] font-medium text-meta-1 border border-meta-1/20">
                    <i class="fas fa-exclamation-triangle text-[10px]"></i>
                    <span class="font-bold">{{ $misFlujosCorreccion }}</span> Requiere Corrección
                </span>
                <span class="inline-flex items-center gap-1 rounded bg-meta-3/10 px-2.5 py-1 text-[11px] font-medium text-meta-3 border border-meta-3/20">
                    <i class="fas fa-check-double text-[10px]"></i>
                    <span class="font-bold">{{ $misFlujosAprobados }}</span> Aprobados
                </span>
            </div>
        </div>
    </div>

    {{-- 🟢 PESTAÑA 1: MIS TICKETS DE SOPORTE --}}
    <div x-show="tab === 'tickets'" x-cloak class="space-y-5">
        <div class="rounded-sm border border-stroke bg-white px-4 pt-4 pb-2 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-6">
            <div class="mb-3 flex items-center justify-between flex-wrap gap-2">
                <h3 class="text-xs font-bold uppercase tracking-wider text-black dark:text-white">Mis Tickets Recientes</h3>
                <div class="flex items-center gap-2">
                    @can('tickets.crear')
                        <a href="{{ route('tickets.create') }}" 
                           class="inline-flex items-center justify-center gap-1.5 rounded bg-primary py-1 px-2.5 text-center text-[11px] font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm">
                            <i class="fas fa-plus text-[9px]"></i> Nuevo Ticket
                        </a>
                    @endcan
                    <a href="{{ route('tickets.index') }}" class="text-[11px] font-bold text-primary hover:underline uppercase">
                        Ver Todos <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            <div class="max-w-full overflow-x-auto">
                <table class="w-full table-auto border-collapse">
                    <thead>
                        <tr class="bg-gray-2 text-left dark:bg-meta-4">
                            <th class="py-2.5 px-3 font-bold text-black dark:text-white text-[11px] uppercase w-12">#</th>
                            <th class="py-2.5 px-3 font-bold text-black dark:text-white text-[11px] uppercase min-w-[220px]">Título</th>
                            <th class="py-2.5 px-3 font-bold text-black dark:text-white text-[11px] uppercase">Categoría</th>
                            <th class="py-2.5 px-3 font-bold text-black dark:text-white text-[11px] uppercase text-center w-28">Estado</th>
                            <th class="py-2.5 px-3 font-bold text-black dark:text-white text-[11px] uppercase min-w-[150px]">Técnico Asignado</th>
                            <th class="py-2.5 px-3 font-bold text-black dark:text-white text-[11px] uppercase text-right w-24">Fecha</th>
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
                            @endphp
                            <tr class="hover:bg-gray-2 dark:hover:bg-meta-4/50 transition">
                                <td class="py-2 px-3 text-xs font-mono text-bodydark2 whitespace-nowrap">#{{ $ticket->id }}</td>
                                <td class="py-2 px-3">
                                    <a href="{{ route('tickets.show', $ticket) }}" class="font-bold text-xs text-black dark:text-white hover:text-primary transition truncate block max-w-[320px]">
                                        {{ $ticket->titulo }}
                                    </a>
                                </td>
                                <td class="py-2 px-3 text-xs text-black dark:text-white whitespace-nowrap">
                                    <i class="fas {{ $ticket->icono_categoria }} text-[10px] mr-1 text-primary"></i>{{ $ticket->label_categoria }}
                                </td>
                                <td class="py-2 px-3 text-center whitespace-nowrap">
                                    <span class="inline-flex rounded-full py-0.5 px-2 text-[9px] font-bold uppercase {{ $colorEst }}">
                                        {{ $ticket->label_estado }}
                                    </span>
                                </td>
                                <td class="py-2 px-3 whitespace-nowrap">
                                    @if ($ticket->asignado)
                                        <div class="flex items-center gap-1.5">
                                            <div class="flex h-5 w-5 items-center justify-center rounded-full bg-meta-3/20 text-meta-3 font-bold text-[9px] shrink-0">
                                                {{ substr($ticket->asignado->name, 0, 1) }}
                                            </div>
                                            <span class="text-xs text-black dark:text-white truncate max-w-[140px]">
                                                {{ $ticket->asignado->name }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-[10px] text-bodydark2 font-mono">Pendiente</span>
                                    @endif
                                </td>
                                <td class="py-2 px-3 text-right font-mono text-[11px] text-bodydark2 whitespace-nowrap">
                                    {{ $ticket->created_at->format('d/m/Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center">
                                    <div class="flex flex-col items-center text-bodydark2">
                                        <i class="fas fa-ticket-alt text-2xl mb-1.5"></i>
                                        <p class="text-xs mb-2">Aún no has registrado ningún ticket de soporte.</p>
                                        @can('tickets.crear')
                                            <a href="{{ route('tickets.create') }}" 
                                               class="inline-flex items-center gap-1.5 rounded bg-primary py-1 px-3 text-xs font-bold uppercase text-white hover:bg-opacity-90 transition">
                                                <i class="fas fa-plus text-[9px]"></i> Crear mi primer ticket
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
    <div x-show="tab === 'flujos'" x-cloak class="space-y-5">
        <div class="rounded-sm border border-stroke bg-white px-4 pt-4 pb-2 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-6">
            <div class="mb-3 flex items-center justify-between flex-wrap gap-2">
                <h3 class="text-xs font-bold uppercase tracking-wider text-black dark:text-white">Mis Flujos Recientes</h3>
                <div class="flex items-center gap-2">
                    @can('flujos.iniciar')
                        <a href="{{ route('flujos.instancias.create') }}" 
                           class="inline-flex items-center justify-center gap-1.5 rounded bg-primary py-1 px-2.5 text-center text-[11px] font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm">
                            <i class="fas fa-play-circle text-[9px]"></i> Iniciar Flujo
                        </a>
                    @endcan
                    <a href="{{ route('flujos.instancias.index', ['tab' => 'iniciados']) }}" class="text-[11px] font-bold text-primary hover:underline uppercase">
                        Ver Todos <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
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
                        @forelse ($misFlujos as $flujo)
                            @include('dashboard.partials._flujo_row', [
                                'flujo' => $flujo, 
                                'mostrarCodigoInterno' => $mostrarCodigoInterno
                            ])
                        @empty
                            <tr>
                                <td colspan="{{ $mostrarCodigoInterno ? 5 : 4 }}" class="py-6 text-center">
                                    <div class="flex flex-col items-center text-bodydark2">
                                        <i class="fas fa-project-diagram text-2xl mb-1.5"></i>
                                        <p class="text-xs mb-2">Aún no has iniciado ningún flujo de trabajo.</p>
                                        @can('flujos.iniciar')
                                            <a href="{{ route('flujos.instancias.create') }}" 
                                               class="inline-flex items-center gap-1.5 rounded bg-primary py-1 px-3 text-xs font-bold uppercase text-white hover:bg-opacity-90 transition">
                                                <i class="fas fa-play-circle text-[9px]"></i> Iniciar mi primer flujo
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