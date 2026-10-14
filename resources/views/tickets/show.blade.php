{{-- resources/views/tickets/show.blade.php --}}
<x-app-layout>
    <x-slot name="title">Ticket #{{ $ticket->id }}</x-slot>

    @push('styles')
        <style>
            .pulse-neon {
                box-shadow: 0 0 10px rgba(60, 80, 224, 0.4), inset 0 0 10px rgba(60, 80, 224, 0.2);
                animation: pulse-border 2s infinite;
            }
            @keyframes pulse-border {
                0% { box-shadow: 0 0 5px rgba(60, 80, 224, 0.2); }
                50% { box-shadow: 0 0 15px rgba(60, 80, 224, 0.6); }
                100% { box-shadow: 0 0 5px rgba(60, 80, 224, 0.2); }
            }
            .stepper-line {
                transition: width 1s ease-in-out;
            }
        </style>
    @endpush

    @php
        // ── Precómputo de permisos y estados ─────────────────────────────
        $user = auth()->user();
        $esAdmin = $user->hasAnyRole(['admin', 'super-admin']);
        $esTecnicoAsignado = $user->id === $ticket->asignado_a;
        $esSolicitante = $user->id === $ticket->solicitante_id;

        $puedeIniciar       = $user->can('tickets.iniciar')   && $ticket->puedeIniciar()   && $esTecnicoAsignado;
        $puedeResolver      = $user->can('tickets.resolver')  && $ticket->puedeResolver()  && $esTecnicoAsignado;
        $puedeSolicitarPartes = $user->can('tickets.resolver') && $ticket->puedeSolicitarPartes() && $esTecnicoAsignado;
        $puedeRecibirPartes   = $user->can('tickets.resolver') && $ticket->puedeRecibirPartes()   && $esTecnicoAsignado;
        $puedeAsignar       = $user->can('tickets.asignar')   && ! in_array($ticket->estado, ['cerrado','cancelado']);
        $puedeCerrar        = $user->can('tickets.cerrar')    && $ticket->puedeCerrar();
        $puedeCancelarT     = $user->can('tickets.cancelar')  && $ticket->puedeCancelar() && ($esAdmin || $esSolicitante);
        $puedeEditar        = $user->can('tickets.editar')    && Route::has('tickets.edit')
                              && ! in_array($ticket->estado, ['cerrado','cancelado','resuelto'])
                              && ($esAdmin || $esSolicitante);
        $puedeEliminar      = $user->can('tickets.eliminar');

        $hayAcciones = $puedeIniciar || $puedeResolver || $puedeSolicitarPartes || $puedeRecibirPartes
                       || $puedeAsignar || $puedeCerrar || $puedeCancelarT || $puedeEditar;

        // ── Stepper: flujo lineal del ciclo de vida ──────────────────────
        // La etapa de partes solo aparece si el ticket la usó
        $ultimaParte = $ticket->solicitudesPartes->first();
        $flujo = array_values(array_filter([
            ['key' => 'abierto',     'label' => 'Abierto',     'date' => $ticket->created_at],
            ['key' => 'asignado',    'label' => 'Asignado',    'date' => $ticket->asignado_at],
            ['key' => 'en_progreso', 'label' => 'En progreso', 'date' => $ticket->iniciado_at],
            $ultimaParte ? [
                'key'   => 'esperando_partes',
                'label' => $ultimaParte->pendiente ? 'Esperando partes' : 'Partes recibidas',
                'date'  => $ultimaParte->recibido_at ?? $ultimaParte->created_at,
            ] : null,
            ['key' => 'resuelto',    'label' => 'Resuelto',    'date' => $ticket->resuelto_at],
            ['key' => 'cerrado',     'label' => 'Cerrado',     'date' => $ticket->cerrado_at],
        ]));
        $indices = array_flip(array_column($flujo, 'key'));
        $esCancelado = $ticket->estado === 'cancelado';

        if ($esCancelado) {
            $indiceActual = match (true) {
                $ultimaParte !== null         => $indices['esperando_partes'],
                $ticket->iniciado_at !== null => $indices['en_progreso'],
                $ticket->asignado_at !== null => $indices['asignado'],
                default                       => 0,
            };
        } elseif ($ticket->estado === 'en_progreso' && $ultimaParte) {
            // Retomado después de recibir partes
            $indiceActual = $indices['esperando_partes'];
        } else {
            $indiceActual = $indices[$ticket->estado] ?? 0;
        }

        $colorEstado = match($ticket->estado) {
            'resuelto' => 'bg-meta-3/10 text-meta-3 border-meta-3/20',
            'en_progreso', 'esperando_partes' => 'bg-meta-6/10 text-meta-6 border-meta-6/20',
            'cerrado','cancelado' => 'bg-meta-1/10 text-meta-1 border-meta-1/20',
            default => 'bg-primary/10 text-primary border-primary/20',
        };
        $colorPrio = match($ticket->prioridad) {
            'critica' => 'bg-meta-1/10 text-meta-1 border-meta-1/20',
            'alta' => 'bg-meta-6/10 text-meta-6 border-meta-6/20',
            default => 'bg-primary/10 text-primary border-primary/20',
        };
    @endphp

    {{-- Flash Alerts Compactos --}}
    @if (session('success'))
        <div class="mb-3 flex w-full border-l-4 border-meta-3 bg-meta-3/10 px-3 py-2 shadow-sm rounded-sm">
            <i class="fas fa-check-circle text-meta-3 mr-2 mt-0.5 text-xs"></i>
            <p class="text-xs text-meta-3 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error') || $errors->any())
        <div class="mb-3 flex w-full border-l-4 border-meta-1 bg-meta-1/10 px-3 py-2 shadow-sm rounded-sm">
            <i class="fas fa-exclamation-triangle text-meta-1 mr-2 mt-0.5 text-xs"></i>
            <p class="text-xs text-meta-1 font-medium">{{ session('error') ?? $errors->first() }}</p>
        </div>
    @endif

    {{-- ── Cabecera Compacta & Acciones ────────────────────────────────────── --}}
    <div class="mb-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-stroke pb-2.5 dark:border-strokedark">
        <div class="flex items-center gap-2.5 flex-wrap min-w-0">
            <span class="font-mono text-xs font-bold text-primary bg-primary/10 px-2 py-0.5 rounded border border-primary/20">
                #{{ $ticket->id }}
            </span>

            <h2 class="text-title-md font-bold text-black dark:text-white truncate" title="{{ $ticket->titulo }}">
                {{ $ticket->titulo }}
            </h2>

            <span class="inline-flex rounded-full px-2 py-0.5 text-[9px] font-bold uppercase border {{ $colorEstado }}">
                {{ $ticket->label_estado }}
            </span>
            <span class="inline-flex rounded-full px-2 py-0.5 text-[9px] font-bold uppercase border {{ $colorPrio }}">
                PRIORIDAD {{ $ticket->label_prioridad }}
            </span>
        </div>

        <a href="{{ route('tickets.index') }}" 
           class="inline-flex items-center gap-1 rounded border border-stroke py-1.5 px-3 text-xs font-bold text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition shadow-sm shrink-0">
            <i class="fas fa-arrow-left text-[10px]"></i> Volver
        </a>
    </div>

    {{-- ── Strip de Métricas Rápidas (4 KPIs) ─────────────────────── --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-3">
        <div class="rounded-sm border border-stroke bg-white p-2 shadow-default dark:border-strokedark dark:bg-boxdark flex items-center gap-2">
            <div class="flex h-7 w-7 items-center justify-center rounded bg-primary/10 text-primary shrink-0">
                <i class="fas {{ $ticket->icono_categoria }} text-xs"></i>
            </div>
            <div class="min-w-0">
                <span class="text-[8px] font-bold text-bodydark2 uppercase block">Categoría</span>
                <span class="text-[11px] font-bold text-black dark:text-white truncate block">
                    {{ $ticket->label_categoria }}
                </span>
            </div>
        </div>

        <div class="rounded-sm border border-stroke bg-white p-2 shadow-default dark:border-strokedark dark:bg-boxdark flex items-center gap-2">
            <div class="flex h-7 w-7 items-center justify-center rounded bg-meta-6/10 text-meta-6 shrink-0">
                <i class="fas fa-user text-xs"></i>
            </div>
            <div class="min-w-0">
                <span class="text-[8px] font-bold text-bodydark2 uppercase block">Solicitante</span>
                <span class="text-[11px] font-bold text-black dark:text-white truncate block" title="{{ $ticket->solicitante?->name }}">
                    {{ $ticket->solicitante?->name ?? 'Usuario Eliminado' }}
                </span>
            </div>
        </div>

        <div class="rounded-sm border border-stroke bg-white p-2 shadow-default dark:border-strokedark dark:bg-boxdark flex items-center gap-2">
            <div class="flex h-7 w-7 items-center justify-center rounded bg-meta-3/10 text-meta-3 shrink-0">
                <i class="fas fa-headset text-xs"></i>
            </div>
            <div class="min-w-0">
                <span class="text-[8px] font-bold text-bodydark2 uppercase block">Técnico Asignado</span>
                <span class="text-[11px] font-bold text-black dark:text-white truncate block" title="{{ $ticket->asignado?->name }}">
                    {{ $ticket->asignado?->name ?? 'Por Asignar' }}
                </span>
            </div>
        </div>

        <div class="rounded-sm border border-stroke bg-white p-2 shadow-default dark:border-strokedark dark:bg-boxdark flex items-center gap-2">
            <div class="flex h-7 w-7 items-center justify-center rounded bg-gray-2 text-bodydark2 dark:bg-meta-4 shrink-0">
                <i class="fas fa-clock text-xs"></i>
            </div>
            <div class="min-w-0">
                <span class="text-[8px] font-bold text-bodydark2 uppercase block">Tiempo Transcurrido</span>
                <span class="text-[11px] font-bold font-mono text-black dark:text-white truncate block">
                    {{ $ticket->created_at->diffForHumans(null, true) }}
                </span>
            </div>
        </div>
    </div>

    {{-- ── Grid Principal (8 Cols Izq / 4 Cols Der) ─────────────── --}}
    <div class="grid grid-cols-12 gap-3">

        {{-- COLUMNA IZQUIERDA: HUD Ciclo de Vida & Terminal (8 cols) --}}
        <div class="col-span-12 lg:col-span-8 space-y-3">
            
            {{-- Stepper Cibernético (Ciclo de Vida) --}}
            <div class="rounded-sm border border-stroke bg-[#0c101d] text-white shadow-xl dark:border-strokedark overflow-hidden relative">
                <div class="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-transparent via-primary to-transparent opacity-50"></div>
                
                <div class="flex items-center justify-between py-2.5 px-4 border-b border-white/10 bg-white/5">
                    <h3 class="font-mono text-[10px] uppercase tracking-widest flex items-center gap-1.5 font-bold text-white">
                        <i class="fas fa-project-diagram text-primary"></i> Trazabilidad del Ticket
                    </h3>
                    @if ($esCancelado)
                        <span class="text-[9px] font-mono text-meta-1 bg-meta-1/20 px-1.5 py-0.5 rounded border border-meta-1/30">
                            PROCESO CANCELADO
                        </span>
                    @endif
                </div>

                <div class="p-5 overflow-x-auto">
                    <div class="flex items-center justify-between min-w-[500px] relative">
                        {{-- Línea de Progreso Base --}}
                        <div class="absolute top-4 left-6 right-6 h-0.5 bg-white/10 z-0"></div>
                        
                        {{-- Línea de Progreso Activa (Neon) --}}
                        @php $progressWidth = ($indiceActual / (count($flujo) - 1)) * 100; @endphp
                        <div class="absolute top-4 left-6 h-0.5 bg-primary z-0 stepper-line shadow-[0_0_8px_rgba(60,80,224,0.8)]" 
                             style="width: calc({{ $progressWidth }}% - 24px);"></div>

                        @foreach ($flujo as $i => $step)
                            @php
                                if ($esCancelado) {
                                    $isDone = $i <= $indiceActual;
                                    $isCurrent = false;
                                    $isCancelled = $i > $indiceActual;
                                } else {
                                    $isDone = $i < $indiceActual;
                                    $isCurrent = $i === $indiceActual;
                                    $isCancelled = false;
                                }
                            @endphp

                            <div class="flex flex-col items-center relative z-10 w-24">
                                {{-- Nodo --}}
                                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-[10px] transition-all duration-500 border-2 bg-[#0c101d]
                                    {{ $isDone ? 'border-primary text-primary shadow-[0_0_10px_rgba(60,80,224,0.5)]' : '' }}
                                    {{ $isCurrent ? 'border-meta-3 text-meta-3 pulse-neon' : '' }}
                                    {{ $isCancelled || (!$isDone && !$isCurrent) ? 'border-white/20 text-white/40' : '' }}">
                                    @if ($isDone)
                                        <i class="fas fa-check"></i>
                                    @elseif ($isCurrent)
                                        <i class="fas fa-spinner fa-spin"></i>
                                    @else
                                        {{ $i + 1 }}
                                    @endif
                                </div>

                                {{-- Textos --}}
                                <span class="text-[9px] font-mono font-bold uppercase mt-2 text-center {{ $isDone || $isCurrent ? 'text-white' : 'text-white/40' }}">
                                    {{ $step['label'] }}
                                </span>
                                <span class="text-[8px] font-mono text-white/30 text-center mt-0.5">
                                    {{ $step['date']?->format('d/m H:i') ?? '—' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Terminal de Diagnóstico (Descripción y Resolución) --}}
            <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark flex flex-col">
                <div class="border-b border-stroke py-2.5 px-4 dark:border-strokedark bg-gray-2 dark:bg-meta-4/20">
                    <h3 class="font-bold text-[11px] uppercase tracking-wider text-black dark:text-white flex items-center gap-1.5">
                        <i class="fas fa-terminal text-primary"></i> Registro del Incidente
                    </h3>
                </div>
                
                <div class="p-4 space-y-4">
                    {{-- Bloque Descripción --}}
                    <div>
                        <span class="text-[9px] font-bold text-bodydark2 uppercase block mb-1">Descripción del Problema</span>
                        <div class="p-3 bg-gray-2 dark:bg-meta-4/40 rounded border border-stroke dark:border-strokedark text-xs text-black dark:text-white leading-relaxed whitespace-pre-wrap font-mono">
                            {{ $ticket->descripcion }}
                        </div>
                        
                        @if ($ticket->foto_solicitud)
                            <div class="mt-2">
                                <span class="text-[9px] font-bold text-bodydark2 uppercase block mb-1"><i class="fas fa-paperclip mr-1"></i>Adjunto del Solicitante</span>
                                <a href="{{ $ticket->foto_solicitud_url }}" target="_blank" rel="noopener" class="block w-max">
                                    <img src="{{ $ticket->foto_solicitud_url }}" alt="Adjunto" class="h-20 rounded border border-stroke dark:border-strokedark hover:opacity-80 transition">
                                </a>
                            </div>
                        @endif
                    </div>

                    {{-- Bloque Resolución --}}
                    @if ($ticket->notas_resolucion || $ticket->evidencia_resolucion)
                        <div class="pt-3 border-t border-dashed border-stroke dark:border-strokedark">
                            <span class="text-[9px] font-bold text-meta-3 uppercase block mb-1"><i class="fas fa-check-circle mr-1"></i>Notas de Resolución</span>
                            @if ($ticket->notas_resolucion)
                                <div class="p-3 bg-meta-3/10 rounded border border-meta-3/20 text-xs text-black dark:text-white leading-relaxed whitespace-pre-wrap font-mono">
                                    {{ $ticket->notas_resolucion }}
                                </div>
                            @endif

                            @if ($ticket->evidencia_resolucion)
                                <div class="mt-2">
                                    <span class="text-[9px] font-bold text-bodydark2 uppercase block mb-1"><i class="fas fa-paperclip mr-1"></i>Evidencia de Resolución</span>
                                    <a href="{{ $ticket->evidencia_resolucion_url }}" target="_blank" rel="noopener" class="block w-max">
                                        <img src="{{ $ticket->evidencia_resolucion_url }}" alt="Evidencia" class="h-20 rounded border border-stroke dark:border-strokedark hover:opacity-80 transition">
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Partes solicitadas y línea de tiempo --}}
            @include('tickets.partials._partes')
            @include('tickets.partials._historial')

        </div>

        {{-- COLUMNA DERECHA: Ficha Técnica, Entidades y Acciones (4 cols) --}}
        <div class="col-span-12 lg:col-span-4 space-y-3">

            {{-- Equipo o impresora vinculada --}}
            @include('tickets.partials._vinculo')

            {{-- Ficha de Entidad y Ubicación --}}
            @if ($ticket->empresa || $ticket->sede)
                <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                    <div class="border-b border-stroke py-2.5 px-4 dark:border-strokedark">
                        <h3 class="font-bold text-black dark:text-white text-[10px] uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fas fa-map-marker-alt text-primary"></i> Entidad y Ubicación
                        </h3>
                    </div>
                    <div class="p-3.5 space-y-2.5 text-xs">
                        @if ($ticket->empresa)
                            <div>
                                <span class="text-[9px] font-bold text-bodydark2 uppercase block">Empresa</span>
                                <div class="text-black dark:text-white font-bold mt-0.5 truncate">{{ $ticket->empresa->nombre }}</div>
                            </div>
                        @endif
                        @if ($ticket->sede)
                            <div class="pt-2 border-t border-dashed border-stroke dark:border-strokedark">
                                <span class="text-[9px] font-bold text-bodydark2 uppercase block mb-0.5">Sede</span>
                                <div class="text-black dark:text-white font-medium truncate">{{ $ticket->sede->nombre }}</div>
                                @if ($ticket->sede->ciudad || $ticket->sede->direccion)
                                    <div class="text-[9px] text-bodydark2 mt-0.5 truncate">
                                        <i class="fas fa-location-arrow text-primary text-[8px] mr-0.5"></i>{{ $ticket->sede->ciudad ?? '' }} {{ $ticket->sede->direccion ? '- '.$ticket->sede->direccion : '' }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Panel de Comandos (Acciones) --}}
            <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="border-b border-stroke py-2.5 px-4 dark:border-strokedark">
                    <h3 class="font-bold text-black dark:text-white text-[10px] uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fas fa-bolt text-primary"></i> Panel de Comandos
                    </h3>
                </div>
                
                <div class="p-4 space-y-2">
                    @if (! $hayAcciones && ! $puedeEliminar)
                        <p class="text-[10px] font-mono text-bodydark2 text-center py-2">
                            > SIN ACCIONES DISPONIBLES
                        </p>
                    @endif

                    {{-- 1) INICIAR --}}
                    @if ($puedeIniciar)
                        <form method="POST" action="{{ route('tickets.iniciar', $ticket) }}">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 rounded bg-primary py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm">
                                <i class="fas fa-play text-[9px]"></i> Iniciar Trabajo
                            </button>
                        </form>
                    @endif

                    {{-- 1b) PARTES: solicitar / recibidas --}}
                    @include('tickets.partials._acciones_partes')

                    {{-- 2) RESOLVER --}}
                    @if ($puedeResolver)
                        <div x-data="{ openRes: false }">
                            <button type="button" @click="openRes = !openRes" 
                                    class="w-full inline-flex items-center justify-center gap-1.5 rounded bg-meta-3 py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm">
                                <i class="fas fa-check text-[9px]"></i> <span x-text="openRes ? 'Ocultar Resolución' : 'Resolver Ticket'"></span>
                            </button>

                            <div x-show="openRes" x-cloak x-transition class="mt-2 p-3 bg-gray-2 dark:bg-meta-4/30 rounded border border-stroke dark:border-strokedark">
                                <form method="POST" action="{{ route('tickets.resolver', $ticket) }}" enctype="multipart/form-data" class="space-y-2.5">
                                    @csrf
                                    <div>
                                        <label class="mb-1 block text-[9px] font-bold uppercase text-black dark:text-white">Notas de Resolución <span class="text-meta-1">*</span></label>
                                        <textarea name="notas_resolucion" rows="3" required minlength="10" placeholder="Solución aplicada..." class="w-full rounded border border-stroke bg-transparent py-1.5 px-2 text-[11px] outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white"></textarea>
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-[9px] font-bold uppercase text-black dark:text-white">Evidencia</label>
                                        <input type="file" name="evidencia_resolucion" accept="image/*" class="w-full text-[9px] text-bodydark2 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-[9px] file:font-bold file:uppercase file:bg-meta-3/10 file:text-meta-3 hover:file:bg-meta-3/20 cursor-pointer">
                                    </div>
                                    <button type="submit" class="w-full inline-flex items-center justify-center gap-1 rounded bg-meta-3 py-1.5 px-3 text-[10px] font-bold uppercase text-white hover:bg-opacity-90 transition">
                                        Confirmar
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif

                    {{-- 3) ASIGNAR / REASIGNAR (el cambio y su motivo quedan en el historial) --}}
                    @if ($puedeAsignar)
                        <div class="pt-2 border-t border-stroke dark:border-strokedark" x-data="{ tecnico: '' }">
                            <label class="mb-1 block text-[9px] font-bold uppercase text-black dark:text-white">{{ $ticket->asignado_a ? 'Reasignar Técnico' : 'Asignar Técnico' }}</label>
                            <form method="POST" action="{{ route('tickets.asignar', $ticket) }}" class="space-y-1.5">
                                @csrf
                                <select name="tecnico_id" x-model="tecnico" required
                                        class="w-full appearance-none rounded border border-stroke bg-transparent py-1.5 px-2 text-[10px] outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white cursor-pointer">
                                    <option value="" class="bg-[#1c2434] text-gray-400">— Elegir técnico —</option>
                                    @foreach ($tecnicos as $tecnico)
                                        <option value="{{ $tecnico->id }}" @disabled($ticket->asignado_a == $tecnico->id) class="bg-[#1c2434] text-white">
                                            {{ $tecnico->name }}{{ $ticket->asignado_a == $tecnico->id ? ' (actual)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($ticket->asignado_a)
                                    <input type="text" name="motivo" maxlength="500" x-show="tecnico" x-cloak
                                           placeholder="Motivo del cambio (opcional)"
                                           class="w-full rounded border border-stroke bg-transparent py-1.5 px-2 text-[10px] outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                                @endif
                                <button type="submit" x-show="tecnico" x-cloak
                                        class="w-full inline-flex items-center justify-center gap-1 rounded bg-primary py-1.5 px-3 text-[10px] font-bold uppercase text-white hover:bg-opacity-90 transition">
                                    <i class="fas {{ $ticket->asignado_a ? 'fa-exchange-alt' : 'fa-user-check' }} text-[9px]"></i>
                                    {{ $ticket->asignado_a ? 'Reasignar' : 'Asignar' }}
                                </button>
                            </form>
                        </div>
                    @endif

                    {{-- 4) EDITAR --}}
                    @if ($puedeEditar)
                        <a href="{{ route('tickets.edit', $ticket) }}" 
                           class="w-full inline-flex items-center justify-center gap-1.5 rounded border border-stroke py-1.5 px-3 text-[10px] font-bold uppercase tracking-wider text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition">
                            <i class="fas fa-pen text-[9px]"></i> Editar
                        </a>
                    @endif

                    {{-- 5) CERRAR --}}
                    @if ($puedeCerrar)
                        <form method="POST" action="{{ route('tickets.cerrar', $ticket) }}" onsubmit="return confirm('¿Cerrar definitivamente el ticket #{{ $ticket->id }}?')">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 rounded border border-stroke py-1.5 px-3 text-[10px] font-bold uppercase tracking-wider text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition">
                                <i class="fas fa-lock text-[9px]"></i> Cerrar Ticket
                            </button>
                        </form>
                    @endif

                    {{-- 6) CANCELAR --}}
                    @if ($puedeCancelarT)
                        <form method="POST" action="{{ route('tickets.cancelar', $ticket) }}" onsubmit="return confirm('¿Cancelar el ticket #{{ $ticket->id }}?')">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 rounded bg-meta-1/10 text-meta-1 py-1.5 px-3 text-[10px] font-bold uppercase hover:bg-meta-1/20 transition border border-meta-1/30">
                                <i class="fas fa-ban text-[9px]"></i> Cancelar Ticket
                            </button>
                        </form>
                    @endif

                    {{-- 7) ELIMINAR --}}
                    @if ($puedeEliminar)
                        <div class="pt-2 mt-2 border-t border-stroke dark:border-strokedark">
                            <form method="POST" action="{{ route('tickets.destroy', $ticket) }}" onsubmit="return confirm('¿Eliminar PERMANENTEMENTE el ticket #{{ $ticket->id }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 rounded bg-meta-1 text-white py-1.5 px-3 text-[10px] font-bold uppercase hover:bg-opacity-90 transition shadow-sm">
                                    <i class="fas fa-trash-alt text-[9px]"></i> Eliminar Registro
                                </button>
                            </form>
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</x-app-layout>