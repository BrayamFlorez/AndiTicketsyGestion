{{-- resources/views/tickets/partials/index_usuario.blade.php --}}

<div class="db-header mb-5 flex items-center justify-between flex-wrap gap-3">
    <div>
        <h4>Mis solicitudes de soporte</h4>
        <p>{{ $tickets->total() }} ticket(s) reportado(s) — sigue su evolución aquí</p>
    </div>
    @can('tickets.crear')
        <a href="{{ route('tickets.create') }}" class="db-btn solid">
            <i class="fas fa-plus"></i> Nuevo ticket
        </a>
    @endcan
</div>

{{-- Chips --}}
<div class="flex flex-wrap items-center gap-2 mb-4">
    <a href="{{ route('tickets.index') }}" class="db-chip {{ empty($filtros) ? 'active' : '' }}">
        <i class="fas fa-list"></i> Todos
    </a>
    <a href="{{ route('tickets.index', ['estado' => 'abierto']) }}" class="db-chip {{ ($filtros['estado'] ?? '') === 'abierto' ? 'active' : '' }}">
        <i class="fas fa-hourglass-half"></i> Pendientes
    </a>
    <a href="{{ route('tickets.index', ['estado' => 'en_progreso']) }}" class="db-chip {{ ($filtros['estado'] ?? '') === 'en_progreso' ? 'active' : '' }}">
        <i class="fas fa-sync-alt"></i> En atención
    </a>
    <a href="{{ route('tickets.index', ['estado' => 'resuelto']) }}" class="db-chip {{ ($filtros['estado'] ?? '') === 'resuelto' ? 'active' : '' }}">
        <i class="fas fa-check-circle"></i> Resueltos
    </a>
</div>

{{-- Filtros --}}
<form method="GET" action="{{ route('tickets.index') }}" class="db-card" style="margin-bottom: 1.25rem;">
    <div class="form-card-body" style="padding: 1rem 1.25rem;">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="form-group">
                <label class="form-label" for="q">Buscar</label>
                <input type="text" id="q" name="q" value="{{ $filtros['q'] ?? '' }}" placeholder="Buscar en mis tickets..." class="form-input">
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
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th>Técnico</th>
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
                        <td><span class="db-badge {{ $ticket->prioridad }}">{{ $ticket->label_prioridad }}</span></td>
                        <td><span class="db-badge {{ $ticket->estado }}">{{ $ticket->label_estado }}</span></td>
                        <td>
                            @if ($ticket->asignado)
                                <div class="flex items-center gap-2">
                                    <div class="db-avatar">{{ substr($ticket->asignado->name, 0, 1) }}</div>
                                    <span class="db-sub" style="color: var(--db-text)">{{ $ticket->asignado->name }}</span>
                                </div>
                            @else
                                <span class="db-sub">Pendiente de asignación</span>
                            @endif
                        </td>
                        <td class="db-sub">{{ $ticket->created_at->format('d/m/Y') }}</td>

                        {{-- ── ACCIONES (dropdown) ──────────────────────────── --}}
                        <td>
                            <div x-data="dropdownMenu()" class="inline-block">
                                <button type="button" x-ref="trigger" @click="toggle()" class="db-btn ghost" title="Acciones">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>

                                <template x-teleport="body">
                                    <div x-show="open" x-cloak @click.outside="open = false"
                                         :style="`top: ${top}px; left: ${left}px;`"
                                         class="db-dropdown-fixed">

                                        <a href="{{ route('tickets.show', $ticket) }}" class="db-dropdown-item">
                                            <i class="fas fa-eye"></i> Ver detalle
                                        </a>

                                        @can('tickets.editar')
                                            @if (Route::has('tickets.edit') && ! in_array($ticket->estado, ['cerrado','cancelado','resuelto']))
                                                <a href="{{ route('tickets.edit', $ticket) }}" class="db-dropdown-item">
                                                    <i class="fas fa-pen"></i> Editar
                                                </a>
                                            @endif
                                        @endcan

                                        @can('tickets.cancelar')
                                            @if ($ticket->puedeCancelar())
                                                <div class="db-dropdown-divider"></div>
                                                <form method="POST" action="{{ route('tickets.cancelar', $ticket) }}"
                                                      onsubmit="return confirm('¿Estás seguro de cancelar tu ticket #{{ $ticket->id }}?')">
                                                    @csrf
                                                    <button type="submit" class="db-dropdown-item">
                                                        <i class="fas fa-ban"></i> Cancelar ticket
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan

                                    </div>
                                </template>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="db-empty">
                                <i class="fas fa-ticket-alt"></i>
                                <p>
                                    @if (!empty($filtros))
                                        No hay tickets que coincidan con los filtros.
                                    @else
                                        Aún no has reportado tickets de soporte.
                                    @endif
                                </p>
                                @if (!empty($filtros))
                                    <a href="{{ route('tickets.index') }}" class="db-btn outline">Limpiar filtros</a>
                                @elseif ($tickets->total() === 0)
                                    @can('tickets.crear')
                                        <a href="{{ route('tickets.create') }}" class="db-btn solid">
                                            <i class="fas fa-plus"></i> Crear mi primer ticket
                                        </a>
                                    @endcan
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
