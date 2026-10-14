{{-- resources/views/dashboard/partials/super_admin.blade.php --}}
@php
    $mostrarCodigoInterno = \App\Models\Setting::get('flujos.mostrar_codigo_interno', true);
@endphp

<div x-data="{ tab: 'flujos' }">

    {{-- ── Header Compacto: Selector de Pestañas + KPIs Inline ──────────────── --}}
    <div class="mb-5 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between border-b border-stroke pb-3.5 dark:border-strokedark">
        
        {{-- Botones de Navegación --}}
        <div class="flex items-center gap-2 flex-wrap shrink-0">
            <button type="button" @click="tab = 'tickets'"
                :class="tab === 'tickets' ? 'bg-primary text-white font-bold shadow-sm' : 'bg-white text-black hover:bg-stroke border border-stroke dark:bg-meta-4 dark:text-white dark:border-strokedark'"
                class="rounded-sm py-1.5 px-3 text-xs uppercase tracking-wider transition cursor-pointer">
                <i class="fas fa-ticket-alt mr-1.5"></i> Tickets
            </button>
            <button type="button" @click="tab = 'flujos'"
                :class="tab === 'flujos' ? 'bg-primary text-white font-bold shadow-sm' : 'bg-white text-black hover:bg-stroke border border-stroke dark:bg-meta-4 dark:text-white dark:border-strokedark'"
                class="rounded-sm py-1.5 px-3 text-xs uppercase tracking-wider transition cursor-pointer">
                <i class="fas fa-project-diagram mr-1.5"></i> Flujos
            </button>
            <button type="button" @click="tab = 'plantillas'"
                :class="tab === 'plantillas' ? 'bg-primary text-white font-bold shadow-sm' : 'bg-white text-black hover:bg-stroke border border-stroke dark:bg-meta-4 dark:text-white dark:border-strokedark'"
                class="rounded-sm py-1.5 px-3 text-xs uppercase tracking-wider transition cursor-pointer">
                <i class="fas fa-layer-group mr-1.5"></i> Plantillas & Equipos
            </button>
        </div>

        {{-- KPIs Compactos Inline --}}
        <div class="flex items-center gap-2 flex-wrap">
            
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
                <span class="inline-flex items-center gap-1 rounded bg-gray-2 dark:bg-meta-4/60 px-2.5 py-1 text-[11px] font-medium text-bodydark2 border border-stroke dark:border-strokedark">
                    <i class="fas fa-ban text-[10px]"></i>
                    <span class="font-bold text-black dark:text-white">{{ $flujosPorEstado['cancelado'] ?? 0 }}</span> Cancelados
                </span>
            </div>

            <div class="text-xs font-mono text-bodydark2 hidden xl:flex items-center gap-1.5 ml-2 pl-2 border-l border-stroke dark:border-strokedark">
                <span class="h-2 w-2 rounded-full bg-meta-1 animate-pulse"></span>
                <span class="font-bold uppercase tracking-wider text-[10px] text-black dark:text-white">Super Admin</span>
            </div>
        </div>
    </div>

    {{-- Layout Principal Grid Ampliado (9 cols Tabla / 3 cols Lateral) --}}
    <div class="grid grid-cols-12 gap-4 md:gap-5 2xl:gap-6">

        {{-- COLUMNA IZQUIERDA: Módulos Principales (9 cols -> 75% Ancho) --}}
        <div class="col-span-12 lg:col-span-9 space-y-5">

            {{-- 🟢 PESTAÑA 1: TICKETS --}}
            <div x-show="tab === 'tickets'" x-cloak class="space-y-5">

                {{-- Tabla Tickets Recientes --}}
                <div class="rounded-sm border border-stroke bg-white px-4 pt-4 pb-2 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-6">
                    <div class="mb-3 flex items-center justify-between flex-wrap gap-2">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-black dark:text-white">Tickets Recientes</h3>
                        <a href="{{ route('tickets.index') }}" class="text-[11px] font-bold text-primary hover:underline uppercase">
                            Ir al Módulo <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <div class="max-w-full overflow-x-auto">
                        <table class="w-full table-auto border-collapse">
                            <thead>
                                <tr class="bg-gray-2 text-left dark:bg-meta-4">
                                    <th class="py-2 px-3 font-bold text-black dark:text-white text-[11px] uppercase w-12">#</th>
                                    <th class="py-2 px-3 font-bold text-black dark:text-white text-[11px] uppercase min-w-[200px]">Título / Solicitante</th>
                                    <th class="py-2 px-3 font-bold text-black dark:text-white text-[11px] uppercase">Empresa</th>
                                    <th class="py-2 px-3 font-bold text-black dark:text-white text-[11px] uppercase text-center w-24">Estado</th>
                                    <th class="py-2 px-3 font-bold text-black dark:text-white text-[11px] uppercase text-center w-24">Prioridad</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stroke dark:divide-strokedark">
                                @forelse ($ticketsRecientes as $ticket)
                                    @php
                                        $colorEst = match ($ticket->estado) {
                                            'resuelto' => 'bg-meta-3/10 text-meta-3',
                                            'en_progreso' => 'bg-meta-6/10 text-meta-6',
                                            'cerrado', 'cancelado' => 'bg-meta-1/10 text-meta-1',
                                            default => 'bg-primary/10 text-primary',
                                        };
                                        $colorPrio = match ($ticket->prioridad) {
                                            'critica' => 'bg-meta-1/10 text-meta-1',
                                            'alta' => 'bg-meta-6/10 text-meta-6',
                                            default => 'bg-primary/10 text-primary',
                                        };
                                    @endphp
                                    <tr class="hover:bg-gray-2 dark:hover:bg-meta-4/50 transition">
                                        <td class="py-2 px-3 font-mono text-xs text-bodydark2">#{{ $ticket->id }}</td>
                                        <td class="py-2 px-3">
                                            <div class="flex items-center gap-2 whitespace-nowrap">
                                                <a href="{{ route('tickets.show', $ticket) }}" class="font-bold text-xs text-black dark:text-white hover:text-primary transition truncate max-w-[280px]">
                                                    {{ $ticket->titulo }}
                                                </a>
                                                <span class="text-[10px] text-bodydark2 shrink-0">
                                                    · {{ $ticket->solicitante?->name ?? 'Usuario borrado' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="py-2 px-3 text-xs font-medium text-black dark:text-white truncate max-w-[140px] whitespace-nowrap">
                                            {{ $ticket->empresa?->nombre ?? '—' }}
                                        </td>
                                        <td class="py-2 px-3 text-center whitespace-nowrap">
                                            <span class="inline-flex rounded-full py-0.5 px-2 text-[9px] font-bold uppercase {{ $colorEst }}">
                                                {{ $ticket->label_estado }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-3 text-center whitespace-nowrap">
                                            <span class="inline-flex rounded-full py-0.5 px-2 text-[9px] font-bold uppercase {{ $colorPrio }}">
                                                {{ $ticket->label_prioridad }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-5 text-center text-xs text-bodydark2">Sin tickets registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Desglose Tickets por Estado --}}
                <div class="rounded-sm border border-stroke bg-white p-4 shadow-default dark:border-strokedark dark:bg-boxdark">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-black dark:text-white mb-2.5">Desglose de Tickets por Estado</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-2">
                        @foreach (\App\Models\Ticket::ESTADOS as $key => $label)
                            @php
                                $badgeColor = match ($key) {
                                    'resuelto' => 'bg-meta-3/10 text-meta-3',
                                    'en_progreso' => 'bg-meta-6/10 text-meta-6',
                                    'cerrado', 'cancelado' => 'bg-meta-1/10 text-meta-1',
                                    default => 'bg-primary/10 text-primary',
                                };
                            @endphp
                            <div class="rounded border border-stroke bg-gray-2 p-2 text-center dark:border-strokedark dark:bg-meta-4/20">
                                <span class="inline-flex rounded-full py-0.5 px-2 text-[9px] font-bold uppercase mb-0.5 {{ $badgeColor }}">
                                    {{ $label }}
                                </span>
                                <span class="text-xs font-bold text-black dark:text-white block">{{ $ticketsPorEstado[$key] ?? 0 }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- 🟣 PESTAÑA 2: FLUJOS DE TRABAJO --}}
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

                {{-- Desglose Flujos por Estado --}}
                <div class="rounded-sm border border-stroke bg-white p-4 shadow-default dark:border-strokedark dark:bg-boxdark">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-black dark:text-white mb-2.5">Desglose de Flujos por Estado</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-2">
                        @php
                            $estadosFlujos = [
                                'en_progreso' => 'En progreso',
                                'requiere_correccion' => 'Corrección',
                                'aprobado' => 'Aprobado',
                                'cancelado' => 'Cancelado',
                                'archivado' => 'Archivado',
                            ];
                        @endphp
                        @foreach ($estadosFlujos as $key => $label)
                            @php
                                $badgeColor = match ($key) {
                                    'en_progreso' => 'bg-primary/10 text-primary',
                                    'requiere_correccion' => 'bg-meta-1/10 text-meta-1',
                                    'aprobado' => 'bg-meta-3/10 text-meta-3',
                                    default => 'bg-gray-2 text-bodydark2 dark:bg-meta-4',
                                };
                            @endphp
                            <div class="rounded border border-stroke bg-gray-2 p-2 text-center dark:border-strokedark dark:bg-meta-4/20">
                                <span class="inline-flex rounded-full py-0.5 px-2 text-[9px] font-bold uppercase mb-0.5 {{ $badgeColor }}">
                                    {{ $label }}
                                </span>
                                <span class="text-xs font-bold text-black dark:text-white block">{{ $flujosPorEstado[$key] ?? 0 }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- 🟠 PESTAÑA 3: PLANTILLAS Y EQUIPOS --}}
            <div x-show="tab === 'plantillas'" x-cloak class="space-y-5">
                {{-- Uso de Plantillas --}}
                <div class="rounded-sm border border-stroke bg-white px-4 pt-4 pb-2 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-6">
                    <div class="mb-3 flex items-center justify-between flex-wrap gap-2">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-black dark:text-white">Uso de Plantillas de Flujos</h3>
                        <a href="{{ route('flujos.plantillas.index') }}" class="text-[11px] font-bold text-primary hover:underline uppercase">
                            Gestionar Plantillas <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <div class="max-w-full overflow-x-auto">
                        <table class="w-full table-auto">
                            <thead>
                                <tr class="bg-gray-2 text-left dark:bg-meta-4">
                                    <th class="py-2.5 px-3 font-bold text-black dark:text-white text-[11px] uppercase">Plantilla</th>
                                    <th class="py-2.5 px-3 font-bold text-black dark:text-white text-[11px] uppercase text-center w-36">Instancias Abiertas</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stroke dark:divide-strokedark">
                                @forelse ($flujosPorPlantilla as $plantilla)
                                    <tr class="hover:bg-gray-2 dark:hover:bg-meta-4/50 transition">
                                        <td class="py-2 px-3">
                                            <div class="flex items-center gap-2 whitespace-nowrap">
                                                <a href="{{ route('flujos.plantillas.edit', $plantilla) }}" class="font-bold text-xs text-black dark:text-white hover:text-primary transition">
                                                    {{ $plantilla->nombre }}
                                                </a>
                                                <span class="text-[10px] text-bodydark2 font-mono">v{{ $plantilla->version }}</span>
                                            </div>
                                        </td>
                                        <td class="py-2 px-3 text-center whitespace-nowrap">
                                            <span class="inline-flex rounded-full py-0.5 px-2.5 text-[9px] font-bold uppercase {{ $plantilla->instancias_abiertas > 0 ? 'bg-meta-6/10 text-meta-6' : 'bg-gray-2 text-bodydark2 dark:bg-meta-4' }}">
                                                {{ $plantilla->instancias_abiertas }} abiertas
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="py-5 text-center text-xs text-bodydark2">Sin plantillas activas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Equipos por Estado --}}
                <div class="rounded-sm border border-stroke bg-white p-4 shadow-default dark:border-strokedark dark:bg-boxdark">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-black dark:text-white mb-2.5">Equipos Registrados por Estado</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        @forelse ($equiposPorEstado as $estado => $total)
                            <div class="rounded border border-stroke bg-gray-2 p-2 text-center dark:border-strokedark dark:bg-meta-4/20">
                                <span class="inline-flex rounded-full bg-primary/10 px-2 py-0.5 text-[9px] font-bold text-primary uppercase mb-0.5">
                                    {{ ucfirst(str_replace('_', ' ', $estado)) }}
                                </span>
                                <span class="text-xs font-bold text-black dark:text-white block">{{ $total }}</span>
                            </div>
                        @empty
                            <div class="col-span-4 text-center py-4 text-bodydark2 text-xs">Sin equipos registrados.</div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

        {{-- COLUMNA DERECHA: Resumen de Infraestructura & Cuentas (3 cols -> 25% Ancho) --}}
        <div class="col-span-12 lg:col-span-3 space-y-5">
            {{-- Widget Infraestructura Compacto --}}
            <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="border-b border-stroke py-2.5 px-4 dark:border-strokedark">
                    <h3 class="font-bold text-black dark:text-white text-[11px] uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fas fa-server text-primary"></i> Infraestructura
                    </h3>
                </div>
                <div class="p-3 grid grid-cols-2 gap-2 text-center">
                    <div class="rounded border border-stroke bg-gray-2 p-2 dark:border-strokedark dark:bg-meta-4/20">
                        <span class="text-[9px] font-bold text-bodydark2 uppercase block">Usuarios</span>
                        <span class="text-xs font-bold text-black dark:text-white block mt-0.5">{{ $totalUsuarios }}</span>
                    </div>
                    <div class="rounded border border-stroke bg-gray-2 p-3 dark:border-strokedark dark:bg-meta-4/20">
                        <span class="text-[9px] font-bold text-bodydark2 uppercase block">Empresas</span>
                        <span class="text-xs font-bold text-black dark:text-white block mt-0.5">{{ $totalEmpresas }}</span>
                    </div>
                    <div class="rounded border border-stroke bg-gray-2 p-3 dark:border-strokedark dark:bg-meta-4/20">
                        <span class="text-[9px] font-bold text-bodydark2 uppercase block">Sedes</span>
                        <span class="text-xs font-bold text-black dark:text-white block mt-0.5">{{ $totalSedes }}</span>
                    </div>
                    <div class="rounded border border-stroke bg-gray-2 p-3 dark:border-strokedark dark:bg-meta-4/20">
                        <span class="text-[9px] font-bold text-bodydark2 uppercase block">Equipos</span>
                        <span class="text-xs font-bold text-black dark:text-white block mt-0.5">{{ $totalEquipos }}</span>
                    </div>
                </div>
            </div>

            {{-- Widget Acciones Rápidas Compacto --}}
            <div class="rounded-sm border border-stroke bg-white p-4 shadow-default dark:border-strokedark dark:bg-boxdark space-y-1.5">
                <span class="text-[9px] font-bold text-bodydark2 uppercase block mb-2">Administración</span>
                <a href="{{ route('usuarios.index') }}"
                    class="flex items-center gap-2 rounded border border-stroke p-2 text-[11px] font-bold text-black dark:text-white hover:border-primary hover:text-primary dark:border-strokedark transition">
                    <i class="fas fa-users text-primary w-3.5 text-center text-xs"></i> Usuarios
                </a>
                <a href="{{ route('roles.index') }}"
                    class="flex items-center gap-2 rounded border border-stroke p-2 text-[11px] font-bold text-black dark:text-white hover:border-primary hover:text-primary dark:border-strokedark transition">
                    <i class="fas fa-shield-alt text-primary w-3.5 text-center text-xs"></i> Roles & Permisos
                </a>
                <a href="{{ route('flujos.plantillas.index') }}"
                    class="flex items-center gap-2 rounded border border-stroke p-2 text-[11px] font-bold text-black dark:text-white hover:border-primary hover:text-primary dark:border-strokedark transition">
                    <i class="fas fa-project-diagram text-primary w-3.5 text-center text-xs"></i> Diseñador Flujos
                </a>
            </div>
        </div>
    </div>
</div>