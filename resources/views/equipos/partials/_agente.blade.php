{{-- resources/views/equipos/partials/_agente.blade.php --}}
{{-- Uso en equipos/show: @include('equipos.partials._agente', ['equipo' => $equipo]) --}}

@php
    $agente = $equipo->agente;

    $estados = [
        'activo'       => ['texto' => 'Reportando',     'clase' => 'bg-success/10 text-success', 'icono' => 'fa-circle-check'],
        'pendiente'    => ['texto' => 'Sin reportes',   'clase' => 'bg-warning/10 text-warning', 'icono' => 'fa-clock'],
        'sin_conexion' => ['texto' => 'Sin conexión',   'clase' => 'bg-meta-1/10 text-meta-1',  'icono' => 'fa-plug-circle-xmark'],
        'revocado'     => ['texto' => 'Revocado',       'clase' => 'bg-bodydark2/10 text-bodydark2', 'icono' => 'fa-ban'],
    ];
    $estado = $agente ? $estados[$agente->estado_conexion] : null;
@endphp

<div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark p-4">
    <div class="mb-3 flex items-center justify-between gap-2">
        <h3 class="font-bold text-black dark:text-white uppercase tracking-wider text-xs flex items-center gap-1.5">
            <i class="fas fa-satellite-dish text-primary"></i> Agente de inventario
        </h3>
        @if ($estado)
            <span class="inline-flex items-center gap-1 rounded px-2 py-0.5 text-[10px] font-bold uppercase {{ $estado['clase'] }}">
                <i class="fas {{ $estado['icono'] }}"></i> {{ $estado['texto'] }}
            </span>
        @endif
    </div>

    @if ($agente)
        <dl class="grid grid-cols-2 gap-x-4 gap-y-1.5 text-[11px]">
            <dt class="text-bodydark2">Nombre en Windows</dt>
            <dd class="font-mono text-black dark:text-white">{{ $agente->hostname ?? '—' }}</dd>

            <dt class="text-bodydark2">Usuario en sesión</dt>
            <dd class="text-black dark:text-white">{{ $agente->usuario_sesion ?? '—' }}</dd>

            <dt class="text-bodydark2">IP / MAC</dt>
            <dd class="font-mono text-black dark:text-white">{{ $agente->ip ?? '—' }} · {{ $agente->mac ?? '—' }}</dd>

            <dt class="text-bodydark2">Último reporte</dt>
            <dd class="text-black dark:text-white">
                @if ($agente->ultimo_reporte_at)
                    <span title="{{ $agente->ultimo_reporte_at->format('d/m/Y H:i') }}">{{ $agente->ultimo_reporte_at->diffForHumans() }}</span>
                @else
                    —
                @endif
            </dd>

            <dt class="text-bodydark2">Vinculado</dt>
            <dd class="text-black dark:text-white">{{ $agente->vinculado_at->format('d/m/Y H:i') }}</dd>

            <dt class="text-bodydark2">Versión del agente</dt>
            <dd class="font-mono text-black dark:text-white">{{ $agente->version_agente ?? '—' }}</dd>

            @if (! $agente->estaActivo())
                <dt class="text-bodydark2">Revocado</dt>
                <dd class="text-black dark:text-white">
                    {{ $agente->revocado_at->format('d/m/Y H:i') }}
                    @if ($agente->revocador) · {{ $agente->revocador->name }} @endif
                </dd>
            @endif
        </dl>

        @if ($agente->estaActivo())
            @can('equipos.editar')
                <form method="POST" action="{{ route('equipos.agente.revocar', $equipo) }}" class="mt-3"
                      onsubmit="return confirm('¿Revocar el agente? El PC dejará de actualizar este equipo hasta que se vuelva a vincular.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 rounded border border-meta-1/40 py-1 px-3 text-[10px] font-bold uppercase text-meta-1 hover:bg-meta-1 hover:text-white transition">
                        <i class="fas fa-ban"></i> Revocar agente
                    </button>
                </form>
            @endcan
        @endif
    @else
        <p class="text-[11px] text-bodydark2">
            Este equipo no tiene agente. Instalalo en el PC con el token de su empresa para que
            marca, modelo y especificaciones se actualicen solos.
        </p>
    @endif
</div>
