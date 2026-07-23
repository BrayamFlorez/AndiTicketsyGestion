{{-- resources/views/layouts/partials/navbar.blade.php --}}
<nav
    class="h-16 bg-[#1e1e1e] border-b border-gray-800 flex items-center justify-between px-4 lg:px-8 sticky top-0 z-40 shadow-sm">
    <div class="flex items-center">
        <button @click="sidebarOpen = !sidebarOpen"
            class="p-2 rounded-md text-gray-400 hover:bg-[#2d2d2d] hover:text-white focus:outline-none transition-colors">
            <i class="fas fa-bars text-lg"></i>
        </button>

        <div class="ml-4 hidden sm:block">
            <h2 class="text-sm font-bold text-white uppercase tracking-widest">
                @yield('page_title', 'Dashboard')
            </h2>
        </div>
    </div>

    <div class="flex items-center space-x-6">
        {{-- Buscador --}}
        <div class="hidden md:flex items-center bg-[#2d2d2d] rounded-full px-4 py-1.5 border border-transparent focus-within:border-[#007acc] transition-all">
            <i class="fas fa-search text-gray-400 text-xs"></i>
            <input type="text" placeholder="Buscar diagnóstico..."
                class="bg-transparent border-none text-xs text-white focus:ring-0 w-48 placeholder-gray-500">
        </div>

        {{-- Notificaciones --}}
        @include('layouts.partials.navbar-notificaciones')

        {{-- Perfil de Usuario --}}
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center space-x-3 focus:outline-none group">
                <div class="flex flex-col text-right">
                    <span class="text-xs font-bold text-gray-200 leading-none group-hover:text-[#007acc] transition">{{ Auth::user()->name }}</span>
                    <span class="text-[10px] text-gray-400 font-medium">Usuario</span>
                </div>
                <div class="w-9 h-9 rounded-lg bg-[#121212] flex items-center justify-center border-2 border-[#007acc] text-white shadow-md">
                    <i class="fas fa-user-md text-sm"></i>
                </div>
            </button>

            {{-- Dropdown --}}
            <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                class="absolute right-0 mt-3 w-48 bg-[#1e1e1e] rounded-xl shadow-2xl border border-gray-800 py-2 z-50" x-cloak>

                <a href="{{ route('profile.edit') }}"
                    class="block px-4 py-2 text-xs font-bold text-gray-300 hover:bg-[#2d2d2d] hover:text-[#007acc] transition">
                    <i class="fas fa-user-cog mr-2"></i> {{ __('Perfil') }}
                </a>

                <div class="border-t border-gray-800 my-1"></div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full text-left px-4 py-2 text-xs font-bold text-red-400 hover:bg-red-900/20 hover:text-red-300 transition">
                        <i class="fas fa-power-off mr-2"></i> {{ __('Cerrar Sesión') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>