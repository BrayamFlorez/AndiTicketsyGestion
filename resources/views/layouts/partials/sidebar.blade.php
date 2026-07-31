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

<aside :class="sidebarOpen ? 'w-64' : 'w-20'"
    class="bg-vscode-dark h-screen transition-all duration-300 ease-in-out flex flex-col shadow-2xl z-50 relative border-r border-gray-800 select-none"
    style="background-color: #1e1e1e;">

    {{-- ── Brand ─────────────────────────────────────────────────────── --}}
    <div class="h-16 flex items-center px-6 bg-[#007acc] text-white overflow-hidden flex-shrink-0">
        <i class="fas fa-ticket text-xl flex-shrink-0"></i>
        <span x-show="sidebarOpen" x-transition:enter.duration.300ms
            class="ml-4 font-bold tracking-tighter uppercase whitespace-nowrap">
            {{ config('app.name', env('APP_NAME')) }}
        </span>
    </div>

    <nav class="flex-1 mt-4 px-3 space-y-1 overflow-y-auto custom-scrollbar">

        {{-- ── Dashboard (Acceso rápido global) ───────────────────────────── --}}
        <a href="{{ route('dashboard') }}"
            class="flex items-center px-3 py-2 rounded-lg transition-colors mb-2 {{ request()->routeIs('dashboard') ? 'bg-[#323233] text-[#007acc] border-l-4 border-[#007acc]' : 'text-gray-400 hover:bg-[#2d2d2d] hover:text-white' }} group">
            <i class="fas fa-th-large w-6 text-center group-hover:scale-110 transition-transform"></i>
            <span x-show="sidebarOpen" class="ml-3 text-xs font-bold uppercase tracking-tight whitespace-nowrap">Panel
                Principal</span>
        </a>

        {{-- ── Sección: Soporte Técnico (Desplegable) ────────────────────── --}}
        <div x-data="{ open: {{ request()->routeIs(['tickets.*']) ? 'true' : 'false' }} }" class="py-1">
            <button @click="open = !open"
                class="w-full flex items-center justify-between px-3 py-2 text-gray-400 hover:text-white rounded-lg hover:bg-[#252526] transition-colors focus:outline-none">
                <div class="flex items-center">
                    <i class="fas fa-headset w-6 text-center"></i>
                    <span x-show="sidebarOpen"
                        class="ml-3 text-xs font-bold uppercase tracking-tight whitespace-nowrap">Soporte Técnico</span>
                </div>
                <i x-show="sidebarOpen" class="fas text-[10px] transition-transform duration-200"
                    :class="open ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
            </button>

            <div x-show="open && sidebarOpen" x-collapse class="pl-4 mt-1 space-y-1">
                {{-- Listado de tickets --}}
                @if ($ticketsEntry)
                    <a href="{{ route('tickets.index') }}"
                        class="flex items-center px-3 py-1.5 rounded-lg text-xs transition-colors {{ request()->routeIs('tickets.index') ? 'bg-[#323233] text-[#007acc] font-bold' : 'text-gray-400 hover:bg-[#2d2d2d] hover:text-white' }}">
                        <i class="fas {{ $ticketsEntry['icon'] }} w-5 text-center text-[11px]"></i>
                        <span
                            class="ml-2 uppercase tracking-tight whitespace-nowrap">{{ $ticketsEntry['label'] }}</span>
                    </a>
                @endif

                {{-- Nuevo ticket --}}
                @can('tickets.crear')
                    <a href="{{ route('tickets.create') }}"
                        class="flex items-center px-3 py-1.5 rounded-lg text-xs transition-colors {{ request()->routeIs('tickets.create') ? 'bg-[#323233] text-[#007acc] font-bold' : 'text-gray-400 hover:bg-[#2d2d2d] hover:text-white' }}">
                        <i class="fas fa-plus-circle w-5 text-center text-[11px]"></i>
                        <span class="ml-2 uppercase tracking-tight whitespace-nowrap">Nuevo Ticket</span>
                    </a>
                @endcan

                {{-- Sin Asignar --}}
                @can('tickets.asignar')
                    <a href="{{ route('tickets.index', ['estado' => 'abierto', 'sin_asignar' => 1]) }}"
                        class="flex items-center px-3 py-1.5 rounded-lg text-xs transition-colors {{ request()->fullUrlIs('*sin_asignar=1*') ? 'bg-[#323233] text-[#007acc] font-bold' : 'text-gray-400 hover:bg-[#2d2d2d] hover:text-white' }}">
                        <i class="fas fa-inbox w-5 text-center text-[11px]"></i>
                        <span class="ml-2 uppercase tracking-tight whitespace-nowrap">Sin Asignar</span>
                    </a>
                @endcan
            </div>
        </div>

        {{-- ── Sección: Gestión (Desplegable) ──────────────────────────────── --}}
        @if ($verGestion)
            <div x-data="{ open: {{ request()->routeIs(['empresas.*', 'sedes.*', 'equipos.*']) ? 'true' : 'false' }} }" class="py-1">
                <button @click="open = !open"
                    class="w-full flex items-center justify-between px-3 py-2 text-gray-400 hover:text-white rounded-lg hover:bg-[#252526] transition-colors focus:outline-none">
                    <div class="flex items-center">
                        <i class="fas fa-briefcase w-6 text-center"></i>
                        <span x-show="sidebarOpen"
                            class="ml-3 text-xs font-bold uppercase tracking-tight whitespace-nowrap">Gestión</span>
                    </div>
                    <i x-show="sidebarOpen" class="fas text-[10px] transition-transform duration-200"
                        :class="open ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                </button>

                <div x-show="open && sidebarOpen" x-collapse class="pl-4 mt-1 space-y-1">
                    @can('empresas.gestionar')
                        <a href="{{ route('empresas.index') }}"
                            class="flex items-center px-3 py-1.5 rounded-lg text-xs transition-colors {{ request()->routeIs('empresas.*') ? 'bg-[#323233] text-[#007acc] font-bold' : 'text-gray-400 hover:bg-[#2d2d2d] hover:text-white' }}">
                            <i class="fas fa-building w-5 text-center text-[11px]"></i>
                            <span class="ml-2 uppercase tracking-tight whitespace-nowrap">Empresas</span>
                        </a>
                    @endcan

                    @can('sedes.gestionar')
                        <a href="{{ route('sedes.index') }}"
                            class="flex items-center px-3 py-1.5 rounded-lg text-xs transition-colors {{ request()->routeIs('sedes.*') ? 'bg-[#323233] text-[#007acc] font-bold' : 'text-gray-400 hover:bg-[#2d2d2d] hover:text-white' }}">
                            <i class="fas fa-map-marker-alt w-5 text-center text-[11px]"></i>
                            <span class="ml-2 uppercase tracking-tight whitespace-nowrap">Sedes</span>
                        </a>
                    @endcan

                    @can('equipos.ver')
                        <a href="{{ route('equipos.index') }}"
                            class="flex items-center px-3 py-1.5 rounded-lg text-xs transition-colors {{ request()->routeIs('equipos.*') ? 'bg-[#323233] text-[#007acc] font-bold' : 'text-gray-400 hover:bg-[#2d2d2d] hover:text-white' }}">
                            <i class="fas fa-desktop w-5 text-center text-[11px]"></i>
                            <span class="ml-2 uppercase tracking-tight whitespace-nowrap">Equipos</span>
                        </a>
                    @endcan
                </div>
            </div>
        @endif

        {{-- ── Sección: Flujos de Trabajo (Desplegable) ────────────────────── --}}
        @if ($verFlujos)
            <div x-data="{ open: {{ request()->routeIs('flujos.*') ? 'true' : 'false' }} }" class="py-1">
                <button @click="open = !open"
                    class="w-full flex items-center justify-between px-3 py-2 text-gray-400 hover:text-white rounded-lg hover:bg-[#252526] transition-colors focus:outline-none">
                    <div class="flex items-center">
                        <i class="fas fa-project-diagram w-6 text-center"></i>
                        <span x-show="sidebarOpen"
                            class="ml-3 text-xs font-bold uppercase tracking-tight whitespace-nowrap">Flujos</span>
                    </div>
                    <i x-show="sidebarOpen" class="fas text-[10px] transition-transform duration-200"
                        :class="open ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                </button>

                <div x-show="open && sidebarOpen" x-collapse class="pl-4 mt-1 space-y-1">
                    @can('flujos.ver-participando')
                        @php $countBandeja = $flujosPendientesSidebar ?? 0; @endphp
                        <a href="{{ route('flujos.instancias.index') }}"
                            class="flex items-center p-3 rounded-lg transition-colors {{ request()->routeIs('flujos.instancias.index') || request()->routeIs('flujos.instancias.show') ? 'bg-[#323233] text-[#007acc] border-l-4 border-[#007acc]' : 'text-gray-400 hover:bg-[#2d2d2d] hover:text-white' }} group relative">

                            {{-- Ícono con dot cuando el sidebar está colapsado --}}
                            <div class="relative w-6 text-center">
                                <i class="fas fa-inbox group-hover:scale-110 transition-transform"></i>
                                @if ($countBandeja > 0)
                                    <span x-show="!sidebarOpen"
                                        class="absolute -top-1.5 -right-1.5 min-w-[14px] h-[14px] px-1 rounded-full bg-red-500 text-white text-[8px] font-black flex items-center justify-center leading-none">
                                        {{ $countBandeja > 9 ? '9+' : $countBandeja }}
                                    </span>
                                @endif
                            </div>

                            <span x-show="sidebarOpen"
                                class="ml-4 text-xs font-bold uppercase tracking-tight whitespace-nowrap flex-1">
                                Mi Bandeja
                            </span>

                            {{-- Badge pill cuando el sidebar está expandido --}}
                            @if ($countBandeja > 0)
                                <span x-show="sidebarOpen"
                                    class="min-w-[20px] h-5 px-1.5 rounded-full bg-red-500 text-white text-[10px] font-black flex items-center justify-center leading-none">
                                    {{ $countBandeja > 99 ? '99+' : $countBandeja }}
                                </span>
                            @endif
                        </a>
                    @endcan

                    @can('flujos.iniciar')
                        <a href="{{ route('flujos.instancias.create') }}"
                            class="flex items-center px-3 py-1.5 rounded-lg text-xs transition-colors {{ request()->routeIs('flujos.instancias.create') ? 'bg-[#323233] text-[#007acc] font-bold' : 'text-gray-400 hover:bg-[#2d2d2d] hover:text-white' }}">
                            <i class="fas fa-play-circle w-5 text-center text-[11px]"></i>
                            <span class="ml-2 uppercase tracking-tight whitespace-nowrap">Nuevo Proceso</span>
                        </a>
                    @endcan

                    @can('flujos.tipos-documento.gestionar')
                        <a href="{{ route('flujos.tipos-documento.index') }}"
                            class="flex items-center px-3 py-1.5 rounded-lg text-xs transition-colors {{ request()->routeIs('flujos.tipos-documento.*') ? 'bg-[#323233] text-[#007acc] font-bold' : 'text-gray-400 hover:bg-[#2d2d2d] hover:text-white' }}">
                            <i class="fas fa-file-signature w-5 text-center text-[11px]"></i>
                            <span class="ml-2 uppercase tracking-tight whitespace-nowrap">Tipos Documento</span>
                        </a>
                    @endcan

                    @can('flujos.plantillas.gestionar')
                        <a href="{{ route('flujos.plantillas.index') }}"
                            class="flex items-center px-3 py-1.5 rounded-lg text-xs transition-colors {{ request()->routeIs('flujos.plantillas.*') ? 'bg-[#323233] text-[#007acc] font-bold' : 'text-gray-400 hover:bg-[#2d2d2d] hover:text-white' }}">
                            <i class="fas fa-sitemap w-5 text-center text-[11px]"></i>
                            <span class="ml-2 uppercase tracking-tight whitespace-nowrap">Plantillas</span>
                        </a>
                    @endcan
                </div>
            </div>
        @endif

        {{-- ── Sección: Sistema (Desplegable) ──────────────────────────────── --}}
        @if ($verSistema)
            <div x-data="{ open: {{ request()->routeIs(['usuarios.*', 'roles.*', 'auditoria.*']) ? 'true' : 'false' }} }" class="py-1">
                <button @click="open = !open"
                    class="w-full flex items-center justify-between px-3 py-2 text-gray-400 hover:text-white rounded-lg hover:bg-[#252526] transition-colors focus:outline-none">
                    <div class="flex items-center">
                        <i class="fas fa-cogs w-6 text-center"></i>
                        <span x-show="sidebarOpen"
                            class="ml-3 text-xs font-bold uppercase tracking-tight whitespace-nowrap">Sistema</span>
                    </div>
                    <i x-show="sidebarOpen" class="fas text-[10px] transition-transform duration-200"
                        :class="open ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                </button>

                <div x-show="open && sidebarOpen" x-collapse class="pl-4 mt-1 space-y-1">
                    @can('usuarios.ver')
                        <a href="{{ route('usuarios.index') }}"
                            class="flex items-center px-3 py-1.5 rounded-lg text-xs transition-colors {{ request()->routeIs('usuarios.*') ? 'bg-[#323233] text-[#007acc] font-bold' : 'text-gray-400 hover:bg-[#2d2d2d] hover:text-white' }}">
                            <i class="fas fa-users-cog w-5 text-center text-[11px]"></i>
                            <span class="ml-2 uppercase tracking-tight whitespace-nowrap">Usuarios</span>
                        </a>
                    @endcan

                    @can('roles.gestionar')
                        <a href="{{ route('roles.index') }}"
                            class="flex items-center px-3 py-1.5 rounded-lg text-xs transition-colors {{ request()->routeIs('roles.*') ? 'bg-[#323233] text-[#007acc] font-bold' : 'text-gray-400 hover:bg-[#2d2d2d] hover:text-white' }}">
                            <i class="fas fa-key w-5 text-center text-[11px]"></i>
                            <span class="ml-2 uppercase tracking-tight whitespace-nowrap">Roles y Accesos</span>
                        </a>
                    @endcan
                </div>
            </div>
        @endif

    </nav>

    {{-- ── Footer: usuario actual ─────────────────────────────────────── --}}
    <div class="p-3 bg-[#1a1a1b] border-t border-gray-800 flex-shrink-0">
        <div class="flex items-center overflow-hidden">
            <div
                class="w-8 h-8 rounded bg-[#007acc] flex-shrink-0 flex items-center justify-center text-white font-black text-xs">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
            <div x-show="sidebarOpen" class="ml-3 overflow-hidden">
                <p class="text-[10px] font-bold text-white truncate">{{ Auth::user()->name }}</p>
                <p class="text-[9px] text-[#007acc] uppercase font-black truncate">
                    {{ Auth::user()->getRoleNames()->first() ?? 'Sin Rol' }}
                </p>
            </div>
        </div>
    </div>

    {{-- Botón para colapsar --}}
    <button @click="sidebarOpen = !sidebarOpen"
        class="hidden md:flex items-center justify-center p-3 text-gray-500 hover:text-white border-t border-gray-800 transition-colors flex-shrink-0">
        <i class="fas" :class="sidebarOpen ? 'fa-angle-double-left' : 'fa-angle-double-right'"></i>
    </button>
</aside>
