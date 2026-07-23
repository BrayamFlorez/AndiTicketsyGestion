{{-- resources/views/dashboard/partials/usuario.blade.php --}}

<div class="db-header mb-6">
    <h4>Mi actividad</h4>
    <p>Hola, {{ auth()->user()->name }} — tus tickets y trámites en curso</p>
</div>

{{-- ── Métricas: tickets ────────────────────────────────────────────── --}}
<h5 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3">
    <i class="fas fa-ticket-alt mr-1"></i> Tickets de soporte
</h5>
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

{{-- ── Métricas: flujos ─────────────────────────────────────────────── --}}
<h5 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3">
    <i class="fas fa-project-diagram mr-1"></i> Flujos de trabajo
</h5>
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">

    <div class="db-stat-card">
        <div class="db-stat-icon"><i class="fas fa-play-circle"></i></div>
        <div>
            <div class="db-stat-value">{{ $misFlujosAbiertos }}</div>
            <div class="db-stat-label">Flujos abiertos</div>
        </div>
    </div>

    <div class="db-stat-card">
        <div class="db-stat-icon danger"><i class="fas fa-hourglass-half"></i></div>
        <div>
            <div class="db-stat-value">{{ $misFlujosCorreccion }}</div>
            <div class="db-stat-label">Requieren corrección</div>
        </div>
    </div>

    <div class="db-stat-card">
        <div class="db-stat-icon success"><i class="fas fa-check-double"></i></div>
        <div>
            <div class="db-stat-value">{{ $misFlujosAprobados }}</div>
            <div class="db-stat-label">Aprobados</div>
        </div>
    </div>

</div>

{{-- ── Tabla: tickets recientes ─────────────────────────────────────── --}}
<div class="db-card mb-4">
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
                    <th>#</th><th>Título</th><th>Categoría</th><th>Estado</th><th>Técnico</th><th>Fecha</th>
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

{{-- ── Tabla: flujos iniciados ──────────────────────────────────────── --}}
<div class="db-card">
    <div class="db-card-header">
        <span>Mis flujos recientes</span>
        <div class="flex gap-2">
            @can('flujos.iniciar')
                <a href="{{ route('flujos.instancias.create') }}" class="db-btn solid">
                    <i class="fas fa-play-circle"></i> Iniciar flujo
                </a>
            @endcan
            <a href="{{ route('flujos.instancias.index', ['tab' => 'iniciados']) }}" class="db-btn outline">
                Ver todos
            </a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="db-table">
            <thead>
                <tr>
                    <th>Código</th><th>Trámite</th><th>Etapa</th><th>Estado</th><th>Iniciado</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($misFlujos as $flujo)
                    @include('dashboard.partials._flujo_row', ['flujo' => $flujo])
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="db-empty">
                                <i class="fas fa-project-diagram"></i>
                                <p>Aún no has iniciado ningún flujo</p>
                                @can('flujos.iniciar')
                                    <a href="{{ route('flujos.instancias.create') }}" class="db-btn solid">
                                        Iniciar mi primer flujo
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