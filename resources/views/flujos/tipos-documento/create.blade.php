<x-app-layout>
    <x-slot name="title">Nuevo Tipo de Documento</x-slot>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
        <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
    @endpush

    <div class="db-header mb-6 flex items-center justify-between flex-wrap gap-3">
        <div>
            <h2 class="text-2xl font-black text-white uppercase tracking-tighter">Nuevo Tipo de Documento</h2>
            <p class="text-xs text-gray-400 font-medium uppercase tracking-widest">
                Agregar entrada al catálogo del módulo de flujos
            </p>
        </div>
        <a href="{{ route('flujos.tipos-documento.index') }}" class="db-btn ghost">
            <i class="fas fa-arrow-left mr-2"></i> Volver
        </a>
    </div>

    <form action="{{ route('flujos.tipos-documento.store') }}" method="POST" class="db-card">
        @csrf
        @include('flujos.tipos-documento.partials._form', ['tipo' => $tipo, 'plantillas' => $plantillas])

        <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-gray-800">
            <a href="{{ route('flujos.tipos-documento.index') }}" class="db-btn ghost">Cancelar</a>
            <button type="submit" class="db-btn solid">
                <i class="fas fa-save mr-2"></i> Guardar Tipo
            </button>
        </div>
    </form>
</x-app-layout>