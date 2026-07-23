<x-app-layout>
    <x-slot name="title">Editar Tipo de Documento</x-slot>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
        <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
    @endpush

    <div class="db-header mb-6 flex items-center justify-between flex-wrap gap-3">
        <div>
            <h2 class="text-2xl font-black text-white uppercase tracking-tighter">Editar Tipo de Documento</h2>
            <p class="text-xs text-gray-400 font-medium uppercase tracking-widest">
                {{ $tipo->nombre }} · <span class="font-mono">{{ $tipo->slug }}</span>
            </p>
        </div>
        <a href="{{ route('flujos.tipos-documento.index') }}" class="db-btn ghost">
            <i class="fas fa-arrow-left mr-2"></i> Volver
        </a>
    </div>

    <form action="{{ route('flujos.tipos-documento.update', $tipo) }}" method="POST" class="db-card">
        @csrf
        @method('PUT')
        @include('flujos.tipos-documento.partials._form', ['tipo' => $tipo, 'plantillas' => $plantillas])

        <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-gray-800">
            <a href="{{ route('flujos.tipos-documento.index') }}" class="db-btn ghost">Cancelar</a>
            <button type="submit" class="db-btn solid">
                <i class="fas fa-save mr-2"></i> Guardar Cambios
            </button>
        </div>
    </form>
</x-app-layout>