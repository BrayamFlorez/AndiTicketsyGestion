{{-- resources/views/auditoria/partials/_badge_accion.blade.php --}}
@php
    /** @var \App\Enums\DocumentAuditAction $accionEnum */
    $accionEnum = $accion instanceof \App\Enums\DocumentAuditAction
        ? $accion
        : \App\Enums\DocumentAuditAction::from($accion);

    $badgeColor = match ($accionEnum->claseBadge()) {
        'resuelto', 'exito', 'meta-3' => 'bg-meta-3/10 text-meta-3',
        'critica', 'error', 'meta-1'  => 'bg-meta-1/10 text-meta-1',
        'en_progreso', 'warning', 'meta-6' => 'bg-meta-6/10 text-meta-6',
        default => match ($accionEnum->value ?? '') {
            'creado', 'iniciado', 'subido'      => 'bg-primary/10 text-primary',
            'aprobado', 'resuelto', 'reabierto' => 'bg-meta-3/10 text-meta-3',
            'rechazado', 'cancelado', 'eliminado' => 'bg-meta-1/10 text-meta-1',
            'devuelto', 'corregido', 'desistido'  => 'bg-meta-6/10 text-meta-6',
            default => 'bg-primary/10 text-primary',
        },
    };
@endphp

<span class="inline-flex items-center gap-1 rounded-full py-0.5 px-2.5 text-[10px] font-bold uppercase {{ $badgeColor }}">
    <i class="fas {{ $accionEnum->icono() }} text-[9px]"></i>
    {{ $accionEnum->etiqueta() }}
</span>