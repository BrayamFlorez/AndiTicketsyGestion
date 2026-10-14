{{-- resources/views/flujos/instancias/partials/_documentos_expediente.blade.php --}}
@php
    $adjuntosTramite = $instancia->attachments ?? collect();
    $documentosPorRevision = $adjuntosTramite->groupBy('revision')->sortKeysDesc();
    $totalAdjuntos = $adjuntosTramite->count();
    $puedeAdjuntar = $puede['adjuntar_expediente'] ?? auth()->user()->can('subirAdjuntoInstancia', $instancia);
@endphp

<div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark"
     x-data="{ open: {{ $totalAdjuntos > 0 ? 'true' : 'false' }}, formSubida: false }">
    
    {{-- Header Compacto con Toggle e Indicadores --}}
    <div class="flex items-center justify-between p-3 border-b border-stroke dark:border-strokedark cursor-pointer select-none"
         @click="open = !open">
        <div class="flex items-center gap-2 min-w-0">
            <i class="fas fa-folder-open text-primary text-xs shrink-0"></i>
            <h3 class="font-bold text-black dark:text-white uppercase tracking-wider text-[11px] truncate">
                Documentos del Expediente
            </h3>
            <span class="rounded-full bg-primary/10 px-2 py-0.5 text-[10px] font-bold text-primary shrink-0">
                {{ $totalAdjuntos }}
            </span>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            @if ($puedeAdjuntar)
                <button type="button" @click.stop="open = true; formSubida = !formSubida"
                        class="inline-flex items-center gap-1 rounded bg-primary/10 px-2 py-0.5 text-[10px] font-bold text-primary hover:bg-primary hover:text-white transition">
                    <i class="fas" :class="formSubida ? 'fa-minus' : 'fa-plus'"></i>
                    <span x-text="formSubida ? 'Cancelar' : 'Subir'"></span>
                </button>
            @endif
            <button type="button" class="text-bodydark2 hover:text-primary transition p-0.5">
                <i class="fas text-xs" :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
            </button>
        </div>
    </div>

    {{-- Contenido Desplegable --}}
    <div x-show="open" x-cloak x-collapse class="p-3 space-y-3">
        
        {{-- Formulario de Subida (Oculto por defecto, se activa con el botón "Subir") --}}
        @if ($puedeAdjuntar)
            <div x-show="formSubida" x-cloak x-collapse class="rounded border border-dashed border-primary/40 bg-primary/5 p-2.5">
                <form method="POST" action="{{ route('flujos.instancias.adjuntos.subir', $instancia) }}"
                      enctype="multipart/form-data" class="space-y-2">
                    @csrf
                    <input type="file" name="archivo" required
                        accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.txt,.jpg,.jpeg,.png,.zip"
                        class="w-full text-xs text-bodydark2 file:mr-2 file:py-1 file:px-2.5 file:rounded file:border-0 file:text-[10px] file:font-bold file:uppercase file:bg-primary file:text-white hover:file:bg-opacity-90 cursor-pointer">
                    
                    <div class="flex items-center justify-between text-[9px] text-bodydark2">
                        <span>Máx. 20 MB · PDF, Word, Excel, Img, ZIP.</span>
                        <button type="submit" class="inline-flex items-center gap-1 rounded bg-primary py-1 px-2.5 text-[10px] font-bold uppercase text-white hover:bg-opacity-90 transition cursor-pointer">
                            <i class="fas fa-upload text-[9px]"></i> Subir Archivo
                        </button>
                    </div>
                </form>
            </div>
        @endif

        {{-- Listado de Documentos Agrupados por Revisión --}}
        @if ($totalAdjuntos === 0)
            <div class="text-center py-2 text-[11px] text-bodydark2 italic">
                Sin documentos adjuntos en el expediente.
            </div>
        @else
            <div class="space-y-3">
                @foreach ($documentosPorRevision as $rev => $docs)
                    <div>
                        <div class="flex items-center justify-between mb-1.5 pb-0.5 border-b border-stroke dark:border-strokedark">
                            <span class="text-[9px] font-bold uppercase tracking-wider text-bodydark2">
                                Revisión {{ $rev }}
                            </span>
                            @if ($rev == $instancia->revision)
                                <span class="text-[9px] font-bold text-primary uppercase">· Activa</span>
                            @endif
                        </div>

                        <div class="space-y-1.5">
                            @foreach ($docs as $adj)
                                <div class="flex items-center justify-between gap-2 p-2 rounded border border-stroke bg-gray-2 dark:border-strokedark dark:bg-meta-4/30 text-xs">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <i class="fas {{ \App\Support\Flujos::iconoAdjunto($adj->mime_type) }} text-primary text-xs shrink-0"></i>
                                        <div class="min-w-0">
                                            <a href="{{ route('flujos.instancias.adjuntos.descargar', [$instancia, $adj]) }}"
                                               class="font-bold text-black dark:text-white hover:text-primary transition truncate block text-[11px]"
                                               title="{{ $adj->nombre_original }}">
                                                {{ $adj->nombre_original }}
                                            </a>
                                            <span class="text-[9px] text-bodydark2 block font-mono">
                                                {{ number_format($adj->tamano_bytes / 1024, 1) }} KB ·
                                                {{ $adj->uploader?->name ?? 'Usuario eliminado' }} ·
                                                {{ $adj->created_at?->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>

                                    @can('eliminarAdjunto', $adj)
                                        <form method="POST" action="{{ route('flujos.instancias.adjuntos.eliminar', [$instancia, $adj]) }}"
                                              onsubmit="return confirm('¿Eliminar {{ $adj->nombre_original }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-bodydark2 hover:text-meta-1 transition p-1 shrink-0" title="Eliminar archivo">
                                                <i class="fas fa-times text-xs"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</div>