{{-- Recibe: $flujo (WorkflowInstance con relaciones template, iniciador, etapaActual, documentType) --}}
@php
    $cfg = match ($flujo->estado) {
        'en_progreso'         => ['color' => 'en_progreso', 'texto' => 'En progreso'],
        'requiere_correccion' => ['color' => 'critica',     'texto' => 'Corrección'],
        'aprobado'            => ['color' => 'asignado',    'texto' => 'Aprobado'],
        'cancelado'           => ['color' => 'cancelado',   'texto' => 'Cancelado'],
        'archivado'           => ['color' => 'abierto',     'texto' => 'Archivado'],
        default               => ['color' => 'abierto',     'texto' => ucfirst($flujo->estado)],
    };
@endphp
<tr>
    <td style="color:var(--db-text-muted); font-family: monospace; font-size: 0.7rem;">
        {{ $flujo->codigo }}
    </td>
    <td>
        <a href="{{ route('flujos.instancias.show', $flujo) }}" class="db-ticket-title">
            {{ Str::limit($flujo->titulo, 38) }}
        </a>
        <div class="db-sub">
            @if ($flujo->documentType)
                <i class="fas {{ $flujo->documentType->icono ?: 'fa-file-alt' }} mr-1"></i>{{ $flujo->documentType->nombre }} ·
            @endif
            {{ $flujo->template->nombre }}
        </div>
    </td>
    <td>
        <div class="text-xs" style="color: var(--db-text)">
            {{ $flujo->etapaActual->nombre ?? '—' }}
        </div>
        @if ($flujo->currentStep ?? null)
            <div class="mt-1">
                @include('flujos.instancias.partials._sla_badge', ['step' => $flujo->currentStep])
            </div>
        @endif
    </td>
    <td><span class="db-badge {{ $cfg['color'] }}">{{ $cfg['texto'] }}</span></td>
    <td class="db-sub">{{ $flujo->iniciado_en?->format('d/m/Y') }}</td>
</tr>