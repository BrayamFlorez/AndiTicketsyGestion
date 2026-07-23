{{-- resources/views/dashboard/partials/tecnico.blade.php --}}

<div class="db-header mb-6">
    <h4>Mi jornada</h4>
    <p>Hola, {{ auth()->user()->name }} — tickets asignados y aprobaciones pendientes</p>
</div>

{{-- ── Métricas: tickets ────────────────────────────────────────────── --}}
<h5 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3">
    <i class="fas fa-ticket-alt mr-1"></i> Tickets
</h5>
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

{{-- ── Métricas: flujos ─────────────────────────────────────────────── --}}
<h5 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3">
    <i class="fas fa-project-diagram mr-1"></i> Aprobaciones pendientes
</h5>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-6">

    <div class="db-stat-card">
        <div class="db-stat-icon danger"><i class="fas fa-hourglass-half"></i></div>
        <div>
            <div class="db-stat-value">{{ $flujosPendientesCount }}</div>
            <div class="db-stat-label">Requieren mi decisión</div>
        </div>
    </div>

    <div class="db-stat-card">
        <div class="db-stat-icon info"><i class="fas fa-user-friends"></i></div>
        <div>
            <div class="db-stat-value">{{ $flujosParticipandoCount }}</div>
            <div class="db-stat-label">Participando</div>
        </div>
    </div>

</div>

{{-- ── Tabla: tickets activos ───────────────────────────────────────── --}}
<div class="db-card mb-4">
    <div class="db-card-header">
        <span>Tickets activos — ordenados por prioridad</span>
        <a href="{{ route('tickets.index') }}" class="db-btn outline">Ver todos</a>
    </div>
    <div class="overflow-x-auto">
        <table class="db-table">
            <thead>
                <tr>
                    <th>#</th><th>Título</th><th>Empresa / Sede</th><th>Prioridad</th><th>Estado</th><th></th>
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

{{-- ── Tabla: flujos que requieren mi decisión ──────────────────────── --}}
<div class="db-card">
    <div class="db-card-header">
        <span>Aprobaciones pendientes</span>
        <a href="{{ route('flujos.instancias.index', ['tab' => 'pendientes']) }}" class="db-btn outline">
            Ver bandeja
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="db-table">
            <thead>
                <tr>
                    <th>Código</th><th>Trámite</th><th>Etapa</th><th>Estado</th><th>Iniciado</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($flujosPendientes as $flujo)
                    @include('dashboard.partials._flujo_row', ['flujo' => $flujo])
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="db-empty">
                                <i class="fas fa-check-double"></i>
                                <p>Sin decisiones pendientes por ahora</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>