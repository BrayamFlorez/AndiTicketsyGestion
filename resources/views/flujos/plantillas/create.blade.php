{{-- resources/views/flujos/plantillas/create.blade.php --}}
<x-app-layout>
    <x-slot name="title">Nueva Plantilla</x-slot>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    @endpush

    {{-- Cabecera --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-title-md2 font-bold text-black dark:text-white flex items-center gap-2">
                <i class="fas fa-project-diagram text-primary"></i> Nueva Plantilla de Flujo
            </h2>
            <p class="text-xs text-bodydark2 mt-1">
                Diseñador de secuencia de etapas y reglas de aprobación
            </p>
        </div>

        <a href="{{ route('flujos.plantillas.index') }}" 
           class="inline-flex items-center justify-center gap-2 rounded border border-stroke py-2 px-4 text-center text-xs font-medium text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition shadow-sm">
            <i class="fas fa-arrow-left text-[10px]"></i> Volver al Listado
        </a>
    </div>

    @include('flujos.plantillas.partials._form', [
        'plantilla' => $plantilla,
        'roles'     => $roles,
        'bloqueada' => $bloqueada,
        'modo'      => $modo,
    ])
</x-app-layout>