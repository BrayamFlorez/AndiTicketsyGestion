{{-- resources/views/tickets/partials/index_tecnico.blade.php --}}

<div class="db-header mb-5">
    <h4>Mis tickets asignados</h4>
    <p>{{ $tickets->total() }} resultado(s) — tickets bajo tu responsabilidad</p>
</div>

{{-- Chips --}}
<div class="flex flex-wrap items-center gap-2 mb-4">
    <a href="{{ route('tickets.index') }}" class="db-chip {{ empty($filtros) ? 'active' : '' }}">
        <i class="fas fa-list"></i> Todos los míos
    </a>
    <a href="{{ route('tickets.index', ['estado' => 'asignado']) }}" class="db-chip {{ ($filtros['estado'] ?? '') === 'asignado' ? 'active' : '' }}">
        <i class="fas fa-clock"></i> Por iniciar
    </a>
    <a href="{{ route('tickets.index', ['estado' => 'en_progreso']) }}" class="db-chip {{ ($filtros['estado'] ?? '') === 'en_progreso' ? 'active' : '' }}">
        <i class="fas fa-sync-alt"></i> En progreso
    </a>
    <a href="{{ route('tickets.index', ['estado' => 'resuelto']) }}" class="db-chip {{ ($filtros['estado'] ?? '') === 'resuelto' ? 'active' : '' }}">
        <i class="fas fa-check"></i> Resueltos
    </a>
    <a href="{{ route('tickets.index', ['prioridad' => 'critica']) }}" class="db-chip {{ ($filtros['prioridad'] ?? '') === 'critica' ? 'active' : '' }}">
        <i class="fas fa-fire"></i> Críticas
    </a>
</div>

{{-- Filtros --}}
<form method="GET" action="{{ route('tickets.index') }}" class="db-card" style="margin-bottom: 1.25rem;">
    <div class="form-card-body" style="padding: 1rem 1.25rem;">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="form-group">
                <label class="form-label" for="q">Buscar</label>
                <input type="text" id="q" name="q" value="{{ $filtros['q'] ?? '' }}" placeholder="Título o descripción..." class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label" for="estado">Estado</label>
                <select id="estado" name="estado" class="form-select">
                    <option value="">Todos</option>
                    @foreach (\App\Models\Ticket::ESTADOS as $key => $label)
                        <option value="{{ $key }}" @selected(($filtros['estado'] ?? '') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="prioridad">Prioridad</label>
                <select id="prioridad" name="prioridad" class="form-select">
                    <option value="">Todas</option>
                    @foreach (\App\Models\Ticket::PRIORIDADES as $key => $label)
                        <option value="{{ $key }}" @selected(($filtros['prioridad'] ?? '') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="categoria">Categoría</label>
                <select id="categoria" name="categoria" class="form-select">
                    <option value="">Todas</option>
                    @foreach (\App\Models\Ticket::CATEGORIAS as $key => $label)
                        <option value="{{ $key }}" @selected(($filtros['categoria'] ?? '') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="flex items-center justify-end gap-2 mt-3">
            @if (!empty($filtros))
                <a href="{{ route('tickets.index') }}" class="db-btn ghost"><i class="fas fa-times"></i> Limpiar</a>
            @endif
            <button type="submit" class="db-btn outline"><i class="fas fa-filter"></i> Filtrar</button>
        </div>
    </div>
</form>

{{-- Tabla --}}
<div class="db-card">
    <div class="overflow-x-auto">
        <table class="db-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Título</th>
                    <th>Solicitante</th>
                    <th>Empresa / Sede</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tickets as $ticket)
                    <tr>
                        <td style="color: var(--db-text-muted)">{{ $ticket->id }}</td>
                        <td>
                            <a href="{{ route('tickets.show', $ticket) }}" class="db-ticket-title">
                                {{ Str::limit($ticket->titulo, 50) }}
                            </a>
                            <div class="db-sub">{{ $ticket->label_categoria }}</div>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="db-avatar">{{ substr($ticket->solicitante->name, 0, 1) }}</div>
                                <span class="db-sub" style="color: var(--db-text)">{{ $ticket->solicitante->name }}</span>
                            </div>
                        </td>
                        <td class="db-sub" style="color: var(--db-text)">
                            {{ $ticket->empresa?->nombre ?? '—' }}
                            @if ($ticket->sede)
                                <div class="db-sub">{{ $ticket->sede->nombre }}</div>
                            @endif
                        </td>
                        <td><span class="db-badge {{ $ticket->prioridad }}">{{ $ticket->label_prioridad }}</span></td>
                        <td><span class="db-badge {{ $ticket->estado }}">{{ $ticket->label_estado }}</span></td>
                        <td class="db-sub">{{ $ticket->created_at->format('d/m/Y') }}</td>

                        {{-- ── ACCIONES (inline — pocas opciones) ───────────── --}}
                        <td>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('tickets.show', $ticket) }}" class="db-btn outline" title="Ver detalle">
                                    <i class="fas fa-eye"></i>
                                </a>

                                {{-- Iniciar trabajo (solo si está asignado a este técnico y en estado 'asignado') --}}
                                @can('tickets.iniciar')
                                    @if ($ticket->puedeIniciar() && $ticket->asignado_a === auth()->id())
                                        <form method="POST" action="{{ route('tickets.iniciar', $ticket) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="db-btn solid" title="Iniciar trabajo">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <div class="db-empty">
                                <i class="fas fa-check-double" style="color: #4ade80;"></i>
                                <p>
                                    @if (!empty($filtros))
                                        No hay tickets que coincidan con los filtros.
                                    @else
                                        No tienes tickets asignados — ¡todo al día!
                                    @endif
                                </p>
                                @if (!empty($filtros))
                                    <a href="{{ route('tickets.index') }}" class="db-btn outline">Limpiar filtros</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($tickets->hasPages())
        <div class="px-4 py-3 border-t" style="border-color: var(--db-border)">
            {{ $tickets->onEachSide(1)->links() }}
        </div>
    @endif
</div>
