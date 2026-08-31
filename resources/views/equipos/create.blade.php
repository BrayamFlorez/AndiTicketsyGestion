{{-- resources/views/equipos/create.blade.php --}}
<x-app-layout>
    <x-slot name="title">Registrar equipo</x-slot>

    {{-- Cabecera --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-title-md2 font-bold text-black dark:text-white flex items-center gap-2">
                <i class="fas fa-desktop text-primary"></i> Registrar Nuevo Equipo
            </h2>
            <p class="text-xs text-bodydark2 mt-1">
                Los campos de especificaciones cambiarán dinámicamente según la categoría que selecciones.
            </p>
        </div>
        
        <a href="{{ route('equipos.index') }}" 
           class="inline-flex items-center justify-center gap-2 rounded border border-stroke py-2 px-4 text-center text-xs font-medium text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition shadow-sm">
            <i class="fas fa-arrow-left text-[10px]"></i> Volver
        </a>
    </div>

    {{-- Alert Flash Error --}}
    @if (session('error'))
        <div class="mb-6 flex w-full border-l-6 border-meta-1 bg-meta-1/10 px-7 py-3 shadow-md dark:bg-meta-1/20 dark:border-meta-1 rounded-sm">
            <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-meta-1 text-white shrink-0">
                <i class="fas fa-exclamation-circle text-lg"></i>
            </div>
            <div class="w-full">
                <h5 class="mb-1 font-bold text-meta-1">Error de procesamiento</h5>
                <p class="text-sm text-meta-1">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @include('equipos.partials.form')
</x-app-layout>