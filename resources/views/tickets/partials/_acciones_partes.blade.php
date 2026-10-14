{{-- resources/views/tickets/partials/_acciones_partes.blade.php --}}
{{-- Panel de comandos: solicitar partes / partes recibidas. Recibe: $ticket, $puedeSolicitarPartes, $puedeRecibirPartes --}}

@php
    $inputClase = 'w-full rounded border border-stroke bg-transparent py-1.5 px-2 text-[11px] outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white';
    $reabrir = old('_accion') === 'partes';
@endphp

{{-- SOLICITAR PARTES: en progreso → esperando partes --}}
@if ($puedeSolicitarPartes)
    <div x-data="{ openPartes: {{ $reabrir ? 'true' : 'false' }} }">
        <button type="button" @click="openPartes = !openPartes"
                class="w-full inline-flex items-center justify-center gap-1.5 rounded bg-meta-6 py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm">
            <i class="fas fa-box-open text-[9px]"></i> <span x-text="openPartes ? 'Ocultar' : 'Solicitar Partes'"></span>
        </button>

        <div x-show="openPartes" x-cloak x-transition class="mt-2 p-3 bg-gray-2 dark:bg-meta-4/30 rounded border border-stroke dark:border-strokedark">
            <p class="text-[9px] text-bodydark2 mb-2">
                El ticket queda <b>esperando partes</b> hasta que lleguen. Si la espera se alarga, se puede reasignar el técnico sin perder el estado.
            </p>
            <form method="POST" action="{{ route('tickets.partes.solicitar', $ticket) }}" enctype="multipart/form-data" class="space-y-2.5">
                @csrf
                <input type="hidden" name="_accion" value="partes">
                <div>
                    <label class="mb-1 block text-[9px] font-bold uppercase text-black dark:text-white">Partes solicitadas <span class="text-meta-1">*</span></label>
                    <textarea name="partes" rows="3" required minlength="3" maxlength="2000"
                              placeholder="Ej: Fusor HP M404, disco SSD 480 GB..." class="{{ $inputClase }}">{{ $reabrir ? old('partes') : '' }}</textarea>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="mb-1 block text-[9px] font-bold uppercase text-black dark:text-white">Llegada estimada</label>
                        <input type="date" name="fecha_estimada" min="{{ today()->toDateString() }}"
                               value="{{ $reabrir ? old('fecha_estimada') : '' }}" class="{{ $inputClase }}">
                    </div>
                    <div>
                        <label class="mb-1 block text-[9px] font-bold uppercase text-black dark:text-white">Costo estimado</label>
                        <input type="number" name="costo_estimado" min="0" step="any" placeholder="$"
                               value="{{ $reabrir ? old('costo_estimado') : '' }}" class="{{ $inputClase }}">
                    </div>
                </div>
                <div>
                    <label class="mb-1 block text-[9px] font-bold uppercase text-black dark:text-white">Evidencia</label>
                    <input type="file" name="evidencia" accept="image/*,application/pdf"
                           class="w-full text-[9px] text-bodydark2 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-[9px] file:font-bold file:uppercase file:bg-meta-6/10 file:text-meta-6 hover:file:bg-meta-6/20 cursor-pointer">
                    <p class="text-[8px] text-bodydark2 mt-0.5">Foto o PDF (cotización, orden de compra) — máx. 5MB</p>
                </div>
                <button type="submit" class="w-full inline-flex items-center justify-center gap-1 rounded bg-meta-6 py-1.5 px-3 text-[10px] font-bold uppercase text-white hover:bg-opacity-90 transition">
                    Confirmar solicitud
                </button>
            </form>
        </div>
    </div>
@endif

{{-- PARTES RECIBIDAS: esperando partes → en progreso --}}
@if ($puedeRecibirPartes)
    @php $abierta = $ticket->solicitudesPartes->firstWhere('pendiente', true); @endphp
    <div x-data="{ openRecibir: false }">
        @if ($abierta)
            <div class="mb-2 rounded border {{ $abierta->atrasada ? 'border-meta-1/30 bg-meta-1/10' : 'border-meta-6/30 bg-meta-6/10' }} p-2 text-[10px]">
                <p class="font-bold uppercase text-[9px] {{ $abierta->atrasada ? 'text-meta-1' : 'text-meta-6' }}">
                    <i class="fas fa-hourglass-half mr-0.5"></i>
                    Esperando partes · {{ $abierta->dias_espera }} {{ $abierta->dias_espera === 1 ? 'día' : 'días' }}
                    @if ($abierta->atrasada) · atrasada @endif
                </p>
                <p class="text-black dark:text-white mt-0.5 line-clamp-2">{{ $abierta->descripcion }}</p>
            </div>
        @endif

        <button type="button" @click="openRecibir = !openRecibir"
                class="w-full inline-flex items-center justify-center gap-1.5 rounded bg-primary py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm">
            <i class="fas fa-boxes text-[9px]"></i> <span x-text="openRecibir ? 'Ocultar' : 'Partes Recibidas — Continuar'"></span>
        </button>

        <div x-show="openRecibir" x-cloak x-transition class="mt-2 p-3 bg-gray-2 dark:bg-meta-4/30 rounded border border-stroke dark:border-strokedark">
            <form method="POST" action="{{ route('tickets.partes.recibir', $ticket) }}" class="space-y-2.5">
                @csrf
                <div>
                    <label class="mb-1 block text-[9px] font-bold uppercase text-black dark:text-white">Notas de recepción</label>
                    <textarea name="notas_recepcion" rows="2" maxlength="1000" placeholder="Opcional: qué llegó, estado, proveedor..." class="{{ $inputClase }}"></textarea>
                </div>
                <p class="text-[9px] text-bodydark2">El ticket vuelve a <b>En progreso</b>. Si ya instaló las partes y quedó solucionado, puede usar <b>Resolver</b> directamente.</p>
                <button type="submit" class="w-full inline-flex items-center justify-center gap-1 rounded bg-primary py-1.5 px-3 text-[10px] font-bold uppercase text-white hover:bg-opacity-90 transition">
                    Confirmar recepción
                </button>
            </form>
        </div>
    </div>
@endif
