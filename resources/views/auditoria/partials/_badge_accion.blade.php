@php
    /** @var \App\Enums\DocumentAuditAction $accionEnum */
    $accionEnum = $accion instanceof \App\Enums\DocumentAuditAction
        ? $accion
        : \App\Enums\DocumentAuditAction::from($accion);
@endphp
<span class="db-badge {{ $accionEnum->claseBadge() }}" style="font-size: 0.65rem;">
    <i class="fas {{ $accionEnum->icono() }} mr-1"></i> {{ $accionEnum->etiqueta() }}
</span>
