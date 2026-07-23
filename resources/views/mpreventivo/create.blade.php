<x-app-layout>
    <x-slot name="title">Nuevo Mantenimiento</x-slot>

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
                <span style="color: var(--db-text-bright); font-weight: 500;">Nuevo Preventivo</span>
            </div>
            <h4>Programar Mantenimiento Preventivo</h4>
            <p>Registra las acciones y estado para el equipo (S/N: {{ $equipo->numero_serie ?? 'N/A' }})</p>
        </div>
    </div>

    {{-- ── Formulario ──────────────────────────────────────────────────── --}}
    <form action="{{ route('mpreventivo.store', $equipo) }}" method="POST" enctype="multipart/form-data" class="form-card">
        @csrf
        @include('mpreventivo.form')
    </form>
    
</x-app-layout>