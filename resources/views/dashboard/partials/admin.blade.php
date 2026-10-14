{{-- resources/views/dashboard/partials/admin.blade.php --}}
@php
    $mostrarCodigoInterno = \App\Models\Setting::get('flujos.mostrar_codigo_interno', true);
@endphp

<div x-data="{ tab: 'flujos' }">

    {{-- ── Header Compacto: Selector de Pestañas + KPIs Inline ──────────────── --}}
    <div class="mb-5 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between border-b border-stroke pb-3.5 dark:border-strokedark">
        
        {{-- Botones de Navegación --}}
        <div class="flex items-center gap-2 flex-wrap shrink-0">
            <button type="button" @click="tab = 'flujos'"
                :class="tab === 'flujos' ? 'bg-primary text-white font-bold shadow-sm' : 'bg-white text-black hover:bg-stroke border border-stroke dark:bg-meta-4 dark:text-white dark:border-strokedark'"
                class="rounded-sm py-1.5 px-3 text-xs uppercase tracking-wider transition cursor-pointer">
                <i class="fas fa-project-diagram mr-1.5"></i> Flujos de Trabajo
            </button>
            <button type="button" @click="tab = 'tickets'"
                :class="tab === 'tickets' ? 'bg-primary text-white font-bold shadow-sm' : 'bg-white text-black hover:bg-stroke border border-stroke dark:bg-meta-4 dark:text-white dark:border-strokedark'"
                class="rounded-sm py-1.5 px-3 text-xs uppercase tracking-wider transition cursor-pointer">
                <i class="fas fa-ticket-alt mr-1.5"></i> Tickets de Soporte
            </button>
        </div>

        {{-- KPIs Compactos Inline --}}
        <div class="flex items-center gap-2 flex-wrap">
            
            {{-- Métricas Compactas de Flujos --}}
            <div x-show="tab === 'flujos'" class="flex items-center gap-1.5 flex-wrap text-xs">
                <span class="inline-flex items-center gap-1 rounded bg-primary/10 px-2.5 py-1 text-[11px] font-medium text-primary border border-primary/20">
                    <i class="fas fa-play-circle text-[10px]"></i>
                    <span class="font-bold">{{ $flujosAbiertos }}</span> Abiertos
                </span>
                <span class="inline-flex items-center gap-1 rounded bg-meta-1/10 px-2.5 py-1 text-[11px] font-medium text-meta-1 border border-meta-1/20">
                    <i class="fas fa-edit text-[10px]"></i>
                    <span class="font-bold">{{ $flujosCorreccion }}</span> Corrección
                </span>
                <span class="inline-flex items-center gap-1 rounded bg-meta-3/10 px-2.5 py-1 text-[11px] font-medium text-meta-3 border border-meta-3/20">
                    <i class="fas fa-check-double text-[10px]"></i>
                    <span class="font-bold">{{ $flujosCerradosMes }}</span> Aprobados Mes
                </span>
                <span class="inline-flex items-center gap-1 rounded bg-meta-6/10 px-2.5 py-1 text-[11px] font-medium text-meta-6 border border-meta-6/20">
                    <i class="fas fa-user-clock text-[10px]"></i>
                    <span class="font-bold">{{ $misFlujosPendientes }}</span> Mi Acción
                </span>
            </div>

            {{-- Métricas Compactas de Tickets --}}
            <div x-show="tab === 'tickets'" class="flex items-center gap-1.5 flex-wrap text-xs">
                <span class="inline-flex items-center gap-1 rounded bg-gray-2 dark:bg-meta-4/60 px-2.5 py-1 text-[11px] font-medium text-black dark:text-white border border-stroke dark:border-strokedark">
                    <span class="font-bold text-primary">{{ $totalTickets }}</span> Totales
                </span>
                <span class="inline-flex items-center gap-1 rounded bg-meta-1/10 px-2.5 py-1 text-[11px] font-medium text-meta-1 border border-meta-1/20">
                    <span class="font-bold">{{ $sinAsignar }}</span> Sin Asignar
                </span>
                <span class="inline-flex items-center gap-1 rounded bg-meta-6/10 px-2.5 py-1 text-[11px] font-medium text-meta-6 border border-meta-6/20">
                    <span class="font-bold">{{ $ticketsPorEstado['en_progreso'] ?? 0 }}</span> En Progreso
                </span>
                <span class="inline-flex items-center gap-1 rounded bg-meta-3/10 px-2.5 py-1 text-[11px] font-medium text-meta-3 border border-meta-3/20">
                    <span class="font-bold">{{ $ticketsPorEstado['resuelto'] ?? 0 }}</span> Resueltos
                </span>
            </div>

            {{-- Estado del Sistema --}}
            <div class="text-xs font-mono text-bodydark2 hidden xl:flex items-center gap-1.5 ml-2 pl-2 border-l border-stroke dark:border-strokedark">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-meta-3 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-meta-3"></span>
                </span>
                <span class="font-bold uppercase tracking-wider text-[10px] text-black dark:text-white">Operativo</span>
            </div>
        </div>
    </div>

    {{-- Layout Principal Grid Ampliado (9 cols Tabla / 3 cols Lateral) --}}
    <div class="grid grid-cols-12 gap-4 md:gap-5 2xl:gap-6">

        {{-- COLUMNA IZQUIERDA: Módulos Principales (9 cols -> 75% Ancho) --}}
        <div class="col-span-12 lg:col-span-9 space-y-5">

            {{-- 🟣 SECCIÓN 1: FLUJOS DE TRABAJO --}}
            <div x-show="tab === 'flujos'" x-cloak class="space-y-5">

                {{-- Tabla Flujos Recientes Ampliada --}}
                <div class="rounded-sm border border-stroke bg-white px-4 pt-4 pb-2 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-6">
                    <div class="mb-3 flex items-center justify-between flex-wrap gap-2">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-black dark:text-white">Flujos de Trabajo Recientes</h3>
                        <a href="{{ route('flujos.instancias.index', ['tab' => 'todos']) }}" class="text-[11px] font-bold text-primary hover:underline uppercase">
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
                                @forelse ($flujosRecientes as $flujo)
                                    @include('dashboard.partials._flujo_row', [
                                        'flujo' => $flujo, 
                                        'mostrarCodigoInterno' => $mostrarCodigoInterno
                                    ])
                                @empty
                                    <tr>
                                        <td colspan="{{ $mostrarCodigoInterno ? 5 : 4 }}" class="py-5 text-center text-xs text-bodydark2">No hay trámites activos.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            {{-- 🟢 SECCIÓN 2: TICKETS DE SOPORTE --}}
            <div x-show="tab === 'tickets'" x-cloak class="space-y-5">

                {{-- Tabla Tickets Recientes Comprimida --}}
                <div class="rounded-sm border border-stroke bg-white px-4 pt-4 pb-2 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-6">
                    <div class="mb-3 flex items-center justify-between flex-wrap gap-2">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-black dark:text-white">Últimos Tickets Recibidos</h3>
                        <a href="{{ route('tickets.index') }}" class="text-[11px] font-bold text-primary hover:underline uppercase">
                            Ver Todos <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>

                    <div class="max-w-full overflow-x-auto">
                        <table class="w-full table-auto border-collapse">
                            <thead>
                                <tr class="bg-gray-2 text-left dark:bg-meta-4">
                                    <th class="py-2 px-3 font-bold text-black dark:text-white text-[11px] uppercase w-12">#</th>
                                    <th class="py-2 px-3 font-bold text-black dark:text-white text-[11px] uppercase min-w-[220px]">Título / Solicitante</th>
                                    <th class="py-2 px-3 font-bold text-black dark:text-white text-[11px] uppercase text-center w-24">Estado</th>
                                    <th class="py-2 px-3 font-bold text-black dark:text-white text-[11px] uppercase text-center w-24">Prioridad</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stroke dark:divide-strokedark">
                                @forelse ($ticketsRecientes as $ticket)
                                    <tr class="hover:bg-gray-2 dark:hover:bg-meta-4/50 transition">
                                        <td class="py-2 px-3 font-mono text-xs text-bodydark2 whitespace-nowrap">#{{ $ticket->id }}</td>
                                        <td class="py-2 px-3">
                                            <div class="flex items-center gap-2 whitespace-nowrap">
                                                <a href="{{ route('tickets.show', $ticket) }}" class="font-bold text-xs text-black dark:text-white hover:text-primary transition truncate max-w-[300px]">
                                                    {{ $ticket->titulo }}
                                                </a>
                                                <span class="text-[10px] text-bodydark2 shrink-0">
                                                    · {{ $ticket->solicitante?->name ?? 'Usuario Eliminado' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="py-2 px-3 text-center whitespace-nowrap">
                                            <span class="inline-flex rounded-full bg-opacity-10 py-0.5 px-2 text-[9px] font-bold uppercase {{ $ticket->estado === 'resuelto' ? 'bg-meta-3 text-meta-3' : ($ticket->estado === 'en_progreso' ? 'bg-meta-6 text-meta-6' : 'bg-bodydark2 text-bodydark2') }}">
                                                {{ $ticket->label_estado }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-3 text-center whitespace-nowrap">
                                            <span class="inline-flex rounded-full bg-opacity-10 py-0.5 px-2 text-[9px] font-bold uppercase {{ $ticket->prioridad === 'critica' ? 'bg-meta-1 text-meta-1' : 'bg-primary text-primary' }}">
                                                {{ $ticket->label_prioridad }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-5 text-center text-xs text-bodydark2">No hay tickets registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>

        {{-- COLUMNA DERECHA: Sidebar Infraestructura & Técnicos (3 cols -> 25% Ancho) --}}
        <div class="col-span-12 lg:col-span-3 space-y-5">

            {{-- Widget Infraestructura Compacto --}}
            <div class="rounded-sm border border-stroke bg-white p-3.5 shadow-default dark:border-strokedark dark:bg-boxdark">
                <h4 class="mb-3 text-[11px] font-bold uppercase tracking-wider text-black dark:text-white flex items-center gap-1.5">
                    <i class="fas fa-server text-primary"></i> Infraestructura
                </h4>

                <div class="grid grid-cols-3 gap-1.5 text-center">
                    <div class="rounded border border-stroke p-2 dark:border-strokedark bg-gray dark:bg-meta-4/30">
                        <span class="block text-[9px] font-bold text-bodydark2 uppercase">Empresas</span>
                        <span class="text-xs font-extrabold text-black dark:text-white mt-0.5 block">{{ $totalEmpresas }}</span>
                    </div>
                    <div class="rounded border border-stroke p-2 dark:border-strokedark bg-gray dark:bg-meta-4/30">
                        <span class="block text-[9px] font-bold text-bodydark2 uppercase">Sedes</span>
                        <span class="text-xs font-extrabold text-black dark:text-white mt-0.5 block">{{ $totalSedes }}</span>
                    </div>
                    <div class="rounded border border-stroke p-2 dark:border-strokedark bg-gray dark:bg-meta-4/30">
                        <span class="block text-[9px] font-bold text-bodydark2 uppercase">Equipos</span>
                        <span class="text-xs font-extrabold text-black dark:text-white mt-0.5 block">{{ $totalEquipos }}</span>
                    </div>
                </div>
            </div>

            {{-- Widget Carga de Técnicos Compacto --}}
            <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="flex items-center justify-between border-b border-stroke py-2.5 px-3.5 dark:border-strokedark">
                    <h4 class="text-[11px] font-bold uppercase tracking-wider text-black dark:text-white flex items-center gap-1.5">
                        <i class="fas fa-users-cog text-primary"></i> Carga Técnicos
                    </h4>
                    <a href="{{ route('usuarios.index') }}" class="text-[10px] font-bold text-primary hover:underline uppercase">Gestionar</a>
                </div>

                <div class="divide-y divide-stroke dark:divide-strokedark">
                    @forelse ($tecnicos as $tec)
                        <div class="flex items-center justify-between p-2.5 hover:bg-gray dark:hover:bg-meta-4/40 transition">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="flex h-6 w-6 items-center justify-center rounded-full bg-primary font-bold text-[9px] text-white shrink-0">
                                    {{ substr($tec->name, 0, 1) }}
                                </div>
                                <div class="truncate">
                                    <h5 class="text-xs font-bold text-black dark:text-white truncate leading-tight">{{ $tec->name }}</h5>
                                    <p class="text-[9px] text-bodydark2 font-mono truncate leading-tight">{{ $tec->email }}</p>
                                </div>
                            </div>
                            <span class="inline-flex rounded-full px-2 py-0.5 text-[9px] font-bold uppercase shrink-0 {{ $tec->activos > 5 ? 'bg-meta-1/10 text-meta-1' : ($tec->activos > 0 ? 'bg-meta-6/10 text-meta-6' : 'bg-meta-3/10 text-meta-3') }}">
                                {{ $tec->activos }} tks
                            </span>
                        </div>
                    @empty
                        <div class="p-4 text-center text-xs text-bodydark2 italic">Sin técnicos activos.</div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>