{{-- resources/views/dashboard/partials/usuario.blade.php --}}

<div class="db-header mb-6">
    <h4>Mis solicitudes de soporte</h4>
    <p>Hola, {{ auth()->user()->name }} — revisa el estado de tus tickets</p>
</div>

{{-- ── Tarjetas de métrica ──────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">

    <div class="db-stat-card">
        <div class="db-stat-icon"><i class="fas fa-ticket-alt"></i></div>
        <div>
            <div class="db-stat-value">{{ $totalMisTickets }}</div>
            <div class="db-stat-label">Mis tickets</div>
        </div>
    </div>

    <div class="db-stat-card">
        <div class="db-stat-icon warning"><i class="fas fa-hourglass-half"></i></div>
        <div>
            <div class="db-stat-value">{{ $abiertos }}</div>
            <div class="db-stat-label">En proceso</div>
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

{{-- ── Tabla de tickets ─────────────────────────────────────────────────── --}}
<div class="db-card">
    <div class="db-card-header">
        <span>Mis tickets recientes</span>
        @can('tickets.crear')
            <a href="{{ route('tickets.create') }}" class="db-btn solid">
                <i class="fas fa-plus"></i> Nuevo ticket
            </a>
        @endcan
    </div>
    <div class="overflow-x-auto">
        <table class="db-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Título</th>
                    <th>Categoría</th>
                    <th>Estado</th>
                    <th>Técnico</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($misTickets as $ticket)
                    <tr>
                        <td style="color:var(--db-text-muted)">{{ $ticket->id }}</td>
                        <td>
                            <a href="{{ route('tickets.show', $ticket) }}" class="db-ticket-title">
                                {{ Str::limit($ticket->titulo, 40) }}
                            </a>
                        </td>
                        <td class="db-sub" style="color:var(--db-text)">{{ $ticket->label_categoria }}</td>
                        <td><span class="db-badge {{ $ticket->estado }}">{{ $ticket->label_estado }}</span></td>
                        <td>
                            @if ($ticket->asignado)
                                <div class="flex items-center gap-2">
                                    <div class="db-avatar">{{ substr($ticket->asignado->name, 0, 1) }}</div>
                                    <span class="db-sub" style="color:var(--db-text)">{{ $ticket->asignado->name }}</span>
                                </div>
                            @else
                                <span style="color:var(--db-text-muted); font-size:0.75rem">Pendiente</span>
                            @endif
                        </td>
                        <td class="db-sub">{{ $ticket->created_at->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="db-empty">
                                <i class="fas fa-ticket-alt"></i>
                                <p>Aún no tienes tickets</p>
                                @can('tickets.crear')
                                    <a href="{{ route('tickets.create') }}" class="db-btn solid">
                                        Crear mi primer ticket
                                    </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
