{{-- resources/views/tickets/partials/index_admin.blade.php --}}

<div class="db-header mb-5 flex items-center justify-between flex-wrap gap-3">
    <div>
        <h4>Todos los tickets</h4>
        <p>{{ $tickets->total() }} resultado(s) — vista global del sistema</p>
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
    <a href="{{ route('tickets.index', ['sin_asignar' => 1]) }}" class="db-chip {{ !empty($filtros['sin_asignar']) ? 'active' : '' }}">
        <i class="fas fa-inbox"></i> Sin asignar
    </a>
    <a href="{{ route('tickets.index', ['prioridad' => 'critica']) }}" class="db-chip {{ ($filtros['prioridad'] ?? '') === 'critica' ? 'active' : '' }}">
        <i class="fas fa-exclamation-circle"></i> Críticas
    </a>
    <a href="{{ route('tickets.index', ['estado' => 'en_progreso']) }}" class="db-chip {{ ($filtros['estado'] ?? '') === 'en_progreso' ? 'active' : '' }}">
        <i class="fas fa-sync-alt"></i> En progreso
    </a>
</div>

{{-- Filtros --}}
<form method="GET" action="{{ route('tickets.index') }}" class="db-card" style="margin-bottom: 1.25rem;">
    <div class="form-card-body" style="padding: 1rem 1.25rem;">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
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
            <div class="form-group">
                <label class="form-label" for="empresa_id">Empresa</label>
                <select id="empresa_id" name="empresa_id" class="form-select">
                    <option value="">Todas</option>
                    @foreach ($empresas as $empresa)
                        <option value="{{ $empresa->id }}" @selected(($filtros['empresa_id'] ?? '') == $empresa->id)>{{ $empresa->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="flex items-center justify-between flex-wrap gap-2 mt-3">
            <label class="flex items-center gap-2 text-xs" style="color: var(--db-text);">
                <input type="checkbox" name="sin_asignar" value="1" @checked(!empty($filtros['sin_asignar']))>
                <span>Solo sin asignar</span>
            </label>
            <div class="flex gap-2">
                @if (!empty($filtros))
                    <a href="{{ route('tickets.index') }}" class="db-btn ghost"><i class="fas fa-times"></i> Limpiar</a>
                @endif
                <button type="submit" class="db-btn outline"><i class="fas fa-filter"></i> Filtrar</button>
            </div>
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
                    <th>Empresa</th>
                    <th>Asignado</th>
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
                        <td>
                            @if ($ticket->asignado)
                                <div class="flex items-center gap-2">
                                    <div class="db-avatar">{{ substr($ticket->asignado->name, 0, 1) }}</div>
                                    <span class="db-sub" style="color: var(--db-text)">{{ $ticket->asignado->name }}</span>
                                </div>
                            @else
                                <span class="db-badge cancelado">Sin asignar</span>
                            @endif
                        </td>
                        <td><span class="db-badge {{ $ticket->prioridad }}">{{ $ticket->label_prioridad }}</span></td>
                        <td><span class="db-badge {{ $ticket->estado }}">{{ $ticket->label_estado }}</span></td>
                        <td class="db-sub">{{ $ticket->created_at->format('d/m/Y') }}</td>

                        {{-- ── ACCIONES ─────────────────────────────────────── --}}
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
                                            @if (Route::has('tickets.edit') && ! in_array($ticket->estado, ['cerrado','cancelado']))
                                                <a href="{{ route('tickets.edit', $ticket) }}" class="db-dropdown-item">
                                                    <i class="fas fa-pen"></i> Editar
                                                </a>
                                            @endif
                                        @endcan

                                        @can('tickets.asignar')
                                            @if (! in_array($ticket->estado, ['cerrado','cancelado']))
                                                @php
                                                    // Filtrado por cobertura empresa+sede via empresa_user
                                                    $tecnicosDisponibles = $ticket->tecnicosDisponibles();
                                                @endphp
                                                <div class="db-dropdown-divider"></div>
                                                <div class="db-dropdown-label">
                                                    @if ($tecnicosDisponibles->isEmpty())
                                                        Sin técnicos con cobertura
                                                    @else
                                                        Asignar técnico
                                                    @endif
                                                </div>

                                                @if ($tecnicosDisponibles->isNotEmpty())
                                                    <form method="POST" action="{{ route('tickets.asignar', $ticket) }}" class="px-1">
                                                        @csrf
                                                        <select name="tecnico_id"
                                                                onchange="if(this.value && this.value != '{{ $ticket->asignado_a }}') this.form.submit()"
                                                                class="form-select"
                                                                style="font-size: 0.75rem; padding: 0.35rem 0.5rem;">
                                                            <option value="">— Elegir —</option>
                                                            @foreach ($tecnicosDisponibles as $t)
                                                                <option value="{{ $t->id }}" @selected($ticket->asignado_a == $t->id)>
                                                                    {{ $t->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </form>
                                                @else
                                                    <div style="padding: 0.4rem 0.75rem; font-size: 0.72rem; color: var(--db-text-muted);">
                                                        Asigna técnicos a esta empresa/sede primero.
                                                    </div>
                                                @endif
                                            @endif
                                        @endcan

                                        @can('tickets.cancelar')
                                            @if ($ticket->puedeCancelar())
                                                <div class="db-dropdown-divider"></div>
                                                <form method="POST" action="{{ route('tickets.cancelar', $ticket) }}"
                                                      onsubmit="return confirm('¿Cancelar el ticket #{{ $ticket->id }}?')">
                                                    @csrf
                                                    <button type="submit" class="db-dropdown-item">
                                                        <i class="fas fa-ban"></i> Cancelar ticket
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan

                                        @can('tickets.eliminar')
                                            <div class="db-dropdown-divider"></div>
                                            <form method="POST" action="{{ route('tickets.destroy', $ticket) }}"
                                                  onsubmit="return confirm('¿Eliminar PERMANENTEMENTE el ticket #{{ $ticket->id }}? Esta acción no se puede deshacer.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="db-dropdown-item danger">
                                                    <i class="fas fa-trash"></i> Eliminar
                                                </button>
                                            </form>
                                        @endcan

                                    </div>
                                </template>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9">
                            <div class="db-empty">
                                <i class="fas fa-inbox"></i>
                                <p>
                                    @if (!empty($filtros))
                                        No hay tickets que coincidan con los filtros aplicados.
                                    @else
                                        No hay tickets en el sistema todavía.
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
