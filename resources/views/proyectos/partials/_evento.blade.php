{{-- resources/views/proyectos/partials/_evento.blade.php — Una tarjeta de la línea de tiempo --}}
{{-- Recibe: $item (clase, modelo, fecha), $proyecto, $modificables --}}
@php
    $m      = $item['modelo'];
    $moneda = fn ($n) => '$ ' . number_format((float) $n, 0, ',', '.');

    [$color, $icono, $etiqueta] = match ($item['clase']) {
        'etapa'     => [$m->config_tipo['color'], $m->config_tipo['icono'], $m->config_tipo['label']],
        'solicitud' => ['#f97316', 'fas fa-comment-dots', 'Solicitud'],
        default     => ['#64748b', $m->icono, 'Sistema'],
    };

    $puedeModificar = match ($item['clase']) {
        'etapa'     => in_array($m->id, $modificables['etapas'], true),
        'solicitud' => in_array($m->id, $modificables['solicitudes'], true),
        default     => false,
    };
@endphp

{{-- Punto de la línea --}}
<div class="absolute -left-12 top-2 w-9 h-9 rounded-full grid place-items-center text-white text-xs border-[3px] border-white dark:border-boxdark"
     style="background: {{ $color }}">
    <i class="{{ $icono }}"></i>
</div>

<div class="rounded border border-stroke dark:border-strokedark border-l-[3px] px-3 py-2.5 {{ $item['clase'] === 'solicitud' ? 'bg-[#f97316]/[.04]' : 'bg-gray-2/40 dark:bg-meta-4/30' }}"
     style="border-left-color: {{ $color }}">

    {{-- ── Encabezado ── --}}
    <div class="flex items-center gap-2 flex-wrap">
        <span class="text-[9px] font-bold uppercase tracking-wider" style="color: {{ $color }}">{{ $etiqueta }}</span>

        @if ($item['clase'] === 'actividad')
            <span class="text-xs text-black dark:text-white">{{ $m->descripcion }}</span>
        @else
            <span class="text-sm font-semibold text-black dark:text-white">{{ $m->titulo }}</span>
        @endif

        {{-- Estado de la etapa (solo si no está completada) --}}
        @if ($item['clase'] === 'etapa' && $m->estado !== \App\Models\ProyectoEtapa::ESTADO_COMPLETADA)
            <span class="inline-flex rounded-full px-2 py-0.5 text-[9px] font-bold uppercase border {{ $m->estado === 'en_curso' ? 'bg-primary/10 text-primary border-primary/30' : 'bg-gray-2 text-bodydark2 border-stroke dark:bg-meta-4 dark:border-strokedark' }}">
                {{ $m->label_estado }}
            </span>
        @endif

        {{-- Estado de la solicitud: editable desde la tarjeta --}}
        @if ($item['clase'] === 'solicitud')
            @if ($puedeModificar)
                <form method="POST" action="{{ route('proyectos.solicitudes.estado', [$proyecto, $m]) }}" class="inline">
                    @csrf @method('PATCH')
                    <select name="estado" onchange="this.form.submit()"
                            class="rounded-full border px-1.5 py-0 text-[9px] font-bold uppercase cursor-pointer bg-transparent focus:outline-none {{ $m->clase_estado }}">
                        @foreach (\App\Models\ProyectoSolicitud::ESTADOS as $clave => $cfg)
                            <option value="{{ $clave }}" @selected($m->estado === $clave) class="bg-white dark:bg-boxdark text-black dark:text-white normal-case">{{ $cfg['label'] }}</option>
                        @endforeach
                    </select>
                </form>
            @else
                <span class="inline-flex rounded-full px-2 py-0.5 text-[9px] font-bold uppercase border {{ $m->clase_estado }}">{{ $m->label_estado }}</span>
            @endif
        @endif

        <span class="ml-auto flex items-center gap-2">
            <span class="font-mono text-[10px] text-bodydark2">
                {{ \Illuminate\Support\Carbon::parse($item['fecha'])->format('d/m/Y') }}
            </span>

            @if ($puedeModificar)
                @if ($item['clase'] === 'etapa')
                    <button type="button" @click="editarEtapa({{ Js::from([
                                'accion'        => route('proyectos.etapas.update', [$proyecto, $m]),
                                'tipo'          => $m->tipo,
                                'fecha'         => $m->fecha->format('Y-m-d'),
                                'estado'        => $m->estado,
                                'titulo'        => $m->titulo,
                                'descripcion'   => $m->descripcion,
                                'monto'         => $m->monto !== null ? (float) $m->monto : '',
                                'fecha_validez' => $m->fecha_validez?->format('Y-m-d') ?? '',
                                'medio_pago'    => $m->medio_pago ?? 'transferencia',
                                'version'       => $m->version ?? '',
                                'ronda'         => $m->ronda ?? '',
                                'avance'        => $m->avance ?? '',
                                'enlace'        => $m->enlace ?? '',
                            ]) }})"
                            class="text-bodydark2 hover:text-primary" title="Editar">
                        <i class="fas fa-pen text-[10px]"></i>
                    </button>
                    <form method="POST" action="{{ route('proyectos.etapas.destroy', [$proyecto, $m]) }}" class="inline"
                          onsubmit="return confirm('¿Eliminar este registro de {{ $etiqueta }}?{{ $m->solicitudesAtendidas->isNotEmpty() ? ' Las solicitudes que atendía volverán a En proceso.' : '' }}')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-bodydark2 hover:text-meta-1" title="Eliminar"><i class="fas fa-trash text-[10px]"></i></button>
                    </form>
                @elseif ($item['clase'] === 'solicitud')
                    <button type="button" @click="editarSolicitud({{ Js::from([
                                'accion'          => route('proyectos.solicitudes.update', [$proyecto, $m]),
                                'fecha_solicitud' => $m->fecha_solicitud->format('Y-m-d'),
                                'canal'           => $m->canal,
                                'prioridad'       => $m->prioridad,
                                'titulo'          => $m->titulo,
                                'descripcion'     => $m->descripcion,
                            ]) }})"
                            class="text-bodydark2 hover:text-primary" title="Editar">
                        <i class="fas fa-pen text-[10px]"></i>
                    </button>
                    <form method="POST" action="{{ route('proyectos.solicitudes.destroy', [$proyecto, $m]) }}" class="inline"
                          onsubmit="return confirm('¿Eliminar esta solicitud?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-bodydark2 hover:text-meta-1" title="Eliminar"><i class="fas fa-trash text-[10px]"></i></button>
                    </form>
                @endif
            @endif
        </span>
    </div>

    {{-- ── Descripción ── --}}
    @if ($item['clase'] !== 'actividad' && $m->descripcion)
        <p class="text-xs text-bodydark2 mt-1 whitespace-pre-line">{{ $m->descripcion }}</p>
    @endif

    {{-- ── Metadatos ── --}}
    <div class="flex flex-wrap gap-x-3 gap-y-1 mt-1.5 text-[11px] text-bodydark2">
        @if ($item['clase'] === 'solicitud')
            <span><i class="{{ $m->canal_config['icono'] }} mr-0.5"></i> {{ $m->canal_config['label'] }}</span>
            <span class="{{ $m->prioridad_config['clase'] }}"><i class="fas fa-flag mr-0.5"></i> Prioridad {{ mb_strtolower($m->prioridad_config['label']) }}</span>
        @endif

        @if ($item['clase'] === 'etapa')
            @if ($m->monto !== null)
                <span><i class="fas fa-dollar-sign mr-0.5"></i> <b class="text-black dark:text-white font-mono">{{ $moneda($m->monto) }}</b></span>
            @endif
            @if ($m->medio_pago)
                <span><i class="fas fa-building-columns mr-0.5"></i> {{ $m->label_medio_pago }}</span>
            @endif
            @if ($m->fecha_validez)
                <span class="{{ $m->fecha_validez->isPast() ? 'text-meta-1' : '' }}"><i class="fas fa-hourglass-half mr-0.5"></i> Válida hasta {{ $m->fecha_validez->format('d/m/Y') }}</span>
            @endif
            @if ($m->version)
                <span><i class="fas fa-code-branch mr-0.5"></i> Versión <b class="text-black dark:text-white">{{ $m->version }}</b></span>
            @endif
            @if ($m->ronda)
                <span><i class="fas fa-rotate mr-0.5"></i> Ronda {{ $m->ronda }}</span>
            @endif
            @if ($m->avance !== null)
                <span><i class="fas fa-gauge mr-0.5"></i> Avance <b class="text-black dark:text-white">{{ $m->avance }}%</b></span>
            @endif
            @if ($m->enlace)
                <a href="{{ $m->enlace }}" target="_blank" rel="noopener noreferrer" class="text-primary hover:underline truncate max-w-[260px]">
                    <i class="fas fa-link mr-0.5"></i> {{ preg_replace('#^https?://#', '', $m->enlace) }}
                </a>
            @endif
        @endif

        @php $autor = $item['clase'] === 'actividad' ? $m->usuario : $m->autor; @endphp
        @if ($autor)
            <span><i class="fas fa-user mr-0.5"></i> {{ $autor->name }}</span>
        @endif
        @if ($item['clase'] === 'actividad')
            <span class="font-mono">{{ $m->created_at->format('H:i') }}</span>
        @endif
    </div>

    {{-- ── Vínculos solicitud ↔ etapa ── --}}
    @if ($item['clase'] === 'solicitud')
        @if ($m->estado === 'atendida' && $m->etapaResolucion)
            <div class="mt-1.5 text-[11px] text-meta-3">
                <i class="fas fa-check"></i> Atendida en <b>{{ $m->etapaResolucion->titulo }}</b>
                @if ($m->etapaResolucion->trashed()) <span class="text-bodydark2">(registro eliminado)</span> @endif
            </div>
        @elseif ($m->estado === 'fuera_alcance')
            <div class="mt-1.5 text-[11px] text-[#a855f7]"><i class="fas fa-circle-exclamation"></i> No incluida en la cotización vigente.</div>
        @endif
        @if ($m->respuesta)
            <div class="mt-1 text-[11px] text-bodydark2"><i class="fas fa-reply"></i> {{ $m->respuesta }}</div>
        @endif
    @endif

    @if ($item['clase'] === 'etapa' && $m->solicitudesAtendidas->isNotEmpty())
        <div class="mt-1.5 text-[11px] text-meta-3">
            <i class="fas fa-reply"></i> Atiende: {{ $m->solicitudesAtendidas->pluck('titulo')->join(', ') }}
        </div>
    @endif

    {{-- ── Evidencias ── --}}
    @if ($item['clase'] !== 'actividad' && $m->adjuntos->isNotEmpty())
        @include('proyectos.partials._evidencias', ['adjuntos' => $m->adjuntos, 'puedeEliminar' => $puedeModificar])
    @endif
</div>
