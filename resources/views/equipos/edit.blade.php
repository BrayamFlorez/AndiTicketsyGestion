{{-- resources/views/equipos/edit.blade.php --}}
<x-app-layout>
    <x-slot name="title">Editar equipo</x-slot>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
        <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
    @endpush

    <div class="db-header mb-6 flex items-center justify-between flex-wrap gap-3">
        <div>
            <h4>Editar equipo: {{ $equipo->nombre }}</h4>
            <p>
                <span class="db-badge {{ $equipo->color_estado }}">{{ $equipo->label_estado }}</span>
                · {{ $equipo->label_categoria }}
                @if ($equipo->numero_serie)
                    · S/N <code style="color: var(--db-text)">{{ $equipo->numero_serie }}</code>
                @endif
            </p>
        </div>
        <a href="{{ route('equipos.index') }}" class="db-btn ghost">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    @include('equipos.partials.form')
</x-app-layout>
