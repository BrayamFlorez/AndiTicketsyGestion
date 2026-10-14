{{-- resources/views/equipos/mantenimientos/edit.blade.php --}}
<x-app-layout>
    <x-slot name="title">Editar servicio · {{ $equipo->nombre }}</x-slot>

    <div class="mb-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-stroke pb-2.5 dark:border-strokedark">
        <div class="flex items-center gap-2.5 flex-wrap min-w-0">
            <span class="font-mono text-xs font-bold text-primary bg-primary/10 px-2 py-0.5 rounded border border-primary/20">{{ $equipo->numero_serie ?? 'SIN-SERIE' }}</span>
            <h2 class="text-title-md font-bold text-black dark:text-white truncate">Editar servicio{{ $mantenimiento->exists ? " " . $mantenimiento->numero_orden : "" }}</h2>
            <span class="text-xs text-bodydark2 truncate">{{ $equipo->nombre }} · {{ $equipo->label_categoria }} · {{ $equipo->empresa?->nombre }}{{ $equipo->sede ? ' / ' . $equipo->sede->nombre : '' }}</span>
        </div>
        <a href="{{ route('equipos.show', $equipo) }}" class="inline-flex items-center gap-1 rounded border border-stroke py-1 px-3 text-xs font-bold text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition shadow-sm shrink-0">
            <i class="fas fa-arrow-left text-[10px]"></i> Volver al equipo
        </a>
    </div>

    @include('equipos.mantenimientos.partials._form')
</x-app-layout>
