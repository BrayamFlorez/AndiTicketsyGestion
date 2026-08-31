{{-- resources/views/tickets/show.blade.php --}}
<x-app-layout>
    <x-slot name="title">Ticket #{{ $ticket->id }}</x-slot>

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
            $indiceActual = match (true) {
                $ticket->iniciado_at !== null => 2,
                $ticket->asignado_at !== null => 1,
                default                       => 0,
            };
        } else {
            $indiceActual = $indices[$ticket->estado] ?? 0;
        }
    @endphp

    {{-- ── Cabecera ────────────────────────────────────────────────────── --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-title-md2 font-bold text-black dark:text-white flex items-center gap-2">
                <span class="font-mono text-bodydark2">#{{ $ticket->id }}</span>
                <span>{{ $ticket->titulo }}</span>
            </h2>
            <p class="text-sm text-bodydark2 mt-1">
                Reportado por <strong class="text-black dark:text-white">{{ $ticket->solicitante?->name ?? 'Usuario Eliminado' }}</strong>
                · {{ $ticket->created_at->diffForHumans() }}
            </p>
        </div>
        
        <a href="{{ route('tickets.index') }}" 
           class="inline-flex items-center justify-center gap-2.5 rounded-md border border-stroke py-2 px-4 text-center font-medium text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition shadow-sm">
            <i class="fas fa-arrow-left"></i> Volver al Listado
        </a>
    </div>

    {{-- Flash Alerts --}}
    @if (session('success'))
        <div class="mb-6 flex w-full border-l-6 border-meta-3 bg-meta-3/10 px-7 py-4 shadow-md dark:bg-meta-3/20 dark:border-meta-3 rounded-sm">
            <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-meta-3 text-white shrink-0">
                <i class="fas fa-check text-lg"></i>
            </div>
            <div class="w-full">
                <h5 class="mb-1 font-bold text-meta-3">Operación exitosa</h5>
                <p class="text-sm text-meta-3">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if (session('error') || $errors->any())
        <div class="mb-6 flex w-full border-l-6 border-meta-1 bg-meta-1/10 px-7 py-4 shadow-md dark:bg-meta-1/20 dark:border-meta-1 rounded-sm">
            <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-meta-1 text-white shrink-0">
                <i class="fas fa-exclamation-circle text-lg"></i>
            </div>
            <div class="w-full">
                <h5 class="mb-1 font-bold text-meta-1">Error</h5>
                <p class="text-sm text-meta-1">{{ session('error') ?? $errors->first() }}</p>
            </div>
        </div>
    @endif

    {{-- ── Card Ciclo de Vida (Stepper NATIVO TailAdmin) ───────────────────── --}}
    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark mb-6">
        <div class="border-b border-stroke py-4 px-6 dark:border-strokedark flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-3">
                <span class="text-xs font-bold text-bodydark2 uppercase tracking-wider">Estado Actual:</span>
                
                @php
                    $colorEstado = match($ticket->estado) {
                        'resuelto' => 'bg-meta-3/10 text-meta-3',
                        'en_progreso' => 'bg-meta-6/10 text-meta-6',
                        'cerrado','cancelado' => 'bg-meta-1/10 text-meta-1',
                        default => 'bg-primary/10 text-primary',
                    };
                    $colorPrio = match($ticket->prioridad) {
                        'critica' => 'bg-meta-1/10 text-meta-1',
                        'alta' => 'bg-meta-6/10 text-meta-6',
                        default => 'bg-primary/10 text-primary',
                    };
                @endphp

                <span class="inline-flex rounded-full py-1 px-3 text-xs font-bold uppercase {{ $colorEstado }}">
                    {{ $ticket->label_estado }}
                </span>
                <span class="inline-flex rounded-full py-1 px-3 text-xs font-bold uppercase {{ $colorPrio }}">
                    Prioridad {{ $ticket->label_prioridad }}
                </span>
            </div>

            <div class="text-xs text-bodydark2 font-mono flex items-center gap-1.5">
                <i class="fas {{ $ticket->icono_categoria }}"></i> {{ $ticket->label_categoria }}
            </div>
        </div>

        @if ($esCancelado)
            <div class="m-6 p-4 rounded-sm bg-meta-1/10 border border-meta-1/30 text-meta-1 text-xs flex items-center gap-3">
                <i class="fas fa-ban text-base shrink-0"></i>
                <div>
                    <strong class="font-bold">Ticket Cancelado</strong> · {{ $ticket->updated_at->translatedFormat('d \\d\\e F, Y H:i') }}
                </div>
            </div>
        @endif

        {{-- Stepper TailAdmin --}}
        <div class="p-6 overflow-x-auto">
            <div class="flex items-center justify-between min-w-[600px] relative">
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

                    <div class="flex flex-col items-center relative z-10">
                        {{-- Círculo Indicador --}}
                        <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs transition border-2 
                            {{ $isDone ? 'bg-primary border-primary text-white' : '' }}
                            {{ $isCurrent ? 'bg-white dark:bg-boxdark border-primary text-primary shadow-md' : '' }}
                            {{ !$isDone && !$isCurrent ? 'bg-gray border-stroke dark:border-strokedark text-bodydark2 dark:bg-meta-4' : '' }}">
                            
                            @if ($isDone)
                                <i class="fas fa-check"></i>
                            @elseif ($isCurrent)
                                <i class="fas fa-circle text-[8px] animate-pulse"></i>
                            @else
                                {{ $i + 1 }}
                            @endif
                        </div>

                        {{-- Textos del Paso --}}
                        <span class="text-xs font-bold uppercase mt-2 {{ $isDone || $isCurrent ? 'text-black dark:text-white' : 'text-bodydark2' }}">
                            {{ $step['label'] }}
                        </span>
                        <span class="text-[10px] font-mono text-bodydark2">
                            {{ $step['date']?->format('d/m H:i') ?? '—' }}
                        </span>
                    </div>

                    {{-- Conector entre Pasos --}}
                    @if (! $loop->last)
                        <div class="flex-1 h-0.5 mx-2 -mt-7 transition {{ $i < $indiceActual ? 'bg-primary' : 'bg-stroke dark:bg-strokedark' }}"></div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── Contenido Principal & Lateral ─────────────────────────────────── --}}
    <div class="grid grid-cols-12 gap-4 md:gap-6 2xl:gap-7.5">

        {{-- COLUMNA PRINCIPAL (8 cols) --}}
        <div class="col-span-12 lg:col-span-8 space-y-6">

            {{-- Descripción del Problema --}}
            <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="border-b border-stroke py-4 px-6 dark:border-strokedark">
                    <h3 class="font-bold text-black dark:text-white text-xs uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-align-left text-primary"></i> Descripción del Problema
                    </h3>
                </div>
                <div class="p-6 text-sm text-black dark:text-white leading-relaxed whitespace-pre-wrap">
                    {{ $ticket->descripcion }}
                </div>
            </div>

            {{-- Adjunto del Solicitante --}}
            @if ($ticket->foto_solicitud)
                <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                    <div class="border-b border-stroke py-4 px-6 dark:border-strokedark">
                        <h3 class="font-bold text-black dark:text-white text-xs uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-paperclip text-primary"></i> Archivo Adjunto del Solicitante
                        </h3>
                    </div>
                    <div class="p-6">
                        <a href="{{ $ticket->foto_solicitud_url }}" target="_blank" rel="noopener">
                            <img src="{{ $ticket->foto_solicitud_url }}" alt="Foto del ticket"
                                 class="max-w-full rounded border border-stroke dark:border-strokedark">
                        </a>
                    </div>
                </div>
            @endif

            {{-- Resolución (si aplica) --}}
            @if ($ticket->notas_resolucion || $ticket->evidencia_resolucion)
                <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                    <div class="border-b border-stroke py-4 px-6 dark:border-strokedark">
                        <h3 class="font-bold text-meta-3 text-xs uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-check-circle"></i> Detalles de la Resolución
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        @if ($ticket->notas_resolucion)
                            <p class="text-sm text-black dark:text-white leading-relaxed whitespace-pre-wrap">
                                {{ $ticket->notas_resolucion }}
                            </p>
                        @endif

                        @if ($ticket->evidencia_resolucion)
                            <div>
                                <a href="{{ $ticket->evidencia_resolucion_url }}" target="_blank" rel="noopener">
                                    <img src="{{ $ticket->evidencia_resolucion_url }}" alt="Evidencia de resolución"
                                         class="max-w-full rounded border border-stroke dark:border-strokedark">
                                </a>
                            </div>
                        @endif

                        @if ($ticket->resuelto_at)
                            <div class="text-xs text-bodydark2 pt-3 border-t border-stroke dark:border-strokedark">
                                Resuelto por <strong class="text-black dark:text-white">{{ $ticket->asignado?->name ?? 'Técnico Eliminado' }}</strong>
                                el {{ $ticket->resuelto_at->translatedFormat('d \\d\\e F, Y H:i') }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif

        </div>

        {{-- COLUMNA LATERAL (4 cols) --}}
        <div class="col-span-12 lg:col-span-4 space-y-6">

            {{-- ACCIONES DISPONIBLES --}}
            <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="border-b border-stroke py-4 px-6 dark:border-strokedark">
                    <h3 class="font-bold text-black dark:text-white text-xs uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-bolt text-primary"></i> Acciones Disponibles
                    </h3>
                </div>
                
                <div class="p-5 space-y-3">
                    @if (! $hayAcciones && ! $puedeEliminar)
                        <p class="text-xs text-bodydark2 text-center py-2 italic">
                            No hay acciones disponibles en el estado actual.
                        </p>
                    @endif

                    {{-- 1) INICIAR --}}
                    @if ($puedeIniciar)
                        <form method="POST" action="{{ route('tickets.iniciar', $ticket) }}">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded bg-primary py-2.5 px-4 text-xs font-bold uppercase text-white hover:bg-opacity-90 transition">
                                <i class="fas fa-play text-[10px]"></i> Iniciar Trabajo
                            </button>
                        </form>
                    @endif

                    {{-- 2) RESOLVER --}}
                    @if ($puedeResolver)
                        <div x-data="{ open: false }">
                            <button type="button" @click="open = !open" 
                                    class="w-full inline-flex items-center justify-center gap-2 rounded bg-meta-3 py-2.5 px-4 text-xs font-bold uppercase text-white hover:bg-opacity-90 transition">
                                <i class="fas fa-check text-[10px]"></i>
                                <span x-text="open ? 'Cancelar' : 'Marcar como Resuelto'"></span>
                            </button>

                            <div x-show="open" x-cloak class="mt-3 pt-3 border-t border-stroke dark:border-strokedark">
                                <form method="POST" action="{{ route('tickets.resolver', $ticket) }}" enctype="multipart/form-data" class="space-y-3">
                                    @csrf

                                    <div>
                                        <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">
                                            Notas de Resolución <span class="text-meta-1">*</span>
                                        </label>
                                        <textarea name="notas_resolucion" rows="3" required minlength="10"
                                                  placeholder="Describe la solución aplicada..."
                                                  class="w-full rounded border border-stroke bg-transparent py-2 px-3 text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">{{ old('notas_resolucion') }}</textarea>
                                    </div>

                                    <div>
                                        <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Evidencia (Opcional)</label>
                                        <input type="file" name="evidencia_resolucion" accept="image/*"
                                               class="w-full cursor-pointer rounded border border-stroke bg-transparent text-xs text-bodydark2 outline-none file:mr-2 file:border-0 file:bg-gray-2 file:py-1 file:px-2 file:text-xs dark:border-form-strokedark dark:file:bg-meta-4 dark:file:text-white">
                                    </div>

                                    <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 rounded bg-meta-3 py-2 px-4 text-xs font-bold uppercase text-white hover:bg-opacity-90 transition">
                                        <i class="fas fa-check-double text-[10px]"></i> Confirmar Resolución
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif

                    {{-- 3) ASIGNAR / REASIGNAR --}}
                    @if ($puedeAsignar)
                        <div>
                            <label class="mb-1.5 block text-[10px] font-bold uppercase text-black dark:text-white">
                                {{ $ticket->asignado_a ? 'Reasignar Técnico' : 'Asignar Técnico' }}
                            </label>
                            <form method="POST" action="{{ route('tickets.asignar', $ticket) }}">
                                @csrf
                                <div class="relative z-20">
                                    <select name="tecnico_id"
                                            onchange="if(this.value && this.value != '{{ $ticket->asignado_a }}') this.form.submit()"
                                            class="w-full appearance-none rounded border border-stroke bg-transparent py-2 px-3 text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white cursor-pointer">
                                        <option value="" class="bg-[#1c2434] text-gray-400">— Elegir técnico —</option>
                                        @foreach ($tecnicos as $tecnico)
                                            <option value="{{ $tecnico->id }}" @selected($ticket->asignado_a == $tecnico->id) class="bg-[#1c2434] text-white">
                                                {{ $tecnico->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="absolute top-1/2 right-3 -translate-y-1/2 z-30 pointer-events-none">
                                        <i class="fas fa-chevron-down text-[10px] text-bodydark2"></i>
                                    </span>
                                </div>
                            </form>
                        </div>
                    @endif

                    {{-- 4) CERRAR --}}
                    @if ($puedeCerrar)
                        <form method="POST" action="{{ route('tickets.cerrar', $ticket) }}"
                              onsubmit="return confirm('¿Cerrar definitivamente el ticket #{{ $ticket->id }}?')">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded border border-stroke py-2.5 px-4 text-xs font-bold uppercase text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition">
                                <i class="fas fa-lock text-[10px]"></i> Cerrar Ticket
                            </button>
                        </form>
                    @endif

                    {{-- 5) EDITAR --}}
                    @if ($puedeEditar)
                        <a href="{{ route('tickets.edit', $ticket) }}" 
                           class="w-full inline-flex items-center justify-center gap-2 rounded border border-stroke py-2.5 px-4 text-xs font-bold uppercase text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition">
                            <i class="fas fa-pen text-[10px]"></i> Editar Ticket
                        </a>
                    @endif

                    {{-- 6) CANCELAR --}}
                    @if ($puedeCancelarT)
                        <form method="POST" action="{{ route('tickets.cancelar', $ticket) }}"
                              onsubmit="return confirm('¿Cancelar el ticket #{{ $ticket->id }}?')">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded border border-meta-1/30 text-meta-1 py-2.5 px-4 text-xs font-bold uppercase hover:bg-meta-1/10 transition">
                                <i class="fas fa-ban text-[10px]"></i> Cancelar Ticket
                            </button>
                        </form>
                    @endif

                    {{-- 7) ELIMINAR --}}
                    @if ($puedeEliminar)
                        <div class="pt-2 border-t border-stroke dark:border-strokedark">
                            <form method="POST" action="{{ route('tickets.destroy', $ticket) }}"
                                  onsubmit="return confirm('¿Eliminar PERMANENTEMENTE el ticket #{{ $ticket->id }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded bg-meta-1/10 text-meta-1 py-2.5 px-4 text-xs font-bold uppercase hover:bg-meta-1/20 transition">
                                    <i class="fas fa-trash-alt text-[10px]"></i> Eliminar Ticket
                                </button>
                            </form>
                        </div>
                    @endif

                </div>
            </div>

            {{-- PERSONAS ENVOLUCRADAS --}}
            <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="border-b border-stroke py-4 px-6 dark:border-strokedark">
                    <h3 class="font-bold text-black dark:text-white text-xs uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-users text-primary"></i> Personas
                    </h3>
                </div>
                <div class="p-5 space-y-4">
                    {{-- Solicitante --}}
                    <div>
                        <span class="text-[10px] font-bold text-bodydark2 uppercase block mb-1">Solicitante</span>
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary/20 text-primary font-bold text-xs shrink-0">
                                {{ substr($ticket->solicitante?->name ?? 'U', 0, 1) }}
                            </div>
                            <div class="min-w-0">
                                <span class="block text-xs font-bold text-black dark:text-white truncate">
                                    {{ $ticket->solicitante?->name ?? 'Usuario Eliminado' }}
                                </span>
                                <span class="block text-[10px] text-bodydark2 font-mono truncate">
                                    {{ $ticket->solicitante?->email ?? '—' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Técnico Asignado --}}
                    <div class="pt-3 border-t border-stroke dark:border-strokedark">
                        <span class="text-[10px] font-bold text-bodydark2 uppercase block mb-1">Técnico Asignado</span>
                        @if ($ticket->asignado)
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-meta-3/20 text-meta-3 font-bold text-xs shrink-0">
                                    {{ substr($ticket->asignado->name, 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <span class="block text-xs font-bold text-black dark:text-white truncate">{{ $ticket->asignado->name }}</span>
                                    <span class="block text-[10px] text-bodydark2 font-mono truncate">{{ $ticket->asignado->email }}</span>
                                </div>
                            </div>
                        @else
                            <span class="inline-flex rounded-full bg-meta-1/10 py-1 px-2.5 text-[10px] font-medium text-meta-1">
                                Sin Asignar
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- UBICACIÓN --}}
            @if ($ticket->empresa || $ticket->sede)
                <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                    <div class="border-b border-stroke py-4 px-6 dark:border-strokedark">
                        <h3 class="font-bold text-black dark:text-white text-xs uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-building text-primary"></i> Ubicación
                        </h3>
                    </div>
                    <div class="p-5 space-y-3 text-xs">
                        @if ($ticket->empresa)
                            <div>
                                <span class="text-[10px] font-bold text-bodydark2 uppercase block">Empresa</span>
                                <span class="text-black dark:text-white font-medium">{{ $ticket->empresa->nombre }}</span>
                            </div>
                        @endif

                        @if ($ticket->sede)
                            <div class="pt-2 border-t border-stroke dark:border-strokedark">
                                <span class="text-[10px] font-bold text-bodydark2 uppercase block">Sede</span>
                                <span class="text-black dark:text-white font-medium block">{{ $ticket->sede->nombre }}</span>
                                @if ($ticket->sede->direccion)
                                    <span class="text-[10px] text-bodydark2 block">{{ $ticket->sede->direccion }}</span>
                                @endif
                                @if ($ticket->sede->ciudad)
                                    <span class="text-[10px] text-bodydark2 block">{{ $ticket->sede->ciudad }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>