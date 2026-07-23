@php
    $cfg = match ($estado) {
        'en_progreso'         => ['clase' => 'en_progreso', 'texto' => 'En progreso',    'icono' => 'fa-spinner'],
        'requiere_correccion' => ['clase' => 'critica',     'texto' => 'Corrección',     'icono' => 'fa-exclamation-triangle'],
        'aprobado'            => ['clase' => 'asignado',    'texto' => 'Aprobado',       'icono' => 'fa-check-circle'],
        'cancelado'           => ['clase' => 'cancelado',   'texto' => 'Cancelado',      'icono' => 'fa-ban'],
        'archivado'           => ['clase' => 'abierto',     'texto' => 'Archivado',      'icono' => 'fa-archive'],
        default               => ['clase' => 'abierto',     'texto' => ucfirst($estado), 'icono' => 'fa-circle'],
    };
@endphp
<span class="db-badge {{ $cfg['clase'] }}" style="font-size: 0.65rem;">
    <i class="fas {{ $cfg['icono'] }} mr-1"></i> {{ $cfg['texto'] }}
</span>