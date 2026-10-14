{{-- resources/views/flujos/instancias/show.blade.php --}}
<x-app-layout>
    <x-slot name="title">Flujo {{ $instancia->codigo }}</x-slot>

    @php
        $puedeEditarCabecera = auth()->user()->can('editarCabecera', $instancia);
    @endphp

    {{-- ── Cabecera Principal + Botones de Acción Unificados ────────────────── --}}
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        
        {{-- Título y Metadatos --}}
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap mb-1">
                <span class="text-xs font-mono font-bold text-primary bg-primary/10 px-1.5 py-0.5 rounded border border-primary/20">
                    {{ $instancia->codigo }}
                </span>
                @include('flujos.instancias.partials._badge_estado', ['estado' => $instancia->estado])
                @if ($instancia->referencia_externa)
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-gray-2 dark:bg-meta-4 text-bodydark2 border border-stroke dark:border-strokedark inline-flex items-center gap-1">
                        <i class="fas fa-hashtag text-[9px]"></i>{{ $instancia->referencia_externa }}
                    </span>
                @endif
            </div>

            <h2 class="text-title-md font-bold text-black dark:text-white truncate" title="{{ $instancia->titulo }}">
                {{ $instancia->titulo }}
            </h2>

            <p class="text-[11px] text-bodydark2 mt-0.5">
                Plantilla: <strong class="text-black dark:text-white">{{ $instancia->template->nombre }}</strong> ·
                Revisión {{ $instancia->revision }} · Iniciado {{ $instancia->iniciado_en?->format('d/m/Y H:i') }}
            </p>

            {{-- Historial Desplegable de Cambios --}}
            @if ($instancia->history->isNotEmpty() && $puedeEditarCabecera)
                <div class="mt-1.5" x-data="{ open: false }">
                    <button type="button" @click="open = !open"
                        class="text-[10px] font-bold text-bodydark2 hover:text-primary flex items-center gap-1 transition">
                        <i class="fas text-[8px]" :class="open ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                        Historial de cambios ({{ $instancia->history->count() }})
                    </button>
                    <div x-show="open" x-cloak class="mt-1.5 space-y-1 pl-2.5 border-l-2 border-stroke dark:border-strokedark">
                        @foreach ($instancia->history as $cambio)
                            <div class="text-[10px] text-bodydark2 py-0.5">
                                <span class="font-mono text-primary font-bold">{{ $cambio->label_campo }}</span>
                                modificado por
                                <span class="text-black dark:text-white font-medium">{{ $cambio->editor?->name ?? 'Usuario eliminado' }}</span>
                                · {{ $cambio->editado_en->format('d/m/Y H:i') }}
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Barra de Botones Limpia (Sin Duplicados ni Clases Legacy) --}}
        <div class="flex items-center gap-1.5 shrink-0 flex-wrap sm:flex-nowrap">
            @if ($puedeEditarCabecera)
                <button type="button" @click="$dispatch('open-modal', 'editar-cabecera')"
                    class="inline-flex items-center gap-1.5 rounded border border-stroke py-1.5 px-3 text-xs font-bold text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition shadow-sm"
                    title="Editar título, descripción y referencia">
                    <i class="fas fa-pen text-[10px] text-primary"></i> Editar datos
                </button>
            @endif

            @can('auditoria.ver')
                <a href="{{ route('auditoria.show', $instancia) }}" 
                   class="inline-flex items-center gap-1.5 rounded border border-stroke py-1.5 px-3 text-xs font-bold text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition shadow-sm"
                   title="Ver rastro inmutable de auditoría">
                    <i class="fas fa-shield-alt text-[10px] text-primary"></i> Auditoría
                </a>
            @endcan

            <a href="{{ route('flujos.instancias.index') }}"
                class="inline-flex items-center gap-1.5 rounded border border-stroke py-1.5 px-3.5 text-xs font-bold text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition shadow-sm">
                <i class="fas fa-arrow-left text-[10px]"></i> Volver
            </a>
        </div>
    </div>

    {{-- Alerts Flash --}}
    @if (session('exito'))
        <div class="mb-4 flex w-full border-l-4 border-meta-3 bg-meta-3/10 px-4 py-2.5 shadow-sm dark:bg-meta-3/20 rounded-sm">
            <div class="mr-2.5 flex h-6 w-6 items-center justify-center rounded-full bg-meta-3 text-white shrink-0">
                <i class="fas fa-check text-xs"></i>
            </div>
            <p class="text-xs text-meta-3 font-medium self-center">{{ session('exito') }}</p>
        </div>
    @endif

    @if (session('info'))
        <div class="mb-4 flex w-full border-l-4 border-primary bg-primary/10 px-4 py-2.5 shadow-sm rounded-sm">
            <div class="mr-2.5 flex h-6 w-6 items-center justify-center rounded-full bg-primary text-white shrink-0">
                <i class="fas fa-info text-xs"></i>
            </div>
            <p class="text-xs text-primary font-medium self-center">{{ session('info') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 flex w-full border-l-4 border-meta-1 bg-meta-1/10 px-4 py-2.5 shadow-sm dark:bg-meta-1/20 rounded-sm">
            <div class="mr-2.5 flex h-6 w-6 items-center justify-center rounded-full bg-meta-1 text-white shrink-0">
                <i class="fas fa-exclamation-triangle text-xs"></i>
            </div>
            <div class="w-full">
                <h5 class="font-bold text-xs text-meta-1 uppercase">Revisa los siguientes campos:</h5>
                <ul class="list-disc list-inside space-y-0.5 text-xs text-meta-1 mt-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- ── Grid Principal de Detalle (9 cols Izq / 3 cols Der) ────────────────── --}}
    <div class="grid grid-cols-12 gap-4 md:gap-5 2xl:gap-6">

        {{-- COLUMNA IZQUIERDA: Info General e Historial (9 cols -> 75% Ancho) --}}
        <div class="col-span-12 lg:col-span-9 space-y-4">

            {{-- Info General Card Ultracompacta (Fila Única de 4 Columnas) --}}
            <div class="rounded-sm border border-stroke bg-white p-3.5 shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    
                    <div>
                        <span class="text-[9px] font-bold text-bodydark2 uppercase block">Tipo de Documento</span>
                        <div class="text-black dark:text-white font-bold block truncate mt-0.5">
                            @if ($instancia->documentType)
                                <i class="fas {{ $instancia->documentType->icono ?: 'fa-file-alt' }} text-primary mr-1 text-[10px]"></i>
                                {{ $instancia->documentType->nombre }}
                            @else
                                <span class="text-bodydark2 font-normal">—</span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <span class="text-[9px] font-bold text-bodydark2 uppercase block">Iniciador</span>
                        <div class="text-black dark:text-white font-bold truncate mt-0.5" title="{{ $instancia->iniciador->email }}">
                            {{ $instancia->iniciador->name }}
                        </div>
                    </div>

                    <div>
                        <span class="text-[9px] font-bold text-bodydark2 uppercase block">Etapa Actual</span>
                        <div class="text-black dark:text-white font-bold truncate mt-0.5">
                            {{ $instancia->etapaActual->nombre ?? '—' }}
                        </div>
                    </div>

                    <div>
                        <span class="text-[9px] font-bold text-bodydark2 uppercase block">Fecha de Cierre</span>
                        <div class="text-black dark:text-white font-bold font-mono truncate mt-0.5">
                            {{ $instancia->cerrado_en?->format('d/m/Y H:i') ?? '—' }}
                        </div>
                    </div>
                </div>

                @if ($instancia->descripcion)
                    <div class="mt-2.5 pt-2 border-t border-dashed border-stroke dark:border-strokedark">
                        <span class="text-[9px] font-bold text-bodydark2 uppercase block mb-0.5">
                            Descripción del Trámite
                        </span>
                        <p class="text-xs text-black dark:text-white leading-relaxed whitespace-pre-line">
                            {{ $instancia->descripcion }}
                        </p>
                    </div>
                @endif
            </div>

            {{-- Timeline & Etapas --}}
            <div class="rounded-sm border border-stroke bg-white p-4 shadow-default dark:border-strokedark dark:bg-boxdark">
                <h3 class="font-bold text-black dark:text-white text-xs uppercase tracking-wider border-b border-stroke pb-2.5 mb-3.5 dark:border-strokedark flex items-center gap-1.5">
                    <i class="fas fa-stream text-primary"></i> Línea de Tiempo
                </h3>

                <div class="space-y-3.5">
                    @foreach ($instancia->steps as $step)
                        @php
                            $cfgStep = match ($step->estado) {
                                'aprobado' => ['icon_bg' => 'bg-meta-3 text-white', 'icono' => 'fa-check'],
                                'rechazado' => ['icon_bg' => 'bg-meta-1 text-white', 'icono' => 'fa-times'],
                                'devuelto' => ['icon_bg' => 'bg-meta-6 text-white', 'icono' => 'fa-undo'],
                                'omitido' => ['icon_bg' => 'bg-bodydark2 text-white', 'icono' => 'fa-forward'],
                                'en_curso' => ['icon_bg' => 'bg-primary text-white', 'icono' => 'fa-spinner fa-spin'],
                                default => ['icon_bg' => 'bg-bodydark2 text-white', 'icono' => 'fa-clock'],
                            };

                            $adjuntosStep = $step->attachments;
                            $comentariosExtra = $step->comentariosAdicionales;
                            $stepEnCurso = $step->estado === \App\Models\WorkflowInstanceStep::ESTADO_EN_CURSO;

                            $puedeEtiquetar = auth()->user()->can('etiquetarStep', [$instancia, $step]);
                            $puedeAdjuntar = auth()->user()->can('adjuntarAStep', [$instancia, $step]);
                            $puedeComentar = auth()->user()->can('comentarStep', [$instancia, $step]);
                        @endphp

                        <div class="rounded border border-stroke p-3 dark:border-strokedark dark:bg-meta-4/20">

                            {{-- Cabecera del Step --}}
                            <div class="flex items-center gap-2.5 mb-1.5 flex-wrap">
                                <div class="flex h-6 w-6 items-center justify-center rounded-full font-bold text-[10px] shrink-0 {{ $cfgStep['icon_bg'] }}">
                                    <i class="fas {{ $cfgStep['icono'] }}"></i>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="text-xs font-bold text-black dark:text-white">
                                            {{ $step->stage?->nombre ?? '(Etapa Eliminada)' }}
                                        </h4>

                                        {{-- Código de referencia de la etapa --}}
                                        <div x-data="{ editing: false, codigo: @js($step->codigo_referencia ?? '') }" class="inline-flex items-center gap-1">
                                            <template x-if="!editing">
                                                <span class="inline-flex items-center gap-1">
                                                    @if ($step->codigo_referencia)
                                                        <span class="inline-flex items-center gap-1 rounded bg-primary/10 border border-primary/20 px-1.5 py-0.2 text-[9px] font-mono font-bold text-primary">
                                                            <i class="fas fa-tag text-[8px]"></i>{{ $step->codigo_referencia }}
                                                        </span>
                                                    @endif
                                                    @if ($puedeEtiquetar)
                                                        <button type="button"
                                                            @click="editing = true; $nextTick(() => $refs.inp.focus())"
                                                            class="text-[9px] text-bodydark2 hover:text-primary transition"
                                                            title="Editar código de referencia">
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
                                                        class="rounded border border-stroke bg-transparent py-0.5 px-1.5 text-[10px] font-mono uppercase outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white max-w-[120px]"
                                                        placeholder="REF-ETAPA" maxlength="50"
                                                        @keydown.escape.prevent="editing = false">
                                                    <button type="submit" class="text-meta-3 hover:text-opacity-80 px-0.5"><i class="fas fa-check text-xs"></i></button>
                                                    <button type="button" @click="editing = false" class="text-bodydark2 hover:text-meta-1 px-0.5"><i class="fas fa-times text-xs"></i></button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>

                                    <span class="text-[10px] text-bodydark2 block mt-0.5">
                                        Rev. {{ $step->revision }} · {{ ucfirst(str_replace('_', ' ', $step->estado)) }} · {{ $step->inicio_en?->format('d/m H:i') }}
                                        @if ($step->cierre_en) → {{ $step->cierre_en->format('d/m H:i') }} @endif
                                    </span>
                                </div>
                            </div>

                            {{-- Asignaciones / Actores del Step --}}
                            <div class="space-y-1 mt-2 sm:ml-8">
                                @foreach ($step->assignments as $assignment)
                                    @php
                                        $cfgAsig = match ($assignment->estado) {
                                            'aprobado'  => ['color' => 'text-meta-3',   'icono' => 'fa-check-circle'],
                                            'rechazado' => ['color' => 'text-meta-1',   'icono' => 'fa-times-circle'],
                                            'devuelto'  => ['color' => 'text-meta-6',   'icono' => 'fa-undo'],
                                            default     => ['color' => 'text-bodydark2','icono' => 'fa-clock'],
                                        };
                                    @endphp
                                    <div class="rounded border border-stroke bg-white p-2 dark:border-strokedark dark:bg-boxdark text-xs">
                                        <div class="flex items-center justify-between gap-2 flex-wrap">
                                            <div class="flex items-center gap-1.5">
                                                <i class="fas {{ $cfgAsig['icono'] }} {{ $cfgAsig['color'] }}"></i>
                                                <span class="font-bold text-black dark:text-white">
                                                    {{ $assignment->user?->name ?? ($assignment->label_resuelto ?? 'Pendiente') }}
                                                </span>
                                                <span class="text-[9px] uppercase font-bold {{ $cfgAsig['color'] }}">
                                                    ({{ $assignment->estado }})
                                                </span>
                                            </div>
                                            @if ($assignment->decidido_en)
                                                <span class="text-[9px] font-mono text-bodydark2">{{ $assignment->decidido_en->format('d/m H:i') }}</span>
                                            @endif
                                        </div>
                                        @if ($assignment->comentario)
                                            <p class="mt-0.5 text-[10px] text-bodydark2 italic">"{{ $assignment->comentario }}"</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            {{-- Documentos y Comentarios adicionales --}}
                            @if ($adjuntosStep->isNotEmpty() || $puedeAdjuntar)
                                <div class="mt-2.5 sm:ml-8 pt-2 border-t border-dashed border-stroke dark:border-strokedark">
                                    <span class="text-[9px] font-bold text-bodydark2 uppercase tracking-wider block mb-1.5">
                                        <i class="fas fa-paperclip text-primary mr-1"></i> Documentos de esta etapa
                                    </span>

                                    @if ($adjuntosStep->isNotEmpty())
                                        <div class="space-y-1 mb-2">
                                            @foreach ($adjuntosStep as $adj)
                                                <div class="flex items-center justify-between gap-2 p-1.5 rounded border border-stroke bg-white dark:border-strokedark dark:bg-boxdark text-xs">
                                                    <div class="flex items-center gap-1.5 min-w-0">
                                                        <i class="fas {{ \App\Support\Flujos::iconoAdjunto($adj->mime_type) }} text-primary text-xs shrink-0"></i>
                                                        <a href="{{ route('flujos.instancias.adjuntos.descargar', [$instancia, $adj]) }}"
                                                           class="font-bold text-black dark:text-white hover:text-primary transition truncate block text-[11px]" title="{{ $adj->nombre_original }}">
                                                            {{ $adj->nombre_original }}
                                                        </a>
                                                        <span class="text-[9px] text-bodydark2 shrink-0">({{ number_format($adj->tamano_bytes / 1024, 1) }} KB)</span>
                                                    </div>

                                                    @can('eliminarAdjunto', $adj)
                                                        <form method="POST" action="{{ route('flujos.instancias.adjuntos.eliminar', [$instancia, $adj]) }}"
                                                              onsubmit="return confirm('¿Eliminar {{ $adj->nombre_original }}?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-bodydark2 hover:text-meta-1 transition p-0.5" title="Eliminar archivo">
                                                                <i class="fas fa-times text-xs"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    @if ($puedeAdjuntar)
                                        <form method="POST" action="{{ route('flujos.instancias.adjuntos.subir', $instancia) }}"
                                              enctype="multipart/form-data" class="flex items-center gap-2 mt-1.5">
                                            @csrf
                                            <input type="hidden" name="workflow_instance_step_id" value="{{ $step->id }}">
                                            <input type="file" name="archivo" required
                                                accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.txt,.jpg,.jpeg,.png,.zip"
                                                class="w-full text-xs text-bodydark2 file:mr-2 file:py-1 file:px-2.5 file:rounded file:border-0 file:text-[10px] file:font-bold file:uppercase file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer">
                                            <button type="submit" class="inline-flex items-center gap-1 rounded bg-primary py-1 px-2.5 text-[10px] font-bold uppercase text-white hover:bg-opacity-90 transition shrink-0 cursor-pointer">
                                                <i class="fas fa-upload"></i> Subir
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endif

                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- COLUMNA DERECHA: Acciones, Decisiones y Expediente (3 cols -> 25% Ancho) --}}
        <div class="col-span-12 lg:col-span-3 space-y-4">

            {{-- Componente Parcial del Expediente --}}
            @include('flujos.instancias.partials._documentos_expediente')

            {{-- Requiere Tu Decisión --}}
            @if ($puede['actuar'])
                <div class="rounded-sm border border-stroke bg-white p-4 shadow-default dark:border-strokedark dark:bg-boxdark">
                    <h3 class="font-bold text-primary uppercase tracking-wider text-[11px] border-b border-stroke pb-2 mb-3 dark:border-strokedark flex items-center gap-1.5">
                        <i class="fas fa-hand-paper"></i> Requiere Tu Decisión
                    </h3>

                    @foreach ($misAsignacionesPendientes as $asignacion)
                        <form method="POST" action="{{ route('flujos.instancias.asignaciones.actuar', [$instancia, $asignacion]) }}"
                            class="space-y-2.5 mb-3 last:mb-0" x-data="{ accion: 'aprobar' }">
                            @csrf

                            <span class="text-[9px] font-bold text-bodydark2 uppercase tracking-wider block">
                                {{ $asignacion->label_resuelto }}
                            </span>

                            <div class="grid grid-cols-3 gap-1">
                                <label class="cursor-pointer select-none">
                                    <input type="radio" name="accion" value="aprobar" x-model="accion" class="peer sr-only" checked>
                                    <div class="text-center py-1 px-0.5 rounded border border-stroke text-[9px] font-bold uppercase transition peer-checked:border-meta-3 peer-checked:bg-meta-3/10 peer-checked:text-meta-3 text-bodydark2 dark:border-strokedark">
                                        <i class="fas fa-check"></i> Aprobar
                                    </div>
                                </label>
                                <label class="cursor-pointer select-none">
                                    <input type="radio" name="accion" value="rechazar" x-model="accion" class="peer sr-only">
                                    <div class="text-center py-1 px-0.5 rounded border border-stroke text-[9px] font-bold uppercase transition peer-checked:border-meta-1 peer-checked:bg-meta-1/10 peer-checked:text-meta-1 text-bodydark2 dark:border-strokedark">
                                        <i class="fas fa-times"></i> Rechazar
                                    </div>
                                </label>
                                <label class="cursor-pointer select-none">
                                    <input type="radio" name="accion" value="devolver" x-model="accion" class="peer sr-only">
                                    <div class="text-center py-1 px-0.5 rounded border border-stroke text-[9px] font-bold uppercase transition peer-checked:border-meta-6 peer-checked:bg-meta-6/10 peer-checked:text-meta-6 text-bodydark2 dark:border-strokedark">
                                        <i class="fas fa-undo"></i> Devolver
                                    </div>
                                </label>
                            </div>

                            <textarea name="comentario" rows="2" maxlength="2000"
                                :placeholder="accion === 'aprobar' ? 'Comentario opcional...' : 'Motivo obligatorio...'"
                                :required="accion !== 'aprobar'"
                                class="w-full rounded border border-stroke bg-transparent py-1 px-2 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white"></textarea>

                            <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-1.5 rounded bg-primary py-1.5 px-3 text-center text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm cursor-pointer">
                                <i class="fas fa-paper-plane text-[9px]"></i> Confirmar Decisión
                            </button>
                        </form>
                    @endforeach
                </div>
            @endif

            {{-- Panel de Administración / Acciones secundarias --}}
            @if ($puede['desistir'] || $puede['cancelar'] || $puede['reabrir'])
                <div class="rounded-sm border border-stroke bg-white p-4 shadow-default dark:border-strokedark dark:bg-boxdark space-y-2.5">
                    <h3 class="font-bold text-black dark:text-white uppercase tracking-wider text-[11px] border-b border-stroke pb-1.5 dark:border-strokedark flex items-center gap-1.5">
                        <i class="fas fa-tools text-primary"></i> Opciones de Gestión
                    </h3>

                    @if ($puede['desistir'])
                        <form method="POST" action="{{ route('flujos.instancias.desistir', $instancia) }}" class="space-y-1.5">
                            @csrf
                            <input type="text" name="motivo" maxlength="1000" placeholder="Motivo de desistimiento..."
                                class="w-full rounded border border-stroke bg-transparent py-1 px-2 text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-1 rounded border border-meta-1 py-1 px-2 text-xs font-bold uppercase text-meta-1 hover:bg-meta-1/10 transition"
                                onclick="return confirm('¿Desistir del flujo?')">
                                <i class="fas fa-hand-paper text-[9px]"></i> Desistir Trámite
                            </button>
                        </form>
                    @endif

                    @if ($puede['cancelar'])
                        <form method="POST" action="{{ route('flujos.instancias.cancelar', $instancia) }}" class="space-y-1.5">
                            @csrf
                            <input type="text" name="motivo" required maxlength="1000" placeholder="Motivo obligatorio..."
                                class="w-full rounded border border-stroke bg-transparent py-1 px-2 text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-1 rounded border border-meta-1 py-1 px-2 text-xs font-bold uppercase text-meta-1 hover:bg-meta-1/10 transition"
                                onclick="return confirm('¿Cancelar este flujo?')">
                                <i class="fas fa-ban text-[9px]"></i> Cancelar Flujo
                            </button>
                        </form>
                    @endif
                </div>
            @endif

        </div>

    </div>

    {{-- ── Modal: Editar cabecera ──────────────────────────────────────── --}}
    @if ($puedeEditarCabecera)
        <div x-data="{ open: false }" x-show="open" x-cloak
            @open-modal.window="if ($event.detail === 'editar-cabecera') open = true"
            @keydown.escape.window="open = false"
            class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/60 backdrop-blur-sm py-8">
            <div @click.outside="open = false"
                class="w-full max-w-xl rounded-sm border border-stroke bg-white shadow-2xl dark:border-strokedark dark:bg-boxdark mx-4">

                <div class="flex items-center justify-between px-5 py-3 border-b border-stroke dark:border-strokedark">
                    <h3 class="font-bold text-xs uppercase tracking-wider text-black dark:text-white flex items-center gap-2">
                        <i class="fas fa-pen text-primary"></i> Editar datos del trámite
                    </h3>
                    <button type="button" @click="open = false" class="text-bodydark2 hover:text-black dark:hover:text-white">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>

                <form action="{{ route('flujos.instancias.cabecera', $instancia) }}" method="POST" class="p-5 space-y-3.5">
                    @csrf
                    @method('PATCH')

                    @if ($instancia->estaAbierta())
                        <div>
                            <label for="edit_referencia_externa" class="mb-1 text-[10px] font-bold uppercase text-black dark:text-white block">
                                Código de Referencia <span class="font-normal normal-case text-bodydark2">(opcional)</span>
                            </label>
                            <input type="text" id="edit_referencia_externa" name="referencia_externa" maxlength="50"
                                value="{{ old('referencia_externa', $instancia->referencia_externa) }}"
                                placeholder="Ej: OT-2026-042" style="text-transform: uppercase;"
                                class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 font-mono text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
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
                        <textarea id="edit_descripcion" name="descripcion" rows="4" maxlength="2000"
                            class="w-full rounded border border-stroke bg-transparent py-2 px-3 text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">{{ old('descripcion', $instancia->descripcion) }}</textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-stroke dark:border-strokedark">
                        <button type="button" @click="open = false"
                            class="inline-flex items-center gap-1 rounded border border-stroke py-1.5 px-3 text-xs font-bold uppercase text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="inline-flex items-center gap-1 rounded bg-primary py-1.5 px-3.5 text-xs font-bold uppercase text-white hover:bg-opacity-90 transition cursor-pointer">
                            <i class="fas fa-save text-[10px]"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</x-app-layout>