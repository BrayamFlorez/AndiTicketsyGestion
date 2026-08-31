{{-- resources/views/dashboard/partials/super_admin.blade.php --}}

<div x-data="{ tab: 'flujos' }">

    {{-- Header & Selector de Secciones (Tabs) --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b border-stroke pb-4 dark:border-strokedark">
        <div class="flex items-center gap-2 flex-wrap">
            <button type="button" 
                    @click="tab = 'tickets'" 
                    :class="tab === 'tickets' ? 'bg-primary text-white font-bold shadow-sm' : 'bg-white text-black hover:bg-stroke border border-stroke dark:bg-meta-4 dark:text-white dark:border-strokedark'" 
                    class="rounded-sm py-2 px-4 text-xs uppercase tracking-wider transition cursor-pointer">
                <i class="fas fa-ticket-alt mr-1.5"></i> Tickets
            </button>
            <button type="button" 
                    @click="tab = 'flujos'" 
                    :class="tab === 'flujos' ? 'bg-primary text-white font-bold shadow-sm' : 'bg-white text-black hover:bg-stroke border border-stroke dark:bg-meta-4 dark:text-white dark:border-strokedark'" 
                    class="rounded-sm py-2 px-4 text-xs uppercase tracking-wider transition cursor-pointer">
                <i class="fas fa-project-diagram mr-1.5"></i> Flujos
            </button>
            <button type="button" 
                    @click="tab = 'plantillas'" 
                    :class="tab === 'plantillas' ? 'bg-primary text-white font-bold shadow-sm' : 'bg-white text-black hover:bg-stroke border border-stroke dark:bg-meta-4 dark:text-white dark:border-strokedark'" 
                    class="rounded-sm py-2 px-4 text-xs uppercase tracking-wider transition cursor-pointer">
                <i class="fas fa-layer-group mr-1.5"></i> Plantillas & Equipos
            </button>
        </div>

        <div class="text-xs font-mono text-bodydark2 flex items-center gap-2">
            <span class="h-2.5 w-2.5 rounded-full bg-meta-1 animate-pulse"></span>
            <span class="font-bold uppercase tracking-wider text-black dark:text-white">Consola Super Admin</span>
        </div>
    </div>

    {{-- Layout Principal Grid (8/4) --}}
    <div class="grid grid-cols-12 gap-4 md:gap-6 2xl:gap-7.5">

        {{-- COLUMNA IZQUIERDA: Módulos Principales (8 cols) --}}
        <div class="col-span-12 lg:col-span-8 space-y-6">

            {{-- 🟢 PESTAÑA 1: TICKETS --}}
            <div x-show="tab === 'tickets'" x-cloak class="space-y-6">
                
                {{-- KPIs Tickets --}}
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-4 md:gap-6">
                    <div class="rounded-sm border border-stroke bg-white py-4 px-5 shadow-default dark:border-strokedark dark:bg-boxdark">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-meta-2 text-primary dark:bg-meta-4">
                            <i class="fas fa-ticket-alt text-sm"></i>
                        </div>
                        <div class="mt-3">
                            <h4 class="text-title-sm font-bold text-black dark:text-white">{{ $totalTickets }}</h4>
                            <span class="text-[10px] font-bold text-bodydark2 uppercase">Totales</span>
                        </div>
                    </div>

                    <div class="rounded-sm border border-stroke bg-white py-4 px-5 shadow-default dark:border-strokedark dark:bg-boxdark">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-meta-1/10 text-meta-1">
                            <i class="fas fa-exclamation-circle text-sm"></i>
                        </div>
                        <div class="mt-3">
                            <h4 class="text-title-sm font-bold text-meta-1">{{ $sinAsignar }}</h4>
                            <span class="text-[10px] font-bold text-bodydark2 uppercase">Sin Asignar</span>
                        </div>
                    </div>

                    <div class="rounded-sm border border-stroke bg-white py-4 px-5 shadow-default dark:border-strokedark dark:bg-boxdark">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-meta-6/10 text-meta-6">
                            <i class="fas fa-sync-alt text-sm"></i>
                        </div>
                        <div class="mt-3">
                            <h4 class="text-title-sm font-bold text-meta-6">{{ $ticketsPorEstado['en_progreso'] ?? 0 }}</h4>
                            <span class="text-[10px] font-bold text-bodydark2 uppercase">En Progreso</span>
                        </div>
                    </div>

                    <div class="rounded-sm border border-stroke bg-white py-4 px-5 shadow-default dark:border-strokedark dark:bg-boxdark">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-meta-3/10 text-meta-3">
                            <i class="fas fa-check-circle text-sm"></i>
                        </div>
                        <div class="mt-3">
                            <h4 class="text-title-sm font-bold text-meta-3">{{ $ticketsPorEstado['resuelto'] ?? 0 }}</h4>
                            <span class="text-[10px] font-bold text-bodydark2 uppercase">Resueltos</span>
                        </div>
                    </div>
                </div>

                {{-- Tabla Tickets Recientes --}}
                <div class="rounded-sm border border-stroke bg-white px-5 pt-6 pb-2.5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5">
                    <div class="mb-4 flex items-center justify-between flex-wrap gap-2">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-black dark:text-white">Tickets Recientes</h3>
                        <a href="{{ route('tickets.index') }}" class="text-xs font-bold text-primary hover:underline uppercase">
                            Ir al Módulo <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>

                    <div class="max-w-full overflow-x-auto">
                        <table class="w-full table-auto">
                            <thead>
                                <tr class="bg-gray-2 text-left dark:bg-meta-4">
                                    <th class="py-3 px-4 font-bold text-black dark:text-white text-xs uppercase w-12">#</th>
                                    <th class="py-3 px-4 font-bold text-black dark:text-white text-xs uppercase min-w-[180px]">Título / Solicitante</th>
                                    <th class="py-3 px-4 font-bold text-black dark:text-white text-xs uppercase">Empresa</th>
                                    <th class="py-3 px-4 font-bold text-black dark:text-white text-xs uppercase text-center w-24">Estado</th>
                                    <th class="py-3 px-4 font-bold text-black dark:text-white text-xs uppercase text-center w-24">Prioridad</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stroke dark:divide-strokedark">
                                @forelse ($ticketsRecientes as $ticket)
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
                                        <td class="py-3 px-4 font-mono text-xs text-bodydark2">#{{ $ticket->id }}</td>
                                        <td class="py-3 px-4">
                                            <a href="{{ route('tickets.show', $ticket) }}" class="font-bold text-xs text-black dark:text-white hover:text-primary transition truncate block max-w-[220px]">
                                                {{ $ticket->titulo }}
                                            </a>
                                            <span class="text-[10px] text-bodydark2 block mt-0.5">
                                                {{ $ticket->solicitante?->name ?? 'Usuario borrado' }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-xs font-medium text-black dark:text-white truncate max-w-[120px]">
                                            {{ $ticket->empresa?->nombre ?? '—' }}
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <span class="inline-flex rounded-full py-0.5 px-2.5 text-[9px] font-bold uppercase {{ $colorEst }}">
                                                {{ $ticket->label_estado }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <span class="inline-flex rounded-full py-0.5 px-2.5 text-[9px] font-bold uppercase {{ $colorPrio }}">
                                                {{ $ticket->label_prioridad }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-6 text-center text-xs text-bodydark2">Sin tickets registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Desglose Tickets por Estado --}}
                <div class="rounded-sm border border-stroke bg-white p-5 shadow-default dark:border-strokedark dark:bg-boxdark">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-black dark:text-white mb-3">Desglose de Tickets por Estado</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3">
                        @foreach (\App\Models\Ticket::ESTADOS as $key => $label)
                            @php
                                $badgeColor = match($key) {
                                    'resuelto' => 'bg-meta-3/10 text-meta-3',
                                    'en_progreso' => 'bg-meta-6/10 text-meta-6',
                                    'cerrado','cancelado' => 'bg-meta-1/10 text-meta-1',
                                    default => 'bg-primary/10 text-primary',
                                };
                            @endphp
                            <div class="rounded border border-stroke bg-gray-2 p-3 text-center dark:border-strokedark dark:bg-meta-4/20">
                                <span class="inline-flex rounded-full py-0.5 px-2 text-[9px] font-bold uppercase mb-1 {{ $badgeColor }}">
                                    {{ $label }}
                                </span>
                                <span class="text-sm font-bold text-black dark:text-white block">{{ $ticketsPorEstado[$key] ?? 0 }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            {{-- 🟣 PESTAÑA 2: FLUJOS DE TRABAJO --}}
            <div x-show="tab === 'flujos'" x-cloak class="space-y-6">
                
                {{-- KPIs Flujos --}}
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-4 md:gap-6">
                    <div class="rounded-sm border border-stroke bg-white py-4 px-5 shadow-default dark:border-strokedark dark:bg-boxdark">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-meta-2 text-primary dark:bg-meta-4">
                            <i class="fas fa-play-circle text-sm"></i>
                        </div>
                        <div class="mt-3">
                            <h4 class="text-title-sm font-bold text-black dark:text-white">{{ $flujosAbiertos }}</h4>
                            <span class="text-[10px] font-bold text-bodydark2 uppercase">Abiertos</span>
                        </div>
                    </div>

                    <div class="rounded-sm border border-stroke bg-white py-4 px-5 shadow-default dark:border-strokedark dark:bg-boxdark">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-meta-1/10 text-meta-1">
                            <i class="fas fa-edit text-sm"></i>
                        </div>
                        <div class="mt-3">
                            <h4 class="text-title-sm font-bold text-meta-1">{{ $flujosCorreccion }}</h4>
                            <span class="text-[10px] font-bold text-bodydark2 uppercase">Corrección</span>
                        </div>
                    </div>

                    <div class="rounded-sm border border-stroke bg-white py-4 px-5 shadow-default dark:border-strokedark dark:bg-boxdark">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-meta-3/10 text-meta-3">
                            <i class="fas fa-check-double text-sm"></i>
                        </div>
                        <div class="mt-3">
                            <h4 class="text-title-sm font-bold text-meta-3">{{ $flujosCerradosMes }}</h4>
                            <span class="text-[10px] font-bold text-bodydark2 uppercase">Aprobados Mes</span>
                        </div>
                    </div>

                    <div class="rounded-sm border border-stroke bg-white py-4 px-5 shadow-default dark:border-strokedark dark:bg-boxdark">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-2 text-bodydark2 dark:bg-meta-4">
                            <i class="fas fa-ban text-sm"></i>
                        </div>
                        <div class="mt-3">
                            <h4 class="text-title-sm font-bold text-black dark:text-white">{{ $flujosPorEstado['cancelado'] ?? 0 }}</h4>
                            <span class="text-[10px] font-bold text-bodydark2 uppercase">Cancelados</span>
                        </div>
                    </div>
                </div>

                {{-- Tabla Flujos Recientes --}}
                <div class="rounded-sm border border-stroke bg-white px-5 pt-6 pb-2.5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5">
                    <div class="mb-4 flex items-center justify-between flex-wrap gap-2">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-black dark:text-white">Flujos de Trabajo Recientes</h3>
                        <a href="{{ route('flujos.instancias.index', ['tab' => 'todos']) }}" class="text-xs font-bold text-primary hover:underline uppercase">
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
                                @forelse ($flujosRecientes as $flujo)
                                    @include('dashboard.partials._flujo_row', ['flujo' => $flujo])
                                @empty
                                    <tr>
                                        <td colspan="3" class="py-6 text-center text-xs text-bodydark2">No hay trámites activos.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Desglose Flujos por Estado --}}
                <div class="rounded-sm border border-stroke bg-white p-5 shadow-default dark:border-strokedark dark:bg-boxdark">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-black dark:text-white mb-3">Desglose de Flujos por Estado</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                        @php
                            $estadosFlujos = [
                                'en_progreso'         => 'En progreso',
                                'requiere_correccion' => 'Corrección',
                                'aprobado'            => 'Aprobado',
                                'cancelado'           => 'Cancelado',
                                'archivado'           => 'Archivado',
                            ];
                        @endphp
                        @foreach ($estadosFlujos as $key => $label)
                            @php
                                $badgeColor = match($key) {
                                    'en_progreso' => 'bg-primary/10 text-primary',
                                    'requiere_correccion' => 'bg-meta-1/10 text-meta-1',
                                    'aprobado' => 'bg-meta-3/10 text-meta-3',
                                    default => 'bg-gray-2 text-bodydark2 dark:bg-meta-4',
                                };
                            @endphp
                            <div class="rounded border border-stroke bg-gray-2 p-3 text-center dark:border-strokedark dark:bg-meta-4/20">
                                <span class="inline-flex rounded-full py-0.5 px-2 text-[9px] font-bold uppercase mb-1 {{ $badgeColor }}">
                                    {{ $label }}
                                </span>
                                <span class="text-sm font-bold text-black dark:text-white block">{{ $flujosPorEstado[$key] ?? 0 }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            {{-- 🟠 PESTAÑA 3: PLANTILLAS Y EQUIPOS --}}
            <div x-show="tab === 'plantillas'" x-cloak class="space-y-6">
                
                {{-- Uso de Plantillas --}}
                <div class="rounded-sm border border-stroke bg-white px-5 pt-6 pb-2.5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5">
                    <div class="mb-4 flex items-center justify-between flex-wrap gap-2">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-black dark:text-white">Uso de Plantillas de Flujos</h3>
                        <a href="{{ route('flujos.plantillas.index') }}" class="text-xs font-bold text-primary hover:underline uppercase">
                            Gestionar Plantillas <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>

                    <div class="max-w-full overflow-x-auto">
                        <table class="w-full table-auto">
                            <thead>
                                <tr class="bg-gray-2 text-left dark:bg-meta-4">
                                    <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase">Plantilla</th>
                                    <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase text-center w-36">Instancias Abiertas</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stroke dark:divide-strokedark">
                                @forelse ($flujosPorPlantilla as $plantilla)
                                    <tr class="hover:bg-gray-2 dark:hover:bg-meta-4/50 transition">
                                        <td class="py-3.5 px-4">
                                            <a href="{{ route('flujos.plantillas.edit', $plantilla) }}" class="font-bold text-xs text-black dark:text-white hover:text-primary transition block">
                                                {{ $plantilla->nombre }}
                                            </a>
                                            <span class="text-[10px] text-bodydark2 block font-mono">v{{ $plantilla->version }}</span>
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            <span class="inline-flex rounded-full py-0.5 px-2.5 text-[10px] font-bold uppercase {{ $plantilla->instancias_abiertas > 0 ? 'bg-meta-6/10 text-meta-6' : 'bg-gray-2 text-bodydark2 dark:bg-meta-4' }}">
                                                {{ $plantilla->instancias_abiertas }} abiertas
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="py-6 text-center text-xs text-bodydark2">Sin plantillas activas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Equipos por Estado --}}
                <div class="rounded-sm border border-stroke bg-white p-5 shadow-default dark:border-strokedark dark:bg-boxdark">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-black dark:text-white mb-3">Equipos Registrados por Estado</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        @forelse ($equiposPorEstado as $estado => $total)
                            <div class="rounded border border-stroke bg-gray-2 p-3 text-center dark:border-strokedark dark:bg-meta-4/20">
                                <span class="inline-flex rounded-full bg-primary/10 px-2 py-0.5 text-[9px] font-bold text-primary uppercase mb-1">
                                    {{ ucfirst(str_replace('_', ' ', $estado)) }}
                                </span>
                                <span class="text-sm font-bold text-black dark:text-white block">{{ $total }}</span>
                            </div>
                        @empty
                            <div class="col-span-4 text-center py-4 text-bodydark2 text-xs">Sin equipos registrados.</div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>

        {{-- COLUMNA DERECHA: Resumen de Infraestructura & Cuentas (4 cols) --}}
        <div class="col-span-12 lg:col-span-4 space-y-6">

            {{-- Widget de Infraestructura & Cuentas --}}
            <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="border-b border-stroke py-3.5 px-5 dark:border-strokedark">
                    <h3 class="font-bold text-black dark:text-white text-xs uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fas fa-server text-primary"></i> Infraestructura Global
                    </h3>
                </div>
                <div class="p-4 grid grid-cols-2 gap-3 text-center">
                    <div class="rounded border border-stroke bg-gray-2 p-3 dark:border-strokedark dark:bg-meta-4/20">
                        <span class="text-[10px] font-bold text-bodydark2 uppercase block">Usuarios</span>
                        <span class="text-sm font-bold text-black dark:text-white mt-0.5 block">{{ $totalUsuarios }}</span>
                    </div>
                    <div class="rounded border border-stroke bg-gray-2 p-3 dark:border-strokedark dark:bg-meta-4/20">
                        <span class="text-[10px] font-bold text-bodydark2 uppercase block">Empresas</span>
                        <span class="text-sm font-bold text-black dark:text-white mt-0.5 block">{{ $totalEmpresas }}</span>
                    </div>
                    <div class="rounded border border-stroke bg-gray-2 p-3 dark:border-strokedark dark:bg-meta-4/20">
                        <span class="text-[10px] font-bold text-bodydark2 uppercase block">Sedes</span>
                        <span class="text-sm font-bold text-black dark:text-white mt-0.5 block">{{ $totalSedes }}</span>
                    </div>
                    <div class="rounded border border-stroke bg-gray-2 p-3 dark:border-strokedark dark:bg-meta-4/20">
                        <span class="text-[10px] font-bold text-bodydark2 uppercase block">Equipos</span>
                        <span class="text-sm font-bold text-black dark:text-white mt-0.5 block">{{ $totalEquipos }}</span>
                    </div>
                </div>
            </div>

            {{-- Widget Acciones Rápidas --}}
            <div class="rounded-sm border border-stroke bg-white p-5 shadow-default dark:border-strokedark dark:bg-boxdark space-y-2">
                <span class="text-[10px] font-bold text-bodydark2 uppercase block mb-3">Accesos Directos de Administración</span>
                <a href="{{ route('usuarios.index') }}" 
                   class="flex items-center gap-2.5 rounded border border-stroke p-2.5 text-xs font-bold text-black dark:text-white hover:border-primary hover:text-primary dark:border-strokedark transition">
                    <i class="fas fa-users text-primary w-4 text-center"></i> Gestión de Usuarios
                </a>
                <a href="{{ route('roles.index') }}" 
                   class="flex items-center gap-2.5 rounded border border-stroke p-2.5 text-xs font-bold text-black dark:text-white hover:border-primary hover:text-primary dark:border-strokedark transition">
                    <i class="fas fa-shield-alt text-primary w-4 text-center"></i> Matriz de Roles & Permisos
                </a>
                <a href="{{ route('flujos.plantillas.index') }}" 
                   class="flex items-center gap-2.5 rounded border border-stroke p-2.5 text-xs font-bold text-black dark:text-white hover:border-primary hover:text-primary dark:border-strokedark transition">
                    <i class="fas fa-project-diagram text-primary w-4 text-center"></i> Diseñador de Flujos
                </a>
            </div>

        </div>

    </div>

</div>