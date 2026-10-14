{{-- resources/views/layouts/partials/navbar-notificaciones.blade.php --}}

@php
    $usuarioAuth        = auth()->user();
    $notifsNoLeidasCount = $usuarioAuth->unreadNotifications()->count();
    $notifsRecientes     = $usuarioAuth->notifications()->latest()->limit(6)->get();
@endphp

<li class="relative" x-data="{ dropdownOpen: false }" @click.outside="dropdownOpen = false">
    {{-- Botón de Campana --}}
    <a class="relative flex h-8.5 w-8.5 items-center justify-center rounded-full border-[0.5px] border-stroke bg-gray hover:text-primary dark:border-strokedark dark:bg-meta-4 dark:text-white"
       href="#" @click.prevent="dropdownOpen = !dropdownOpen">
        <i class="fas fa-bell text-xs"></i>
        
        @if ($notifsNoLeidasCount > 0)
            <span class="absolute -top-0.5 right-0 z-1 h-3 w-3 rounded-full bg-meta-1">
                <span class="absolute -z-1 inline-flex h-full w-full animate-ping rounded-full bg-meta-1 opacity-75"></span>
            </span>
        @endif
    </a>

    {{-- Dropdown de Notificaciones --}}
    {{-- Agregamos z-50 para que siempre flote por encima del contenido principal --}}
    <div x-show="dropdownOpen" x-cloak
         class="absolute z-50 -right-27 mt-2.5 flex h-90 w-80 flex-col rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark sm:right-0 sm:w-96">
        
        {{-- Cabecera --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-stroke dark:border-strokedark">
            <div>
                <h5 class="text-xs font-bold text-black dark:text-white uppercase tracking-wider">Notificaciones</h5>
                <p class="text-[10px] text-bodydark2">{{ $notifsNoLeidasCount }} pendientes</p>
            </div>

            @if ($notifsNoLeidasCount > 0)
                <form action="{{ route('notificaciones.marcar-todas') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-[10px] font-bold uppercase text-primary hover:underline">
                        Marcar todas
                    </button>
                </form>
            @endif
        </div>

        {{-- Contenedor de Items --}}
        <ul class="flex h-auto flex-col overflow-y-auto no-scrollbar">
            @forelse ($notifsRecientes as $notif)
                <li>
                    @include('layouts.partials._notificacion_item', ['notif' => $notif, 'compact' => true])
                </li>
            @empty
                <li class="p-6 text-center text-xs text-bodydark2">
                    <i class="fas fa-bell-slash text-xl mb-1 block text-strokedark"></i>
                    Sin notificaciones aún.
                </li>
            @endforelse
        </ul>

        {{-- Footer --}}
        <a href="{{ route('notificaciones.index') }}"
           class="block border-t border-stroke py-2 text-center text-xs font-bold uppercase text-primary hover:bg-gray dark:border-strokedark dark:hover:bg-meta-4">
            Ver todas las notificaciones
        </a>
    </div>
</li>