{{-- resources/views/dashboard/partials/admin.blade.php --}}

<div x-data="{ tab: 'flujos' }">

    {{-- Breadcrumb & Selector de Pestañas TailAdmin Style --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b border-stroke pb-4 dark:border-strokedark">
        
        {{-- Tabs de Navegación --}}
        <div class="flex items-center gap-2">
            <button type="button" 
                    @click="tab = 'flujos'" 
                    :class="tab === 'flujos' ? 'bg-primary text-white font-bold' : 'bg-gray text-black hover:bg-stroke dark:bg-meta-4 dark:text-white dark:hover:bg-opacity-80'" 
                    class="rounded-sm py-2 px-4 text-xs uppercase tracking-wider transition">
                <i class="fas fa-project-diagram mr-1.5"></i> Flujos de Trabajo
            </button>
            <button type="button" 
                    @click="tab = 'tickets'" 
                    :class="tab === 'tickets' ? 'bg-primary text-white font-bold' : 'bg-gray text-black hover:bg-stroke dark:bg-meta-4 dark:text-white dark:hover:bg-opacity-80'" 
                    class="rounded-sm py-2 px-4 text-xs uppercase tracking-wider transition">
                <i class="fas fa-ticket-alt mr-1.5"></i> Tickets de Soporte
            </button>
            
        </div>

        {{-- Estado del Sistema --}}
        <div class="flex items-center gap-2 text-xs font-semibold text-bodydark2">
            <span class="relative flex h-2.5 w-2.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-meta-3 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-meta-3"></span>
            </span>
            <span>Sistema Operativo</span>
        </div>
    </div>

    {{-- Grid Principal (Operación / Sidebar) --}}
    <div class="grid grid-cols-12 gap-4 md:gap-6 2xl:gap-7.5">

        {{-- COLUMNA IZQUIERDA: Módulos Dinámicos (8 cols) --}}
        <div class="col-span-12 xl:col-span-8 space-y-6">

            {{-- 🟣 SECCIÓN 1: FLUJOS DE TRABAJO --}}
            <div x-show="tab === 'flujos'" x-cloak class="space-y-6">
                
                {{-- KPIs Flujos --}}
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-4 md:gap-6 2xl:gap-7.5">
                    
                    <div class="rounded-sm border border-stroke bg-white py-4 px-5 shadow-default dark:border-strokedark dark:bg-boxdark">
                        <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-2 dark:bg-meta-4 text-primary">
                            <i class="fas fa-play-circle text-base"></i>
                        </div>
                        <div class="mt-4 flex items-end justify-between">
                            <div>
                                <h4 class="text-title-md font-bold text-black dark:text-white">{{ $flujosAbiertos }}</h4>
                                <span class="text-xs font-semibold text-bodydark2 uppercase">Abiertos</span>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-sm border border-stroke bg-white py-4 px-5 shadow-default dark:border-strokedark dark:bg-boxdark">
                        <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-1/10 text-meta-1">
                            <i class="fas fa-edit text-base"></i>
                        </div>
                        <div class="mt-4 flex items-end justify-between">
                            <div>
                                <h4 class="text-title-md font-bold text-meta-1">{{ $flujosCorreccion }}</h4>
                                <span class="text-xs font-semibold text-bodydark2 uppercase">Corrección</span>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-sm border border-stroke bg-white py-4 px-5 shadow-default dark:border-strokedark dark:bg-boxdark">
                        <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-3/10 text-meta-3">
                            <i class="fas fa-check-double text-base"></i>
                        </div>
                        <div class="mt-4 flex items-end justify-between">
                            <div>
                                <h4 class="text-title-md font-bold text-meta-3">{{ $flujosCerradosMes }}</h4>
                                <span class="text-xs font-semibold text-bodydark2 uppercase">Aprobados Mes</span>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-sm border border-stroke bg-white py-4 px-5 shadow-default dark:border-strokedark dark:bg-boxdark">
                        <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-6/10 text-meta-6">
                            <i class="fas fa-user-clock text-base"></i>
                        </div>
                        <div class="mt-4 flex items-end justify-between">
                            <div>
                                <h4 class="text-title-md font-bold text-meta-6">{{ $misFlujosPendientes }}</h4>
                                <span class="text-xs font-semibold text-bodydark2 uppercase">Mi Acción</span>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Tabla Flujos Recientes --}}
                <div class="rounded-sm border border-stroke bg-white px-5 pt-6 pb-2.5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-black dark:text-white">Flujos de Trabajo Recientes</h3>
                        <a href="{{ route('flujos.instancias.index', ['tab' => 'todos']) }}" class="text-xs font-bold text-primary hover:underline uppercase">
                            Ir a Bandeja <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>

                    <div class="max-w-full overflow-x-auto">
                        <table class="w-full table-auto">
                            <thead>
                                <tr class="bg-gray-2 text-left dark:bg-meta-4">
                                    <th class="py-3 px-4 font-bold text-xs text-black dark:text-white uppercase">Código / Trámite</th>
                                    <th class="py-3 px-4 font-bold text-xs text-black dark:text-white uppercase">Etapa Actual</th>
                                    <th class="py-3 px-4 font-bold text-xs text-black dark:text-white uppercase text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stroke dark:divide-strokedark">
                                @forelse ($flujosRecientes as $flujo)
                                    @include('dashboard.partials._flujo_row', ['flujo' => $flujo])
                                @empty
                                    <tr>
                                        <td colspan="3" class="py-6 text-center text-xs text-bodydark2 italic">No hay trámites activos.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            {{-- 🟢 SECCIÓN 2: TICKETS DE SOPORTE --}}
            <div x-show="tab === 'tickets'" x-cloak class="space-y-6">
                
                {{-- KPIs Tickets Estilo TailAdmin Card Data --}}
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-4 md:gap-6 2xl:gap-7.5">
                    
                    {{-- Total Tickets --}}
                    <div class="rounded-sm border border-stroke bg-white py-4 px-5 shadow-default dark:border-strokedark dark:bg-boxdark">
                        <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-2 dark:bg-meta-4 text-primary">
                            <i class="fas fa-ticket-alt text-base"></i>
                        </div>
                        <div class="mt-4 flex items-end justify-between">
                            <div>
                                <h4 class="text-title-md font-bold text-black dark:text-white">{{ $totalTickets }}</h4>
                                <span class="text-xs font-semibold text-bodydark2 uppercase">Totales</span>
                            </div>
                        </div>
                    </div>

                    {{-- Sin Asignar --}}
                    <div class="rounded-sm border border-stroke bg-white py-4 px-5 shadow-default dark:border-strokedark dark:bg-boxdark">
                        <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-1/10 text-meta-1">
                            <i class="fas fa-exclamation-circle text-base"></i>
                        </div>
                        <div class="mt-4 flex items-end justify-between">
                            <div>
                                <h4 class="text-title-md font-bold text-meta-1">{{ $sinAsignar }}</h4>
                                <span class="text-xs font-semibold text-bodydark2 uppercase">Sin Asignar</span>
                            </div>
                        </div>
                    </div>

                    {{-- En Progreso --}}
                    <div class="rounded-sm border border-stroke bg-white py-4 px-5 shadow-default dark:border-strokedark dark:bg-boxdark">
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

                    {{-- Resueltos --}}
                    <div class="rounded-sm border border-stroke bg-white py-4 px-5 shadow-default dark:border-strokedark dark:bg-boxdark">
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

                {{-- Tabla Tickets Recientes (TailAdmin Table Three) --}}
                <div class="rounded-sm border border-stroke bg-white px-5 pt-6 pb-2.5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-black dark:text-white">Últimos Tickets Recibidos</h3>
                        <a href="{{ route('tickets.index') }}" class="text-xs font-bold text-primary hover:underline uppercase">
                            Ver Todos <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>

                    <div class="max-w-full overflow-x-auto">
                        <table class="w-full table-auto">
                            <thead>
                                <tr class="bg-gray-2 text-left dark:bg-meta-4">
                                    <th class="py-3 px-4 font-bold text-xs text-black dark:text-white uppercase w-12">#</th>
                                    <th class="py-3 px-4 font-bold text-xs text-black dark:text-white uppercase min-w-[200px]">Título / Solicitante</th>
                                    <th class="py-3 px-4 font-bold text-xs text-black dark:text-white uppercase text-center">Estado</th>
                                    <th class="py-3 px-4 font-bold text-xs text-black dark:text-white uppercase text-center">Prioridad</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stroke dark:divide-strokedark">
                                @forelse ($ticketsRecientes as $ticket)
                                    <tr>
                                        <td class="py-3 px-4 text-xs font-mono text-bodydark2">#{{ $ticket->id }}</td>
                                        <td class="py-3 px-4">
                                            <a href="{{ route('tickets.show', $ticket) }}" class="font-bold text-xs text-black dark:text-white hover:text-primary transition truncate block max-w-[280px]">
                                                {{ $ticket->titulo }}
                                            </a>
                                            <span class="text-[10px] text-bodydark2 block">
                                                {{ $ticket->solicitante?->name ?? 'Usuario Eliminado' }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <span class="inline-flex rounded-full bg-opacity-10 py-1 px-3 text-[10px] font-bold uppercase {{ $ticket->estado === 'resuelto' ? 'bg-meta-3 text-meta-3' : ($ticket->estado === 'en_progreso' ? 'bg-meta-6 text-meta-6' : 'bg-bodydark2 text-bodydark2') }}">
                                                {{ $ticket->label_estado }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <span class="inline-flex rounded-full bg-opacity-10 py-1 px-3 text-[10px] font-bold uppercase {{ $ticket->prioridad === 'critica' ? 'bg-meta-1 text-meta-1' : 'bg-primary text-primary' }}">
                                                {{ $ticket->label_prioridad }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-6 text-center text-xs text-bodydark2 italic">No hay tickets registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            

        </div>

        {{-- COLUMNA DERECHA: Sidebar de Infraestructura y Personal (4 cols) --}}
        <div class="col-span-12 xl:col-span-4 space-y-6">

            {{-- Widget de Infraestructura --}}
            <div class="rounded-sm border border-stroke bg-white p-5 shadow-default dark:border-strokedark dark:bg-boxdark">
                <h4 class="mb-4 text-xs font-bold uppercase tracking-wider text-black dark:text-white flex items-center gap-2">
                    <i class="fas fa-server text-primary"></i> Infraestructura
                </h4>

                <div class="grid grid-cols-3 gap-2 text-center">
                    <div class="rounded border border-stroke p-3 dark:border-strokedark bg-gray dark:bg-meta-4">
                        <span class="block text-[10px] font-bold text-bodydark2 uppercase">Empresas</span>
                        <span class="text-base font-extrabold text-black dark:text-white">{{ $totalEmpresas }}</span>
                    </div>
                    <div class="rounded border border-stroke p-3 dark:border-strokedark bg-gray dark:bg-meta-4">
                        <span class="block text-[10px] font-bold text-bodydark2 uppercase">Sedes</span>
                        <span class="text-base font-extrabold text-black dark:text-white">{{ $totalSedes }}</span>
                    </div>
                    <div class="rounded border border-stroke p-3 dark:border-strokedark bg-gray dark:bg-meta-4">
                        <span class="block text-[10px] font-bold text-bodydark2 uppercase">Equipos</span>
                        <span class="text-base font-extrabold text-black dark:text-white">{{ $totalEquipos }}</span>
                    </div>
                </div>
            </div>

            {{-- Widget Carga de Técnicos --}}
            <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="flex items-center justify-between border-b border-stroke py-4 px-5 dark:border-strokedark">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-black dark:text-white flex items-center gap-2">
                        <i class="fas fa-users-cog text-primary"></i> Carga de Técnicos
                    </h4>
                    <a href="{{ route('usuarios.index') }}" class="text-xs font-bold text-primary hover:underline">Gestionar</a>
                </div>

                <div class="divide-y divide-stroke dark:divide-strokedark">
                    @forelse ($tecnicos as $tec)
                        <div class="flex items-center justify-between p-3.5 hover:bg-gray dark:hover:bg-meta-4 transition">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary font-bold text-xs text-white shrink-0">
                                    {{ substr($tec->name, 0, 1) }}
                                </div>
                                <div class="truncate">
                                    <h5 class="text-xs font-bold text-black dark:text-white truncate">{{ $tec->name }}</h5>
                                    <p class="text-[10px] text-bodydark2 font-mono truncate">{{ $tec->email }}</p>
                                </div>
                            </div>
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase {{ $tec->activos > 5 ? 'bg-meta-1/10 text-meta-1' : ($tec->activos > 0 ? 'bg-meta-6/10 text-meta-6' : 'bg-meta-3/10 text-meta-3') }}">
                                {{ $tec->activos }} tks
                            </span>
                        </div>
                    @empty
                        <div class="p-6 text-center text-xs text-bodydark2 italic">Sin técnicos activos.</div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>