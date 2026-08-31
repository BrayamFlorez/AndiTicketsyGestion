{{-- resources/views/tickets/create.blade.php --}}
<x-app-layout>
    <x-slot name="title">Nuevo ticket</x-slot>

    {{-- Cabecera Estilo TailAdmin --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-title-md2 font-bold text-black dark:text-white flex items-center gap-2">
                <i class="fas fa-plus-circle text-primary"></i> Nuevo Ticket de Soporte
            </h2>
            <p class="text-sm text-bodydark2 mt-1">
                @if ($vista === 'admin')
                    Crear ticket en nombre de un usuario de la organización
                @else
                    Describe tu solicitud y un técnico te atenderá a la brevedad
                @endif
            </p>
        </div>
        
        <a href="{{ url()->previous() }}" 
           class="inline-flex items-center justify-center gap-2.5 rounded-md border border-stroke py-2 px-4 text-center font-medium text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition shadow-sm">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    {{-- Alerta de Error General --}}
    @if (session('error'))
        <div class="mb-6 flex w-full border-l-6 border-meta-1 bg-meta-1/10 px-7 py-4 shadow-md dark:bg-meta-1/20 dark:border-meta-1 rounded-sm">
            <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-meta-1 text-white shrink-0">
                <i class="fas fa-exclamation-circle text-lg"></i>
            </div>
            <div class="w-full">
                <h5 class="mb-1 font-bold text-meta-1">Error de procesamiento</h5>
                <p class="text-sm text-meta-1">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    {{-- Inclusión del formulario dinámico --}}
    @include('tickets.partials.form_' . $vista)
</x-app-layout>