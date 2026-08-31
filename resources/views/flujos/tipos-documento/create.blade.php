{{-- resources/views/flujos/tipos-documento/create.blade.php --}}
<x-app-layout>
    <x-slot name="title">Nuevo Tipo de Documento</x-slot>

    {{-- Cabecera --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-title-md2 font-bold text-black dark:text-white flex items-center gap-2">
                <i class="fas fa-file-alt text-primary"></i> Nuevo Tipo de Documento
            </h2>
            <p class="text-xs text-bodydark2 mt-1">
                Agregar una nueva entrada al catálogo del módulo de flujos de trabajo
            </p>
        </div>

        <a href="{{ route('flujos.tipos-documento.index') }}" 
           class="inline-flex items-center justify-center gap-2 rounded border border-stroke py-2 px-4 text-center text-xs font-medium text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition shadow-sm">
            <i class="fas fa-arrow-left text-[10px]"></i> Volver al Listado
        </a>
    </div>

    {{-- Formulario --}}
    <form action="{{ route('flujos.tipos-documento.store') }}" method="POST" 
          class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
        @csrf

        @include('flujos.tipos-documento.partials._form', ['tipo' => $tipo, 'plantillas' => $plantillas])

        {{-- Footer de Acciones --}}
        <div class="flex items-center justify-end gap-2 bg-gray-2 dark:bg-meta-4/40 px-6 py-3 border-t border-stroke dark:border-strokedark">
            <a href="{{ route('flujos.tipos-documento.index') }}" 
               class="inline-flex items-center justify-center rounded border border-stroke py-1.5 px-4 text-center text-xs font-medium text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition">
                Cancelar
            </a>
            <button type="submit" 
                    class="inline-flex items-center gap-1.5 rounded bg-primary py-1.5 px-5 text-center text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm cursor-pointer">
                <i class="fas fa-save text-[10px]"></i> Guardar Tipo
            </button>
        </div>
    </form>
</x-app-layout>