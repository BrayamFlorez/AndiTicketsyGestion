{{-- resources/views/dashboard/partials/admin.blade.php --}}

<div class="db-header mb-6">
    <h4>Panel de administración</h4>
    <p>Gestión operativa de tickets y técnicos</p>
</div>

{{-- ── Tarjetas de métrica ──────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 xl:grid-cols-4 gap-3 mb-6">

    <div class="db-stat-card">
        <div class="db-stat-icon"><i class="fas fa-ticket-alt"></i></div>
        <div>
            <div class="db-stat-value">{{ $totalTickets }}</div>
            <div class="db-stat-label">Total tickets</div>
        </div>
    </div>

    <div class="db-stat-card">
        <div class="db-stat-icon danger"><i class="fas fa-hourglass-half"></i></div>
        <div>
            <div class="db-stat-value">{{ $sinAsignar }}</div>
            <div class="db-stat-label">Sin asignar</div>
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
        <div class="db-stat-icon success"><i class="fas fa-check-circle"></i></div>
        <div>
            <div class="db-stat-value">{{ $ticketsPorEstado['resuelto'] ?? 0 }}</div>
            <div class="db-stat-label">Resueltos</div>
        </div>
    </div>

</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- Tickets recientes --}}
    <div class="db-card lg:col-span-2">
        <div class="db-card-header">
            <span>Tickets recientes</span>
            @can('tickets.asignar')
                @if ($sinAsignar > 0)
                    <a href="{{ route('tickets.index', ['estado' => 'abierto']) }}" class="db-btn danger-outline">
                        <i class="fas fa-exclamation-triangle"></i> {{ $sinAsignar }} sin asignar
                    </a>
                @endif
            @endcan
        </div>
        <div class="overflow-x-auto">
            <table class="db-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Título</th>
                        <th>Asignado a</th>
                        <th>Estado</th>
                        <th>Prioridad</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ticketsRecientes as $ticket)
                        <tr>
                            <td style="color:var(--db-text-muted)">{{ $ticket->id }}</td>
                            <td>
                                <a href="{{ route('tickets.show', $ticket) }}" class="db-ticket-title">
                                    {{ Str::limit($ticket->titulo, 38) }}
                                </a>
                                <div class="db-sub">{{ $ticket->empresa?->nombre ?? '—' }}</div>
                            </td>
                            <td>
                                @if ($ticket->asignado)
                                    <div class="flex items-center gap-2">
                                        <div class="db-avatar">{{ substr($ticket->asignado->name, 0, 1) }}</div>
                                        <span class="db-sub" style="color:var(--db-text)">{{ $ticket->asignado->name }}</span>
                                    </div>
                                @else
                                    <span class="db-badge cancelado">Sin asignar</span>
                                @endif
                            </td>
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

    {{-- Carga de técnicos --}}
    <div class="db-card">
        <div class="db-card-header"><span>Carga de técnicos</span></div>
        <div class="db-tech-list">
            @forelse ($tecnicos as $tecnico)
                <div class="db-tech-row">
                    <div class="flex items-center gap-2">
                        <div class="db-avatar">{{ substr($tecnico->name, 0, 1) }}</div>
                        <span class="db-tech-name">{{ $tecnico->name }}</span>
                    </div>
                    @php
                        $carga = $tecnico->activos;
                        $clase = $carga > 3 ? 'high' : ($carga > 1 ? 'mid' : 'low');
                    @endphp
                    <span class="db-load-badge {{ $clase }}">{{ $carga }} activos</span>
                </div>
            @empty
                <div class="db-empty">
                    <i class="fas fa-user-cog"></i>
                    <p>No hay técnicos registrados</p>
                </div>
            @endforelse
        </div>
    </div>

</div>
