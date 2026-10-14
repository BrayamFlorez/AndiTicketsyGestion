{{-- resources/views/mpreventivo/create.blade.php --}}
<x-app-layout>
    <x-slot name="title">Nuevo Mantenimiento</x-slot>

    {{-- ── Cabecera & Breadcrumb ────────────────────────────────────────────── --}}
    <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-stroke pb-3 dark:border-strokedark">
        <div>
            <div class="flex items-center gap-1.5 text-[11px] text-bodydark2 mb-0.5">
                <a href="{{ route('equipos.index') }}" class="hover:text-primary transition">Equipos</a>
                <i class="fas fa-chevron-right text-[8px]"></i>
                <a href="{{ route('equipos.show', $equipo) }}" class="hover:text-primary transition">{{ $equipo->nombre }}</a>
                <i class="fas fa-chevron-right text-[8px]"></i>
                <span class="text-black dark:text-white font-semibold">Nuevo Preventivo</span>
            </div>
            <h2 class="text-title-md font-bold text-black dark:text-white flex items-center gap-2">
                <i class="fas fa-tools text-primary"></i> Programar Mantenimiento Preventivo
            </h2>
            <p class="text-xs text-bodydark2 mt-0.5">
                Equipo: <strong class="text-black dark:text-white">{{ $equipo->nombre }}</strong> (S/N: {{ $equipo->numero_serie ?? 'N/A' }})
            </p>
        </div>

        <a href="{{ route('equipos.show', $equipo) }}"
           class="inline-flex items-center gap-1.5 rounded border border-stroke py-1.5 px-3 text-xs font-bold text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition shadow-sm shrink-0">
            <i class="fas fa-arrow-left text-[10px]"></i> Volver
        </a>
    </div>

    {{-- ── Formulario ──────────────────────────────────────────────────── --}}
    <form action="{{ route('mpreventivo.store', $equipo) }}" method="POST" enctype="multipart/form-data"
          class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark mb-6">
        @csrf
        @include('mpreventivo.form')
    </form>
</x-app-layout>