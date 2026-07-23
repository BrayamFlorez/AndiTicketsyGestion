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
                'todas' => ['label' => 'Todas', 'icono' => 'fa-inbox'],
                'no_leidas' => ['label' => 'No leídas', 'icono' => 'fa-envelope'],
                'leidas' => ['label' => 'Leídas', 'icono' => 'fa-envelope-open'],
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
            @include('layouts.partials._notificacion_item', ['notif' => $notif, 'compact' => false])
        @empty
            <div class="db-empty py-12 text-center">
                <i class="fas fa-bell-slash text-4xl mb-3 text-gray-700"></i>
                <p class="text-gray-400">
                    @if ($filtro === 'no_leidas')
                        No tenés notificaciones sin leer.
                    @elseif ($filtro === 'leidas')
                        Aún no tenés notificaciones leídas.
                    @else
                        No tenés notificaciones aún.
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
