{{-- resources/views/equipos/mantenimientos/show.blade.php --}}
{{-- Servicio técnico a un PC: datos del formato, fotos, firmas (cliente y técnico) y reporte PDF --}}
@use('App\Models\EquipoMantenimiento')
@use('App\Models\EquipoMantenimientoEvidencia')

<x-app-layout>
    <x-slot name="title">Servicio {{ $mantenimiento->numero_orden }} · {{ $equipo->nombre }}</x-slot>

    @php
        $finalizado = $mantenimiento->estaFinalizado();
        $editable = $puedeGestionar && ! $finalizado;
        $porMomento = $mantenimiento->evidencias->groupBy('momento');
        $archivo = fn (string $nombre) => route('equipos.mantenimientos.archivo', [$equipo, $mantenimiento, $nombre]);
        $hora = fn (?string $h) => $h ? substr($h, 0, 5) : '—';
        $input = 'w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white';
        $dato = 'text-[8px] font-bold text-bodydark2 uppercase block';
    @endphp

    @if (session('success'))
        <div class="mb-3 flex w-full border-l-4 border-meta-3 bg-meta-3/10 px-4 py-2.5 shadow-sm rounded-sm">
            <i class="fas fa-check-circle text-meta-3 mr-2 mt-0.5 text-xs"></i>
            <p class="text-xs text-meta-3 font-medium">{{ session('success') }}</p>
        </div>
    @endif
    @if ($errors->any())
        <div class="mb-3 flex w-full border-l-4 border-meta-1 bg-meta-1/10 px-4 py-2.5 shadow-sm rounded-sm">
            <i class="fas fa-exclamation-triangle text-meta-1 mr-2 mt-0.5 text-xs"></i>
            <div class="text-xs text-meta-1 font-medium">@foreach ($errors->all() as $e)<p>{{ $e }}</p>@endforeach</div>
        </div>
    @endif

    {{-- ── Cabecera ─────────────────────────────────────────────── --}}
    <div class="mb-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-stroke pb-2.5 dark:border-strokedark">
        <div class="flex items-center gap-2.5 flex-wrap min-w-0">
            <span class="font-mono text-xs font-bold text-primary bg-primary/10 px-2 py-0.5 rounded border border-primary/20">{{ $mantenimiento->numero_orden }}</span>
            <h2 class="text-title-md font-bold text-black dark:text-white truncate">{{ $mantenimiento->label_tipo }}</h2>
            <span class="inline-flex rounded-full px-2 py-0.5 text-[9px] font-bold uppercase border {{ $finalizado ? 'bg-meta-3/10 text-meta-3 border-meta-3/20' : 'bg-meta-6/10 text-meta-6 border-meta-6/20' }}">
                {{ $mantenimiento->label_estado }}
            </span>
        </div>
        <div class="flex items-center gap-1.5 shrink-0">
            @if ($finalizado)
                <a href="{{ route('equipos.mantenimientos.pdf', [$equipo, $mantenimiento]) }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-1 rounded bg-meta-1 py-1 px-3 text-xs font-bold uppercase text-white hover:bg-opacity-90 transition shadow-sm">
                    <i class="fas fa-file-pdf text-[10px]"></i> Ver PDF
                </a>
                <a href="{{ route('equipos.mantenimientos.pdf', [$equipo, $mantenimiento, 'descargar' => 1]) }}" title="Descargar PDF"
                   class="inline-flex items-center gap-1 rounded border border-meta-1 py-1 px-2 text-xs font-bold uppercase text-meta-1 hover:bg-meta-1/10 transition">
                    <i class="fas fa-download text-[10px]"></i>
                </a>
            @elseif ($editable)
                <a href="{{ route('equipos.mantenimientos.edit', [$equipo, $mantenimiento]) }}"
                   class="inline-flex items-center gap-1 rounded border border-stroke py-1 px-3 text-xs font-bold text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition shadow-sm">
                    <i class="fas fa-pen text-[10px]"></i> Editar
                </a>
            @endif
            <a href="{{ route('equipos.show', $equipo) }}"
               class="inline-flex items-center gap-1 rounded border border-stroke py-1 px-3 text-xs font-bold text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition shadow-sm">
                <i class="fas fa-arrow-left text-[10px]"></i> {{ $equipo->nombre }}
            </a>
        </div>
    </div>

    <div class="grid grid-cols-12 gap-3">
        {{-- ── Izquierda: el formato ─────────────────────────────── --}}
        <div class="col-span-12 lg:col-span-8 space-y-3">

            {{-- Servicio --}}
            <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark p-4">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    <div><span class="{{ $dato }}">Modalidad</span><span class="font-bold text-black dark:text-white">{{ $mantenimiento->label_modalidad ?? '—' }}</span></div>
                    <div><span class="{{ $dato }}">Técnico / Ing.</span><span class="font-bold text-black dark:text-white">{{ $mantenimiento->tecnico?->name ?? '—' }}</span></div>
                    <div><span class="{{ $dato }}">Orden de trabajo</span>
                        @if ($mantenimiento->ticket)
                            <a href="{{ route('tickets.show', $mantenimiento->ticket) }}" class="font-bold text-primary hover:underline">Ticket #{{ $mantenimiento->ticket->id }}</a>
                        @else
                            <span class="font-bold text-black dark:text-white">—</span>
                        @endif
                    </div>
                    <div><span class="{{ $dato }}">Depósito</span><span class="font-bold text-black dark:text-white">{{ $mantenimiento->deposito ?? '—' }}</span></div>
                    <div><span class="{{ $dato }}">Fecha de ingreso</span><span class="font-mono font-bold text-black dark:text-white">{{ $mantenimiento->fecha_ingreso?->format('d/m/Y') ?? '—' }}</span></div>
                    <div><span class="{{ $dato }}">Fecha de servicio</span><span class="font-mono font-bold text-black dark:text-white">{{ $mantenimiento->fecha_servicio->format('d/m/Y') }}</span></div>
                    <div><span class="{{ $dato }}">Hora inicial / final</span><span class="font-mono font-bold text-black dark:text-white">{{ $hora($mantenimiento->hora_inicio) }} – {{ $hora($mantenimiento->hora_fin) }}</span></div>
                    <div><span class="{{ $dato }}">Contador inicial / final</span><span class="font-mono font-bold text-black dark:text-white">{{ $mantenimiento->contador_inicial ?? '—' }} / {{ $mantenimiento->contador_final ?? '—' }}</span></div>
                </div>
                <div class="mt-3">
                    <span class="{{ $dato }} mb-1">Falla</span>
                    <p class="p-2.5 bg-gray-2 dark:bg-meta-4/40 rounded border border-stroke dark:border-strokedark text-xs text-black dark:text-white whitespace-pre-line">{{ $mantenimiento->falla }}</p>
                </div>
            </div>

            {{-- Unidades revisadas --}}
            <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="border-b border-stroke py-2.5 px-4 dark:border-strokedark">
                    <h3 class="font-bold text-[10px] uppercase tracking-wider text-black dark:text-white flex items-center gap-1.5"><i class="fas fa-tasks text-primary"></i> Unidades revisadas</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="text-[9px] font-bold uppercase text-bodydark2 bg-gray-2 dark:bg-meta-4/40">
                                <th class="py-1.5 px-3 text-left">Unidad</th>
                                @foreach (EquipoMantenimiento::ACCIONES as [$encabezado])<th class="py-1.5 px-1 text-center w-14">{{ $encabezado }}</th>@endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($mantenimiento->unidades as $u)
                                <tr class="border-b border-stroke/60 dark:border-strokedark/60">
                                    <td class="py-1.5 px-3 text-black dark:text-white">{{ $u->unidad }}</td>
                                    @foreach (array_keys(EquipoMantenimiento::ACCIONES) as $accion)
                                        <td class="py-1.5 px-1 text-center">
                                            @if ($u->{$accion})<i class="fas fa-check text-meta-3"></i>@else<span class="text-stroke dark:text-strokedark">·</span>@endif
                                        </td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr><td colspan="7" class="py-3 text-center text-[10px] text-bodydark2">Sin unidades registradas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <p class="px-4 py-1.5 text-[9px] text-bodydark2">L Limpieza · A Ajuste · Lu Lubricación · C/I Cambio o instalación · Repar. Reparación · Rev. Revisión</p>
            </div>

            {{-- Observaciones y partes --}}
            @if ($mantenimiento->observaciones || $mantenimiento->partes->isNotEmpty())
                <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark p-4 space-y-3 text-xs">
                    @if ($mantenimiento->observaciones)
                        <div>
                            <span class="{{ $dato }} mb-1">Observaciones</span>
                            <p class="text-black dark:text-white whitespace-pre-line leading-relaxed">{{ $mantenimiento->observaciones }}</p>
                        </div>
                    @endif
                    @if ($mantenimiento->partes->isNotEmpty())
                        <div>
                            <span class="{{ $dato }} mb-1">Solicitud y cambio de partes</span>
                            <table class="w-full text-xs">
                                <tr class="text-[9px] font-bold uppercase text-bodydark2 text-left"><th class="py-1 pr-2">N.º de parte</th><th class="py-1 pr-2">Nombre</th><th class="py-1 pr-2">Serial original</th><th class="py-1">Serial reemplazo</th></tr>
                                @foreach ($mantenimiento->partes as $p)
                                    <tr class="border-t border-stroke/60 dark:border-strokedark/60 text-black dark:text-white">
                                        <td class="py-1 pr-2 font-mono">{{ $p->numero_parte ?? '—' }}</td><td class="py-1 pr-2">{{ $p->nombre }}</td>
                                        <td class="py-1 pr-2 font-mono">{{ $p->serial_original ?? '—' }}</td><td class="py-1 font-mono">{{ $p->serial_reemplazo ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Fotos --}}
            <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="border-b border-stroke py-2.5 px-4 dark:border-strokedark">
                    <h3 class="font-bold text-[10px] uppercase tracking-wider text-black dark:text-white flex items-center gap-1.5"><i class="fas fa-camera text-primary"></i> Fotos ({{ $mantenimiento->evidencias->count() }})</h3>
                </div>
                <div class="p-4 space-y-4">
                    @foreach (EquipoMantenimientoEvidencia::MOMENTOS as $clave => $etiqueta)
                        @continue(! $porMomento->has($clave))
                        <div>
                            <span class="text-[9px] font-bold uppercase text-bodydark2 block mb-1.5">{{ $etiqueta }} ({{ $porMomento[$clave]->count() }})</span>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                @foreach ($porMomento[$clave] as $foto)
                                    <figure class="relative rounded border border-stroke dark:border-strokedark overflow-hidden bg-gray-2 dark:bg-meta-4/30">
                                        <a href="{{ $archivo('evidencia-' . $foto->id) }}" target="_blank" rel="noopener">
                                            <img src="{{ $archivo('evidencia-' . $foto->id) }}" alt="{{ $foto->descripcion ?? $etiqueta }}" loading="lazy" class="h-28 w-full object-cover hover:opacity-90 transition">
                                        </a>
                                        @if ($foto->descripcion)<figcaption class="px-2 py-1 text-[9px] text-black dark:text-white truncate" title="{{ $foto->descripcion }}">{{ $foto->descripcion }}</figcaption>@endif
                                        @if ($editable)
                                            <form method="POST" action="{{ route('equipos.mantenimientos.evidencias.destroy', [$equipo, $mantenimiento, $foto]) }}" onsubmit="return confirm('¿Eliminar esta foto?')" class="absolute top-1 right-1">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="flex h-6 w-6 items-center justify-center rounded bg-black/60 text-white hover:bg-meta-1 transition" title="Eliminar"><i class="fas fa-trash text-[9px]"></i></button>
                                            </form>
                                        @endif
                                    </figure>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    @if ($mantenimiento->evidencias->isEmpty())
                        <p class="text-[10px] font-mono text-bodydark2 text-center py-2">> SIN FOTOS</p>
                    @endif

                    @if ($editable)
                        <form method="POST" action="{{ route('equipos.mantenimientos.evidencias.store', [$equipo, $mantenimiento]) }}" enctype="multipart/form-data"
                              x-data="{ momento: '{{ old('momento', 'antes') }}', cantidad: 0 }" class="rounded border border-dashed border-stroke dark:border-strokedark p-3 space-y-2.5">
                            @csrf
                            <span class="text-[9px] font-bold uppercase text-black dark:text-white block">Agregar fotos</span>
                            <div class="flex gap-1.5">
                                @foreach (EquipoMantenimientoEvidencia::MOMENTOS as $clave => $etiqueta)
                                    <label class="flex-1 cursor-pointer">
                                        <input type="radio" name="momento" value="{{ $clave }}" x-model="momento" class="sr-only">
                                        <span class="block text-center rounded border py-1.5 text-[10px] font-bold uppercase transition"
                                              :class="momento === '{{ $clave }}' ? 'border-primary bg-primary text-white' : 'border-stroke dark:border-strokedark text-bodydark2 hover:border-primary'">{{ $clave === 'otra' ? 'Otra' : $etiqueta }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <input type="text" name="descripcion" maxlength="255" placeholder="Descripción (opcional)" class="{{ $input }}">
                            <label class="flex items-center gap-2 rounded border border-stroke dark:border-strokedark px-3 py-2 cursor-pointer hover:border-primary transition">
                                <i class="fas fa-camera text-primary"></i>
                                <span class="text-xs text-black dark:text-white" x-text="cantidad ? cantidad + (cantidad === 1 ? ' foto seleccionada' : ' fotos seleccionadas') : 'Tomar o elegir fotos (hasta 12)'"></span>
                                <input type="file" name="fotos[]" accept="image/*" multiple required class="sr-only" @change="cantidad = $event.target.files.length">
                            </label>
                            <button type="submit" :disabled="!cantidad" class="w-full inline-flex items-center justify-center gap-1 rounded bg-primary py-1.5 px-3 text-[10px] font-bold uppercase text-white hover:bg-opacity-90 transition disabled:opacity-50">
                                <i class="fas fa-upload text-[9px]"></i> Subir
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Derecha: equipo, cliente y firmas ─────────────────── --}}
        <div class="col-span-12 lg:col-span-4 space-y-3">
            <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="border-b border-stroke py-2.5 px-4 dark:border-strokedark">
                    <h3 class="font-bold text-[10px] uppercase tracking-wider text-black dark:text-white flex items-center gap-1.5"><i class="fas fa-desktop text-primary"></i> Equipo y cliente</h3>
                </div>
                <div class="p-4 space-y-1.5 text-xs">
                    @foreach ([
                        ['Cliente', $equipo->empresa?->nombre], ['NIT', $equipo->empresa?->nit], ['Sede', $equipo->sede?->nombre],
                        ['Responsable', $equipo->responsable?->name], ['Equipo', $equipo->label_categoria], ['Marca / modelo', trim($equipo->marca . ' ' . $equipo->modelo) ?: null],
                        ['Serial', $equipo->numero_serie], ['Cód. interno', $equipo->codigo_interno],
                    ] as [$etiqueta, $valor])
                        <div class="flex justify-between gap-2">
                            <span class="text-[9px] font-bold text-bodydark2 uppercase shrink-0">{{ $etiqueta }}</span>
                            <span class="text-black dark:text-white font-medium text-right truncate">{{ $valor ?? '—' }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="border-b border-stroke py-2.5 px-4 dark:border-strokedark">
                    <h3 class="font-bold text-[10px] uppercase tracking-wider text-black dark:text-white flex items-center gap-1.5"><i class="fas fa-signature text-primary"></i> Firmas</h3>
                </div>
                <div class="p-4">
                    @if ($finalizado)
                        <div class="grid grid-cols-2 gap-3">
                            @foreach ([['firma-cliente', 'Cliente (salida)', $mantenimiento->cliente_nombre, trim($mantenimiento->cliente_cargo . ($mantenimiento->cliente_documento ? ' · ' . $mantenimiento->cliente_documento : ''), ' ·'), $mantenimiento->firma_cliente_at],
                                       ['firma-tecnico', 'Técnico', $mantenimiento->tecnico?->name, null, $mantenimiento->firma_tecnico_at]] as [$clave, $rol, $nombre, $detalle, $fecha])
                                <div class="text-center">
                                    <div class="h-20 rounded border border-stroke dark:border-strokedark bg-white flex items-center justify-center">
                                        <img src="{{ $archivo($clave) }}" alt="Firma {{ $rol }}" class="max-h-16 max-w-full">
                                    </div>
                                    <p class="mt-1 text-[11px] font-bold text-black dark:text-white truncate">{{ $nombre }}</p>
                                    @if ($detalle)<p class="text-[9px] text-bodydark2 truncate">{{ $detalle }}</p>@endif
                                    <p class="text-[8px] font-bold uppercase text-bodydark2">{{ $rol }} · {{ $fecha?->format('d/m/Y H:i') }}</p>
                                </div>
                            @endforeach
                        </div>
                    @elseif ($puedeGestionar)
                        <form method="POST" action="{{ route('equipos.mantenimientos.firmar', [$equipo, $mantenimiento]) }}" class="space-y-4"
                              x-data="{ tecnicoListo: false, clienteListo: false }"
                              @firma-cambio="$event.detail.campo === 'firma_tecnico' ? tecnicoListo = $event.detail.lista : clienteListo = $event.detail.lista"
                              onsubmit="return confirm('Al finalizar no se podrán modificar los datos, las fotos ni las firmas. ¿Continuar?')">
                            @csrf
                            @if ($mantenimiento->evidencias->isEmpty())
                                <p class="rounded border border-meta-6/30 bg-meta-6/10 p-2 text-[10px] text-meta-6"><i class="fas fa-exclamation-triangle mr-0.5"></i> Aún no hay fotos. Súbalas antes de firmar si van en el reporte.</p>
                            @endif
                            <div class="space-y-2">
                                <label class="text-[9px] font-bold uppercase text-black dark:text-white block">Recibe por el cliente (salida)</label>
                                <input type="text" name="cliente_nombre" required maxlength="150" value="{{ old('cliente_nombre', $equipo->responsable?->name) }}" placeholder="Nombre completo *" class="{{ $input }}">
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="text" name="cliente_cargo" maxlength="100" value="{{ old('cliente_cargo') }}" placeholder="Cargo" class="{{ $input }}">
                                    <input type="text" name="cliente_documento" maxlength="30" value="{{ old('cliente_documento') }}" placeholder="Documento" class="{{ $input }}">
                                </div>
                                <label class="text-[9px] font-bold uppercase text-black dark:text-white block pt-1">Firma cliente salida <span class="text-meta-1">*</span></label>
                                @include('impresoras.mantenimientos._firma_pad', ['campo' => 'firma_cliente'])
                            </div>
                            <div>
                                <label class="text-[9px] font-bold uppercase text-black dark:text-white block mb-1">
                                    Firma Ing. / Técnico <span class="text-meta-1">*</span> <span class="font-normal normal-case text-bodydark2">— {{ $mantenimiento->tecnico?->name }}</span>
                                </label>
                                @include('impresoras.mantenimientos._firma_pad', ['campo' => 'firma_tecnico'])
                            </div>
                            <button type="submit" :disabled="!tecnicoListo || !clienteListo"
                                    class="w-full inline-flex items-center justify-center gap-1.5 rounded bg-meta-3 py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fas fa-check text-[9px]"></i> Firmar y finalizar
                            </button>
                        </form>
                    @else
                        <p class="text-[11px] text-bodydark2">Pendiente de la firma del cliente y del técnico.</p>
                    @endif
                </div>
            </div>

            <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark p-4 text-[10px] text-bodydark2 space-y-1">
                <p><span class="font-bold uppercase">Elaborado por:</span> {{ $mantenimiento->registrador?->name ?? '—' }} · {{ $mantenimiento->created_at->format('d/m/Y H:i') }}</p>
                @if ($finalizado)
                    <p><span class="font-bold uppercase">Diligenciado por:</span> {{ $mantenimiento->finalizador?->name ?? '—' }} · {{ $mantenimiento->finalizado_at?->format('d/m/Y H:i') }}</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
