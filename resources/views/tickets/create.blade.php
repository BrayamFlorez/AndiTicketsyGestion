{{-- resources/views/tickets/create.blade.php --}}
<x-app-layout>
    <x-slot name="title">Nuevo ticket</x-slot>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
        <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
    @endpush

    {{-- Cabecera común --}}
    <div class="db-header mb-6 flex items-center justify-between flex-wrap gap-3">
        <div>
            <h4>Nuevo ticket de soporte</h4>
            <p>
                @if ($vista === 'admin')
                    Crear ticket en nombre de un usuario
                @else
                    Describe tu solicitud y un técnico te atenderá
                @endif
            </p>
        </div>
        <a href="{{ url()->previous() }}" class="db-btn ghost">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    {{-- Flash de error general --}}
    @if (session('error'))
        <div class="form-alert error">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    @include('tickets.partials.form_' . $vista)
</x-app-layout>
