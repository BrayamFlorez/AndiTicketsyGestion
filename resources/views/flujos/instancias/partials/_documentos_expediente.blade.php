{{-- Recibe: $instancia, $puede
     Nota: $instancia->attachments ya viene filtrado a trámite-level (workflow_instance_step_id IS NULL) --}}
@php
    $documentosPorRevision = $instancia->attachments->groupBy('revision')->sortKeysDesc();
@endphp

<div class="db-card">
    <div class="form-card-body" style="padding: 1.25rem;">
        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">
            <i class="fas fa-folder-open mr-1"></i> Documentos del expediente
            <span class="text-gray-600">({{ $instancia->attachments->count() }})</span>
        </h3>
        <p class="text-[10px] text-gray-500 mb-3">
            Archivos que el iniciador adjunta al trámite. Los documentos de cada etapa aparecen en su lugar en la línea de tiempo.
        </p>

        {{-- Formulario de subida (solo iniciador, mientras esté abierta) --}}
        @if ($puede['adjuntar_expediente'])
            <form method="POST" action="{{ route('flujos.instancias.adjuntos.subir', $instancia) }}"
                  enctype="multipart/form-data" class="mb-3 rounded-lg bg-[#232324] border border-gray-800 p-3">
                @csrf
                <div class="flex gap-2">
                    <input type="file" name="archivo" required class="form-input text-xs flex-1"
                        accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.txt,.jpg,.jpeg,.png,.zip">
                    <button type="submit" class="db-btn solid text-xs">
                        <i class="fas fa-upload"></i>
                    </button>
                </div>
                <p class="text-[10px] text-gray-500 mt-1">
                    Máx. 20 MB · PDF, Word, Excel, imágenes, ZIP.
                </p>
            </form>
        @endif

        {{-- Listado agrupado por revisión --}}
        @if ($documentosPorRevision->isEmpty())
            <p class="text-xs text-gray-500 italic text-center py-4">Sin documentos del expediente.</p>
        @else
            <div class="space-y-3">
                @foreach ($documentosPorRevision as $rev => $docs)
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase tracking-widest font-black mb-2">
                            Revisión {{ $rev }}
                            @if ($rev == $instancia->revision) <span class="text-[#007acc]">· Activa</span> @endif
                        </p>
                        <div class="space-y-1.5">
                            @foreach ($docs as $adj)
                                <div class="flex items-center gap-2 p-2 rounded bg-[#1a1a1b] border border-gray-800 text-xs">
                                    <i class="fas {{ \App\Support\Flujos::iconoAdjunto($adj->mime_type) }} text-[#007acc] flex-shrink-0"></i>
                                    <div class="flex-1 min-w-0">
                                        <a href="{{ route('flujos.instancias.adjuntos.descargar', [$instancia, $adj]) }}"
                                            class="text-white font-semibold hover:text-[#007acc] truncate block"
                                            title="{{ $adj->nombre_original }}">
                                            {{ $adj->nombre_original }}
                                        </a>
                                        <p class="text-[10px] text-gray-500">
                                            {{ number_format($adj->tamano_bytes / 1024, 1) }} KB ·
                                            {{ $adj->uploader?->name ?? 'Usuario eliminado' }} ·
                                            {{ $adj->created_at?->diffForHumans() }}
                                        </p>
                                    </div>
                                    @can('eliminarAdjunto', $adj)
                                        <form method="POST" action="{{ route('flujos.instancias.adjuntos.eliminar', [$instancia, $adj]) }}"
                                            onsubmit="return confirm('¿Eliminar {{ $adj->nombre_original }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-500 hover:text-red-400 transition-colors" title="Eliminar">
                                                <i class="fas fa-times"></i>
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