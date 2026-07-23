<x-app-layout>
    <x-slot name="title">Notificaciones</x-slot>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
        <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
    @endpush

    <div class="db-header mb-6 flex items-center justify-between flex-wrap gap-3">
        <div>
            <h2 class="text-2xl font-black text-white uppercase tracking-tighter">Notificaciones</h2>
            <p class="text-xs text-gray-400 font-medium uppercase tracking-widest">
                Actividad reciente de tus flujos
            </p>
        </div>

        @if (auth()->user()->unreadNotifications()->exists())
            <form action="{{ route('notificaciones.marcar-todas') }}" method="POST">
                @csrf
                <button type="submit" class="db-btn outline">
                    <i class="fas fa-check-double mr-2"></i> Marcar todas como leídas
                </button>
            </form>
        @endif
    </div>

    @if (session('exito'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-green-500/10 border border-green-500/30 text-green-300 text-sm">
            <i class="fas fa-check-circle mr-2"></i> {{ session('exito') }}
        </div>
    @endif

    {{-- Tabs de filtro --}}
    <div class="mb-4 flex flex-wrap items-center gap-1 border-b border-gray-800">
        @php
            $tabs = [
                'todas'     => ['label' => 'Todas',       'icono' => 'fa-inbox'],
                'no_leidas' => ['label' => 'No leídas',   'icono' => 'fa-envelope'],
                'leidas'    => ['label' => 'Leídas',      'icono' => 'fa-envelope-open'],
            ];
        @endphp
        @foreach ($tabs as $key => $cfg)
            <a href="{{ route('notificaciones.index', ['filtro' => $key]) }}"
               class="px-4 py-2.5 text-[11px] font-black uppercase tracking-widest border-b-2 transition-colors
                      {{ $filtro === $key ? 'border-[#007acc] text-white' : 'border-transparent text-gray-500 hover:text-gray-300' }}">
                <i class="fas {{ $cfg['icono'] }} mr-1.5"></i> {{ $cfg['label'] }}
            </a>
        @endforeach
    </div>

    <div class="db-card">
        @forelse ($notificaciones as $notif)
            @php
                $data     = $notif->data;
                $leida    = $notif->read_at !== null;
                $cfgColor = match ($data['color'] ?? 'accent') {
                    'success' => ['borde' => 'border-green-500/40',  'ico' => 'text-green-400'],
                    'warning' => ['borde' => 'border-yellow-500/40', 'ico' => 'text-yellow-400'],
                    'danger'  => ['borde' => 'border-red-500/40',    'ico' => 'text-red-400'],
                    default   => ['borde' => 'border-[#007acc]/40',  'ico' => 'text-[#007acc]'],
                };
            @endphp

            <form action="{{ route('notificaciones.leer', $notif->id) }}" method="POST"
                  class="block border-b border-gray-800 last:border-b-0">
                @csrf
                <button type="submit"
                        class="w-full text-left flex items-start gap-3 px-5 py-4 hover:bg-[var(--db-surface-2)] transition-colors
                               {{ $leida ? 'opacity-60' : '' }} border-l-4 {{ $leida ? 'border-transparent' : $cfgColor['borde'] }}">

                    <span class="w-9 h-9 rounded-lg bg-[#1a1a1b] border border-gray-800 flex items-center justify-center flex-shrink-0 {{ $cfgColor['ico'] }}">
                        <i class="fas {{ $data['icono'] ?? 'fa-bell' }}"></i>
                    </span>

                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-white">
                            {{ $data['mensaje'] ?? 'Notificación' }}
                        </p>
                        <div class="flex flex-wrap items-center gap-2 mt-1">
                            @if (! empty($data['codigo']))
                                <span class="text-[10px] font-mono text-gray-500 uppercase tracking-wider">{{ $data['codigo'] }}</span>
                            @endif
                            @if (! empty($data['titulo']))
                                <span class="text-[11px] text-gray-400 truncate">· {{ $data['titulo'] }}</span>
                            @endif
                            @if (! empty($data['etapa']))
                                <span class="text-[11px] text-gray-500">
                                    <i class="fas fa-map-marker-alt mr-1"></i>{{ $data['etapa'] }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <span class="text-[10px] text-gray-500 uppercase tracking-wider flex-shrink-0 pt-1">
                        {{ $notif->created_at->diffForHumans() }}
                    </span>
                </button>
            </form>
        @empty
            <div class="db-empty py-12 text-center">
                <i class="fas fa-bell-slash text-4xl mb-3 text-gray-700"></i>
                <p class="text-gray-400">
                    @if ($filtro === 'no_leidas')
                        No tienes notificaciones sin leer.
                    @elseif ($filtro === 'leidas')
                        Aún no tienes notificaciones leídas.
                    @else
                        No tienes notificaciones aún.
                    @endif
                </p>
            </div>
        @endforelse

        @if ($notificaciones->hasPages())
            <div class="px-4 py-3 border-t border-gray-800">
                {{ $notificaciones->links() }}
            </div>
        @endif
    </div>
</x-app-layout>