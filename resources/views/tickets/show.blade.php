{{-- resources/views/tickets/show.blade.php --}}
<x-app-layout>
    <x-slot name="title">Ticket #{{ $ticket->id }}</x-slot>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
        <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
    @endpush

    @php
        // ── Precómputo de permisos y estados ─────────────────────────────
        $user = auth()->user();
        $esAdmin = $user->hasAnyRole(['admin', 'super-admin']);
        $esTecnicoAsignado = $user->id === $ticket->asignado_a;
        $esSolicitante = $user->id === $ticket->solicitante_id;

        $puedeIniciar       = $user->can('tickets.iniciar')   && $ticket->puedeIniciar()   && $esTecnicoAsignado;
        $puedeResolver      = $user->can('tickets.resolver')  && $ticket->puedeResolver()  && $esTecnicoAsignado;
        $puedeAsignar       = $user->can('tickets.asignar')   && ! in_array($ticket->estado, ['cerrado','cancelado']);
        $puedeCerrar        = $user->can('tickets.cerrar')    && $ticket->puedeCerrar();
        $puedeCancelarT     = $user->can('tickets.cancelar')  && $ticket->puedeCancelar() && ($esAdmin || $esSolicitante);
        $puedeEditar        = $user->can('tickets.editar')    && Route::has('tickets.edit')
                              && ! in_array($ticket->estado, ['cerrado','cancelado','resuelto'])
                              && ($esAdmin || $esSolicitante);
        $puedeEliminar      = $user->can('tickets.eliminar');

        $hayAcciones = $puedeIniciar || $puedeResolver || $puedeAsignar
                       || $puedeCerrar || $puedeCancelarT || $puedeEditar;

        // ── Stepper: flujo lineal del ciclo de vida ──────────────────────
        $flujo = [
            ['key' => 'abierto',     'label' => 'Abierto',     'date' => $ticket->created_at],
            ['key' => 'asignado',    'label' => 'Asignado',    'date' => $ticket->asignado_at],
            ['key' => 'en_progreso', 'label' => 'En progreso', 'date' => $ticket->iniciado_at],
            ['key' => 'resuelto',    'label' => 'Resuelto',    'date' => $ticket->resuelto_at],
            ['key' => 'cerrado',     'label' => 'Cerrado',     'date' => $ticket->cerrado_at],
        ];
        $indices = ['abierto' => 0, 'asignado' => 1, 'en_progreso' => 2, 'resuelto' => 3, 'cerrado' => 4];
        $esCancelado = $ticket->estado === 'cancelado';

        if ($esCancelado) {
            // Si fue cancelado, determinar el último estado real alcanzado
            $indiceActual = match (true) {
                $ticket->iniciado_at !== null => 2, // en_progreso
                $ticket->asignado_at !== null => 1, // asignado
                default                       => 0, // abierto
            };
        } else {
            $indiceActual = $indices[$ticket->estado] ?? 0;
        }
    @endphp

    {{-- ── Cabecera ────────────────────────────────────────────────────── --}}
    <div class="db-header mb-5 flex items-start justify-between flex-wrap gap-3">
        <div style="min-width: 0; flex: 1;">
            <h4 style="display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;">
                <span style="color: var(--db-text-muted); font-weight: 500;">#{{ $ticket->id }}</span>
                <span>{{ $ticket->titulo }}</span>
            </h4>
            <p>
                Reportado por <strong style="color: var(--db-text);">{{ $ticket->solicitante->name }}</strong>
                · {{ $ticket->created_at->diffForHumans() }}
            </p>
        </div>
        <a href="{{ route('tickets.index') }}" class="db-btn ghost">
            <i class="fas fa-arrow-left"></i> Volver al listado
        </a>
    </div>

    {{-- Flash --}}
    @if (session('success'))
        <div class="form-alert success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if (session('error') || $errors->any())
        <div class="form-alert error">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') ?? $errors->first() }}
        </div>
    @endif

    {{-- ── Card del ciclo de vida (stepper + estado actual) ───────────── --}}
    <div class="db-card" style="margin-bottom: 1.25rem;">

        {{-- Estado actual destacado --}}
        <div class="db-current-state">
            <div class="db-current-state-info">
                <div class="db-current-state-label">Estado actual</div>
                <span class="db-badge {{ $ticket->estado }}" style="font-size: 0.8rem; padding: 0.35rem 0.9rem;">
                    {{ $ticket->label_estado }}
                </span>
                <span class="db-badge {{ $ticket->prioridad }}" style="font-size: 0.75rem; padding: 0.3rem 0.75rem;">
                    Prioridad {{ $ticket->label_prioridad }}
                </span>
            </div>
            <div class="db-sub" style="display: flex; align-items: center; gap: 0.4rem;">
                <i class="fas fa-tag"></i> {{ $ticket->label_categoria }}
            </div>
        </div>

        {{-- Banner si fue cancelado --}}
        @if ($esCancelado)
            <div class="db-cancel-banner" style="margin-top: 1rem;">
                <i class="fas fa-ban"></i>
                <div>
                    <strong>Ticket cancelado</strong> · {{ $ticket->updated_at->translatedFormat('d \\d\\e F, Y H:i') }}
                </div>
            </div>
        @endif

        {{-- Stepper --}}
        <div class="db-stepper">
            @foreach ($flujo as $i => $step)
                @php
                    if ($esCancelado) {
                        $clase = $i <= $indiceActual ? 'completed' : 'cancelled';
                    } else {
                        $clase = match (true) {
                            $i <  $indiceActual => 'completed',
                            $i === $indiceActual => 'current',
                            default              => 'pending',
                        };
                    }
                @endphp

                <div class="db-step {{ $clase }}">
                    <div class="db-step-circle">
                        @if ($clase === 'completed')
                            <i class="fas fa-check"></i>
                        @elseif ($clase === 'cancelled')
                            <i class="fas fa-times"></i>
                        @elseif ($clase === 'current')
                            <i class="fas fa-circle" style="font-size: 0.6rem;"></i>
                        @else
                            {{ $i + 1 }}
                        @endif
                    </div>
                    <div class="db-step-label">{{ $step['label'] }}</div>
                    <div class="db-step-date">
                        {{ $step['date']?->format('d/m H:i') ?? '—' }}
                    </div>
                </div>

                @if (! $loop->last)
                    <div class="db-step-connector {{ $i < $indiceActual ? 'completed' : '' }}"></div>
                @endif
            @endforeach
        </div>
    </div>

    {{-- ── Contenido: dos columnas ──────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- ── Columna principal ─────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Descripción del problema --}}
            <div class="db-card">
                <div class="db-card-header"><span>Descripción del problema</span></div>
                <div class="form-card-body">
                    <p style="color: var(--db-text); font-size: 0.88rem; line-height: 1.6; white-space: pre-wrap;">{{ $ticket->descripcion }}</p>
                </div>
            </div>

            {{-- Foto adjunta del solicitante --}}
            @if ($ticket->foto_solicitud)
                <div class="db-card">
                    <div class="db-card-header"><span>Imagen adjunta por el solicitante</span></div>
                    <div class="form-card-body">
                        <a href="{{ $ticket->foto_solicitud_url }}" target="_blank" rel="noopener">
                            <img src="{{ $ticket->foto_solicitud_url }}"
                                 alt="Foto del ticket"
                                 style="max-width: 100%; border-radius: 0.5rem; border: 1px solid var(--db-border);">
                        </a>
                    </div>
                </div>
            @endif

            {{-- Resolución (si ya fue resuelto) --}}
            @if ($ticket->notas_resolucion || $ticket->evidencia_resolucion)
                <div class="db-card">
                    <div class="db-card-header">
                        <span><i class="fas fa-check-circle" style="color: #4ade80;"></i> Resolución</span>
                    </div>
                    <div class="form-card-body">
                        @if ($ticket->notas_resolucion)
                            <p style="color: var(--db-text); font-size: 0.88rem; line-height: 1.6; white-space: pre-wrap;">{{ $ticket->notas_resolucion }}</p>
                        @endif

                        @if ($ticket->evidencia_resolucion)
                            <div style="margin-top: 1rem;">
                                <a href="{{ $ticket->evidencia_resolucion_url }}" target="_blank" rel="noopener">
                                    <img src="{{ $ticket->evidencia_resolucion_url }}"
                                         alt="Evidencia de resolución"
                                         style="max-width: 100%; border-radius: 0.5rem; border: 1px solid var(--db-border);">
                                </a>
                            </div>
                        @endif

                        @if ($ticket->resuelto_at && $ticket->asignado)
                            <p class="db-sub" style="margin-top: 0.75rem;">
                                Resuelto por <strong style="color: var(--db-text);">{{ $ticket->asignado->name }}</strong>
                                el {{ $ticket->resuelto_at->translatedFormat('d \\d\\e F, Y H:i') }}
                            </p>
                        @endif
                    </div>
                </div>
            @endif

        </div>

        {{-- ── Columna lateral ───────────────────────────────────────── --}}
        <div class="space-y-4">

            {{-- ===== ACCIONES DISPONIBLES ===== --}}
            <div class="db-card">
                <div class="db-card-header"><span><i class="fas fa-bolt"></i> Acciones</span></div>
                <div class="form-card-body" style="padding: 1rem 1.25rem;">

                    @if (! $hayAcciones && ! $puedeEliminar)
                        <p style="color: var(--db-text-muted); font-size: 0.8rem; text-align: center; padding: 0.5rem 0;">
                            No hay acciones disponibles en este estado.
                        </p>
                    @endif

                    {{-- 1) INICIAR (técnico asignado) ──────────────────── --}}
                    @if ($puedeIniciar)
                        <form method="POST" action="{{ route('tickets.iniciar', $ticket) }}" style="margin-bottom: 0.75rem;">
                            @csrf
                            <button type="submit" class="db-btn solid w-full">
                                <i class="fas fa-play"></i> Iniciar trabajo
                            </button>
                        </form>
                    @endif

                    {{-- 2) RESOLVER (técnico asignado, ticket en progreso) — inline form --}}
                    @if ($puedeResolver)
                        <div x-data="{ open: false }" style="margin-bottom: 0.75rem;">
                            <button type="button" @click="open = !open" class="db-btn solid w-full">
                                <i class="fas fa-check"></i>
                                <span x-text="open ? 'Cancelar' : 'Marcar como resuelto'"></span>
                            </button>

                            <div x-show="open" x-cloak x-collapse style="margin-top: 0.75rem;">
                                <form method="POST" action="{{ route('tickets.resolver', $ticket) }}"
                                      enctype="multipart/form-data">
                                    @csrf

                                    <div class="form-group" style="margin-bottom: 0.75rem;">
                                        <label class="form-label">
                                            Notas de resolución <span class="required">*</span>
                                        </label>
                                        <textarea name="notas_resolucion" rows="4" required minlength="10"
                                                  placeholder="¿Qué hiciste para resolver el problema?"
                                                  class="form-textarea">{{ old('notas_resolucion') }}</textarea>
                                    </div>

                                    <div class="form-group" style="margin-bottom: 0.75rem;" x-data="{ nombre: '' }">
                                        <label class="form-label">Evidencia (opcional)</label>
                                        <label class="form-file-wrap" style="padding: 0.5rem 0.75rem;">
                                            <div class="form-file-icon" style="width: 1.75rem; height: 1.75rem;">
                                                <i class="fas fa-camera" style="font-size: 0.7rem;"></i>
                                            </div>
                                            <div>
                                                <div class="form-file-text" x-text="nombre || 'Adjuntar imagen'"></div>
                                                <div class="form-file-hint">JPG, PNG — máx. 5MB</div>
                                            </div>
                                            <input type="file" name="evidencia_resolucion" accept="image/*"
                                                   @change="nombre = $event.target.files[0]?.name ?? ''">
                                        </label>
                                    </div>

                                    <button type="submit" class="db-btn solid w-full">
                                        <i class="fas fa-check-double"></i> Confirmar resolución
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif

                    {{-- 3) ASIGNAR / REASIGNAR (admin) ──────────────────── --}}
                    @if ($puedeAsignar)
                        <div style="margin-bottom: 0.75rem;">
                            <div class="form-label" style="margin-bottom: 0.4rem;">
                                {{ $ticket->asignado_a ? 'Reasignar técnico' : 'Asignar técnico' }}
                            </div>
                            <form method="POST" action="{{ route('tickets.asignar', $ticket) }}">
                                @csrf
                                <select name="tecnico_id"
                                        onchange="if(this.value && this.value != '{{ $ticket->asignado_a }}') this.form.submit()"
                                        class="form-select">
                                    <option value="">— Elegir técnico —</option>
                                    @foreach ($tecnicos as $tecnico)
                                        <option value="{{ $tecnico->id }}" @selected($ticket->asignado_a == $tecnico->id)>
                                            {{ $tecnico->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    @endif

                    {{-- 4) CERRAR (admin) ──────────────────────────────── --}}
                    @if ($puedeCerrar)
                        <form method="POST" action="{{ route('tickets.cerrar', $ticket) }}"
                              onsubmit="return confirm('¿Cerrar definitivamente el ticket #{{ $ticket->id }}?')"
                              style="margin-bottom: 0.75rem;">
                            @csrf
                            <button type="submit" class="db-btn outline w-full">
                                <i class="fas fa-lock"></i> Cerrar ticket
                            </button>
                        </form>
                    @endif

                    {{-- 5) EDITAR ──────────────────────────────────────── --}}
                    @if ($puedeEditar)
                        <a href="{{ route('tickets.edit', $ticket) }}" class="db-btn ghost w-full" style="margin-bottom: 0.75rem;">
                            <i class="fas fa-pen"></i> Editar ticket
                        </a>
                    @endif

                    {{-- 6) CANCELAR ────────────────────────────────────── --}}
                    @if ($puedeCancelarT)
                        <form method="POST" action="{{ route('tickets.cancelar', $ticket) }}"
                              onsubmit="return confirm('¿Cancelar el ticket #{{ $ticket->id }}?')"
                              style="margin-bottom: 0.75rem;">
                            @csrf
                            <button type="submit" class="db-btn ghost w-full">
                                <i class="fas fa-ban"></i> Cancelar ticket
                            </button>
                        </form>
                    @endif

                    {{-- 7) ELIMINAR (super-admin — zona peligrosa) ──────── --}}
                    @if ($puedeEliminar)
                        <div class="db-danger-zone">
                            <div class="db-danger-zone-label">
                                <i class="fas fa-exclamation-triangle"></i> Zona peligrosa
                            </div>
                            <form method="POST" action="{{ route('tickets.destroy', $ticket) }}"
                                  onsubmit="return confirm('¿Eliminar PERMANENTEMENTE el ticket #{{ $ticket->id }}? Esta acción no se puede deshacer.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="db-btn danger-outline w-full">
                                    <i class="fas fa-trash"></i> Eliminar ticket
                                </button>
                            </form>
                        </div>
                    @endif

                </div>
            </div>

            {{-- ===== PERSONAS ===== --}}
            <div class="db-card">
                <div class="db-card-header"><span>Personas</span></div>
                <div class="form-card-body" style="padding: 1rem 1.25rem;">

                    <div style="margin-bottom: 1rem;">
                        <div class="form-label" style="margin-bottom: 0.4rem;">Solicitante</div>
                        <div class="flex items-center gap-2">
                            <div class="db-avatar">{{ substr($ticket->solicitante->name, 0, 1) }}</div>
                            <div style="min-width: 0;">
                                <div style="color: var(--db-text); font-size: 0.85rem; font-weight: 600;">{{ $ticket->solicitante->name }}</div>
                                <div class="db-sub" style="overflow: hidden; text-overflow: ellipsis;">{{ $ticket->solicitante->email }}</div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="form-label" style="margin-bottom: 0.4rem;">Técnico asignado</div>
                        @if ($ticket->asignado)
                            <div class="flex items-center gap-2">
                                <div class="db-avatar">{{ substr($ticket->asignado->name, 0, 1) }}</div>
                                <div style="min-width: 0;">
                                    <div style="color: var(--db-text); font-size: 0.85rem; font-weight: 600;">{{ $ticket->asignado->name }}</div>
                                    <div class="db-sub" style="overflow: hidden; text-overflow: ellipsis;">{{ $ticket->asignado->email }}</div>
                                </div>
                            </div>
                        @else
                            <span class="db-badge cancelado">Sin asignar</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ===== UBICACIÓN ===== --}}
            @if ($ticket->empresa || $ticket->sede)
                <div class="db-card">
                    <div class="db-card-header"><span>Ubicación</span></div>
                    <div class="form-card-body" style="padding: 1rem 1.25rem;">
                        @if ($ticket->empresa)
                            <div style="margin-bottom: 0.75rem;">
                                <div class="form-label" style="margin-bottom: 0.3rem;">Empresa</div>
                                <div style="color: var(--db-text); font-size: 0.85rem;">{{ $ticket->empresa->nombre }}</div>
                            </div>
                        @endif

                        @if ($ticket->sede)
                            <div>
                                <div class="form-label" style="margin-bottom: 0.3rem;">Sede</div>
                                <div style="color: var(--db-text); font-size: 0.85rem;">{{ $ticket->sede->nombre }}</div>
                                @if ($ticket->sede->direccion)
                                    <div class="db-sub">{{ $ticket->sede->direccion }}</div>
                                @endif
                                @if ($ticket->sede->ciudad)
                                    <div class="db-sub">{{ $ticket->sede->ciudad }}</div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>