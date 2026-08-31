{{-- resources/views/tickets/partials/index_tecnico.blade.php --}}

{{-- ── Cabecera y Contexto ────────────────────────────────────────────── --}}
<div class="mb-4 flex items-center justify-between flex-wrap gap-3 pb-3 border-b border-[#2d2d2d]">
    <div>
        <h2 class="text-lg font-black text-white uppercase tracking-tight flex items-center gap-2">
            <i class="fas fa-ticket-alt text-[#007acc] text-sm"></i> Mis Tickets Asignados
        </h2>
        <p class="text-[11px] text-gray-400 font-medium">
            {{ $tickets->total() }} resultado(s) · Gestión directa de incidentes bajo tu responsabilidad
        </p>
    </div>
</div>

{{-- ── Chips de Filtro Rápido ────────────────────────────────────────── --}}
<div class="flex flex-wrap items-center gap-1.5 mb-3">
    <a href="{{ route('tickets.index') }}" class="db-chip {{ empty($filtros) ? 'active' : '' }}">
        <i class="fas fa-list"></i> Todos los Míos
    </a>
    <a href="{{ route('tickets.index', ['estado' => 'asignado']) }}" class="db-chip {{ ($filtros['estado'] ?? '') === 'asignado' ? 'active' : '' }}">
        <i class="fas fa-clock text-sky-400"></i> Por Iniciar
    </a>
    <a href="{{ route('tickets.index', ['estado' => 'en_progreso']) }}" class="db-chip {{ ($filtros['estado'] ?? '') === 'en_progreso' ? 'active' : '' }}">
        <i class="fas fa-sync-alt text-amber-400"></i> En Progreso
    </a>
    <a href="{{ route('tickets.index', ['estado' => 'resuelto']) }}" class="db-chip {{ ($filtros['estado'] ?? '') === 'resuelto' ? 'active' : '' }}">
        <i class="fas fa-check text-emerald-400"></i> Resueltos
    </a>
    <a href="{{ route('tickets.index', ['prioridad' => 'critica']) }}" class="db-chip {{ ($filtros['prioridad'] ?? '') === 'critica' ? 'active' : '' }}">
        <i class="fas fa-fire text-red-400"></i> Críticas
    </a>
</div>

{{-- ── Formulario de Búsqueda y Filtros ──────────────────────────────── --}}
<form method="GET" action="{{ route('tickets.index') }}" class="db-card mb-4">
    <div class="p-3">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5">
            {{-- Búsqueda --}}
            <div class="form-group">
                <label class="form-label text-[10px]" for="q">Buscar</label>
                <input type="text" id="q" name="q" value="{{ $filtros['q'] ?? '' }}" 
                       placeholder="Título o ID..." class="form-input text-xs !py-1">
            </div>

            {{-- Estado --}}
            <div class="form-group">
                <label class="form-label text-[10px]" for="estado">Estado</label>
                <select id="estado" name="estado" class="form-select text-xs !py-1">
                    <option value="">Todos</option>
                    @foreach (\App\Models\Ticket::ESTADOS as $key => $label)
                        <option value="{{ $key }}" @selected(($filtros['estado'] ?? '') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Prioridad --}}
            <div class="form-group">
                <label class="form-label text-[10px]" for="prioridad">Prioridad</label>
                <select id="prioridad" name="prioridad" class="form-select text-xs !py-1">
                    <option value="">Todas</option>
                    @foreach (\App\Models\Ticket::PRIORIDADES as $key => $label)
                        <option value="{{ $key }}" @selected(($filtros['prioridad'] ?? '') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Categoría --}}
            <div class="form-group">
                <label class="form-label text-[10px]" for="categoria">Categoría</label>
                <select id="categoria" name="categoria" class="form-select text-xs !py-1">
                    <option value="">Todas</option>
                    @foreach (\App\Models\Ticket::CATEGORIAS as $key => $label)
                        <option value="{{ $key }}" @selected(($filtros['categoria'] ?? '') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex items-center justify-end gap-1.5 mt-2.5 pt-2 border-t border-[#2d2d2d]">
            @if (!empty($filtros))
                <a href="{{ route('tickets.index') }}" class="db-btn ghost text-[11px]">
                    <i class="fas fa-times"></i> Limpiar
                </a>
            @endif
            <button type="submit" class="db-btn outline text-[11px]">
                <i class="fas fa-filter"></i> Filtrar
            </button>
        </div>
    </div>
</form>

{{-- ── Tabla de Datos Fina y Compacta ───────────────────────────────── --}}
<div class="db-card">
    <div class="overflow-x-auto">
        <table class="db-table">
            <thead>
                <tr>
                    <th class="w-12">#</th>
                    <th>Título / Categoría</th>
                    <th>Solicitante</th>
                    <th>Empresa / Sede</th>
                    <th class="text-center w-24">Prioridad</th>
                    <th class="text-center w-28">Estado</th>
                    <th class="w-24">Fecha</th>
                    <th class="w-20 text-center">Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tickets as $ticket)
                    <tr>
                        {{-- ID --}}
                        <td class="font-mono text-gray-500 text-[11px]">#{{ $ticket->id }}</td>

                        {{-- Título --}}
                        <td>
                            <a href="{{ route('tickets.show', $ticket) }}" class="font-bold text-white hover:text-[#007acc] transition-colors block text-xs truncate max-w-[240px]">
                                {{ $ticket->titulo }}
                            </a>
                            <span class="text-[10px] text-gray-500 block">
                                <i class="fas {{ $ticket->icono_categoria }} text-[9px] mr-1"></i>{{ $ticket->label_categoria }}
                            </span>
                        </td>

                        {{-- Solicitante --}}
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="w-5 h-5 rounded bg-[#007acc]/20 text-[#007acc] font-bold text-[10px] flex items-center justify-center shrink-0">
                                    {{ substr($ticket->solicitante?->name ?? 'U', 0, 1) }}
                                </div>
                                <span class="text-xs text-gray-300 truncate max-w-[120px]">
                                    {{ $ticket->solicitante?->name ?? 'Usuario Eliminado' }}
                                </span>
                            </div>
                        </td>

                        {{-- Empresa / Sede --}}
                        <td>
                            <div class="text-xs text-gray-300 truncate max-w-[130px]">
                                {{ $ticket->empresa?->nombre ?? '—' }}
                            </div>
                            @if ($ticket->sede)
                                <span class="text-[10px] text-gray-500 block truncate max-w-[130px]">
                                    <i class="fas fa-map-marker-alt text-[9px] mr-1"></i>{{ $ticket->sede->nombre }}
                                </span>
                            @endif
                        </td>

                        {{-- Prioridad --}}
                        <td class="text-center">
                            <span class="db-badge {{ $ticket->prioridad }}">{{ $ticket->label_prioridad }}</span>
                        </td>

                        {{-- Estado --}}
                        <td class="text-center">
                            <span class="db-badge {{ $ticket->estado }}">{{ $ticket->label_estado }}</span>
                        </td>

                        {{-- Fecha --}}
                        <td class="text-[11px] text-gray-400 font-mono">
                            {{ $ticket->created_at->format('d/m/Y') }}
                        </td>

                        {{-- Acciones Directas Inline --}}
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('tickets.show', $ticket) }}" class="db-btn ghost !p-1" title="Ver detalle">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>

                                {{-- Botón rápido para iniciar atención --}}
                                @can('tickets.iniciar')
                                    @if ($ticket->puedeIniciar() && $ticket->asignado_a === auth()->id())
                                        <form method="POST" action="{{ route('tickets.iniciar', $ticket) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="db-btn solid !p-1" title="Iniciar atención">
                                                <i class="fas fa-play text-[10px]"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-8">
                            <div class="text-gray-500">
                                <i class="fas fa-check-double text-2xl mb-1 block text-emerald-500/60"></i>
                                <span class="text-xs">
                                    @if (!empty($filtros))
                                        No hay tickets que coincidan con los filtros.
                                    @else
                                        No tienes tickets asignados activos — ¡todo al día!
                                    @endif
                                </span>
                                @if (!empty($filtros))
                                    <div class="mt-2">
                                        <a href="{{ route('tickets.index') }}" class="db-btn outline text-[11px]">Limpiar filtros</a>
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    @if ($tickets->hasPages())
        <div class="px-3 py-2 border-t border-[#2d2d2d]">
            {{ $tickets->onEachSide(1)->links() }}
        </div>
    @endif
</div>