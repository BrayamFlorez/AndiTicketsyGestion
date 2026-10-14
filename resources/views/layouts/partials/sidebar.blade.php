{{-- resources/views/layouts/partials/sidebar.blade.php --}}

@php
    $u = auth()->user();

    $ticketsEntry = match (true) {
        $u->can('tickets.ver-todos') => ['label' => 'Todos los Tickets', 'icon' => 'fa-ticket-alt'],
        $u->can('tickets.ver-asignados') => ['label' => 'Tickets Asignados', 'icon' => 'fa-user-clock'],
        $u->can('tickets.ver-propios') => ['label' => 'Mis Tickets', 'icon' => 'fa-ticket-alt'],
        default => null,
    };

    $verGestion = $u->canAny(['empresas.gestionar', 'sedes.gestionar', 'equipos.ver', 'impresoras.ver']);
    $verFlujos = $u->canAny([
        'flujos.plantillas.gestionar',
        'flujos.tipos-documento.gestionar',
        'flujos.iniciar',
        'flujos.ver-todos',
        'flujos.ver-participando',
    ]);
    $verSistema = $u->canAny(['usuarios.ver', 'roles.gestionar', 'auditoria.ver']);
@endphp

<aside :class="sidebarToggle ? 'translate-x-0 lg:ml-0' : '-translate-x-full lg:ml-[-290px]'"
    class="absolute lg:static left-0 top-0 z-[9999] flex h-screen w-[290px] flex-col overflow-y-hidden bg-[#1c2434] duration-300 ease-in-out dark:bg-boxdark shadow-lg"
    @click.outside="if (window.innerWidth < 1024) sidebarToggle = false">

    {{-- Sidebar Header / Logo --}}
    <div
        class="relative flex flex-col items-center justify-center px-6 pt-6 pb-4 lg:pt-8 lg:pb-6 text-center border-b border-white/5">

        <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center gap-2 group">
            {{-- Logo Más Grande (64x64 px) --}}
            <div
                class="flex h-16 w-16 items-center justify-center rounded-xl bg-white/5 p-2 shadow-md border border-white/10 group-hover:border-primary/50 transition duration-200">
                <img src="{{ asset('img/andi.webp') }}" alt="{{ config('app.name', 'ANDI SOFTWARE') }}"
                    class="h-full w-full object-contain drop-shadow-sm">
            </div>

            {{-- Nombre Más Pequeño y Estilizado --}}
            <span class="text-xs font-bold text-white tracking-widest uppercase truncate max-w-[180px] mt-1">
                {{ config('app.name', env('APP_NAME', 'ANDI SOFTWARE')) }}
            </span>
        </a>

        {{-- Botón Cerrar en Móvil (Posición Absoluta Superior Derecha) --}}
        <button class="absolute top-4 right-4 block lg:hidden text-bodydark hover:text-white transition"
            @click.stop="sidebarToggle = false">
            <i class="fas fa-times text-lg"></i>
        </button>
    </div>

    {{-- Navigation Menu --}}
    <div class="no-scrollbar flex flex-col overflow-y-auto duration-300 ease-linear">
        <nav class="py-2 px-4 lg:px-6">

            {{-- Menú Principal Group --}}
            <div>
                <h3 class="mb-4 ml-4 text-[11px] font-bold text-bodydark2 uppercase tracking-widest">
                    Menú Principal
                </h3>

                <ul class="mb-8 flex flex-col gap-1.5">

                    {{-- Dashboard --}}
                    <li>
                        <a href="{{ route('dashboard') }}"
                            class="group relative flex items-center gap-3 rounded-md px-4 py-2.5 text-sm font-medium text-bodydark1 transition-all duration-300 ease-in-out hover:text-white hover:bg-white/10 dark:hover:bg-meta-4 {{ request()->routeIs('dashboard') ? 'bg-white/10 dark:bg-meta-4 text-white font-semibold' : '' }}">
                            <i class="fas fa-th-large w-5 text-center text-[15px]"></i>
                            Dashboard
                        </a>
                    </li>

                    {{-- Soporte Técnico Dropdown --}}
                    <li x-data="{ open: {{ request()->routeIs(['tickets.*']) ? 'true' : 'false' }} }">
                        <a href="#" @click.prevent="open = !open"
                            class="group relative flex items-center justify-between rounded-md px-4 py-2.5 text-sm font-medium text-bodydark1 transition-all duration-300 ease-in-out hover:text-white hover:bg-white/10 dark:hover:bg-meta-4 {{ request()->routeIs(['tickets.*']) ? 'bg-white/10 dark:bg-meta-4 text-white font-semibold' : '' }}">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-headset w-5 text-center text-[15px]"></i>
                                Soporte Técnico
                            </div>
                            <i class="fas fa-chevron-down text-xs transition-transform duration-200"
                                :class="open && 'rotate-180'"></i>
                        </a>

                        <div x-show="open" x-cloak class="overflow-hidden transition-all duration-300">
                            <ul class="mt-2 mb-3 flex flex-col gap-1 pl-8 pr-2">
                                @if ($ticketsEntry)
                                    <li>
                                        <a href="{{ route('tickets.index') }}"
                                            class="flex items-center gap-2 rounded-md px-3 py-2 text-xs font-medium text-bodydark2 transition-colors duration-300 hover:text-white {{ request()->routeIs('tickets.index') && !request()->fullUrlIs('*sin_asignar=1*') ? 'text-white font-semibold' : '' }}">
                                            <i class="fas {{ $ticketsEntry['icon'] }} w-4 text-center text-xs"></i>
                                            <span>{{ $ticketsEntry['label'] }}</span>
                                        </a>
                                    </li>
                                @endif

                                @can('tickets.crear')
                                    <li>
                                        <a href="{{ route('tickets.create') }}"
                                            class="flex items-center gap-2 rounded-md px-3 py-2 text-xs font-medium text-bodydark2 transition-colors duration-300 hover:text-white {{ request()->routeIs('tickets.create') ? 'text-white font-semibold' : '' }}">
                                            <i class="fas fa-plus-circle w-4 text-center text-xs"></i>
                                            <span>Nuevo Ticket</span>
                                        </a>
                                    </li>
                                @endcan

                                @can('tickets.asignar')
                                    <li>
                                        <a href="{{ route('tickets.index', ['estado' => 'abierto', 'sin_asignar' => 1]) }}"
                                            class="flex items-center gap-2 rounded-md px-3 py-2 text-xs font-medium text-bodydark2 transition-colors duration-300 hover:text-white {{ request()->fullUrlIs('*sin_asignar=1*') ? 'text-white font-semibold' : '' }}">
                                            <i class="fas fa-inbox w-4 text-center text-xs"></i>
                                            <span>Sin Asignar</span>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </li>

                    {{-- Proyectos Dropdown --}}
                    @canany(['proyectos.ver', 'proyectos.ver-todos'])
                        <li x-data="{ open: {{ request()->routeIs(['proyectos.*', 'clientes.*']) ? 'true' : 'false' }} }">
                            <a href="#" @click.prevent="open = !open"
                                class="group relative flex items-center justify-between rounded-md px-4 py-2.5 text-sm font-medium text-bodydark1 transition-all duration-300 ease-in-out hover:text-white hover:bg-white/10 dark:hover:bg-meta-4 {{ request()->routeIs(['proyectos.*', 'clientes.*']) ? 'bg-white/10 dark:bg-meta-4 text-white font-semibold' : '' }}">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-diagram-project w-5 text-center text-[15px]"></i>
                                    Proyectos
                                </div>
                                <i class="fas fa-chevron-down text-xs transition-transform duration-200"
                                    :class="open && 'rotate-180'"></i>
                            </a>

                            <div x-show="open" x-cloak class="overflow-hidden transition-all duration-300">
                                <ul class="mt-2 mb-3 flex flex-col gap-1 pl-8 pr-2">
                                    <li>
                                        <a href="{{ route('proyectos.index') }}"
                                            class="flex items-center gap-2 rounded-md px-3 py-2 text-xs font-medium text-bodydark2 transition-colors duration-300 hover:text-white {{ request()->routeIs('proyectos.*') ? 'text-white font-semibold' : '' }}">
                                            <i class="fas fa-list w-4 text-center text-xs"></i>
                                            <span>Todos los Proyectos</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endcanany

                    {{-- Gestión Entidades Dropdown --}}
                    @if ($verGestion)
                        <li x-data="{ open: {{ request()->routeIs(['empresas.*', 'sedes.*', 'equipos.*', 'impresoras.*']) ? 'true' : 'false' }} }">
                            <a href="#" @click.prevent="open = !open"
                                class="group relative flex items-center justify-between rounded-md px-4 py-2.5 text-sm font-medium text-bodydark1 transition-all duration-300 ease-in-out hover:text-white hover:bg-white/10 dark:hover:bg-meta-4 {{ request()->routeIs(['empresas.*', 'sedes.*', 'equipos.*', 'impresoras.*']) ? 'bg-white/10 dark:bg-meta-4 text-white font-semibold' : '' }}">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-briefcase w-5 text-center text-[15px]"></i>
                                    Gestión Entidades
                                </div>
                                <i class="fas fa-chevron-down text-xs transition-transform duration-200"
                                    :class="open && 'rotate-180'"></i>
                            </a>

                            <div x-show="open" x-cloak class="overflow-hidden transition-all duration-300">
                                <ul class="mt-2 mb-3 flex flex-col gap-1 pl-8 pr-2">
                                    @can('empresas.gestionar')
                                        <li>
                                            <a href="{{ route('empresas.index') }}"
                                                class="flex items-center gap-2 rounded-md px-3 py-2 text-xs font-medium text-bodydark2 transition-colors duration-300 hover:text-white {{ request()->routeIs('empresas.*') ? 'text-white font-semibold' : '' }}">
                                                <i class="fas fa-building w-4 text-center text-xs"></i>
                                                <span>Empresas</span>
                                            </a>
                                        </li>
                                    @endcan

                                    @can('sedes.gestionar')
                                        <li>
                                            <a href="{{ route('sedes.index') }}"
                                                class="flex items-center gap-2 rounded-md px-3 py-2 text-xs font-medium text-bodydark2 transition-colors duration-300 hover:text-white {{ request()->routeIs('sedes.*') ? 'text-white font-semibold' : '' }}">
                                                <i class="fas fa-map-marker-alt w-4 text-center text-xs"></i>
                                                <span>Sedes</span>
                                            </a>
                                        </li>
                                    @endcan

                                    @can('equipos.ver')
                                        <li>
                                            <a href="{{ route('equipos.index') }}"
                                                class="flex items-center gap-2 rounded-md px-3 py-2 text-xs font-medium text-bodydark2 transition-colors duration-300 hover:text-white {{ request()->routeIs('equipos.*') ? 'text-white font-semibold' : '' }}">
                                                <i class="fas fa-desktop w-4 text-center text-xs"></i>
                                                <span>Equipos</span>
                                            </a>
                                        </li>
                                    @endcan

                                    @can('impresoras.ver')
                                        <li>
                                            <a href="{{ route('impresoras.index') }}"
                                                class="flex items-center gap-2 rounded-md px-3 py-2 text-xs font-medium text-bodydark2 transition-colors duration-300 hover:text-white {{ request()->routeIs('impresoras.*') ? 'text-white font-semibold' : '' }}">
                                                <i class="fas fa-print w-4 text-center text-xs"></i>
                                                <span>Impresoras</span>
                                            </a>
                                        </li>
                                    @endcan

                                    @can('impresoras.gestionar-catalogo')
                                        <li>
                                            <a href="{{ route('impresoras.catalogo.index') }}"
                                                class="flex items-center gap-2 rounded-md px-3 py-2 text-xs font-medium text-bodydark2 transition-colors duration-300 hover:text-white {{ request()->routeIs('impresoras.catalogo.*') ? 'text-white font-semibold' : '' }}">
                                                <i class="fas fa-layer-group w-4 text-center text-xs"></i>
                                                <span>Catálogo de impresoras</span>
                                            </a>
                                        </li>
                                    @endcan

                                    @can('reportes.inventario')
                                        <li>
                                            <a href="{{ route('reportes.inventario.index') }}"
                                                class="flex items-center gap-2 rounded-md px-3 py-2 text-xs font-medium text-bodydark2 transition-colors duration-300 hover:text-white {{ request()->routeIs('reportes.inventario.*') ? 'text-white font-semibold' : '' }}">
                                                <i class="fas fa-layer-group w-4 text-center text-xs"></i>
                                                <span>Reporte de inventario</span>
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                    @endif

                    {{-- Flujos Dropdown --}}
                    @if ($verFlujos)
                        <li x-data="{ open: {{ request()->routeIs('flujos.*') ? 'true' : 'false' }} }">
                            <a href="#" @click.prevent="open = !open"
                                class="group relative flex items-center justify-between rounded-md px-4 py-2.5 text-sm font-medium text-bodydark1 transition-all duration-300 ease-in-out hover:text-white hover:bg-white/10 dark:hover:bg-meta-4 {{ request()->routeIs('flujos.*') ? 'bg-white/10 dark:bg-meta-4 text-white font-semibold' : '' }}">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-project-diagram w-5 text-center text-[15px]"></i>
                                    Flujos de Trabajo
                                </div>
                                <i class="fas fa-chevron-down text-xs transition-transform duration-200"
                                    :class="open && 'rotate-180'"></i>
                            </a>

                            <div x-show="open" x-cloak class="overflow-hidden transition-all duration-300">
                                <ul class="mt-2 mb-3 flex flex-col gap-1 pl-8 pr-2">
                                    @can('flujos.ver-participando')
                                        @php $countBandeja = $flujosPendientesSidebar ?? 0; @endphp
                                        <li>
                                            <a href="{{ route('flujos.instancias.index') }}"
                                                class="flex items-center justify-between rounded-md px-3 py-2 text-xs font-medium text-bodydark2 transition-colors duration-300 hover:text-white {{ request()->routeIs('flujos.instancias.index') || request()->routeIs('flujos.instancias.show') ? 'text-white font-semibold' : '' }}">
                                                <div class="flex items-center gap-2">
                                                    <i class="fas fa-inbox w-4 text-center text-xs"></i>
                                                    <span>Mi Bandeja</span>
                                                </div>
                                                @if ($countBandeja > 0)
                                                    <span
                                                        class="rounded bg-meta-1 px-1.5 py-0.2 text-[9px] font-bold text-white shadow-sm">
                                                        {{ $countBandeja > 99 ? '99+' : $countBandeja }}
                                                    </span>
                                                @endif
                                            </a>
                                        </li>
                                    @endcan

                                    @can('flujos.iniciar')
                                        <li>
                                            <a href="{{ route('flujos.instancias.create') }}"
                                                class="flex items-center gap-2 rounded-md px-3 py-2 text-xs font-medium text-bodydark2 transition-colors duration-300 hover:text-white {{ request()->routeIs('flujos.instancias.create') ? 'text-white font-semibold' : '' }}">
                                                <i class="fas fa-play-circle w-4 text-center text-xs"></i>
                                                <span>Nuevo Flujo</span>
                                            </a>
                                        </li>
                                    @endcan

                                    @can('flujos.tipos-documento.gestionar')
                                        <li>
                                            <a href="{{ route('flujos.tipos-documento.index') }}"
                                                class="flex items-center gap-2 rounded-md px-3 py-2 text-xs font-medium text-bodydark2 transition-colors duration-300 hover:text-white {{ request()->routeIs('flujos.tipos-documento.*') ? 'text-white font-semibold' : '' }}">
                                                <i class="fas fa-file-alt w-4 text-center text-xs"></i>
                                                <span>Tipos de Documento</span>
                                            </a>
                                        </li>
                                    @endcan

                                    @can('flujos.plantillas.gestionar')
                                        <li>
                                            <a href="{{ route('flujos.plantillas.index') }}"
                                                class="flex items-center gap-2 rounded-md px-3 py-2 text-xs font-medium text-bodydark2 transition-colors duration-300 hover:text-white {{ request()->routeIs('flujos.plantillas.*') ? 'text-white font-semibold' : '' }}">
                                                <i class="fas fa-sitemap w-4 text-center text-xs"></i>
                                                <span>Plantillas de Flujo</span>
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                    @endif

                </ul>
            </div>

            {{-- Configuración / Sistema Group --}}
            @if ($verSistema)
                <div>
                    <h3 class="mb-4 ml-4 text-[11px] font-bold text-bodydark2 uppercase tracking-widest">
                        Sistema y Configuración
                    </h3>

                    <ul class="mb-6 flex flex-col gap-1.5">
                        <li x-data="{ open: {{ request()->routeIs(['usuarios.*', 'roles.*', 'auditoria.*', 'configuracion.*']) ? 'true' : 'false' }} }">
                            <a href="#" @click.prevent="open = !open"
                                class="group relative flex items-center justify-between rounded-md px-4 py-2.5 text-sm font-medium text-bodydark1 transition-all duration-300 ease-in-out hover:text-white hover:bg-white/10 dark:hover:bg-meta-4 {{ request()->routeIs(['usuarios.*', 'roles.*', 'auditoria.*', 'configuracion.*']) ? 'bg-white/10 dark:bg-meta-4 text-white font-semibold' : '' }}">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-cogs w-5 text-center text-[15px]"></i>
                                    Ajustes y Seguridad
                                </div>
                                <i class="fas fa-chevron-down text-xs transition-transform duration-200"
                                    :class="open && 'rotate-180'"></i>
                            </a>

                            <div x-show="open" x-cloak class="overflow-hidden transition-all duration-300">
                                <ul class="mt-2 mb-3 flex flex-col gap-1 pl-8 pr-2">
                                    @can('usuarios.ver')
                                        <li>
                                            <a href="{{ route('usuarios.index') }}"
                                                class="flex items-center gap-2 rounded-md px-3 py-2 text-xs font-medium text-bodydark2 transition-colors duration-300 hover:text-white {{ request()->routeIs('usuarios.*') ? 'text-white font-semibold' : '' }}">
                                                <i class="fas fa-users w-4 text-center text-xs"></i>
                                                <span>Usuarios</span>
                                            </a>
                                        </li>
                                    @endcan

                                    @can('roles.gestionar')
                                        <li>
                                            <a href="{{ route('roles.index') }}"
                                                class="flex items-center gap-2 rounded-md px-3 py-2 text-xs font-medium text-bodydark2 transition-colors duration-300 hover:text-white {{ request()->routeIs('roles.*') ? 'text-white font-semibold' : '' }}">
                                                <i class="fas fa-user-shield w-4 text-center text-xs"></i>
                                                <span>Roles y Permisos</span>
                                            </a>
                                        </li>
                                    @endcan

                                    @role('super-admin')
                                        <li>
                                            <a href="{{ route('configuracion.ajustes.index') }}"
                                                class="flex items-center gap-2 rounded-md px-3 py-2 text-xs font-medium text-bodydark2 transition-colors duration-300 hover:text-white {{ request()->routeIs('configuracion.ajustes.*') ? 'text-white font-semibold' : '' }}">
                                                <i class="fas fa-sliders-h w-4 text-center text-xs"></i>
                                                <span>Ajustes Globales</span>
                                            </a>
                                        </li>
                                    @endrole

                                    @can('auditoria.ver')
                                        <li>
                                            <a href="{{ route('auditoria.index') }}"
                                                class="flex items-center gap-2 rounded-md px-3 py-2 text-xs font-medium text-bodydark2 transition-colors duration-300 hover:text-white {{ request()->routeIs('auditoria.*') ? 'text-white font-semibold' : '' }}">
                                                <i class="fas fa-shield-alt w-4 text-center text-xs"></i>
                                                <span>Auditoría</span>
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            @endif

        </nav>
    </div>
</aside>
