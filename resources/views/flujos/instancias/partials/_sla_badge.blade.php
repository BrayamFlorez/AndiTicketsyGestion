{{-- Recibe: $step — WorkflowInstanceStep con stage cargado, o null
     Muestra badge crítico si el SLA está vencido, informativo si está cerca.
     No renderiza nada si no aplica. --}}

@php
    $slaBadge = null;

    if (! empty($step) && ($step->stage?->sla_horas ?? 0) > 0 && $step->inicio_en) {
        $limite = $step->inicio_en->copy()->addHours($step->stage->sla_horas);
        $ahora  = now();

        if ($ahora->gt($limite)) {
            $slaBadge = [
                'tipo'  => 'vencido',
                'horas' => (int) $limite->diffInHours($ahora, true),
            ];
        } else {
            $horasRest = (int) $ahora->diffInHours($limite, true);
            $umbral    = max(2, (int) ($step->stage->sla_horas * 0.25));
            if ($horasRest <= $umbral) {
                $slaBadge = ['tipo' => 'cerca', 'horas' => $horasRest];
            }
        }
    }
@endphp

@if ($slaBadge && $slaBadge['tipo'] === 'vencido')
    <span class="db-badge critica inline-flex items-center gap-1"
          style="font-size: 0.6rem;" title="SLA vencido">
        <i class="fas fa-hourglass-end"></i> Vencido {{ $slaBadge['horas'] }}h
    </span>
@elseif ($slaBadge && $slaBadge['tipo'] === 'cerca')
    <span class="db-badge en_progreso inline-flex items-center gap-1"
          style="font-size: 0.6rem;" title="Faltan {{ $slaBadge['horas'] }}h para vencer">
        <i class="fas fa-clock"></i> {{ $slaBadge['horas'] }}h restantes
    </span>
@endif