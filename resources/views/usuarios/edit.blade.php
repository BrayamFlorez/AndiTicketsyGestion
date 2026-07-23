{{-- resources/views/usuarios/edit.blade.php --}}
<x-app-layout>
    <x-slot name="title">Editar usuario</x-slot>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
        <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
    @endpush

    <div class="db-header mb-6 flex items-center justify-between flex-wrap gap-3">
        <div>
            <h4>Editar usuario: {{ $usuario->name }}</h4>
            <p>Actualiza datos, rol o coberturas asignadas</p>
        </div>
        <a href="{{ route('usuarios.index') }}" class="db-btn ghost">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    @include('usuarios.partials.form')
</x-app-layout>
