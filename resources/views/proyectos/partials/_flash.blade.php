{{-- resources/views/proyectos/partials/_flash.blade.php — Mensajes de sesión y errores de validación --}}
@if (session('exito'))
    <div class="mb-3 flex w-full border-l-4 border-meta-3 bg-meta-3/10 px-4 py-2.5 shadow-sm rounded-sm">
        <i class="fas fa-check-circle text-meta-3 mr-2 mt-0.5 text-xs"></i>
        <p class="text-xs text-meta-3 font-medium">{{ session('exito') }}</p>
    </div>
@endif

@if (session('error'))
    <div class="mb-3 flex w-full border-l-4 border-meta-1 bg-meta-1/10 px-4 py-2.5 shadow-sm rounded-sm">
        <i class="fas fa-exclamation-triangle text-meta-1 mr-2 mt-0.5 text-xs"></i>
        <p class="text-xs text-meta-1 font-medium">{{ session('error') }}</p>
    </div>
@endif

@if ($errors->any())
    <div class="mb-3 flex w-full border-l-4 border-meta-1 bg-meta-1/10 px-4 py-2.5 shadow-sm rounded-sm">
        <i class="fas fa-exclamation-triangle text-meta-1 mr-2 mt-0.5 text-xs"></i>
        <ul class="text-xs text-meta-1 space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
