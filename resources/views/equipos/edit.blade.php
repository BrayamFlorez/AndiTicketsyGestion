{{-- resources/views/equipos/edit.blade.php --}}
<x-app-layout>
    <x-slot name="title">Editar equipo</x-slot>

    {{-- Cabecera --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-title-md2 font-bold text-black dark:text-white flex items-center gap-2">
                <i class="fas fa-edit text-primary"></i> Editar Equipo: {{ $equipo->nombre }}
            </h2>
            <div class="flex items-center gap-2 mt-1">
                @php
                    $colorEst = match($equipo->estado) {
                        'activo' => 'bg-meta-3/10 text-meta-3',
                        'mantenimiento' => 'bg-meta-6/10 text-meta-6',
                        'dado_de_baja' => 'bg-meta-1/10 text-meta-1',
                        default => 'bg-primary/10 text-primary',
                    };
                @endphp
                <span class="inline-flex rounded-full py-0.5 px-2.5 text-[10px] font-bold uppercase {{ $colorEst }}">
                    {{ $equipo->label_estado }}
                </span>
                <span class="text-xs text-bodydark2">· {{ $equipo->label_categoria }}</span>
                @if ($equipo->numero_serie)
                    <span class="text-xs text-bodydark2">· S/N: <code class="font-mono text-black dark:text-white">{{ $equipo->numero_serie }}</code></span>
                @endif
            </div>
        </div>
        
        <a href="{{ route('equipos.index') }}" 
           class="inline-flex items-center justify-center gap-2 rounded border border-stroke py-2 px-4 text-center text-xs font-medium text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition shadow-sm">
            <i class="fas fa-arrow-left text-[10px]"></i> Volver
        </a>
    </div>

    @include('equipos.partials.form')
</x-app-layout>