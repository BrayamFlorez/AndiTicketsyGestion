{{-- Recibe: $instancia, $stepActual, $puede --}}
@php
    $adjuntosPorRevision = $instancia->attachments->groupBy('revision')->sortKeysDesc();
    $stageRequiereAdjunto = $stepActual?->stage?->requiere_adjunto ?? false;

    // Helper local para íconos según mime (MOVIDO AQUÍ ARRIBA)
    if (! function_exists('iconoAdjunto')) {
        function iconoAdjunto(string $mime): string {
            return match (true) {
                str_contains($mime, 'pdf')      => 'fa-file-pdf',
                str_contains($mime, 'word')     => 'fa-file-word',
                str_contains($mime, 'excel'),
                str_contains($mime, 'spreadsheet') => 'fa-file-excel',
                str_contains($mime, 'image')    => 'fa-file-image',
                str_contains($mime, 'zip')      => 'fa-file-archive',
                str_contains($mime, 'text')     => 'fa-file-alt',
                default                         => 'fa-file',
            };
        }
    }
@endphp

<div class="db-card">
    <div class="form-card-body" style="padding: 1.25rem;">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest">
                <i class="fas fa-paperclip mr-1"></i> Documentos ({{ $instancia->attachments->count() }})
            </h3>
        </div>

        {{-- Aviso de requiere_adjunto --}}
        @if ($stageRequiereAdjunto && $stepActual)
            @php
                $miAdjuntoEnStep = $instancia->attachments
                    ->where('workflow_instance_step_id', $stepActual->id)
                    ->where('subido_por', auth()->id())
                    ->count() > 0;
            @endphp

            @if (! $miAdjuntoEnStep && $puede['adjuntar_step'])
                <div class="mb-3 px-3 py-2 rounded-lg bg-yellow-500/10 border border-yellow-500/30 text-yellow-300 text-xs">
                    <i class="fas fa-info-circle mr-1"></i>
                    <strong>Esta etapa requiere adjunto</strong> — subí un archivo antes de aprobar.
                </div>
            @endif
        @endif

        {{-- Formularios de subida --}}
        @if ($puede['adjuntar_instancia'] || $puede['adjuntar_step'])
            <div x-data="{ destino: '{{ $puede['adjuntar_step'] ? 'step' : 'instancia' }}' }" class="mb-4 rounded-lg bg-[#232324] border border-gray-800 p-3">

                @if ($puede['adjuntar_instancia'] && $puede['adjuntar_step'])
                    <div class="flex items-center gap-1 mb-2">
                        <button type="button"
                            class="text-[10px] px-3 py-1 rounded font-bold uppercase tracking-wider transition-colors"
                            :class="destino === 'instancia' ? 'bg-[#007acc] text-white' : 'bg-transparent text-gray-500 hover:text-white'"
                            @click="destino = 'instancia'">
                            <i class="fas fa-folder"></i> Al trámite
                        </button>
                        <button type="button"
                            class="text-[10px] px-3 py-1 rounded font-bold uppercase tracking-wider transition-colors"
                            :class="destino === 'step' ? 'bg-[#007acc] text-white' : 'bg-transparent text-gray-500 hover:text-white'"
                            @click="destino = 'step'">
                            <i class="fas fa-tag"></i> A esta etapa
                        </button>
                    </div>
                @endif

                <form method="POST" action="{{ route('flujos.instancias.adjuntos.subir', $instancia) }}"
                      enctype="multipart/form-data">
                    @csrf
                    <template x-if="destino === 'step'">
                        <input type="hidden" name="workflow_instance_step_id" value="{{ $stepActual?->id }}">
                    </template>

                    <div class="flex gap-2">
                        <input type="file" name="archivo" required
                            class="form-input text-xs flex-1"
                            accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.txt,.jpg,.jpeg,.png,.zip">
                        <button type="submit" class="db-btn solid text-xs">
                            <i class="fas fa-upload"></i>
                        </button>
                    </div>
                    <p class="text-[10px] text-gray-500 mt-1">
                        Máx. 20 MB · PDF, Word, Excel, imágenes, ZIP.
                    </p>
                </form>
            </div>
        @endif

        {{-- Listado agrupado por revisión --}}
        @if ($adjuntosPorRevision->isEmpty())
            <p class="text-xs text-gray-500 italic text-center py-4">Sin documentos adjuntos.</p>
        @else
            <div class="space-y-3">
                @foreach ($adjuntosPorRevision as $rev => $adjuntos)
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase tracking-widest font-black mb-2">
                            Revisión {{ $rev }}
                            @if ($rev == $instancia->revision)
                                <span class="text-[#007acc]">· Activa</span>
                            @endif
                        </p>

                        <div class="space-y-1.5">
                            @foreach ($adjuntos as $adj)
                                <div class="flex items-center gap-2 p-2 rounded bg-[#1a1a1b] border border-gray-800 text-xs">
                                    <i class="fas {{ iconoAdjunto($adj->mime_type) }} text-[#007acc] flex-shrink-0"></i>
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
                                            @if ($adj->workflow_instance_step_id)
                                                · <span class="text-yellow-400">etapa</span>
                                            @else
                                                · <span class="text-blue-400">trámite</span>
                                            @endif
                                        </p>
                                    </div>

                                    @can('eliminarAdjunto', $adj)
                                        <form method="POST"
                                              action="{{ route('flujos.instancias.adjuntos.eliminar', [$instancia, $adj]) }}"
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

