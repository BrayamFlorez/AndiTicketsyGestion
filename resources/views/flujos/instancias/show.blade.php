{{-- resources/views/flujos/instancias/show.blade.php --}}
<x-app-layout>
    <x-slot name="title">Flujo {{ $instancia->codigo }}</x-slot>

    @php
        $puedeEditarCabecera = auth()->user()->can('editarCabecera', $instancia);
    @endphp

    {{-- ── Cabecera y Estado General ──────────────────────────────────── --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-3 flex-wrap mb-1">
                <span class="text-xs font-mono font-bold text-bodydark2 uppercase tracking-wider">
                    {{ $instancia->codigo }}
                </span>
                @include('flujos.instancias.partials._badge_estado', ['estado' => $instancia->estado])
                @if ($instancia->referencia_externa)
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-primary/10 border border-primary/30 text-primary inline-flex items-center gap-1">
                        <i class="fas fa-hashtag text-[9px]"></i>{{ $instancia->referencia_externa }}
                    </span>
                @endif
            </div>

            <h2 class="text-title-md2 font-bold text-black dark:text-white">
                {{ $instancia->titulo }}
            </h2>

            <p class="text-xs text-bodydark2 mt-1">
                Plantilla: <strong class="text-black dark:text-white">{{ $instancia->template->nombre }}</strong> ·
                Revisión {{ $instancia->revision }} · Iniciado {{ $instancia->iniciado_en?->format('d/m/Y H:i') }}
            </p>

            {{-- Historial de cambios de cabecera --}}
            @if ($instancia->history->isNotEmpty() && $puedeEditarCabecera)
                <div class="mt-3" x-data="{ open: false }">
                    <button type="button" @click="open = !open"
                        class="text-[11px] text-bodydark2 hover:text-primary flex items-center gap-1 transition">
                        <i class="fas text-[9px]" :class="open ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                        Historial de cambios ({{ $instancia->history->count() }})
                    </button>
                    <div x-show="open" x-cloak class="mt-2 space-y-1 pl-3 border-l-2 border-stroke dark:border-strokedark">
                        @foreach ($instancia->history as $cambio)
                            <div class="text-[11px] text-bodydark2 py-1">
                                <span class="font-mono text-primary">{{ $cambio->label_campo }}</span>
                                modificado por
                                <span class="text-black dark:text-white font-medium">{{ $cambio->editor?->name ?? 'Usuario eliminado' }}</span>
                                · {{ $cambio->editado_en->format('d/m/Y H:i') }}
                                @if ($cambio->valor_anterior)
                                    <div class="mt-0.5 text-[10px] text-bodydark2 italic">
                                        "{{ Str::limit($cambio->valor_anterior, 80) }}"
                                        <i class="fas fa-arrow-right text-[8px] mx-1"></i>
                                        "{{ Str::limit($cambio->valor_nuevo ?? '(vacío)', 80) }}"
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="flex items-center gap-2 shrink-0">
            @if ($puedeEditarCabecera)
                <button type="button" @click="$dispatch('open-modal', 'editar-cabecera')"
                    class="inline-flex items-center justify-center gap-1.5 rounded border border-stroke py-2 px-3 text-xs font-medium text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition"
                    title="Editar título, descripción y referencia">
                    <i class="fas fa-pen text-[10px]"></i> Editar datos
                </button>
            @endif

            <a href="{{ route('flujos.instancias.index') }}"
                class="inline-flex items-center justify-center gap-2 rounded border border-stroke py-2 px-4 text-center text-xs font-medium text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition shadow-sm">
                <i class="fas fa-arrow-left text-[10px]"></i> Volver
            </a>
        </div>
        <div class="flex items-center gap-2">
            @can('auditoria.ver')
                <a href="{{ route('auditoria.show', $instancia) }}" class="db-btn ghost" title="Ver historial de auditoría">
                    <i class="fas fa-shield-alt mr-2"></i> Auditoría
                </a>
            @endcan
            <a href="{{ route('flujos.instancias.index') }}" class="db-btn ghost">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
        </div>
    </div>

    {{-- Alerts Flash --}}
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

    @if (session('info'))
        <div class="mb-6 flex w-full border-l-6 border-primary bg-primary/10 px-7 py-3 shadow-md rounded-sm">
            <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-primary text-white shrink-0">
                <i class="fas fa-info"></i>
            </div>
            <div class="w-full">
                <p class="text-sm text-primary">{{ session('info') }}</p>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 flex w-full border-l-6 border-meta-1 bg-meta-1/10 px-7 py-3 shadow-md dark:bg-meta-1/20 dark:border-meta-1 rounded-sm">
            <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-meta-1 text-white shrink-0">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="w-full">
                <h5 class="mb-1 font-bold text-meta-1">Revisa los siguientes campos</h5>
                <ul class="list-disc list-inside space-y-0.5 text-xs text-meta-1 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- ── Grid Principal de Detalle (8/4 cols) ────────────────────────── --}}
    <div class="grid grid-cols-12 gap-4 md:gap-6 2xl:gap-7.5">

        {{-- COLUMNA IZQUIERDA: Info General y Timeline (8 cols) --}}
        <div class="col-span-12 lg:col-span-8 space-y-6">

            {{-- Info General Card --}}
            <div class="rounded-sm border border-stroke bg-white p-5 shadow-default dark:border-strokedark dark:bg-boxdark">
                <h3 class="font-bold text-black dark:text-white text-xs uppercase tracking-wider border-b border-stroke pb-3 mb-4 dark:border-strokedark flex items-center gap-2">
                    <i class="fas fa-info-circle text-primary"></i> Información General
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-[10px] font-bold text-bodydark2 uppercase block">Tipo de Documento</span>
                        <div class="text-black dark:text-white font-medium block mt-0.5">
                            @if ($instancia->documentType)
                                <i class="fas {{ $instancia->documentType->icono ?: 'fa-file-alt' }} text-primary mr-1"></i>
                                {{ $instancia->documentType->nombre }}
                            @else
                                <span class="text-bodydark2">—</span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <span class="text-[10px] font-bold text-bodydark2 uppercase block">Iniciador</span>
                        <div class="text-black dark:text-white font-medium block mt-0.5">
                            {{ $instancia->iniciador->name }}
                        </div>
                        <span class="text-[10px] text-bodydark2 font-mono block">{{ $instancia->iniciador->email }}</span>
                    </div>

                    <div>
                        <span class="text-[10px] font-bold text-bodydark2 uppercase block">Etapa Actual</span>
                        <div class="text-black dark:text-white font-medium block mt-0.5">
                            {{ $instancia->etapaActual->nombre ?? '—' }}
                        </div>
                    </div>

                    <div>
                        <span class="text-[10px] font-bold text-bodydark2 uppercase block">Cerrado En</span>
                        <div class="text-black dark:text-white font-medium block mt-0.5">
                            {{ $instancia->cerrado_en?->format('d/m/Y H:i') ?? '—' }}
                        </div>
                    </div>
                </div>

                @if ($instancia->descripcion)
                    <div class="mt-4 pt-3 border-t border-dashed border-stroke dark:border-strokedark">
                        <span class="text-[10px] font-bold text-bodydark2 uppercase block mb-1">
                            Descripción del Trámite
                        </span>
                        <p class="text-xs text-black dark:text-white leading-relaxed whitespace-pre-line">
                            {{ $instancia->descripcion }}
                        </p>
                    </div>
                @endif
            </div>

            {{-- Timeline & Etapas --}}
            <div class="rounded-sm border border-stroke bg-white p-5 shadow-default dark:border-strokedark dark:bg-boxdark">
                <h3 class="font-bold text-black dark:text-white text-xs uppercase tracking-wider border-b border-stroke pb-3 mb-4 dark:border-strokedark flex items-center gap-2">
                    <i class="fas fa-stream text-primary"></i> Línea de Tiempo
                </h3>

                <div class="space-y-4">
                    @foreach ($instancia->steps as $step)
                        @php
                            $cfgStep = match ($step->estado) {
                                'aprobado' => [
                                    'icon_bg' => 'bg-meta-3 text-white',
                                    'icono'   => 'fa-check',
                                ],
                                'rechazado' => [
                                    'icon_bg' => 'bg-meta-1 text-white',
                                    'icono'   => 'fa-times',
                                ],
                                'devuelto' => [
                                    'icon_bg' => 'bg-meta-6 text-white',
                                    'icono'   => 'fa-undo',
                                ],
                                'omitido' => [
                                    'icon_bg' => 'bg-bodydark2 text-white',
                                    'icono'   => 'fa-forward',
                                ],
                                'en_curso' => [
                                    'icon_bg' => 'bg-primary text-white',
                                    'icono'   => 'fa-spinner fa-spin',
                                ],
                                default => [
                                    'icon_bg' => 'bg-bodydark2 text-white',
                                    'icono'   => 'fa-clock',
                                ],
                            };

                            $adjuntosStep     = $step->attachments;
                            $comentariosExtra = $step->comentariosAdicionales;
                            $stepEnCurso      = $step->estado === \App\Models\WorkflowInstanceStep::ESTADO_EN_CURSO;

                            // Permisos por step — resueltos por la Policy, no por estado
                            $puedeEtiquetar = auth()->user()->can('etiquetarStep',  [$instancia, $step]);
                            $puedeAdjuntar  = auth()->user()->can('adjuntarAStep',  [$instancia, $step]);
                            $puedeComentar  = auth()->user()->can('comentarStep',   [$instancia, $step]);
                        @endphp

                        <div class="rounded border border-stroke p-4 dark:border-strokedark dark:bg-meta-4/20">

                            {{-- Cabecera del Step --}}
                            <div class="flex items-center gap-3 mb-2 flex-wrap">
                                <div class="flex h-7 w-7 items-center justify-center rounded-full font-bold text-xs shrink-0 {{ $cfgStep['icon_bg'] }}">
                                    <i class="fas {{ $cfgStep['icono'] }}"></i>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="text-xs font-bold text-black dark:text-white">
                                            {{ $step->stage?->nombre ?? '(Etapa Eliminada)' }}
                                        </h4>

                                        {{-- Código de referencia de la etapa --}}
                                        <div x-data="{ editing: false, codigo: @js($step->codigo_referencia ?? '') }"
                                             class="inline-flex items-center gap-1">
                                            <template x-if="!editing">
                                                <span class="inline-flex items-center gap-1">
                                                    @if ($step->codigo_referencia)
                                                        <span class="inline-flex items-center gap-1 rounded bg-primary/10 border border-primary/20 px-2 py-0.5 text-[9px] font-mono font-bold text-primary"
                                                              title="Código de referencia de la etapa">
                                                            <i class="fas fa-tag text-[8px]"></i>{{ $step->codigo_referencia }}
                                                        </span>
                                                    @endif
                                                    @if ($puedeEtiquetar)
                                                        <button type="button"
                                                            @click="editing = true; $nextTick(() => $refs.inp.focus())"
                                                            class="text-[10px] text-bodydark2 hover:text-primary transition"
                                                            title="{{ $step->codigo_referencia ? 'Editar código' : 'Agregar código de referencia' }}">
                                                            <i class="fas {{ $step->codigo_referencia ? 'fa-pen' : 'fa-plus-circle' }}"></i>
                                                        </button>
                                                    @endif
                                                </span>
                                            </template>

                                            @if ($puedeEtiquetar)
                                                <form x-show="editing" x-cloak method="POST"
                                                    action="{{ route('flujos.instancias.steps.codigo-referencia', [$instancia, $step]) }}"
                                                    class="inline-flex items-center gap-1">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="text" name="codigo_referencia" x-model="codigo" x-ref="inp"
                                                        class="rounded border border-stroke bg-transparent py-0.5 px-2 text-[10px] font-mono uppercase outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white max-w-[130px]"
                                                        placeholder="REF-ETAPA-042" maxlength="50"
                                                        @keydown.escape.prevent="editing = false; codigo = @js($step->codigo_referencia ?? '')">
                                                    <button type="submit" class="text-meta-3 hover:text-opacity-80 px-1" title="Guardar">
                                                        <i class="fas fa-check text-xs"></i>
                                                    </button>
                                                    <button type="button"
                                                        @click="editing = false; codigo = @js($step->codigo_referencia ?? '')"
                                                        class="text-bodydark2 hover:text-meta-1 px-1" title="Cancelar">
                                                        <i class="fas fa-times text-xs"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>

                                    <span class="text-[10px] text-bodydark2 block mt-0.5">
                                        Rev. {{ $step->revision }} ·
                                        {{ ucfirst(str_replace('_', ' ', $step->estado)) }} ·
                                        {{ $step->inicio_en?->format('d/m H:i') }}
                                        @if ($step->cierre_en)
                                            → {{ $step->cierre_en->format('d/m H:i') }}
                                        @endif
                                    </span>
                                </div>

                                @if ($adjuntosStep->isNotEmpty())
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-bodydark2 bg-gray-2 px-2 py-0.5 rounded dark:bg-meta-4">
                                        <i class="fas fa-paperclip text-primary"></i> {{ $adjuntosStep->count() }}
                                    </span>
                                @endif
                            </div>

                            {{-- Asignaciones / Actores del Step --}}
                            <div class="space-y-1.5 mt-3 sm:ml-10">
                                @foreach ($step->assignments as $assignment)
                                    @php
                                        $cfgAsig = match ($assignment->estado) {
                                            'aprobado'  => ['color' => 'text-meta-3',   'icono' => 'fa-check-circle'],
                                            'rechazado' => ['color' => 'text-meta-1',   'icono' => 'fa-times-circle'],
                                            'devuelto'  => ['color' => 'text-meta-6',   'icono' => 'fa-undo'],
                                            default     => ['color' => 'text-bodydark2','icono' => 'fa-clock'],
                                        };
                                    @endphp
                                    <div class="rounded border border-stroke bg-white p-2.5 dark:border-strokedark dark:bg-boxdark text-xs">
                                        <div class="flex items-center justify-between gap-2 flex-wrap">
                                            <div class="flex items-center gap-1.5">
                                                <i class="fas {{ $cfgAsig['icono'] }} {{ $cfgAsig['color'] }}"></i>
                                                <span class="font-bold text-black dark:text-white">
                                                    {{ $assignment->user?->name ?? ($assignment->label_resuelto ?? 'Pendiente de tomar') }}
                                                </span>
                                                <span class="text-[10px] uppercase font-bold {{ $cfgAsig['color'] }}">
                                                    ({{ $assignment->estado }})
                                                </span>
                                            </div>
                                            @if ($assignment->decidido_en)
                                                <span class="text-[10px] font-mono text-bodydark2">
                                                    {{ $assignment->decidido_en->format('d/m H:i') }}
                                                </span>
                                            @endif
                                        </div>
                                        @if ($assignment->comentario)
                                            <p class="mt-1 text-[11px] text-bodydark2 italic">"{{ $assignment->comentario }}"</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            {{-- ── Documentos de esta etapa ──────────────────────── --}}
                            @if ($adjuntosStep->isNotEmpty() || $puedeAdjuntar)
                                <div class="mt-3 sm:ml-10 pt-2 border-t border-dashed border-stroke dark:border-strokedark">
                                    <span class="text-[10px] font-bold text-bodydark2 uppercase tracking-wider block mb-2">
                                        <i class="fas fa-paperclip text-primary mr-1"></i> Documentos de esta etapa
                                    </span>

                                    @if ($adjuntosStep->isNotEmpty())
                                        <div class="space-y-1.5 mb-2">
                                            @foreach ($adjuntosStep as $adj)
                                                @php
                                                    // Adjunto subido después del cierre del step
                                                    $adjuntoPostCierre = $step->cierre_en
                                                        && $adj->created_at
                                                        && $adj->created_at->gt($step->cierre_en);
                                                @endphp
                                                <div class="flex items-center justify-between gap-2 p-2 rounded border border-stroke bg-white dark:border-strokedark dark:bg-boxdark text-xs">
                                                    <div class="flex items-center gap-2 min-w-0">
                                                        <i class="fas {{ \App\Support\Flujos::iconoAdjunto($adj->mime_type) }} text-primary text-sm shrink-0"></i>
                                                        <div class="min-w-0">
                                                            <a href="{{ route('flujos.instancias.adjuntos.descargar', [$instancia, $adj]) }}"
                                                                class="font-bold text-black dark:text-white hover:text-primary transition truncate block"
                                                                title="{{ $adj->nombre_original }}">
                                                                {{ $adj->nombre_original }}
                                                            </a>
                                                            <span class="text-[10px] text-bodydark2 block">
                                                                Rev. {{ $adj->revision }} ·
                                                                {{ number_format($adj->tamano_bytes / 1024, 1) }} KB ·
                                                                {{ $adj->uploader?->name ?? 'Usuario eliminado' }}
                                                                @if ($adjuntoPostCierre)
                                                                    · <span class="text-yellow-500 font-bold">post-cierre</span>
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </div>

                                                    @can('eliminarAdjunto', $adj)
                                                        <form method="POST"
                                                            action="{{ route('flujos.instancias.adjuntos.eliminar', [$instancia, $adj]) }}"
                                                            onsubmit="return confirm('¿Eliminar {{ $adj->nombre_original }}?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-bodydark2 hover:text-meta-1 transition p-1" title="Eliminar archivo">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    {{-- Subida de archivos a ESTE step --}}
                                    @if ($puedeAdjuntar)
                                        @unless ($stepEnCurso)
                                            <div class="mb-2 text-[10px] text-yellow-500 flex items-center gap-1">
                                                <i class="fas fa-unlock text-[9px]"></i>
                                                Etapa cerrada — adjuntar habilitado por ajuste global del sistema.
                                            </div>
                                        @endunless

                                        <form method="POST"
                                            action="{{ route('flujos.instancias.adjuntos.subir', $instancia) }}"
                                            enctype="multipart/form-data" class="flex items-center gap-2 mt-2">
                                            @csrf
                                            <input type="hidden" name="workflow_instance_step_id" value="{{ $step->id }}">
                                            <input type="file" name="archivo" required
                                                accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.txt,.jpg,.jpeg,.png,.zip"
                                                class="w-full rounded border border-stroke bg-transparent py-1 px-2 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                                            <button type="submit"
                                                class="inline-flex items-center gap-1 rounded bg-primary py-1.5 px-3 text-xs font-bold uppercase text-white hover:bg-opacity-90 transition shrink-0 cursor-pointer"
                                                title="Subir a esta etapa">
                                                <i class="fas fa-upload text-[10px]"></i> Subir
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endif

                            {{-- ── Comentarios adicionales (post-firma) ──────────── --}}
                            @if ($comentariosExtra->isNotEmpty() || $puedeComentar)
                                <div class="mt-3 sm:ml-10 pt-2 border-t border-dashed border-stroke dark:border-strokedark">
                                    <span class="text-[10px] font-bold text-bodydark2 uppercase tracking-wider block mb-2">
                                        <i class="fas fa-comments text-primary mr-1"></i>
                                        Comentarios adicionales
                                        @if ($comentariosExtra->isNotEmpty())
                                            ({{ $comentariosExtra->count() }})
                                        @endif
                                    </span>

                                    @if ($comentariosExtra->isNotEmpty())
                                        <div class="space-y-1.5 mb-2">
                                            @foreach ($comentariosExtra as $comentario)
                                                @php
                                                    $comentarioPostCierre = $step->cierre_en
                                                        && $comentario->created_at
                                                        && $comentario->created_at->gt($step->cierre_en);
                                                @endphp
                                                <div class="rounded border border-stroke bg-white p-2.5 dark:border-strokedark dark:bg-boxdark">
                                                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                                                        <div class="w-5 h-5 rounded-full bg-primary text-white text-[9px] font-bold flex items-center justify-center shrink-0">
                                                            {{ \App\Support\Flujos::iniciales($comentario->user?->name ?? '?') }}
                                                        </div>
                                                        <span class="text-[11px] font-bold text-black dark:text-white">
                                                            {{ $comentario->user?->name ?? 'Usuario eliminado' }}
                                                        </span>
                                                        <span class="text-[10px] font-mono text-bodydark2">
                                                            {{ $comentario->created_at->format('d/m/Y H:i') }}
                                                        </span>
                                                        @if ($comentarioPostCierre)
                                                            <span class="text-[9px] px-1.5 py-0.5 rounded bg-yellow-500/10 text-yellow-500 border border-yellow-500/30 font-bold uppercase">
                                                                post-cierre
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <p class="text-xs text-black dark:text-white whitespace-pre-wrap">
                                                        {{ $comentario->comentario }}
                                                    </p>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    @if ($puedeComentar)
                                        <div x-data="{ abierto: false }">
                                            <button type="button" @click="abierto = !abierto"
                                                class="text-[11px] text-primary hover:text-opacity-80 flex items-center gap-1 transition">
                                                <i class="fas text-[9px]" :class="abierto ? 'fa-minus' : 'fa-plus'"></i>
                                                <span x-text="abierto ? 'Cancelar' : 'Agregar comentario'"></span>
                                            </button>

                                            <form x-show="abierto" x-cloak
                                                action="{{ route('flujos.instancias.steps.comentar', [$instancia, $step]) }}"
                                                method="POST" class="mt-2 space-y-2">
                                                @csrf
                                                <textarea name="comentario" required minlength="3" maxlength="1000" rows="3"
                                                    placeholder="Escribe un comentario adicional a esta etapa..."
                                                    class="w-full rounded border border-stroke bg-transparent py-2 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white"></textarea>
                                                <div class="flex justify-end">
                                                    <button type="submit"
                                                        class="inline-flex items-center gap-1 rounded bg-primary py-1.5 px-3 text-[11px] font-bold uppercase text-white hover:bg-opacity-90 transition cursor-pointer">
                                                        <i class="fas fa-paper-plane text-[10px]"></i> Guardar
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            @endif

                        </div>
                    @endforeach

                    @if ($instancia->steps->isEmpty())
                        <div class="text-center py-6 text-bodydark2 text-xs italic">
                            Sin actividad registrada aún.
                        </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- COLUMNA DERECHA: Acciones & Decisiones (4 cols) --}}
        <div class="col-span-12 lg:col-span-4 space-y-6">

            {{-- Documentos del expediente — SIEMPRE visible, hereda todo el scope --}}
            @include('flujos.instancias.partials._documentos_expediente')

            {{-- Requiere Tu Decisión --}}
            @if ($puede['actuar'])
                <div class="rounded-sm border border-stroke bg-white p-5 shadow-default dark:border-strokedark dark:bg-boxdark">
                    <h3 class="font-bold text-primary uppercase tracking-wider text-xs border-b border-stroke pb-2 mb-4 dark:border-strokedark flex items-center gap-1.5">
                        <i class="fas fa-hand-paper"></i> Requiere Tu Decisión
                    </h3>

                    @foreach ($misAsignacionesPendientes as $asignacion)
                        <form method="POST"
                            action="{{ route('flujos.instancias.asignaciones.actuar', [$instancia, $asignacion]) }}"
                            class="space-y-3 mb-4 last:mb-0" x-data="{ accion: 'aprobar' }">
                            @csrf

                            <span class="text-[10px] font-bold text-bodydark2 uppercase tracking-wider block">
                                {{ $asignacion->label_resuelto }}
                                @if ($asignacion->stageActor?->etiqueta)
                                    · {{ $asignacion->stageActor->etiqueta }}
                                @endif
                            </span>

                            <div class="grid grid-cols-3 gap-1.5">
                                <label class="cursor-pointer select-none">
                                    <input type="radio" name="accion" value="aprobar" x-model="accion" class="peer sr-only" checked>
                                    <div class="text-center py-1.5 px-1 rounded border border-stroke text-[10px] font-bold uppercase transition peer-checked:border-meta-3 peer-checked:bg-meta-3/10 peer-checked:text-meta-3 text-bodydark2 hover:text-black dark:border-strokedark dark:hover:text-white">
                                        <i class="fas fa-check"></i> Aprobar
                                    </div>
                                </label>
                                <label class="cursor-pointer select-none">
                                    <input type="radio" name="accion" value="rechazar" x-model="accion" class="peer sr-only">
                                    <div class="text-center py-1.5 px-1 rounded border border-stroke text-[10px] font-bold uppercase transition peer-checked:border-meta-1 peer-checked:bg-meta-1/10 peer-checked:text-meta-1 text-bodydark2 hover:text-black dark:border-strokedark dark:hover:text-white">
                                        <i class="fas fa-times"></i> Rechazar
                                    </div>
                                </label>
                                <label class="cursor-pointer select-none">
                                    <input type="radio" name="accion" value="devolver" x-model="accion" class="peer sr-only">
                                    <div class="text-center py-1.5 px-1 rounded border border-stroke text-[10px] font-bold uppercase transition peer-checked:border-meta-6 peer-checked:bg-meta-6/10 peer-checked:text-meta-6 text-bodydark2 hover:text-black dark:border-strokedark dark:hover:text-white">
                                        <i class="fas fa-undo"></i> Devolver
                                    </div>
                                </label>
                            </div>

                            <textarea name="comentario" rows="2" maxlength="2000"
                                :placeholder="accion === 'aprobar' ? 'Comentario opcional...' : 'Motivo obligatorio...'"
                                :required="accion !== 'aprobar'"
                                class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white"></textarea>

                            <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-1.5 rounded bg-primary py-2 px-4 text-center text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm cursor-pointer">
                                <i class="fas fa-paper-plane text-[10px]"></i> Registrar Decisión
                            </button>
                        </form>
                    @endforeach
                </div>
            @endif

            {{-- Panel de Corrección --}}
            @if ($puede['corregir'])
                <div class="rounded-sm border border-stroke bg-white p-5 shadow-default dark:border-strokedark dark:bg-boxdark">
                    <h3 class="font-bold text-meta-6 uppercase tracking-wider text-xs mb-2 flex items-center gap-1.5">
                        <i class="fas fa-exclamation-triangle"></i> Requiere Corrección
                    </h3>
                    <p class="text-xs text-bodydark2 mb-4 leading-relaxed">
                        Revisa los comentarios de rechazo, realiza las modificaciones necesarias y reenvía el trámite.
                        Se registrará como <strong>Revisión {{ $instancia->revision + 1 }}</strong>.
                    </p>
                    <form method="POST" action="{{ route('flujos.instancias.corregir', $instancia) }}">
                        @csrf
                        <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-1.5 rounded bg-meta-6 py-2 px-4 text-center text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm cursor-pointer"
                            onclick="return confirm('¿Reenviar el flujo tras corregir?')">
                            <i class="fas fa-redo text-[10px]"></i> Reenviar Corregido
                        </button>
                    </form>
                </div>
            @endif

            {{-- Panel de Administración --}}
            @if ($puede['desistir'] || $puede['cancelar'] || $puede['reabrir'])
                <div class="rounded-sm border border-stroke bg-white p-5 shadow-default dark:border-strokedark dark:bg-boxdark space-y-3">
                    <h3 class="font-bold text-black dark:text-white uppercase tracking-wider text-xs border-b border-stroke pb-2 dark:border-strokedark flex items-center gap-1.5">
                        <i class="fas fa-tools text-primary"></i> Administración
                    </h3>

                    @if ($puede['desistir'])
                        <form method="POST" action="{{ route('flujos.instancias.desistir', $instancia) }}" class="space-y-2">
                            @csrf
                            <input type="text" name="motivo" maxlength="1000"
                                placeholder="Motivo de desistimiento (opcional)..."
                                class="w-full rounded border border-stroke bg-transparent py-1 px-3 text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                            <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-1 rounded border border-meta-1 py-1.5 px-3 text-xs font-bold uppercase text-meta-1 hover:bg-meta-1/10 transition"
                                onclick="return confirm('¿Desistir del flujo? No se podrá continuar sin reabrirlo.')">
                                <i class="fas fa-hand-paper text-[10px]"></i> Desistir del Flujo
                            </button>
                        </form>
                    @endif

                    @if ($puede['cancelar'])
                        <form method="POST" action="{{ route('flujos.instancias.cancelar', $instancia) }}" class="space-y-2">
                            @csrf
                            <input type="text" name="motivo" required maxlength="1000"
                                placeholder="Motivo de cancelación (obligatorio)..."
                                class="w-full rounded border border-stroke bg-transparent py-1 px-3 text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                            <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-1 rounded border border-meta-1 py-1.5 px-3 text-xs font-bold uppercase text-meta-1 hover:bg-meta-1/10 transition"
                                onclick="return confirm('¿Cancelar este flujo?')">
                                <i class="fas fa-ban text-[10px]"></i> Cancelar Flujo (Admin)
                            </button>
                        </form>
                    @endif

                    @if ($puede['reabrir'])
                        <form method="POST" action="{{ route('flujos.instancias.reabrir', $instancia) }}">
                            @csrf
                            <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-1 rounded border border-stroke py-1.5 px-3 text-xs font-bold uppercase text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition"
                                onclick="return confirm('¿Reabrir el flujo cancelado?')">
                                <i class="fas fa-play text-[10px]"></i> Reabrir Flujo
                            </button>
                        </form>
                    @endif
                </div>
            @endif

        </div>

    </div>

    {{-- ── Modal: editar cabecera ──────────────────────────────────────── --}}
    @if ($puedeEditarCabecera)
        <div x-data="{ open: false }" x-show="open" x-cloak
            @open-modal.window="if ($event.detail === 'editar-cabecera') open = true"
            @keydown.escape.window="open = false"
            class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/60 backdrop-blur-sm py-8">
            <div @click.outside="open = false"
                class="w-full max-w-2xl rounded-sm border border-stroke bg-white shadow-2xl dark:border-strokedark dark:bg-boxdark mx-4">

                <div class="flex items-center justify-between px-6 py-4 border-b border-stroke dark:border-strokedark">
                    <h3 class="font-bold text-black dark:text-white flex items-center gap-2">
                        <i class="fas fa-pen text-primary"></i> Editar datos del trámite
                    </h3>
                    <button type="button" @click="open = false" class="text-bodydark2 hover:text-black dark:hover:text-white">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form action="{{ route('flujos.instancias.cabecera', $instancia) }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    @method('PATCH')

                    @if ($instancia->estaAbierta())
                        <div>
                            <label for="edit_referencia_externa"
                                class="mb-1 text-[10px] font-bold uppercase text-black dark:text-white flex items-center gap-1.5">
                                <i class="fas fa-hashtag text-primary text-[9px]"></i>
                                Código de Referencia
                                <span class="font-normal normal-case text-bodydark2">(opcional)</span>
                            </label>
                            <input type="text" id="edit_referencia_externa" name="referencia_externa" maxlength="50"
                                value="{{ old('referencia_externa', $instancia->referencia_externa) }}"
                                placeholder="Ej: OT-2026-042" style="text-transform: uppercase;"
                                class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 font-mono text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                            <p class="mt-1 text-[10px] text-bodydark2">
                                Solo letras, números, guiones y barras. Sin espacios.
                            </p>
                        </div>
                    @else
                        <div class="rounded-sm border-l-4 border-meta-6 bg-meta-6/10 p-3 text-xs text-meta-6">
                            <i class="fas fa-info-circle"></i>
                            Este flujo está {{ $instancia->estado }}. El código de referencia queda fijado y no se puede modificar.
                        </div>
                    @endif

                    <div>
                        <label for="edit_titulo" class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">
                            Título del Trámite <span class="text-meta-1">*</span>
                        </label>
                        <input type="text" id="edit_titulo" name="titulo" required maxlength="200"
                            value="{{ old('titulo', $instancia->titulo) }}"
                            class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                    </div>

                    <div>
                        <label for="edit_descripcion" class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">
                            Descripción
                        </label>
                        <textarea id="edit_descripcion" name="descripcion" rows="5" maxlength="2000"
                            class="w-full rounded border border-stroke bg-transparent py-2 px-3 text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">{{ old('descripcion', $instancia->descripcion) }}</textarea>
                    </div>

                    @if ($instancia->estaAbierta())
                        <div class="rounded-sm border-l-4 border-primary bg-primary/5 p-3 text-xs text-primary">
                            <i class="fas fa-bell"></i>
                            Los actores con decisión pendiente serán notificados del cambio.
                        </div>
                    @endif

                    <div class="flex justify-end gap-2 pt-4 border-t border-stroke dark:border-strokedark">
                        <button type="button" @click="open = false"
                            class="inline-flex items-center gap-1.5 rounded border border-stroke py-1.5 px-4 text-xs font-bold uppercase tracking-wider text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="inline-flex items-center gap-1.5 rounded bg-primary py-1.5 px-4 text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition cursor-pointer">
                            <i class="fas fa-save text-[10px]"></i> Guardar cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</x-app-layout>