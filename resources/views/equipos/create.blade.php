{{-- resources/views/equipos/create.blade.php --}}
<x-app-layout>
    <x-slot name="title">Registrar equipo</x-slot>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
        <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
    @endpush

    <div class="db-header mb-6 flex items-center justify-between flex-wrap gap-3">
        <div>
            <h4>Registrar nuevo equipo</h4>
            <p>Los campos cambian según la categoría que selecciones</p>
        </div>
        <a href="{{ route('equipos.index') }}" class="db-btn ghost">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    @if (session('error'))
        <div class="form-alert error">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    @include('equipos.partials.form')
</x-app-layout>
