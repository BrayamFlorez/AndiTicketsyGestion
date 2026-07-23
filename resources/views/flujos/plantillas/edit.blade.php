<x-app-layout>
    <x-slot name="title">
        {{ $modo === 'create' ? 'Nueva Plantilla' : 'Editar Plantilla' }}
    </x-slot>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
        <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    @endpush

    <div class="db-header mb-6 flex items-center justify-between flex-wrap gap-3">
        <div>
            <h2 class="text-2xl font-black text-white uppercase tracking-tighter">
                {{ $modo === 'create' ? 'Nueva Plantilla de Flujo' : 'Editar Plantilla' }}
            </h2>
            <p class="text-xs text-gray-400 font-medium uppercase tracking-widest">
                @if ($modo === 'edit')
                    {{ $plantilla->nombre }} · v{{ $plantilla->version }}
                @else
                    Diseñador de proceso de aprobación
                @endif
            </p>
        </div>
        <a href="{{ route('flujos.plantillas.index') }}" class="db-btn ghost">
            <i class="fas fa-arrow-left mr-2"></i> Volver
        </a>
    </div>

    @if (session('exito'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-green-500/10 border border-green-500/30 text-green-300 text-sm">
            <i class="fas fa-check-circle mr-2"></i> {{ session('exito') }}
        </div>
    @endif

    @if ($bloqueada)
        <div class="mb-4 px-4 py-3 rounded-lg bg-yellow-500/10 border border-yellow-500/30 text-yellow-300 text-sm">
            <i class="fas fa-lock mr-2"></i>
            <strong>Estructura bloqueada:</strong> esta plantilla ya tiene {{ $instanciasCount }} instancia(s) registrada(s).
            Solo puedes modificar el nombre, descripción y estado. Para cambiar etapas o actores, crea una nueva plantilla.
        </div>
    @endif

    @include('flujos.plantillas.partials._form', [
        'plantilla'       => $plantilla,
        'roles'           => $roles,
        'bloqueada'       => $bloqueada,
        'modo'            => $modo,
    ])
</x-app-layout>