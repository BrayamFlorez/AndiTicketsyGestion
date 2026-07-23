{{-- Recibe: $notif, $compact (bool) --}}
@php
    $data       = $notif->data ?? [];
    $leida      = $notif->read_at !== null;
    $mensaje    = $data['mensaje'] ?? 'Notificación';
    $actor      = $data['actor'] ?? null;
    $comentario = $data['comentario'] ?? $data['motivo'] ?? null;

    // Compatibilidad con notificaciones viejas (que no tienen 'actor', 'tipo_documento', etc.)
    $esVieja = ! isset($data['actor']) && ! isset($data['tipo_documento']);

    $bordeColor = match ($data['color'] ?? 'accent') {
        'success' => 'border-green-500',
        'warning' => 'border-yellow-500',
        'danger'  => 'border-red-500',
        default   => 'border-[#007acc]',
    };
    $iconoBg = match ($data['color'] ?? 'accent') {
        'success' => 'bg-green-500/10 text-green-400 border-green-500/30',
        'warning' => 'bg-yellow-500/10 text-yellow-400 border-yellow-500/30',
        'danger'  => 'bg-red-500/10 text-red-400 border-red-500/30',
        default   => 'bg-[#007acc]/10 text-[#007acc] border-[#007acc]/30',
    };
    $padding = $compact ? 'px-4 py-3' : 'px-5 py-4';
    $avatarSize = $compact ? 'w-9 h-9' : 'w-11 h-11';
@endphp

<form action="{{ route('notificaciones.leer', $notif->id) }}" method="POST" class="block border-b border-gray-800 last:border-b-0">
    @csrf
    <button type="submit"
        class="w-full text-left flex items-start gap-3 {{ $padding }} hover:bg-[var(--db-surface-2,#2d2d2d)] transition-colors border-l-2 {{ $leida ? 'border-transparent opacity-60' : $bordeColor }}">

        {{-- Avatar del actor o ícono del tipo --}}
        @if ($actor)
            <div class="{{ $avatarSize }} rounded-full bg-[#007acc] flex items-center justify-center text-white text-xs font-black flex-shrink-0 shadow-sm"
                 title="{{ $actor['name'] }}{{ isset($actor['rol']) ? ' — '.$actor['rol'] : '' }}">
                {{ $actor['iniciales'] }}
            </div>
        @else
            <div class="{{ $avatarSize }} rounded-lg flex items-center justify-center border flex-shrink-0 {{ $iconoBg }}">
                <i class="fas {{ $data['icono'] ?? 'fa-bell' }} text-xs"></i>
            </div>
        @endif

        <div class="flex-1 min-w-0">
            {{-- Mensaje principal (rico) --}}
            <p class="text-xs text-white leading-snug font-medium">
                {{ $mensaje }}
            </p>

            {{-- Meta contextual: tipo doc + etapa + código --}}
            @if (! $esVieja)
                <div class="flex items-center gap-3 mt-1.5 flex-wrap text-[10px]">
                    @if (! empty($data['tipo_documento']))
                        <span class="text-gray-400 flex items-center gap-1">
                            <i class="fas {{ $data['tipo_icono'] ?? 'fa-file-alt' }}"></i>
                            {{ $data['tipo_documento'] }}
                        </span>
                    @endif
                    @if (! empty($data['etapa']))
                        <span class="text-gray-500">
                            <i class="fas fa-flag mr-0.5"></i>{{ $data['etapa'] }}
                        </span>
                    @endif
                    @if (! empty($data['codigo']))
                        <span class="font-mono text-gray-600">{{ $data['codigo'] }}</span>
                    @endif
                </div>
            @elseif (! empty($data['codigo']))
                <div class="mt-1">
                    <span class="text-[10px] font-mono text-gray-500">{{ $data['codigo'] }}</span>
                </div>
            @endif

            {{-- Comentario/motivo (cita) --}}
            @if ($comentario)
                <p class="text-[10px] text-gray-400 mt-1.5 italic pl-2 border-l-2 border-gray-700 line-clamp-2">
                    "{{ Str::limit($comentario, 140) }}"
                </p>
            @endif

            {{-- Firmantes (para aprobado) --}}
            @if (! empty($data['firmantes']))
                <p class="text-[10px] text-gray-500 mt-1.5">
                    <i class="fas fa-signature mr-1"></i>
                    Firmado por: {{ implode(' · ', array_slice($data['firmantes'], 0, 4)) }}
                    @if (count($data['firmantes']) > 4) y {{ count($data['firmantes']) - 4 }} más @endif
                </p>
            @endif

            {{-- Tiempo --}}
            <div class="mt-1.5 flex items-center justify-between">
                <span class="text-[9px] text-gray-500">
                    <i class="fas fa-clock mr-0.5"></i>{{ $notif->created_at->diffForHumans() }}
                </span>
                @if (! $leida)
                    <span class="text-[9px] text-[#007acc] font-bold uppercase tracking-wider">Nueva</span>
                @endif
            </div>
        </div>
    </button>
</form>