{{-- resources/views/equipos/index.blade.php --}}
<x-app-layout>
    <x-slot name="title">Equipos</x-slot>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
        <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
    @endpush

    {{-- Cabecera --}}
    <div class="db-header mb-5 flex items-center justify-between flex-wrap gap-3">
        <div>
            <h4>Inventario de equipos</h4>
            <p>{{ $equipos->total() }} equipo(s) registrado(s)</p>
        </div>
        @can('equipos.crear')
            <a href="{{ route('equipos.create') }}" class="db-btn solid">
                <i class="fas fa-plus"></i> Registrar equipo
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="form-alert success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="form-alert error">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Chips --}}
    <div class="flex flex-wrap items-center gap-2 mb-4">
        <a href="{{ route('equipos.index') }}" class="db-chip {{ empty($filtros) ? 'active' : '' }}">
            <i class="fas fa-list"></i> Todos
        </a>
        @foreach (\App\Models\Equipo::CATEGORIAS as $key => $label)
            <a href="{{ route('equipos.index', ['categoria' => $key]) }}"
               class="db-chip {{ ($filtros['categoria'] ?? '') === $key ? 'active' : '' }}">
                <i class="fas {{ ['computadora' => 'fa-desktop', 'portatil' => 'fa-laptop', 'impresora' => 'fa-print'][$key] ?? 'fa-cube' }}"></i>
                {{ $label }}
            </a>
        @endforeach
        <a href="{{ route('equipos.index', ['estado' => 'mantenimiento']) }}"
           class="db-chip {{ ($filtros['estado'] ?? '') === 'mantenimiento' ? 'active' : '' }}">
            <i class="fas fa-wrench"></i> En mantenimiento
        </a>
        <a href="{{ route('equipos.index', ['estado' => 'dado_de_baja']) }}"
           class="db-chip {{ ($filtros['estado'] ?? '') === 'dado_de_baja' ? 'active' : '' }}">
            <i class="fas fa-ban"></i> Dados de baja
        </a>
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('equipos.index') }}" class="db-card" style="margin-bottom: 1.25rem;">
        <div class="form-card-body" style="padding: 1rem 1.25rem;">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">

                <div class="form-group">
                    <label class="form-label" for="q">Buscar</label>
                    <input type="text" id="q" name="q" value="{{ $filtros['q'] ?? '' }}"
                           placeholder="Nombre, marca, modelo, serie..." class="form-input">
                </div>

                <div class="form-group">
                    <label class="form-label" for="categoria">Categoría</label>
                    <select id="categoria" name="categoria" class="form-select">
                        <option value="">Todas</option>
                        @foreach (\App\Models\Equipo::CATEGORIAS as $key => $label)
                            <option value="{{ $key }}" @selected(($filtros['categoria'] ?? '') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="estado">Estado</label>
                    <select id="estado" name="estado" class="form-select">
                        <option value="">Todos</option>
                        @foreach (\App\Models\Equipo::ESTADOS as $key => $label)
                            <option value="{{ $key }}" @selected(($filtros['estado'] ?? '') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="empresa_id">Empresa</label>
                    <select id="empresa_id" name="empresa_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach ($empresas as $empresa)
                            <option value="{{ $empresa->id }}" @selected(($filtros['empresa_id'] ?? '') == $empresa->id)>
                                {{ $empresa->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>

            <div class="flex items-center justify-end gap-2 mt-3">
                @if (!empty($filtros))
                    <a href="{{ route('equipos.index') }}" class="db-btn ghost"><i class="fas fa-times"></i> Limpiar</a>
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
                        <th>Equipo</th>
                        <th>Categoría</th>
                        <th>Empresa / Sede</th>
                        <th>Responsable</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($equipos as $equipo)
                        @php
                            $puedeGestionar = $equipo->puedeSerGestionadoPor(auth()->user());
                            $puedeEliminar  = auth()->user()->can('equipos.eliminar') && ! $equipo->tieneHistorial();
                        @endphp
                        <tr>
                            <td>
                                <div style="color: var(--db-text-bright); font-weight: 600; font-size: 0.85rem;">
                                    {{ $equipo->nombre }}
                                </div>
                                @if ($equipo->marca || $equipo->modelo)
                                    <div class="db-sub">{{ trim(($equipo->marca ?? '') . ' ' . ($equipo->modelo ?? '')) }}</div>
                                @endif
                                @if ($equipo->numero_serie)
                                    <div class="db-sub" style="font-family: monospace;">S/N: {{ $equipo->numero_serie }}</div>
                                @endif
                            </td>
                            <td class="db-sub" style="color: var(--db-text)">
                                {{ $equipo->label_categoria }}
                            </td>
                            <td class="db-sub" style="color: var(--db-text)">
                                {{ $equipo->empresa->nombre }}
                                @if ($equipo->sede)
                                    <div class="db-sub">{{ $equipo->sede->nombre }}</div>
                                @endif
                                @if ($equipo->ubicacion)
                                    <div class="db-sub"><i class="fas fa-map-marker-alt"></i> {{ $equipo->ubicacion }}</div>
                                @endif
                            </td>
                            <td>
                                @if ($equipo->responsable)
                                    <div class="flex items-center gap-2">
                                        <div class="db-avatar">{{ substr($equipo->responsable->name, 0, 1) }}</div>
                                        <span class="db-sub" style="color: var(--db-text)">{{ $equipo->responsable->name }}</span>
                                    </div>
                                @else
                                    <span class="db-sub">Sin responsable</span>
                                @endif
                            </td>
                            <td>
                                <span class="db-badge {{ $equipo->color_estado }}">{{ $equipo->label_estado }}</span>
                            </td>

                            {{-- ── ACCIONES ─────────────────────────────────── --}}
                            <td>
                                <div x-data="dropdownMenu()" class="inline-block">
                                    <button type="button" x-ref="trigger" @click="toggle()" class="db-btn ghost" title="Acciones">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>

                                    <template x-teleport="body">
                                        <div x-show="open" x-cloak @click.outside="open = false"
                                             :style="`top: ${top}px; left: ${left}px;`"
                                             class="db-dropdown-fixed">

                                            @if (Route::has('equipos.show'))
                                                <a href="{{ route('equipos.show', $equipo) }}" class="db-dropdown-item">
                                                    <i class="fas fa-eye"></i> Ver detalle
                                                </a>
                                                <div class="db-dropdown-divider"></div>
                                            @endif

                                            @can('equipos.editar')
                                                <a href="{{ route('mpreventivo.create', $equipo) }}" class="db-dropdown-item">
                                                    <i class="fas fa-pen"></i> Mantenimiento preventivo
                                                </a>
                                            @endcan

                                            @can('equipos.editar')
                                                @if ($puedeGestionar)
                                                    <a href="{{ route('equipos.edit', $equipo) }}" class="db-dropdown-item">
                                                        <i class="fas fa-pen"></i> Editar
                                                    </a>

                                                    {{-- Toggle estado: dar de baja / reactivar --}}
                                                    @if ($equipo->estado !== 'dado_de_baja')
                                                        <form method="POST" action="{{ route('equipos.cambiar-estado', $equipo) }}"
                                                              onsubmit="return confirm('¿Dar de baja el equipo «{{ $equipo->nombre }}»?')">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="estado" value="dado_de_baja">
                                                            <button type="submit" class="db-dropdown-item">
                                                                <i class="fas fa-ban"></i> Dar de baja
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form method="POST" action="{{ route('equipos.cambiar-estado', $equipo) }}">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="estado" value="activo">
                                                            <button type="submit" class="db-dropdown-item">
                                                                <i class="fas fa-power-off"></i> Reactivar
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif
                                            @endcan

                                            @can('equipos.eliminar')
                                                <div class="db-dropdown-divider"></div>
                                                @if ($puedeEliminar)
                                                    <form method="POST" action="{{ route('equipos.destroy', $equipo) }}"
                                                          onsubmit="return confirm('¿Eliminar PERMANENTEMENTE el equipo «{{ $equipo->nombre }}»? Esta acción no se puede deshacer.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="db-dropdown-item danger">
                                                            <i class="fas fa-trash"></i> Eliminar
                                                        </button>
                                                    </form>
                                                @else
                                                    <div style="padding: 0.4rem 0.75rem; font-size: 0.72rem; color: var(--db-text-muted);">
                                                        <i class="fas fa-lock"></i> No se puede eliminar:
                                                        tiene historial. Inhabilítalo en su lugar.
                                                    </div>
                                                @endif
                                            @endcan

                                        </div>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="db-empty">
                                    <i class="fas fa-desktop"></i>
                                    <p>
                                        @if (!empty($filtros))
                                            No hay equipos que coincidan con los filtros.
                                        @else
                                            Aún no hay equipos registrados.
                                        @endif
                                    </p>
                                    @if (!empty($filtros))
                                        <a href="{{ route('equipos.index') }}" class="db-btn outline">Limpiar filtros</a>
                                    @elseif ($equipos->total() === 0)
                                        @can('equipos.crear')
                                            <a href="{{ route('equipos.create') }}" class="db-btn solid">
                                                <i class="fas fa-plus"></i> Registrar primer equipo
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

        @if ($equipos->hasPages())
            <div class="px-4 py-3 border-t" style="border-color: var(--db-border)">
                {{ $equipos->onEachSide(1)->links() }}
            </div>
        @endif
    </div>

    {{-- Helper de dropdown reutilizable --}}
    @push('scripts')
        @once
        <script>
            function dropdownMenu() {
                return {
                    open: false, top: 0, left: 0,
                    toggle() {
                        this.open = !this.open;
                        if (this.open) {
                            this.$nextTick(() => {
                                const rect = this.$refs.trigger.getBoundingClientRect();
                                const ancho = 240, margen = 8;
                                let left = rect.right - ancho;
                                if (left < margen) left = rect.left;
                                this.top = rect.bottom + 4;
                                this.left = left;
                            });
                        }
                    },
                };
            }
        </script>
        @endonce
    @endpush
</x-app-layout>
