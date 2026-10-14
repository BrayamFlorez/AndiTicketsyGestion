{{-- resources/views/proyectos/partials/_evidencias.blade.php --}}
{{-- Recibe: $adjuntos, $puedeEliminar, $proyecto --}}
<div class="flex flex-wrap gap-2 mt-2.5">
    @foreach ($adjuntos as $a)
        <div class="relative group">
            @switch($a->tipo)
                @case('imagen')
                    <a href="{{ $a->url }}" target="_blank" title="{{ $a->nombre_original }} · {{ $a->peso_legible }}"
                       class="block w-32 h-[86px] rounded overflow-hidden border border-stroke dark:border-strokedark bg-gray-2 dark:bg-meta-4 relative">
                        <img src="{{ $a->url }}" alt="{{ $a->nombre_original }}" loading="lazy" class="w-full h-full object-cover">
                        <span class="absolute inset-x-0 bottom-0 px-1.5 py-0.5 text-[9px] text-white truncate bg-gradient-to-t from-black/80 to-transparent">
                            {{ $a->nombre_original }}
                        </span>
                    </a>
                    @break

                @case('video')
                    <div class="w-56">
                        <video src="{{ $a->url }}" controls preload="metadata"
                               class="w-56 h-[126px] rounded border border-stroke dark:border-strokedark bg-black object-cover"></video>
                        <div class="text-[9px] text-bodydark2 truncate mt-0.5" title="{{ $a->nombre_original }}">
                            <i class="fas fa-video"></i> {{ $a->nombre_original }} · {{ $a->peso_legible }}
                        </div>
                    </div>
                    @break

                @case('audio')
                    <div class="w-64 rounded border border-stroke dark:border-strokedark bg-white dark:bg-boxdark px-2 py-1.5">
                        <audio src="{{ $a->url }}" controls preload="none" class="w-full h-8"></audio>
                        <div class="text-[9px] text-bodydark2 truncate mt-0.5" title="{{ $a->nombre_original }}">
                            <i class="fas fa-microphone"></i> {{ $a->nombre_original }} · {{ $a->peso_legible }}
                        </div>
                    </div>
                    @break

                @default
                    <a href="{{ $a->url_descarga }}" title="Descargar {{ $a->nombre_original }}"
                       class="flex items-center gap-2 w-56 rounded border border-stroke dark:border-strokedark bg-white dark:bg-boxdark px-2.5 py-2 hover:border-primary transition">
                        <i class="fas {{ $a->icono }} text-xl {{ $a->extension === 'pdf' ? 'text-meta-1' : 'text-primary' }}"></i>
                        <span class="min-w-0">
                            <span class="block text-[11px] font-semibold text-black dark:text-white truncate">{{ $a->nombre_original }}</span>
                            <span class="block text-[10px] text-bodydark2 font-mono">{{ $a->peso_legible }}</span>
                        </span>
                        <i class="fas fa-download text-[10px] text-bodydark2 ml-auto"></i>
                    </a>
            @endswitch

            @if ($puedeEliminar)
                <form method="POST" action="{{ route('proyectos.adjuntos.destroy', [$proyecto, $a]) }}"
                      onsubmit="return confirm('¿Eliminar el adjunto {{ addslashes($a->nombre_original) }}?')"
                      class="absolute -top-1.5 -right-1.5 opacity-0 group-hover:opacity-100 transition">
                    @csrf @method('DELETE')
                    <button type="submit" title="Eliminar adjunto"
                            class="w-5 h-5 rounded-full bg-meta-1 text-white grid place-items-center text-[9px] shadow">
                        <i class="fas fa-times"></i>
                    </button>
                </form>
            @endif
        </div>
    @endforeach
</div>
