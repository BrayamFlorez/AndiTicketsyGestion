{{-- resources/views/tickets/partials/_tickets_activo.blade.php --}}
{{-- Tickets de un equipo o impresora + botón "Crear ticket".                     --}}
{{-- Uso: @include('tickets.partials._tickets_activo', ['activo' => $equipo])        --}}
{{--      @include('tickets.partials._tickets_activo', ['activo' => $impresora])     --}}

@php
    $usuario = auth()->user();
    $esImpresora = $activo instanceof \App\Models\Impresora;
    $parametro = $esImpresora ? 'impresora' : 'equipo';

    $puedeVer = $usuario->canAny(['tickets.ver-todos', 'tickets.ver-asignados', 'tickets.ver-propios']);
    $puedeCrear = $usuario->can('tickets.crear');

    $consulta = $activo->tickets()->visiblesPara($usuario);
    $totalTickets = (clone $consulta)->count();
    $activos = (clone $consulta)->whereNotIn('estado', ['cerrado', 'cancelado'])->count();
    $ultimos = (clone $consulta)->with('asignado:id,name')->latest()->latest('id')->limit(10)->get();

    $colorEstado = fn (string $estado) => match ($estado) {
        'resuelto'                        => 'bg-meta-3/10 text-meta-3 border-meta-3/20',
        'en_progreso', 'esperando_partes' => 'bg-meta-6/10 text-meta-6 border-meta-6/20',
        'cerrado', 'cancelado'            => 'bg-meta-1/10 text-meta-1 border-meta-1/20',
        default                           => 'bg-primary/10 text-primary border-primary/20',
    };
@endphp

@if ($puedeVer || $puedeCrear)
    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="flex items-center justify-between gap-2 border-b border-stroke py-2.5 px-4 dark:border-strokedark">
            <h3 class="font-bold text-black dark:text-white text-[10px] uppercase tracking-wider flex items-center gap-1.5">
                <i class="fas fa-ticket-alt text-primary"></i> Tickets
                @if ($puedeVer)
                    <span class="font-mono text-bodydark2 normal-case">({{ $totalTickets }}{{ $activos ? " · {$activos} activos" : '' }})</span>
                @endif
            </h3>
            @if ($puedeCrear)
                <a href="{{ route('tickets.create', [$parametro => $activo->id]) }}"
                   class="inline-flex items-center gap-1 rounded bg-primary py-1 px-2.5 text-[9px] font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm">
                    <i class="fas fa-plus text-[8px]"></i> Crear ticket
                </a>
            @endif
        </div>

        @if ($puedeVer)
            <div class="p-2">
                @forelse ($ultimos as $t)
                    <a href="{{ route('tickets.show', $t) }}"
                       class="flex items-center gap-2 rounded px-2 py-1.5 hover:bg-gray-2 dark:hover:bg-meta-4/40 transition">
                        <span class="font-mono text-[10px] font-bold text-primary shrink-0">#{{ $t->id }}</span>
                        <span class="min-w-0 flex-1">
                            <span class="block text-[11px] font-medium text-black dark:text-white truncate" title="{{ $t->titulo }}">{{ $t->titulo }}</span>
                            <span class="block text-[9px] text-bodydark2 truncate">
                                {{ $t->created_at->format('d/m/Y') }} · {{ $t->asignado?->name ?? 'Sin técnico' }}
                            </span>
                        </span>
                        <span class="shrink-0 inline-flex rounded-full px-1.5 py-0.5 text-[8px] font-bold uppercase border {{ $colorEstado($t->estado) }}">
                            {{ $t->label_estado }}
                        </span>
                    </a>
                @empty
                    <p class="text-[10px] font-mono text-bodydark2 text-center py-3">> SIN TICKETS REGISTRADOS</p>
                @endforelse

                @if ($totalTickets > $ultimos->count())
                    <a href="{{ route('tickets.index', [$parametro . '_id' => $activo->id]) }}"
                       class="block text-center text-[9px] font-bold uppercase text-bodydark2 hover:text-primary pt-1.5 mt-1 border-t border-stroke dark:border-strokedark">
                        Ver los {{ $totalTickets }} tickets <i class="fas fa-arrow-right text-[8px]"></i>
                    </a>
                @endif
            </div>
        @endif
    </div>
@endif
