{{-- resources/views/layouts/partials/sidebar.blade.php --}}

@php
    $u = auth()->user();

    $ticketsEntry = match (true) {
        $u->can('tickets.ver-todos') => ['label' => 'Todos los Tickets', 'icon' => 'fa-ticket-alt'],
        $u->can('tickets.ver-asignados') => ['label' => 'Tickets Asignados', 'icon' => 'fa-user-clock'],
        $u->can('tickets.ver-propios') => ['label' => 'Mis Tickets', 'icon' => 'fa-ticket-alt'],
        default => null,
    };

    $verGestion = $u->canAny(['empresas.gestionar', 'sedes.gestionar', 'equipos.gestionar']);
    $verFlujos = $u->canAny([
        'flujos.plantillas.gestionar',
        'flujos.tipos-documento.gestionar',
        'flujos.iniciar',
        'flujos.ver-todos',
        'flujos.ver-participando',
    ]);
    $verSistema = $u->canAny(['usuarios.ver', 'roles.gestionar', 'auditoria.ver']);
@endphp

<aside :class="sidebarToggle ? 'translate-x-0' : '-translate-x-full'"
    class="fixed left-0 top-0 z-[9999] flex h-screen w-72.5 flex-col overflow-y-hidden bg-[#1c2434] duration-300 ease-linear dark:bg-boxdark lg:static lg:translate-x-0"
    @click.outside="sidebarToggle = false">

    {{-- Sidebar Header / Logo --}}
    <div class="flex items-center justify-between gap-2 px-6 py-5.5 lg:py-6.5">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary text-white">
                <i class="fas fa-ticket-alt text-lg"></i>
            </div>
            <span class="text-xl font-bold text-white tracking-wide">
                {{ config('app.name', env('APP_NAME', 'TailAdmin')) }}
            </span>
        </a>

        <button class="block lg:hidden text-bodydark hover:text-white" @click.stop="sidebarToggle = !sidebarToggle">
            <svg class="fill-current" width="20" height="18" viewBox="0 0 20 18" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M19 8.175H2.98748L9.36248 1.6875C9.69998 1.35 9.69998 0.825 9.36248 0.4875C9.02498 0.15 8.49998 0.15 8.16248 0.4875L0.399976 8.3625C0.0624756 8.7 0.0624756 9.225 0.399976 9.5625L8.16248 17.4375C8.31248 17.5875 8.53748 17.7 8.76248 17.7C8.98748 17.7 9.21248 17.6125 9.36248 17.4375C9.69998 17.1 9.69998 16.575 9.36248 16.2375L2.98748 9.75H19C19.45 9.75 19.7625 9.4125 19.7625 8.9625C19.7625 8.5125 19.45 8.175 19 8.175Z"
                    fill="" />
            </svg>
        </button>
    </div>

    {{-- Navigation Menu --}}
    <div class="no-scrollbar flex flex-col overflow-y-auto duration-300 ease-linear">
        <nav class="mt-5 py-4 px-4 lg:mt-9 lg:px-6">

            {{-- Menú Principal Group --}}
            <div>
                <h3 class="mb-4 ml-4 text-xs font-semibold text-bodydark2 uppercase tracking-wider">
                    MENÚ PRINCIPAL
                </h3>

                <ul class="mb-6 flex flex-col gap-1.5">

                    {{-- Dashboard Single Link --}}
                    <li>
                        <a href="{{ route('dashboard') }}"
                            class="group relative flex items-center gap-2.5 rounded-sm py-2 px-4 font-medium text-bodydark1 duration-300 ease-in-out hover:bg-graydark dark:hover:bg-meta-4 {{ request()->routeIs('dashboard') ? 'bg-graydark dark:bg-meta-4 text-white' : '' }}">
                            <i class="fas fa-th-large w-5 text-center text-sm"></i>
                            Dashboard
                        </a>
                    </li>

                    {{-- Soporte Técnico Dropdown --}}
                    <li x-data="{ open: {{ request()->routeIs(['tickets.*']) ? 'true' : 'false' }} }">
                        <a href="#" @click.prevent="open = !open"
                            class="group relative flex items-center justify-between rounded-sm py-2 px-4 font-medium text-bodydark1 duration-300 ease-in-out hover:bg-graydark dark:hover:bg-meta-4 {{ request()->routeIs(['tickets.*']) ? 'bg-graydark dark:bg-meta-4 text-white' : '' }}">
                            <div class="flex items-center gap-2.5">
                                <i class="fas fa-headset w-5 text-center text-sm"></i>
                                Soporte Técnico
                            </div>
                            <svg class="absolute right-4 top-1/2 -translate-y-1/2 fill-current transition-transform duration-200"
                                :class="open && 'rotate-180'" width="20" height="20" viewBox="0 0 20 20"
                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M4.41107 6.9107 4.41107 6.9107C4.73651 6.58527 5.26415 6.58527 5.58959 6.9107L10 12.3211L14.4104 6.91071C14.7358 6.58527 15.2635 6.58527 15.5889 6.91071C15.9144 7.23615 15.9144 7.76379 15.5889 8.08923L10.5893 14.0892C10.2638 14.4147 9.73618 14.4147 9.41074 14.0892L4.41107 8.08923C4.08563 7.76379 4.08563 7.23614 4.41107 6.9107Z"
                                    fill="" />
                            </svg>
                        </a>

                        {{-- Submenu --}}
                        <div x-show="open" x-cloak class="overflow-hidden transition-all duration-300">
                            <ul class="mt-2 mb-3 flex flex-col gap-1.5 pl-6">
                                @if ($ticketsEntry)
                                    <li>
                                        <a href="{{ route('tickets.index') }}"
                                            class="group relative flex items-center gap-2.5 rounded-md font-medium text-bodydark2 duration-300 ease-in-out hover:text-white {{ request()->routeIs('tickets.index') && !request()->fullUrlIs('*sin_asignar=1*') ? 'text-white' : '' }}">
                                            {{ $ticketsEntry['label'] }}
                                        </a>
                                    </li>
                                @endif

                                @can('tickets.crear')
                                    <li>
                                        <a href="{{ route('tickets.create') }}"
                                            class="group relative flex items-center gap-2.5 rounded-md font-medium text-bodydark2 duration-300 ease-in-out hover:text-white {{ request()->routeIs('tickets.create') ? 'text-white' : '' }}">
                                            Nuevo Ticket
                                        </a>
                                    </li>
                                @endcan

                                @can('tickets.asignar')
                                    <li>
                                        <a href="{{ route('tickets.index', ['estado' => 'abierto', 'sin_asignar' => 1]) }}"
                                            class="group relative flex items-center gap-2.5 rounded-md font-medium text-bodydark2 duration-300 ease-in-out hover:text-white {{ request()->fullUrlIs('*sin_asignar=1*') ? 'text-white' : '' }}">
                                            Sin Asignar
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </li>

                    {{-- Gestión Dropdown --}}
                    @if ($verGestion)
                        <li x-data="{ open: {{ request()->routeIs(['empresas.*', 'sedes.*', 'equipos.*']) ? 'true' : 'false' }} }">
                            <a href="#" @click.prevent="open = !open"
                                class="group relative flex items-center justify-between rounded-sm py-2 px-4 font-medium text-bodydark1 duration-300 ease-in-out hover:bg-graydark dark:hover:bg-meta-4 {{ request()->routeIs(['empresas.*', 'sedes.*', 'equipos.*']) ? 'bg-graydark dark:bg-meta-4 text-white' : '' }}">
                                <div class="flex items-center gap-2.5">
                                    <i class="fas fa-briefcase w-5 text-center text-sm"></i>
                                    Gestión
                                </div>
                                <svg class="absolute right-4 top-1/2 -translate-y-1/2 fill-current transition-transform duration-200"
                                    :class="open && 'rotate-180'" width="20" height="20" viewBox="0 0 20 20"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M4.41107 6.9107 4.41107 6.9107C4.73651 6.58527 5.26415 6.58527 5.58959 6.9107L10 12.3211L14.4104 6.91071C14.7358 6.58527 15.2635 6.58527 15.5889 6.91071C15.9144 7.23615 15.9144 7.76379 15.5889 8.08923L10.5893 14.0892C10.2638 14.4147 9.73618 14.4147 9.41074 14.0892L4.41107 8.08923C4.08563 7.76379 4.08563 7.23614 4.41107 6.9107Z"
                                        fill="" />
                                </svg>
                            </a>

                            <div x-show="open" x-cloak class="overflow-hidden transition-all duration-300">
                                <ul class="mt-2 mb-3 flex flex-col gap-1.5 pl-6">
                                    @can('empresas.gestionar')
                                        <li>
                                            <a href="{{ route('empresas.index') }}"
                                                class="group relative flex items-center gap-2.5 rounded-md font-medium text-bodydark2 duration-300 ease-in-out hover:text-white {{ request()->routeIs('empresas.*') ? 'text-white' : '' }}">
                                                Empresas
                                            </a>
                                        </li>
                                    @endcan

                                    @can('sedes.gestionar')
                                        <li>
                                            <a href="{{ route('sedes.index') }}"
                                                class="group relative flex items-center gap-2.5 rounded-md font-medium text-bodydark2 duration-300 ease-in-out hover:text-white {{ request()->routeIs('sedes.*') ? 'text-white' : '' }}">
                                                Sedes
                                            </a>
                                        </li>
                                    @endcan

                                    @can('equipos.ver')
                                        <li>
                                            <a href="{{ route('equipos.index') }}"
                                                class="group relative flex items-center gap-2.5 rounded-md font-medium text-bodydark2 duration-300 ease-in-out hover:text-white {{ request()->routeIs('equipos.*') ? 'text-white' : '' }}">
                                                Equipos
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
                                class="group relative flex items-center justify-between rounded-sm py-2 px-4 font-medium text-bodydark1 duration-300 ease-in-out hover:bg-graydark dark:hover:bg-meta-4 {{ request()->routeIs('flujos.*') ? 'bg-graydark dark:bg-meta-4 text-white' : '' }}">
                                <div class="flex items-center gap-2.5">
                                    <i class="fas fa-project-diagram w-5 text-center text-sm"></i>
                                    Flujos
                                </div>
                                <svg class="absolute right-4 top-1/2 -translate-y-1/2 fill-current transition-transform duration-200"
                                    :class="open && 'rotate-180'" width="20" height="20" viewBox="0 0 20 20"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M4.41107 6.9107 4.41107 6.9107C4.73651 6.58527 5.26415 6.58527 5.58959 6.9107L10 12.3211L14.4104 6.91071C14.7358 6.58527 15.2635 6.58527 15.5889 6.91071C15.9144 7.23615 15.9144 7.76379 15.5889 8.08923L10.5893 14.0892C10.2638 14.4147 9.73618 14.4147 9.41074 14.0892L4.41107 8.08923C4.08563 7.76379 4.08563 7.23614 4.41107 6.9107Z"
                                        fill="" />
                                </svg>
                            </a>

                            <div x-show="open" x-cloak class="overflow-hidden transition-all duration-300">
                                <ul class="mt-2 mb-3 flex flex-col gap-1.5 pl-6">
                                    @can('flujos.ver-participando')
                                        @php $countBandeja = $flujosPendientesSidebar ?? 0; @endphp
                                        <li>
                                            <a href="{{ route('flujos.instancias.index') }}"
                                                class="group relative flex items-center justify-between rounded-md font-medium text-bodydark2 duration-300 ease-in-out hover:text-white {{ request()->routeIs('flujos.instancias.index') || request()->routeIs('flujos.instancias.show') ? 'text-white' : '' }}">
                                                <span>Mi Bandeja</span>
                                                @if ($countBandeja > 0)
                                                    <span
                                                        class="rounded bg-meta-1 px-1.5 py-0.5 text-[10px] font-extrabold text-white">
                                                        {{ $countBandeja > 99 ? '99+' : $countBandeja }}
                                                    </span>
                                                @endif
                                            </a>
                                        </li>
                                    @endcan

                                    @can('flujos.iniciar')
                                        <li>
                                            <a href="{{ route('flujos.instancias.create') }}"
                                                class="group relative flex items-center gap-2.5 rounded-md font-medium text-bodydark2 duration-300 ease-in-out hover:text-white {{ request()->routeIs('flujos.instancias.create') ? 'text-white' : '' }}">
                                                Nuevo Flujo
                                            </a>
                                        </li>
                                    @endcan

                                    @can('flujos.tipos-documento.gestionar')
                                        <li>
                                            <a href="{{ route('flujos.tipos-documento.index') }}"
                                                class="group relative flex items-center gap-2.5 rounded-md font-medium text-bodydark2 duration-300 ease-in-out hover:text-white {{ request()->routeIs('flujos.tipos-documento.*') ? 'text-white' : '' }}">
                                                Tipos Documento
                                            </a>
                                        </li>
                                    @endcan

                                    @can('flujos.plantillas.gestionar')
                                        <li>
                                            <a href="{{ route('flujos.plantillas.index') }}"
                                                class="group relative flex items-center gap-2.5 rounded-md font-medium text-bodydark2 duration-300 ease-in-out hover:text-white {{ request()->routeIs('flujos.plantillas.*') ? 'text-white' : '' }}">
                                                Plantillas de flujo
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
                    <h3 class="mb-4 ml-4 text-xs font-semibold text-bodydark2 uppercase tracking-wider">
                        CONFIGURACIÓN
                    </h3>

                    <ul class="mb-6 flex flex-col gap-1.5">
                        <li x-data="{ open: {{ request()->routeIs(['usuarios.*', 'roles.*', 'auditoria.*']) ? 'true' : 'false' }} }">
                            <a href="#" @click.prevent="open = !open"
                                class="group relative flex items-center justify-between rounded-sm py-2 px-4 font-medium text-bodydark1 duration-300 ease-in-out hover:bg-graydark dark:hover:bg-meta-4 {{ request()->routeIs(['usuarios.*', 'roles.*', 'auditoria.*']) ? 'bg-graydark dark:bg-meta-4 text-white' : '' }}">
                                <div class="flex items-center gap-2.5">
                                    <i class="fas fa-cogs w-5 text-center text-sm"></i>
                                    Sistema
                                </div>
                                <svg class="absolute right-4 top-1/2 -translate-y-1/2 fill-current transition-transform duration-200"
                                    :class="open && 'rotate-180'" width="20" height="20" viewBox="0 0 20 20"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M4.41107 6.9107 4.41107 6.9107C4.73651 6.58527 5.26415 6.58527 5.58959 6.9107L10 12.3211L14.4104 6.91071C14.7358 6.58527 15.2635 6.58527 15.5889 6.91071C15.9144 7.23615 15.9144 7.76379 15.5889 8.08923L10.5893 14.0892C10.2638 14.4147 9.73618 14.4147 9.41074 14.0892L4.41107 8.08923C4.08563 7.76379 4.08563 7.23614 4.41107 6.9107Z"
                                        fill="" />
                                </svg>
                            </a>

                            <div x-show="open" x-cloak class="overflow-hidden transition-all duration-300">
                                <ul class="mt-2 mb-3 flex flex-col gap-1.5 pl-6">
                                    @can('usuarios.ver')
                                        <li>
                                            <a href="{{ route('usuarios.index') }}"
                                                class="group relative flex items-center gap-2.5 rounded-md font-medium text-bodydark2 duration-300 ease-in-out hover:text-white {{ request()->routeIs('usuarios.*') ? 'text-white' : '' }}">
                                                Usuarios
                                            </a>
                                        </li>
                                    @endcan

                                    @can('roles.gestionar')
                                        <li>
                                            <a href="{{ route('roles.index') }}"
                                                class="group relative flex items-center gap-2.5 rounded-md font-medium text-bodydark2 duration-300 ease-in-out hover:text-white {{ request()->routeIs('roles.*') ? 'text-white' : '' }}">
                                                Roles y Accesos
                                            </a>
                                        </li>
                                    @endcan
                                    @can('roles.gestionar')
                                        <li>
                                            <a href="{{ route('configuracion.ajustes.index') }}"
                                                class="group relative flex items-center gap-2.5 rounded-md font-medium text-bodydark2 duration-300 ease-in-out hover:text-white {{ request()->routeIs('roles.*') ? 'text-white' : '' }}">
                                                Ajustes globales
                                            </a>
                                        </li>
                                    @endcan
                                    @can('auditoria.ver')
                                        <li>
                                            <a href="{{ route('auditoria.index') }}"
                                                class="group relative flex items-center gap-2.5 rounded-md font-medium text-bodydark2 duration-300 ease-in-out hover:text-white {{ request()->routeIs('roles.*') ? 'text-white' : '' }}">
                                                Auditoria
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
