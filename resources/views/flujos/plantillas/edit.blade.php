{{-- resources/views/flujos/plantillas/edit.blade.php --}}
<x-app-layout>
    <x-slot name="title">Editar Plantilla</x-slot>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    @endpush

    {{-- Cabecera --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-title-md2 font-bold text-black dark:text-white flex items-center gap-2">
                <i class="fas fa-edit text-primary"></i> Editar Plantilla de Flujo
            </h2>
            <p class="text-xs text-bodydark2 mt-1">
                {{ $plantilla->nombre }} · <span class="font-mono text-black dark:text-white">v{{ $plantilla->version }}</span>
            </p>
        </div>

        <a href="{{ route('flujos.plantillas.index') }}" 
           class="inline-flex items-center justify-center gap-2 rounded border border-stroke py-2 px-4 text-center text-xs font-medium text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition shadow-sm">
            <i class="fas fa-arrow-left text-[10px]"></i> Volver al Listado
        </a>
    </div>

    {{-- Alert Éxito --}}
    @if (session('exito'))
        <div class="mb-6 flex w-full border-l-6 border-meta-3 bg-meta-3/10 px-7 py-3 shadow-md dark:bg-meta-3/20 dark:border-meta-3 rounded-sm">
            <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-meta-3 text-white shrink-0">
                <i class="fas fa-check"></i>
            </div>
            <div class="w-full">
                <h5 class="mb-1 font-bold text-meta-3">Operación Exitosa</h5>
                <p class="text-sm text-meta-3">{{ session('exito') }}</p>
            </div>
        </div>
    @endif

    {{-- Alert Bloqueada --}}
    @if ($bloqueada)
        <div class="mb-6 flex w-full border-l-6 border-meta-6 bg-meta-6/10 px-7 py-3 shadow-md dark:bg-meta-6/20 dark:border-meta-6 rounded-sm">
            <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-meta-6 text-white shrink-0">
                <i class="fas fa-lock text-sm"></i>
            </div>
            <div class="w-full">
                <h5 class="mb-1 font-bold text-meta-6">Estructura Bloqueada</h5>
                <p class="text-xs text-meta-6">
                    Esta plantilla cuenta con {{ $instanciasCount }} instancia(s) en ejecución/historial. Solo puedes editar metadatos (nombre, descripción, estado). Para rediseñar etapas o actores, crea una nueva plantilla.
                </p>
            </div>
        </div>
    @endif

    @include('flujos.plantillas.partials._form', [
        'plantilla' => $plantilla,
        'roles'     => $roles,
        'bloqueada' => $bloqueada,
        'modo'      => $modo,
    ])
</x-app-layout>