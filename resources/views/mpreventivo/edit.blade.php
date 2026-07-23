<x-app-layout>
    <x-slot name="title">Editar Mantenimiento</x-slot>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
        <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
    @endpush

    {{-- ── Cabecera ────────────────────────────────────────────────────── --}}
    <div class="db-header mb-5 flex items-center justify-between flex-wrap gap-3">
        <div>
            <div class="flex items-center gap-2 mb-2" style="font-size: 0.85rem;">
                <a href="{{ route('equipos.index') }}" style="color: var(--db-text-muted); text-decoration: none;">Equipos</a>
                <i class="fas fa-chevron-right" style="font-size: 0.6rem; color: var(--db-text-muted);"></i>
                
                <span style="color: var(--db-text-bright); font-weight: 500;">Editar Mantenimiento #{{ $mantenimiento->id }}</span>
            </div>
            <h4>Modificar Mantenimiento Preventivo</h4>
            <p>Actualiza la información y el estado de este registro.</p>
        </div>
    </div>

    {{-- ── Formulario ──────────────────────────────────────────────────── --}}
    <form action="{{ route('mpreventivo.update', $mantenimiento) }}" method="POST" enctype="multipart/form-data" class="form-card">
        @csrf
        @method('PUT')
        @include('mpreventivo.form')
    </form>
    
</x-app-layout>