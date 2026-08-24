<x-app-layout>
    <x-slot name="title">Flujo {{ $instancia->codigo }}</x-slot>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
        <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
    @endpush

    {{-- Cabecera --}}
    <div class="db-header mb-6 flex items-center justify-between flex-wrap gap-3">
        <div>
            <div class="flex items-center gap-3 flex-wrap">
                <span
                    class="text-[11px] font-mono text-gray-500 uppercase tracking-wider">{{ $instancia->codigo }}</span>
                @include('flujos.instancias.partials._badge_estado', ['estado' => $instancia->estado])
            </div>
            <h2 class="text-2xl font-black text-white uppercase tracking-tighter mt-1">{{ $instancia->titulo }}</h2>
            <p class="text-xs text-gray-400 font-medium">
                Plantilla: {{ $instancia->template->nombre }} · Revisión {{ $instancia->revision }} · Iniciado
                {{ $instancia->iniciado_en?->format('d/m/Y H:i') }}
            </p>
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

    @if (session('exito'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-green-500/10 border border-green-500/30 text-green-300 text-sm">
            <i class="fas fa-check-circle mr-2"></i> {{ session('exito') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="mb-4 px-4 py-3 rounded-lg bg-red-500/10 border border-red-500/30 text-red-300 text-sm">
            <ul class="list-disc list-inside space-y-1 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Columna izquierda: contenido --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Info general --}}
            <div class="db-card">
                <div class="form-card-body" style="padding: 1.25rem;">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3">
                        <i class="fas fa-info-circle mr-1"></i> Información general
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div>
                            <p class="text-gray-500 uppercase tracking-wider text-[10px]">Tipo de documento</p>
                            <p class="text-white font-semibold mt-0.5">
                                @if ($instancia->documentType)
                                    <i
                                        class="fas {{ $instancia->documentType->icono ?: 'fa-file-alt' }} text-gray-500 mr-1"></i>
                                    {{ $instancia->documentType->nombre }}
                                @else
                                    <span class="text-gray-500">—</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-500 uppercase tracking-wider text-[10px]">Iniciador</p>
                            <p class="text-white font-semibold mt-0.5">{{ $instancia->iniciador->name }}</p>
                            <p class="text-gray-500 text-[10px]">{{ $instancia->iniciador->email }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 uppercase tracking-wider text-[10px]">Etapa actual</p>
                            <p class="text-white font-semibold mt-0.5">{{ $instancia->etapaActual->nombre ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 uppercase tracking-wider text-[10px]">Cerrado</p>
                            <p class="text-white font-semibold mt-0.5">
                                {{ $instancia->cerrado_en?->format('d/m/Y H:i') ?? '—' }}
                            </p>
                        </div>
                    </div>

                    @if ($instancia->descripcion)
                        <div class="mt-4 pt-4 border-t border-gray-800">
                            <p class="text-gray-500 uppercase tracking-wider text-[10px] mb-1">Descripción</p>
                            <p class="text-sm text-gray-300 whitespace-pre-line">{{ $instancia->descripcion }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Timeline con adjuntos por etapa --}}
            <div class="db-card">
                <div class="form-card-body" style="padding: 1.25rem;">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">
                        <i class="fas fa-stream mr-1"></i> Línea de tiempo
                    </h3>

                    <div class="space-y-3">
                        @foreach ($instancia->steps as $step)
                            @php
                                $cfgStep = match ($step->estado) {
                                    'aprobado' => [
                                        'color' => 'text-green-400 bg-green-500/10 border-green-500/30',
                                        'icono' => 'fa-check',
                                    ],
                                    'rechazado' => [
                                        'color' => 'text-red-400 bg-red-500/10 border-red-500/30',
                                        'icono' => 'fa-times',
                                    ],
                                    'devuelto' => [
                                        'color' => 'text-yellow-400 bg-yellow-500/10 border-yellow-500/30',
                                        'icono' => 'fa-undo',
                                    ],
                                    'omitido' => [
                                        'color' => 'text-gray-500 bg-gray-500/10 border-gray-500/30',
                                        'icono' => 'fa-forward',
                                    ],
                                    'en_curso' => [
                                        'color' => 'text-[#007acc] bg-[#007acc]/10 border-[#007acc]/40',
                                        'icono' => 'fa-spinner',
                                    ],
                                    default => [
                                        'color' => 'text-gray-400 bg-gray-500/10 border-gray-500/30',
                                        'icono' => 'fa-clock',
                                    ],
                                };
                                $adjuntosStep = $step->attachments;
                                $puedeSubirEnStep =
                                    $stepActual && $stepActual->id === $step->id && $puede['adjuntar_step'];
                            @endphp

                            <div class="rounded-lg border {{ $cfgStep['color'] }} p-3">

                                {{-- Cabecera del step --}}
                                <div class="flex items-center gap-3 mb-2">
                                    <span
                                        class="w-8 h-8 rounded-full flex items-center justify-center {{ $cfgStep['color'] }} border">
                                        <i class="fas {{ $cfgStep['icono'] }} text-xs"></i>
                                    </span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-white">
                                            {{ $step->stage?->nombre ?? '(etapa eliminada)' }}
                                        </p>
                                        <p class="text-[10px] text-gray-500 uppercase tracking-wider">
                                            Rev. {{ $step->revision }} ·
                                            {{ ucfirst(str_replace('_', ' ', $step->estado)) }} ·
                                            {{ $step->inicio_en?->format('d/m H:i') }}
                                            @if ($step->cierre_en)
                                                → {{ $step->cierre_en->format('d/m H:i') }}
                                            @endif
                                        </p>
                                    </div>
                                    @if ($adjuntosStep->isNotEmpty())
                                        <span class="text-[10px] text-gray-400 flex items-center gap-1">
                                            <i class="fas fa-paperclip"></i> {{ $adjuntosStep->count() }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Asignaciones --}}
                                <div class="space-y-2 mt-2 pl-11">
                                    @foreach ($step->assignments as $assignment)
                                        @php
                                            $cfgAsig = match ($assignment->estado) {
                                                'aprobado' => [
                                                    'color' => 'text-green-400',
                                                    'icono' => 'fa-check-circle',
                                                ],
                                                'rechazado' => [
                                                    'color' => 'text-red-400',
                                                    'icono' => 'fa-times-circle',
                                                ],
                                                'devuelto' => ['color' => 'text-yellow-400', 'icono' => 'fa-undo'],
                                                default => ['color' => 'text-gray-500', 'icono' => 'fa-clock'],
                                            };
                                        @endphp
                                        <div class="rounded bg-[#1a1a1b] border border-gray-800 p-2 text-xs">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <i class="fas {{ $cfgAsig['icono'] }} {{ $cfgAsig['color'] }}"></i>
                                                <span class="text-white font-semibold">
                                                    {{ $assignment->user?->name ?? ($assignment->label_resuelto ?? 'Pendiente de tomar') }}
                                                </span>
                                                <span
                                                    class="{{ $cfgAsig['color'] }} text-[10px] uppercase tracking-wider">
                                                    {{ $assignment->estado }}
                                                </span>
                                                @if ($assignment->decidido_en)
                                                    <span class="text-[10px] text-gray-500 ml-auto">
                                                        {{ $assignment->decidido_en->format('d/m H:i') }}
                                                    </span>
                                                @endif
                                            </div>
                                            @if ($assignment->comentario)
                                                <p class="mt-1 text-gray-400 italic">"{{ $assignment->comentario }}"
                                                </p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Adjuntos de esta etapa --}}
                                @if ($adjuntosStep->isNotEmpty() || $puedeSubirEnStep)
                                    <div class="mt-3 pl-11">
                                        <p class="text-[10px] text-gray-500 uppercase tracking-widest font-black mb-2">
                                            <i class="fas fa-paperclip mr-1"></i> Documentos de esta etapa
                                        </p>

                                        @if ($adjuntosStep->isNotEmpty())
                                            <div class="space-y-1.5">
                                                @foreach ($adjuntosStep as $adj)
                                                    <div
                                                        class="flex items-center gap-2 p-2 rounded bg-[#1a1a1b] border border-gray-800 text-xs">
                                                        <i
                                                            class="fas {{ \App\Support\Flujos::iconoAdjunto($adj->mime_type) }} text-[#007acc] flex-shrink-0"></i>
                                                        <div class="flex-1 min-w-0">
                                                            <a href="{{ route('flujos.instancias.adjuntos.descargar', [$instancia, $adj]) }}"
                                                                class="text-white font-semibold hover:text-[#007acc] truncate block"
                                                                title="{{ $adj->nombre_original }}">
                                                                {{ $adj->nombre_original }}
                                                            </a>
                                                            <p class="text-[10px] text-gray-500">
                                                                Rev. {{ $adj->revision }} ·
                                                                {{ number_format($adj->tamano_bytes / 1024, 1) }} KB ·
                                                                {{ $adj->uploader?->name ?? 'Usuario eliminado' }} ·
                                                                {{ $adj->created_at?->diffForHumans() }}
                                                            </p>
                                                        </div>
                                                        @can('eliminarAdjunto', $adj)
                                                            <form method="POST"
                                                                action="{{ route('flujos.instancias.adjuntos.eliminar', [$instancia, $adj]) }}"
                                                                onsubmit="return confirm('¿Eliminar {{ $adj->nombre_original }}?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="text-gray-500 hover:text-red-400 transition-colors"
                                                                    title="Eliminar">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </form>
                                                        @endcan
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        {{-- Upload inline solo en la etapa actual --}}
                                        @if ($puedeSubirEnStep)
                                            <form method="POST"
                                                action="{{ route('flujos.instancias.adjuntos.subir', $instancia) }}"
                                                enctype="multipart/form-data" class="flex gap-2 mt-2">
                                                @csrf
                                                <input type="hidden" name="workflow_instance_step_id"
                                                    value="{{ $stepActual->id }}">
                                                <input type="file" name="archivo" required
                                                    class="form-input text-xs flex-1"
                                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.txt,.jpg,.jpeg,.png,.zip">
                                                <button type="submit" class="db-btn solid text-xs"
                                                    title="Subir a esta etapa">
                                                    <i class="fas fa-upload"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endif

                            </div>
                        @endforeach

                        @if ($instancia->steps->isEmpty())
                            <div class="text-center py-6 text-xs text-gray-500 italic">
                                Sin actividad registrada aún.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Columna derecha: acciones --}}
        <div class="space-y-4">

            {{-- Panel de acciones para mis asignaciones pendientes --}}
            @if ($puede['actuar'])
                <div class="db-card">
                    <div class="form-card-body" style="padding: 1.25rem;">
                        @include('flujos.instancias.partials._documentos_expediente', [
                            'instancia' => $instancia,
                            'puede' => $puede,
                        ])
                        <h3 class="text-xs font-black text-[#007acc] uppercase tracking-widest mb-3">
                            <i class="fas fa-hand-paper mr-1"></i> Requiere tu decisión
                        </h3>

                        @foreach ($misAsignacionesPendientes as $asignacion)
                            <form method="POST"
                                action="{{ route('flujos.instancias.asignaciones.actuar', [$instancia, $asignacion]) }}"
                                class="mb-3 last:mb-0" x-data="{ accion: 'aprobar' }">
                                @csrf

                                <p class="text-[10px] text-gray-500 uppercase tracking-wider mb-2">
                                    {{ $asignacion->label_resuelto }}
                                    @if ($asignacion->stageActor?->etiqueta)
                                        · {{ $asignacion->stageActor->etiqueta }}
                                    @endif
                                </p>

                                <div class="grid grid-cols-3 gap-1 mb-2">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="accion" value="aprobar" x-model="accion"
                                            class="peer sr-only" checked>
                                        <div
                                            class="text-center py-2 rounded border-2 border-gray-800 text-xs font-bold uppercase tracking-wider
                                                    peer-checked:border-green-500 peer-checked:text-green-400 peer-checked:bg-green-500/10
                                                    text-gray-500 hover:text-gray-300 transition-colors">
                                            <i class="fas fa-check"></i> Aprobar
                                        </div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="accion" value="rechazar" x-model="accion"
                                            class="peer sr-only">
                                        <div
                                            class="text-center py-2 rounded border-2 border-gray-800 text-xs font-bold uppercase tracking-wider
                                                    peer-checked:border-red-500 peer-checked:text-red-400 peer-checked:bg-red-500/10
                                                    text-gray-500 hover:text-gray-300 transition-colors">
                                            <i class="fas fa-times"></i> Rechazar
                                        </div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="accion" value="devolver" x-model="accion"
                                            class="peer sr-only">
                                        <div
                                            class="text-center py-2 rounded border-2 border-gray-800 text-xs font-bold uppercase tracking-wider
                                                    peer-checked:border-yellow-500 peer-checked:text-yellow-400 peer-checked:bg-yellow-500/10
                                                    text-gray-500 hover:text-gray-300 transition-colors">
                                            <i class="fas fa-undo"></i> Devolver
                                        </div>
                                    </label>
                                </div>

                                <textarea name="comentario" rows="2" class="form-input text-xs"
                                    :placeholder="accion === 'aprobar' ? 'Comentario (opcional)...' : 'Motivo (obligatorio)...'"
                                    :required="accion !== 'aprobar'" maxlength="2000"></textarea>

                                <button type="submit" class="db-btn solid w-full mt-2">
                                    <i class="fas fa-paper-plane mr-1"></i> Registrar decisión
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Panel de corrección para el iniciador --}}
            @if ($puede['corregir'])
                <div class="db-card">
                    <div class="form-card-body" style="padding: 1.25rem;">
                        <h3 class="text-xs font-black text-yellow-400 uppercase tracking-widest mb-2">
                            <i class="fas fa-exclamation-triangle mr-1"></i> Requiere corrección
                        </h3>
                        <p class="text-xs text-gray-400 mb-3">
                            Revisa los comentarios de rechazo, corregí lo necesario y reenviá el flujo.
                            Se registrará como <strong>Revisión {{ $instancia->revision + 1 }}</strong>.
                        </p>
                        <form method="POST" action="{{ route('flujos.instancias.corregir', $instancia) }}">
                            @csrf
                            <button type="submit" class="db-btn solid w-full"
                                onclick="return confirm('¿Reenviar el flujo tras corregir?')">
                                <i class="fas fa-redo mr-1"></i> Reenviar corregido
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Otras acciones --}}
            @if ($puede['desistir'] || $puede['cancelar'] || $puede['reabrir'])
                <div class="db-card">
                    <div class="form-card-body" style="padding: 1.25rem;">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3">
                            <i class="fas fa-tools mr-1"></i> Administración
                        </h3>

                        @if ($puede['desistir'])
                            <form method="POST" action="{{ route('flujos.instancias.desistir', $instancia) }}"
                                class="mb-2">
                                @csrf
                                <input type="text" name="motivo" class="form-input text-xs mb-2"
                                    placeholder="Motivo (opcional)" maxlength="1000">
                                <button type="submit" class="db-btn danger-outline w-full text-xs"
                                    onclick="return confirm('¿Desistir del flujo? No se podrá continuar sin reabrirlo.')">
                                    <i class="fas fa-hand-paper mr-1"></i> Desistir del flujo
                                </button>
                            </form>
                        @endif

                        @if ($puede['cancelar'])
                            <form method="POST" action="{{ route('flujos.instancias.cancelar', $instancia) }}"
                                class="mb-2">
                                @csrf
                                <input type="text" name="motivo" class="form-input text-xs mb-2"
                                    placeholder="Motivo (obligatorio)" maxlength="1000" required>
                                <button type="submit" class="db-btn danger-outline w-full text-xs"
                                    onclick="return confirm('¿Cancelar este flujo?')">
                                    <i class="fas fa-ban mr-1"></i> Cancelar flujo (admin)
                                </button>
                            </form>
                        @endif

                        @if ($puede['reabrir'])
                            <form method="POST" action="{{ route('flujos.instancias.reabrir', $instancia) }}">
                                @csrf
                                <button type="submit" class="db-btn outline w-full text-xs"
                                    onclick="return confirm('¿Reabrir el flujo cancelado?')">
                                    <i class="fas fa-play mr-1"></i> Reabrir flujo
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
