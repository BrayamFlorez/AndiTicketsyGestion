{{-- resources/views/layouts/partials/navbar.blade.php --}}

<header class="sticky top-0 z-50 flex w-full bg-white drop-shadow-1 dark:bg-boxdark dark:drop-shadow-none">
    <div class="flex flex-grow items-center justify-between px-4 py-4 shadow-2 md:px-6 2xl:px-11">

        <div class="flex items-center gap-4 sm:gap-6">
            {{-- Toggle Botón Movil + Título --}}
            <button @click.stop="sidebarToggle = !sidebarToggle"
                class="block text-black dark:text-white hover:text-primary transition-colors">
                <i class="fas fa-bars text-xl"></i>
            </button>

            {{-- Título de la vista --}}
            <div class="hidden sm:block">
                <h1 class="text-sm font-bold uppercase tracking-wider text-black dark:text-white">
                    @yield('page_title', 'Consola de Operaciones')
                </h1>
            </div>
        </div>

        {{-- Área derecha del Navbar --}}
        <div class="flex items-center gap-3 2xl:gap-7 ml-auto">
            <ul class="flex items-center gap-2 2xl:gap-4">

                {{-- Notificaciones Dropdown Component --}}
                @include('layouts.partials.navbar-notificaciones')

            </ul>

            {{-- Perfil del Usuario --}}
            <div class="relative" x-data="{ dropdownOpen: false }" @click.outside="dropdownOpen = false">
                <a class="flex items-center gap-3.5" href="#" @click.prevent="dropdownOpen = !dropdownOpen">
                    <span class="hidden text-right lg:block">
                        <span class="block text-xs font-bold text-black dark:text-white">{{ Auth::user()->name }}</span>
                        <span class="block text-[10px] text-bodydark2 font-medium">Usuario</span>
                    </span>

                    <div
                        class="h-9 w-9 rounded-full bg-primary flex items-center justify-center text-white font-bold text-xs">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>

                    <i class="fas fa-chevron-down text-xs text-bodydark"></i>
                </a>

                {{-- Dropdown Menú Perfil --}}
                <div x-data="{ estadoLogout: 'inactivo' }">

                    {{-- Componente Animado del Oso de Salida --}}
                    <x-oso-logout-loader />

                    <div x-show="dropdownOpen" x-cloak
                        class="absolute right-0 mt-4 w-50 flex flex-col rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark z-50">

                        <ul class="flex flex-col gap-1 border-b border-stroke p-2.5 dark:border-strokedark">
                            <li>
                                <a href="{{ route('profile.edit') }}"
                                    class="flex items-center gap-2.5 px-3 py-2 text-xs font-medium text-bodydark1 duration-300 ease-in-out hover:text-primary">
                                    <i class="fas fa-user-cog"></i> {{ __('Mi Perfil') }}
                                </a>
                            </li>
                        </ul>

                        {{-- Formulario con Interceptor para la Animación --}}
                        <form method="POST" action="{{ route('logout') }}" class="p-2.5"
                            @submit.prevent="
                dropdownOpen = false;
                estadoLogout = 'cerrando';
                setTimeout(() => {
                    estadoLogout = 'desconectando';
                    setTimeout(() => $el.submit(), 600);
                }, 1000);
            ">
                            @csrf
                            <button type="submit"
                                class="flex w-full items-center gap-2.5 px-3 py-2 text-xs font-medium text-meta-1 duration-300 ease-in-out hover:text-meta-7">
                                <i class="fas fa-power-off"></i> {{ __('Cerrar Sesión') }}
                            </button>
                        </form>
                    </div>

                </div>
            </div>

        </div>
    </div>
</header>
