{{-- resources/views/dashboard/partials/admin.blade.php --}}

<div class="db-header mb-6">
    <h4>Panel de operación</h4>
    <p>Vista consolidada de tickets, flujos e infraestructura</p>
</div>

{{-- ── Métricas: tickets ────────────────────────────────────────────── --}}
<h5 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3">
    <i class="fas fa-ticket-alt mr-1"></i> Tickets de soporte
</h5>
<div class="grid grid-cols-2 xl:grid-cols-4 gap-3 mb-6">

    <div class="db-stat-card">
        <div class="db-stat-icon"><i class="fas fa-ticket-alt"></i></div>
        <div>
            <div class="db-stat-value">{{ $totalTickets }}</div>
            <div class="db-stat-label">Tickets totales</div>
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

{{-- ── Métricas: flujos ─────────────────────────────────────────────── --}}
<h5 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3">
    <i class="fas fa-project-diagram mr-1"></i> Flujos de trabajo
</h5>
<div class="grid grid-cols-2 xl:grid-cols-4 gap-3 mb-6">

    <div class="db-stat-card">
        <div class="db-stat-icon"><i class="fas fa-play-circle"></i></div>
        <div>
            <div class="db-stat-value">{{ $flujosAbiertos }}</div>
            <div class="db-stat-label">Flujos abiertos</div>
        </div>
    </div>

    <div class="db-stat-card">
        <div class="db-stat-icon danger"><i class="fas fa-hourglass-half"></i></div>
        <div>
            <div class="db-stat-value">{{ $flujosCorreccion }}</div>
            <div class="db-stat-label">En corrección</div>
        </div>
    </div>

    <div class="db-stat-card">
        <div class="db-stat-icon success"><i class="fas fa-check-double"></i></div>
        <div>
            <div class="db-stat-value">{{ $flujosCerradosMes }}</div>
            <div class="db-stat-label">Aprobados este mes</div>
        </div>
    </div>

    <div class="db-stat-card">
        <div class="db-stat-icon warning"><i class="fas fa-user-clock"></i></div>
        <div>
            <div class="db-stat-value">{{ $misFlujosPendientes }}</div>
            <div class="db-stat-label">Requieren mi decisión</div>
        </div>
    </div>

</div>

{{-- ── Métricas: infraestructura ────────────────────────────────────── --}}
<h5 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3">
    <i class="fas fa-server mr-1"></i> Infraestructura
</h5>
<div class="grid grid-cols-3 gap-3 mb-6">

    <div class="db-stat-card">
        <div class="db-stat-icon info"><i class="fas fa-building"></i></div>
        <div>
            <div class="db-stat-value">{{ $totalEmpresas }}</div>
            <div class="db-stat-label">Empresas</div>
        </div>
    </div>

    <div class="db-stat-card">
        <div class="db-stat-icon info"><i class="fas fa-map-marker-alt"></i></div>
        <div>
            <div class="db-stat-value">{{ $totalSedes }}</div>
            <div class="db-stat-label">Sedes</div>
        </div>
    </div>

    <div class="db-stat-card">
        <div class="db-stat-icon info"><i class="fas fa-desktop"></i></div>
        <div>
            <div class="db-stat-value">{{ $totalEquipos }}</div>
            <div class="db-stat-label">Equipos</div>
        </div>
    </div>

</div>

{{-- ── Grid: tickets recientes + flujos recientes ───────────────────── --}}
<div class="grid grid-cols-1 xl:grid-cols-2 gap-4 mb-4">

    <div class="db-card">
        <div class="db-card-header">
            <span>Tickets recientes</span>
            <a href="{{ route('tickets.index') }}" class="db-btn outline">Ver todos</a>
        </div>
        <div class="overflow-x-auto">
            <table class="db-table">
                <thead>
                    <tr><th>#</th><th>Título</th><th>Estado</th><th>Prioridad</th></tr>
                </thead>
                <tbody>
                    @forelse ($ticketsRecientes as $ticket)
                        <tr>
                            <td style="color:var(--db-text-muted)">{{ $ticket->id }}</td>
                            <td>
                                <a href="{{ route('tickets.show', $ticket) }}" class="db-ticket-title">
                                    {{ Str::limit($ticket->titulo, 35) }}
                                </a>
                                <div class="db-sub">{{ $ticket->solicitante->name }}</div>
                            </td>
                            <td><span class="db-badge {{ $ticket->estado }}">{{ $ticket->label_estado }}</span></td>
                            <td><span class="db-badge {{ $ticket->prioridad }}">{{ $ticket->label_prioridad }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4"><div class="db-empty"><p>Sin tickets</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="db-card">
        <div class="db-card-header">
            <span>Flujos recientes</span>
            <a href="{{ route('flujos.instancias.index', ['tab' => 'todos']) }}" class="db-btn outline">Ver bandeja</a>
        </div>
        <div class="overflow-x-auto">
            <table class="db-table">
                <thead>
                    <tr><th>Código</th><th>Trámite</th><th>Etapa</th><th>Estado</th><th>Iniciado</th></tr>
                </thead>
                <tbody>
                    @forelse ($flujosRecientes as $flujo)
                        @include('dashboard.partials._flujo_row', ['flujo' => $flujo])
                    @empty
                        <tr><td colspan="5"><div class="db-empty"><p>Sin flujos aún</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- ── Estado de técnicos ───────────────────────────────────────────── --}}
<div class="db-card">
    <div class="db-card-header">
        <span>Carga de técnicos</span>
        <a href="{{ route('usuarios.index') }}" class="db-btn outline">Gestionar</a>
    </div>
    <div class="overflow-x-auto">
        <table class="db-table">
            <thead>
                <tr><th>Técnico</th><th>Correo</th><th class="text-center">Activos</th></tr>
            </thead>
            <tbody>
                @forelse ($tecnicos as $tec)
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="db-avatar">{{ substr($tec->name, 0, 1) }}</div>
                                <span style="color:var(--db-text)">{{ $tec->name }}</span>
                            </div>
                        </td>
                        <td class="db-sub">{{ $tec->email }}</td>
                        <td class="text-center">
                            <span class="db-badge {{ $tec->activos > 5 ? 'critica' : ($tec->activos > 0 ? 'en_progreso' : 'abierto') }}">
                                {{ $tec->activos }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3"><div class="db-empty"><p>Sin técnicos activos</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>