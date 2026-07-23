{{-- resources/views/usuarios/create.blade.php --}}
<x-app-layout>
    <x-slot name="title">Crear usuario</x-slot>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
        <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
    @endpush

    <div class="db-header mb-6 flex items-center justify-between flex-wrap gap-3">
        <div>
            <h4>Nuevo usuario</h4>
            <p>Crea un usuario y asigna su rol, empresa o coberturas según corresponda</p>
        </div>
        <a href="{{ route('usuarios.index') }}" class="db-btn ghost">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    @include('usuarios.partials.form', ['usuario' => null, 'rolActual' => 'usuario', 'coberturasActuales' => []])
</x-app-layout>
