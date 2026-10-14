{{-- resources/views/mpreventivo/edit.blade.php --}}
<x-app-layout>
    <x-slot name="title">Editar Mantenimiento #{{ $mantenimiento->id }}</x-slot>

    @php
        $eq = $equipo ?? $mantenimiento->equipo;
    @endphp

    {{-- ── Cabecera & Breadcrumb ────────────────────────────────────────────── --}}
    <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-stroke pb-3 dark:border-strokedark">
        <div>
            <div class="flex items-center gap-1.5 text-[11px] text-bodydark2 mb-0.5">
                <a href="{{ route('equipos.index') }}" class="hover:text-primary transition">Equipos</a>
                <i class="fas fa-chevron-right text-[8px]"></i>
                @if($eq)
                    <a href="{{ route('equipos.show', $eq) }}" class="hover:text-primary transition">{{ $eq->nombre }}</a>
                    <i class="fas fa-chevron-right text-[8px]"></i>
                @endif
                <span class="text-black dark:text-white font-semibold">Editar Preventivo #{{ $mantenimiento->id }}</span>
            </div>
            <h2 class="text-title-md font-bold text-black dark:text-white flex items-center gap-2">
                <i class="fas fa-edit text-primary"></i> Modificar Mantenimiento Preventivo
            </h2>
            <p class="text-xs text-bodydark2 mt-0.5">
                Actualiza la información y el estado de este registro de mantenimiento.
            </p>
        </div>

        <a href="{{ $eq ? route('equipos.show', $eq) : route('equipos.index') }}"
           class="inline-flex items-center gap-1.5 rounded border border-stroke py-1.5 px-3 text-xs font-bold text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition shadow-sm shrink-0">
            <i class="fas fa-arrow-left text-[10px]"></i> Volver
        </a>
    </div>

    {{-- ── Formulario ──────────────────────────────────────────────────── --}}
    <form action="{{ route('mpreventivo.update', $mantenimiento) }}" method="POST" enctype="multipart/form-data"
          class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark mb-6">
        @csrf
        @method('PUT')
        @include('mpreventivo.form')
    </form>
</x-app-layout>