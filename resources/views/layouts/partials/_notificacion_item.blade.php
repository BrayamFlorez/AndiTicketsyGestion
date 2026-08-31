{{-- Recibe: $notif, $compact (bool) --}}
@php
    $data       = $notif->data ?? [];
    $leida      = $notif->read_at !== null;
    $mensaje    = $data['mensaje'] ?? 'Notificación';
    $actor      = $data['actor'] ?? null;
    $comentario = $data['comentario'] ?? $data['motivo'] ?? null;

    $bordeColor = match ($data['color'] ?? 'accent') {
        'success' => 'border-meta-3',
        'warning' => 'border-meta-6',
        'danger'  => 'border-meta-1',
        default   => 'border-primary',
    };
@endphp

<form action="{{ route('notificaciones.leer', $notif->id) }}" method="POST" class="block">
    @csrf
    <button type="submit"
            class="flex w-full items-start gap-3 border-b border-stroke p-3 text-left transition hover:bg-gray dark:border-strokedark dark:hover:bg-meta-4 border-l-2 {{ $leida ? 'border-transparent opacity-60' : $bordeColor }}">
        
        {{-- Avatar / Iniciales --}}
        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary text-xs font-black text-white">
            {{ $actor ? $actor['iniciales'] : 'N' }}
        </div>

        <div class="min-w-0 flex-1">
            <p class="text-xs text-black dark:text-white leading-snug font-medium">
                {{ $mensaje }}
            </p>

            @if (!empty($data['codigo']))
                <span class="text-[10px] font-mono text-bodydark2 block mt-0.5">
                    Ref: {{ $data['codigo'] }}
                </span>
            @endif

            @if ($comentario)
                <p class="text-[10px] text-bodydark2 italic border-l border-stroke dark:border-strokedark pl-1.5 mt-1">
                    "{{ Str::limit($comentario, 100) }}"
                </p>
            @endif

            <span class="mt-1 text-[9px] text-bodydark2 block">
                {{ $notif->created_at->diffForHumans() }}
            </span>
        </div>
    </button>
</form>