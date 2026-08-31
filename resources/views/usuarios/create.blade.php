{{-- resources/views/usuarios/create.blade.php --}}
<x-app-layout>
    <x-slot name="title">Crear Usuario</x-slot>

    {{-- Cabecera de la Página --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-title-md2 font-bold text-black dark:text-white flex items-center gap-2">
                <i class="fas fa-user-plus text-primary"></i> Registrar Nuevo Usuario
            </h2>
            <p class="text-xs text-bodydark2 mt-1">
                Crea una nueva cuenta y asigna su rol, empresa base o coberturas operativas según corresponda.
            </p>
        </div>

        <a href="{{ route('usuarios.index') }}" 
           class="inline-flex items-center justify-center gap-2 rounded border border-stroke py-2 px-4 text-center text-xs font-medium text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition shadow-sm">
            <i class="fas fa-arrow-left text-[10px]"></i> Volver al Listado
        </a>
    </div>

    {{-- Partial del Formulario --}}
    @include('usuarios.partials.form', [
        'usuario' => null, 
        'rolActual' => 'usuario', 
        'coberturasActuales' => []
    ])
</x-app-layout>