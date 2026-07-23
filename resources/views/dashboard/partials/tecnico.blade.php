{{-- resources/views/dashboard/partials/tecnico.blade.php --}}

<div class="db-header mb-6">
    <h4>Mis tickets asignados</h4>
    <p>Hola, {{ auth()->user()->name }} — aquí están tus tickets activos</p>
</div>

{{-- ── Tarjetas de métrica ──────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 xl:grid-cols-4 gap-3 mb-6">

    <div class="db-stat-card">
        <div class="db-stat-icon"><i class="fas fa-ticket-alt"></i></div>
        <div>
            <div class="db-stat-value">{{ $totalAsignados }}</div>
            <div class="db-stat-label">Total asignados</div>
        </div>
    </div>

    <div class="db-stat-card">
        <div class="db-stat-icon warning"><i class="fas fa-sync-alt"></i></div>
        <div>
            <div class="db-stat-value">{{ $ticketsPorEstado['en_progreso'] ?? 0 }}</div>
            <div class="db-stat-label">En progreso</div>
        </div>
    </div>

    <div class="db-stat-card">
        <div class="db-stat-icon info"><i class="fas fa-user-check"></i></div>
        <div>
            <div class="db-stat-value">{{ $ticketsPorEstado['asignado'] ?? 0 }}</div>
            <div class="db-stat-label">Por iniciar</div>
        </div>
    </div>

    <div class="db-stat-card">
        <div class="db-stat-icon success"><i class="fas fa-check-circle"></i></div>
        <div>
            <div class="db-stat-value">{{ $resueltos }}</div>
            <div class="db-stat-label">Resueltos</div>
        </div>
    </div>

</div>

{{-- ── Tabla de tickets activos ─────────────────────────────────────────── --}}
<div class="db-card">
    <div class="db-card-header">
        <span>Tickets activos — ordenados por prioridad</span>
        <a href="{{ route('tickets.index') }}" class="db-btn outline">Ver todos</a>
    </div>
    <div class="overflow-x-auto">
        <table class="db-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Título</th>
                    <th>Empresa / Sede</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($misTickets as $ticket)
                    <tr>
                        <td style="color:var(--db-text-muted)">{{ $ticket->id }}</td>
                        <td>
                            <a href="{{ route('tickets.show', $ticket) }}" class="db-ticket-title">
                                {{ Str::limit($ticket->titulo, 42) }}
                            </a>
                            <div class="db-sub">{{ $ticket->solicitante->name }}</div>
                        </td>
                        <td>
                            <span style="color:var(--db-text)">{{ $ticket->empresa?->nombre ?? '—' }}</span>
                            @if ($ticket->sede)
                                <div class="db-sub">{{ $ticket->sede->nombre }}</div>
                            @endif
                        </td>
                        <td><span class="db-badge {{ $ticket->prioridad }}">{{ $ticket->label_prioridad }}</span></td>
                        <td><span class="db-badge {{ $ticket->estado }}">{{ $ticket->label_estado }}</span></td>
                        <td>
                            <a href="{{ route('tickets.show', $ticket) }}" class="db-btn outline">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="db-empty">
                                <i class="fas fa-check-double"></i>
                                <p>No tienes tickets activos — ¡todo al día!</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
