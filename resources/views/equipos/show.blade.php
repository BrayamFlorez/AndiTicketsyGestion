{{-- resources/views/equipos/show.blade.php --}}
<x-app-layout>
    <x-slot name="title">Detalle de Equipo: {{ $equipo->nombre }}</x-slot>

    {{-- ── Cabecera ────────────────────────────────────────────────────── --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 mb-1 text-xs text-bodydark2">
                <a href="{{ route('equipos.index') }}" class="hover:text-primary transition">Equipos</a>
                <i class="fas fa-chevron-right text-[10px]"></i>
                <span class="text-black dark:text-white font-medium">{{ $equipo->nombre }}</span>
            </div>
            <div class="flex items-center gap-3">
                <h2 class="text-title-md2 font-bold text-black dark:text-white">
                    {{ $equipo->nombre }}
                </h2>
                @php
                    $colorEst = match($equipo->estado) {
                        'activo' => 'bg-meta-3/10 text-meta-3',
                        'mantenimiento' => 'bg-meta-6/10 text-meta-6',
                        'dado_de_baja' => 'bg-meta-1/10 text-meta-1',
                        default => 'bg-primary/10 text-primary',
                    };
                @endphp
                <span class="inline-flex rounded-full py-1 px-3 text-xs font-bold uppercase {{ $colorEst }}">
                    {{ $equipo->label_estado }}
                </span>
            </div>
            <p class="text-xs text-bodydark2 mt-1">
                Registrado el {{ $equipo->created_at->format('d/m/Y') }} por {{ $equipo->creador->name ?? 'Sistema' }}
            </p>
        </div>
        
        <div class="flex items-center gap-2">
            @can('mpreventivo.crear')
                <a href="{{ route('mpreventivo.create', $equipo) }}" 
                   class="inline-flex items-center justify-center gap-2 rounded bg-meta-3 py-2 px-4 text-center text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm">
                    <i class="fas fa-tools"></i> Programar Mantenimiento
                </a>
            @endcan
            
            @can('equipos.editar')
                <a href="{{ route('equipos.edit', $equipo) }}" 
                   class="inline-flex items-center justify-center gap-2 rounded border border-stroke py-2 px-4 text-center text-xs font-medium text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition shadow-sm">
                    <i class="fas fa-edit"></i> Editar Equipo
                </a>
            @endcan
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 flex w-full border-l-6 border-meta-3 bg-meta-3/10 px-7 py-3 shadow-md dark:bg-meta-3/20 dark:border-meta-3 rounded-sm">
            <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-meta-3 text-white shrink-0">
                <i class="fas fa-check"></i>
            </div>
            <div class="w-full">
                <h5 class="mb-1 font-bold text-meta-3">¡Éxito!</h5>
                <p class="text-sm text-meta-3">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    {{-- ── Detalles del Equipo (Grilla TailAdmin) ───────────────────────── --}}
    <div class="grid grid-cols-12 gap-4 md:gap-6 2xl:gap-7.5 mb-6">
        
        {{-- Bloque 1: Información General (4 cols) --}}
        <div class="col-span-12 lg:col-span-4 rounded-sm border border-stroke bg-white p-5 shadow-default dark:border-strokedark dark:bg-boxdark">
            <h3 class="font-bold text-black dark:text-white text-xs uppercase tracking-wider border-b border-stroke pb-3 mb-4 dark:border-strokedark flex items-center gap-2">
                <i class="fas fa-info-circle text-primary"></i> Información General
            </h3>
            
            <div class="space-y-3.5 text-xs">
                <div>
                    <span class="text-[10px] font-bold text-bodydark2 uppercase block">Categoría</span>
                    <span class="text-black dark:text-white font-medium block mt-0.5">{{ $equipo->label_categoria }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-bodydark2 uppercase block">Marca y Modelo</span>
                    <span class="text-black dark:text-white font-medium block mt-0.5">{{ $equipo->marca ?? 'N/A' }} {{ $equipo->modelo ? ' - ' . $equipo->modelo : '' }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-bodydark2 uppercase block">Número de Serie</span>
                    <span class="font-mono text-sm font-semibold text-black dark:text-white block mt-0.5">{{ $equipo->numero_serie ?? 'Sin registro' }}</span>
                </div>
                <div class="pt-3 border-t border-dashed border-stroke dark:border-strokedark">
                    <span class="text-[10px] font-bold text-bodydark2 uppercase block mb-1">Ubicación</span>
                    <div class="text-black dark:text-white space-y-1">
                        <div><i class="fas fa-building text-primary mr-1"></i> {{ $equipo->empresa->nombre }}</div>
                        @if($equipo->sede) <div><i class="fas fa-map-marker-alt text-primary mr-1"></i> {{ $equipo->sede->nombre }}</div> @endif
                        @if($equipo->ubicacion) <div class="text-bodydark2"><i class="fas fa-door-open text-primary mr-1"></i> {{ $equipo->ubicacion }}</div> @endif
                    </div>
                </div>
                <div class="pt-3 border-t border-dashed border-stroke dark:border-strokedark">
                    <span class="text-[10px] font-bold text-bodydark2 uppercase block mb-1">Responsable (Usuario)</span>
                    <div class="text-black dark:text-white">
                        @if($equipo->responsable)
                            <i class="fas fa-user text-primary mr-1"></i> {{ $equipo->responsable->name }}
                        @else
                            <span class="text-bodydark2 italic">Sin asignar</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Bloque 2: Especificaciones Técnicas (8 cols) --}}
        <div class="col-span-12 lg:col-span-8 rounded-sm border border-stroke bg-white p-5 shadow-default dark:border-strokedark dark:bg-boxdark">
            <h3 class="font-bold text-black dark:text-white text-xs uppercase tracking-wider border-b border-stroke pb-3 mb-4 dark:border-strokedark flex items-center gap-2">
                <i class="fas fa-microchip text-primary"></i> Especificaciones Técnicas
            </h3>
            
            @if(!empty($equipo->especificaciones))
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($equipo->schema_especificaciones as $key => $schema)
                        @if(isset($equipo->especificaciones[$key]) && $equipo->especificaciones[$key] !== null && $equipo->especificaciones[$key] !== '')
                            <div class="rounded border border-stroke p-3 bg-gray-2 dark:bg-meta-4/40 dark:border-strokedark">
                                <span class="text-[10px] font-bold text-bodydark2 uppercase block mb-1">{{ $schema['label'] }}</span>
                                <div class="text-xs font-bold text-black dark:text-white">{{ $equipo->especificaciones[$key] }}</div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="text-center py-6 text-bodydark2">
                    <i class="fas fa-box-open text-3xl mb-2"></i>
                    <p class="text-xs">No hay especificaciones técnicas registradas para este equipo.</p>
                </div>
            @endif

            @if($equipo->notas)
                <div class="mt-5 pt-3 border-t border-dashed border-stroke dark:border-strokedark">
                    <span class="text-[10px] font-bold text-bodydark2 uppercase block mb-1">Notas Adicionales</span>
                    <p class="text-xs text-black dark:text-white leading-relaxed whitespace-pre-line">{{ $equipo->notas }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ── Historial de Mantenimientos Preventivos ──────────────────────── --}}
    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark mb-6">
        <div class="border-b border-stroke py-4 px-6 dark:border-strokedark">
            <h3 class="font-bold text-black dark:text-white text-xs uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-history text-primary"></i> Historial de Mantenimientos Preventivos
            </h3>
        </div>
        
        <div class="max-w-full overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-gray-2 text-left dark:bg-meta-4">
                        <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase">ID / Fecha</th>
                        <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase">Técnico</th>
                        <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase text-center">Estado</th>
                        <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke dark:divide-strokedark">
                    @forelse($equipo->mantenimientosPreventivos as $maint)
                        <tr class="hover:bg-gray-2 dark:hover:bg-meta-4/50 transition">
                            <td class="py-3.5 px-4">
                                <div class="font-mono text-xs font-bold text-black dark:text-white">#{{ $maint->id }}</div>
                                <div class="text-[10px] text-bodydark2">
                                    {{ $maint->fecha_inicio ? $maint->fecha_inicio->format('d/m/Y h:i A') : 'Sin iniciar' }}
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($maint->tecnico)
                                    <div class="flex items-center gap-2">
                                        <div class="flex h-6 w-6 items-center justify-center rounded-full bg-primary/20 text-primary font-bold text-[10px] shrink-0">
                                            {{ substr($maint->tecnico->name, 0, 1) }}
                                        </div>
                                        <span class="text-xs text-black dark:text-white">{{ $maint->tecnico->name }}</span>
                                    </div>
                                @else
                                    <span class="text-xs text-bodydark2 italic">No asignado</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @php
                                    $colorMaint = match($maint->estado) {
                                        'completado' => 'bg-meta-3/10 text-meta-3',
                                        'en_progreso' => 'bg-meta-6/10 text-meta-6',
                                        default => 'bg-primary/10 text-primary',
                                    };
                                @endphp
                                <span class="inline-flex rounded-full py-1 px-3 text-[10px] font-bold uppercase {{ $colorMaint }}">
                                    {{ ucfirst($maint->estado) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    @can('mpreventivo.ver')
                                        <a href="{{ route('mpreventivo.show', $maint) }}" 
                                           class="flex h-7 w-7 items-center justify-center rounded border border-stroke text-bodydark2 hover:border-primary hover:text-primary dark:border-strokedark transition" title="Ver detalle">
                                            <i class="fas fa-eye text-[10px]"></i>
                                        </a>
                                    @endcan
                                    
                                    @can('mpreventivo.editar')
                                        <a href="{{ route('mpreventivo.edit', $maint) }}" 
                                           class="flex h-7 w-7 items-center justify-center rounded border border-stroke text-meta-6 hover:border-meta-6 dark:border-strokedark transition" title="Editar">
                                            <i class="fas fa-pen text-[10px]"></i>
                                        </a>
                                    @endcan
                                    
                                    @can('mpreventivo.eliminar')
                                        <form action="{{ route('mpreventivo.destroy', $maint) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este registro de mantenimiento? Esta acción no se puede deshacer.')" class="inline-block">
                                            @csrf @method('DELETE')
                                            <button type="submit" 
                                                    class="flex h-7 w-7 items-center justify-center rounded border border-stroke text-meta-1 hover:border-meta-1 dark:border-strokedark transition" title="Eliminar">
                                                <i class="fas fa-trash text-[10px]"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center">
                                <div class="flex flex-col items-center text-bodydark2">
                                    <i class="fas fa-clipboard-check text-3xl mb-2"></i>
                                    <p class="text-xs">Este equipo no tiene mantenimientos registrados en su historial.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>