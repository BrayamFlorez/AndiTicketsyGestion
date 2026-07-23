{{-- resources/views/dashboard/partials/super_admin.blade.php --}}

<div class="db-header mb-6">
    <h4>Panel general del sistema</h4>
    <p>Vista completa — todos los tickets y usuarios</p>
</div>

{{-- ── Tarjetas de métrica ──────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 xl:grid-cols-4 gap-3 mb-6">

    <div class="db-stat-card">
        <div class="db-stat-icon"><i class="fas fa-ticket-alt"></i></div>
        <div>
            <div class="db-stat-value">{{ $totalTickets }}</div>
            <div class="db-stat-label">Tickets totales</div>
        </div>
    </div>

    <div class="db-stat-card">
        <div class="db-stat-icon danger"><i class="fas fa-exclamation-circle"></i></div>
        <div>
            <div class="db-stat-value">{{ $sinAsignar }}</div>
            <div class="db-stat-label">Sin asignar</div>
        </div>
    </div>

    <div class="db-stat-card">
        <div class="db-stat-icon success"><i class="fas fa-users"></i></div>
        <div>
            <div class="db-stat-value">{{ $totalUsuarios }}</div>
            <div class="db-stat-label">Usuarios</div>
        </div>
    </div>

    <div class="db-stat-card">
        <div class="db-stat-icon warning"><i class="fas fa-building"></i></div>
        <div>
            <div class="db-stat-value">{{ $totalEmpresas }}</div>
            <div class="db-stat-label">Empresas</div>
        </div>
    </div>

</div>

{{-- ── Panel inferior: estados + tickets recientes ──────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- Estados --}}
    <div class="db-card">
        <div class="db-card-header"><span>Tickets por estado</span></div>
        <div class="db-status-list">
            @foreach (\App\Models\Ticket::ESTADOS as $key => $label)
                <div class="db-status-row">
                    <span class="db-badge {{ $key }}">{{ $label }}</span>
                    <span class="db-status-count">{{ $ticketsPorEstado[$key] ?? 0 }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Tickets recientes --}}
    <div class="db-card lg:col-span-2">
        <div class="db-card-header">
            <span>Tickets recientes</span>
            <a href="{{ route('tickets.index') }}" class="db-btn outline">Ver todos</a>
        </div>
        <div class="overflow-x-auto">
            <table class="db-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Título</th>
                        <th>Empresa</th>
                        <th>Estado</th>
                        <th>Prioridad</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ticketsRecientes as $ticket)
                        <tr>
                            <td class="text-xs" style="color:var(--db-text-muted)">{{ $ticket->id }}</td>
                            <td>
                                <a href="{{ route('tickets.show', $ticket) }}" class="db-ticket-title">
                                    {{ Str::limit($ticket->titulo, 40) }}
                                </a>
                                <div class="db-sub">{{ $ticket->solicitante->name }}</div>
                            </td>
                            <td class="db-sub">{{ $ticket->empresa?->nombre ?? '—' }}</td>
                            <td><span class="db-badge {{ $ticket->estado }}">{{ $ticket->label_estado }}</span></td>
                            <td><span class="db-badge {{ $ticket->prioridad }}">{{ $ticket->label_prioridad }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="db-empty">
                                    <i class="fas fa-ticket-alt"></i>
                                    <p>No hay tickets aún</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
