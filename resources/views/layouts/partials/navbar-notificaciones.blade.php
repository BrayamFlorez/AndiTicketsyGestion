@php
    $usuarioAuth        = auth()->user();
    $notifsNoLeidasCount = $usuarioAuth->unreadNotifications()->count();
    $notifsRecientes     = $usuarioAuth->notifications()->latest()->limit(6)->get();
@endphp

<div class="relative" x-data="{ open: false }">

    {{-- Botón de campana --}}
    <button @click="open = !open"
        class="relative p-2 rounded-full text-gray-400 hover:bg-[#2d2d2d] hover:text-white focus:outline-none transition-colors"
        aria-label="Notificaciones">
        <i class="fas fa-bell text-sm"></i>
        @if ($notifsNoLeidasCount > 0)
            <span class="absolute top-0 right-0 min-w-[16px] h-[16px] px-1 rounded-full bg-red-500 text-white text-[9px] font-black flex items-center justify-center leading-none">
                {{ $notifsNoLeidasCount > 99 ? '99+' : $notifsNoLeidasCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown --}}
    <div x-show="open" @click.away="open = false"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        class="absolute right-0 mt-3 w-96 max-w-[calc(100vw-2rem)] bg-[#1e1e1e] rounded-xl shadow-2xl border border-gray-800 z-50 overflow-hidden"
        x-cloak>

        {{-- Cabecera --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-800">
            <div>
                <p class="text-xs font-black text-white uppercase tracking-widest">Notificaciones</p>
                @if ($notifsNoLeidasCount > 0)
                    <p class="text-[10px] text-[#007acc] font-bold mt-0.5">{{ $notifsNoLeidasCount }} sin leer</p>
                @else
                    <p class="text-[10px] text-gray-500 mt-0.5">Al día</p>
                @endif
            </div>
            @if ($notifsNoLeidasCount > 0)
                <form action="{{ route('notificaciones.marcar-todas') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-[10px] text-gray-400 hover:text-[#007acc] font-bold uppercase tracking-wider transition-colors">
                        <i class="fas fa-check-double mr-1"></i> Marcar todas
                    </button>
                </form>
            @endif
        </div>

        {{-- Lista --}}
        <div class="max-h-96 overflow-y-auto">
            @forelse ($notifsRecientes as $notif)
                @php
                    $data = $notif->data;
                    $leida = $notif->read_at !== null;
                    $iconColor = match ($data['color'] ?? 'accent') {
                        'success' => 'text-green-400',
                        'warning' => 'text-yellow-400',
                        'danger'  => 'text-red-400',
                        default   => 'text-[#007acc]',
                    };
                @endphp

                <form action="{{ route('notificaciones.leer', $notif->id) }}" method="POST" class="block">
                    @csrf
                    <button type="submit"
                        class="w-full text-left flex items-start gap-3 px-4 py-3 hover:bg-[#2d2d2d] transition-colors border-l-2 {{ $leida ? 'border-transparent opacity-60' : 'border-[#007acc]' }}">
                        <span class="w-7 h-7 rounded-lg bg-[#121212] border border-gray-800 flex items-center justify-center flex-shrink-0">
                            <i class="fas {{ $data['icono'] ?? 'fa-bell' }} {{ $iconColor }} text-xs"></i>
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-white line-clamp-2 leading-tight">
                                {{ $data['mensaje'] ?? 'Notificación' }}
                            </p>
                            <div class="flex items-center gap-2 mt-1 flex-wrap">
                                @if (!empty($data['codigo']))
                                    <span class="text-[9px] font-mono text-gray-500 uppercase tracking-wider">{{ $data['codigo'] }}</span>
                                @endif
                                <span class="text-[9px] text-gray-500">
                                    <i class="fas fa-clock mr-0.5"></i>{{ $notif->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    </button>
                </form>
            @empty
                <div class="text-center py-10 px-4">
                    <i class="fas fa-bell-slash text-2xl text-gray-700 mb-2"></i>
                    <p class="text-xs text-gray-500">Sin notificaciones aún</p>
                </div>
            @endforelse
        </div>

        {{-- Pie --}}
        <a href="{{ route('notificaciones.index') }}"
           class="block text-center px-4 py-3 border-t border-gray-800 text-[11px] font-bold text-[#007acc] hover:bg-[#2d2d2d] uppercase tracking-widest transition-colors">
            Ver todas
        </a>
    </div>
</div>