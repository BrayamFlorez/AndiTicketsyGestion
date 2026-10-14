{{-- resources/views/impresoras/mantenimientos/show.blade.php --}}
{{-- Detalle de un mantenimiento: evidencia, firmas del técnico y del cliente, y reporte PDF --}}
<x-app-layout>
    <x-slot name="title">Mantenimiento #{{ $mantenimiento->id }} · {{ $impresora->nombre_visible }}</x-slot>

    @php
        $finalizado = $mantenimiento->estaFinalizado();
        $editable = $puedeGestionar && ! $finalizado;
        $porMomento = $mantenimiento->evidencias->groupBy('momento');
        $momentos = \App\Models\ImpresoraMantenimientoEvidencia::MOMENTOS;
        $archivo = fn (string $nombre) => route('impresoras.mantenimientos.archivo', [$impresora, $mantenimiento, $nombre]);
        $input = 'w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white';
    @endphp

    {{-- Mensajes --}}
    @if (session('exito'))
        <div class="mb-3 flex w-full border-l-4 border-meta-3 bg-meta-3/10 px-4 py-2.5 shadow-sm rounded-sm">
            <i class="fas fa-check-circle text-meta-3 mr-2 mt-0.5 text-xs"></i>
            <p class="text-xs text-meta-3 font-medium">{{ session('exito') }}</p>
        </div>
    @endif
    @if ($errors->any())
        <div class="mb-3 flex w-full border-l-4 border-meta-1 bg-meta-1/10 px-4 py-2.5 shadow-sm rounded-sm">
            <i class="fas fa-exclamation-triangle text-meta-1 mr-2 mt-0.5 text-xs"></i>
            <div class="text-xs text-meta-1 font-medium">
                @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        </div>
    @endif

    {{-- ── Cabecera ─────────────────────────────────────────────── --}}
    <div class="mb-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-stroke pb-2.5 dark:border-strokedark">
        <div class="flex items-center gap-2.5 flex-wrap min-w-0">
            <span class="font-mono text-xs font-bold text-primary bg-primary/10 px-2 py-0.5 rounded border border-primary/20">
                {{ $impresora->codigo }}
            </span>
            <h2 class="text-title-md font-bold text-black dark:text-white truncate">
                Mantenimiento #{{ $mantenimiento->id }} · {{ $mantenimiento->label_tipo }}
            </h2>
            @if ($finalizado)
                <span class="inline-flex rounded-full px-2 py-0.5 text-[9px] font-bold uppercase border bg-meta-3/10 text-meta-3 border-meta-3/20">Finalizado</span>
            @else
                <span class="inline-flex rounded-full px-2 py-0.5 text-[9px] font-bold uppercase border bg-meta-6/10 text-meta-6 border-meta-6/20">Pendiente de firma</span>
            @endif
        </div>

        <div class="flex items-center gap-1.5 shrink-0">
            @if ($finalizado)
                <a href="{{ route('impresoras.mantenimientos.pdf', [$impresora, $mantenimiento]) }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-1 rounded bg-meta-1 py-1 px-3 text-xs font-bold uppercase text-white hover:bg-opacity-90 transition shadow-sm">
                    <i class="fas fa-file-pdf text-[10px]"></i> Ver PDF
                </a>
                <a href="{{ route('impresoras.mantenimientos.pdf', [$impresora, $mantenimiento, 'descargar' => 1]) }}"
                   class="inline-flex items-center gap-1 rounded border border-meta-1 py-1 px-2 text-xs font-bold uppercase text-meta-1 hover:bg-meta-1/10 transition" title="Descargar PDF">
                    <i class="fas fa-download text-[10px]"></i>
                </a>
            @endif
            <a href="{{ route('impresoras.show', $impresora) }}"
               class="inline-flex items-center gap-1 rounded border border-stroke py-1 px-3 text-xs font-bold text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition shadow-sm">
                <i class="fas fa-arrow-left text-[10px]"></i> {{ $impresora->nombre_visible }}
            </a>
        </div>
    </div>

    <div class="grid grid-cols-12 gap-3">

        {{-- ── Izquierda: detalle y evidencia ─────────────────── --}}
        <div class="col-span-12 lg:col-span-7 space-y-3">

            {{-- Detalle --}}
            <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="border-b border-stroke py-2.5 px-4 dark:border-strokedark">
                    <h3 class="font-bold text-[10px] uppercase tracking-wider text-black dark:text-white flex items-center gap-1.5">
                        <i class="fas {{ $mantenimiento->icono_tipo }} text-primary"></i> Trabajo realizado
                    </h3>
                </div>
                <div class="p-4 space-y-3 text-xs">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div>
                            <span class="text-[8px] font-bold text-bodydark2 uppercase block">Fecha</span>
                            <span class="font-mono font-bold text-black dark:text-white">{{ $mantenimiento->fecha_realizado->format('d/m/Y') }}</span>
                        </div>
                        <div>
                            <span class="text-[8px] font-bold text-bodydark2 uppercase block">Técnico</span>
                            <span class="font-bold text-black dark:text-white">{{ $mantenimiento->realizador?->name ?? '—' }}</span>
                        </div>
                        <div>
                            <span class="text-[8px] font-bold text-bodydark2 uppercase block">Contador</span>
                            <span class="font-mono font-bold text-black dark:text-white">
                                {{ $mantenimiento->contador_paginas !== null ? number_format($mantenimiento->contador_paginas, 0, ',', '.') : '—' }}
                            </span>
                            @if ($mantenimiento->lecturaContador?->paginas_desde_anterior !== null)
                                <span class="block text-[9px] text-meta-3 font-mono">+{{ number_format($mantenimiento->lecturaContador->paginas_desde_anterior, 0, ',', '.') }} págs.</span>
                            @endif
                        </div>
                        <div>
                            <span class="text-[8px] font-bold text-bodydark2 uppercase block">Próximo</span>
                            <span class="font-mono font-bold text-black dark:text-white">{{ $mantenimiento->proximo_programado?->format('d/m/Y') ?? '—' }}</span>
                        </div>
                    </div>

                    <div>
                        <span class="text-[8px] font-bold text-bodydark2 uppercase block mb-1">Descripción</span>
                        <p class="p-3 bg-gray-2 dark:bg-meta-4/40 rounded border border-stroke dark:border-strokedark text-black dark:text-white leading-relaxed whitespace-pre-line">{{ $mantenimiento->descripcion }}</p>
                    </div>

                    @if ($mantenimiento->observaciones)
                        <div>
                            <span class="text-[8px] font-bold text-bodydark2 uppercase block mb-1">Observaciones</span>
                            <p class="text-bodydark2 italic whitespace-pre-line">{{ $mantenimiento->observaciones }}</p>
                        </div>
                    @endif

                    @if ($mantenimiento->consumiblesInstalados->isNotEmpty())
                        <div>
                            <span class="text-[8px] font-bold text-bodydark2 uppercase block mb-1">Consumibles reemplazados</span>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($mantenimiento->consumiblesInstalados as $c)
                                    <span class="inline-flex items-center gap-1 rounded border border-stroke dark:border-strokedark px-2 py-0.5 text-[10px] text-black dark:text-white">
                                        <i class="fas fa-fill-drip text-meta-6 text-[9px]"></i> {{ $c->descripcion_corta }}
                                        <span class="text-bodydark2 font-mono">· {{ $c->dias_estimados }}d est.</span>
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Evidencia --}}
            <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="border-b border-stroke py-2.5 px-4 dark:border-strokedark flex items-center justify-between">
                    <h3 class="font-bold text-[10px] uppercase tracking-wider text-black dark:text-white flex items-center gap-1.5">
                        <i class="fas fa-camera text-primary"></i> Evidencia ({{ $mantenimiento->evidencias->count() }})
                    </h3>
                </div>

                <div class="p-4 space-y-4">
                    @forelse ($momentos as $clave => $etiqueta)
                        @continue(! $porMomento->has($clave))
                        <div>
                            <span class="text-[9px] font-bold uppercase text-bodydark2 block mb-1.5">{{ $etiqueta }} ({{ $porMomento[$clave]->count() }})</span>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                @foreach ($porMomento[$clave] as $foto)
                                    <figure class="relative group rounded border border-stroke dark:border-strokedark overflow-hidden bg-gray-2 dark:bg-meta-4/30">
                                        <a href="{{ $archivo('evidencia-' . $foto->id) }}" target="_blank" rel="noopener">
                                            <img src="{{ $archivo('evidencia-' . $foto->id) }}" alt="{{ $foto->descripcion ?? $etiqueta }}" loading="lazy" class="h-28 w-full object-cover hover:opacity-90 transition">
                                        </a>
                                        @if ($foto->descripcion)
                                            <figcaption class="px-2 py-1 text-[9px] text-black dark:text-white truncate" title="{{ $foto->descripcion }}">{{ $foto->descripcion }}</figcaption>
                                        @endif
                                        @if ($editable)
                                            <form method="POST" action="{{ route('impresoras.mantenimientos.evidencias.destroy', [$impresora, $mantenimiento, $foto]) }}"
                                                  onsubmit="return confirm('¿Eliminar esta foto?')" class="absolute top-1 right-1">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="flex h-6 w-6 items-center justify-center rounded bg-black/60 text-white hover:bg-meta-1 transition" title="Eliminar">
                                                    <i class="fas fa-trash text-[9px]"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </figure>
                                @endforeach
                            </div>
                        </div>
                    @empty
                    @endforelse

                    @if ($mantenimiento->evidencias->isEmpty())
                        <p class="text-[10px] font-mono text-bodydark2 text-center py-2">> SIN FOTOS DE EVIDENCIA</p>
                    @endif

                    {{-- Subir fotos --}}
                    @if ($editable)
                        <form method="POST" action="{{ route('impresoras.mantenimientos.evidencias.store', [$impresora, $mantenimiento]) }}"
                              enctype="multipart/form-data" x-data="{ momento: '{{ old('momento', 'antes') }}', cantidad: 0 }"
                              class="rounded border border-dashed border-stroke dark:border-strokedark p-3 space-y-2.5">
                            @csrf
                            <span class="text-[9px] font-bold uppercase text-black dark:text-white block">Agregar fotos</span>
                            <div class="flex gap-1.5">
                                @foreach ($momentos as $clave => $etiqueta)
                                    <label class="flex-1 cursor-pointer">
                                        <input type="radio" name="momento" value="{{ $clave }}" x-model="momento" class="sr-only">
                                        <span class="block text-center rounded border py-1.5 text-[10px] font-bold uppercase transition"
                                              :class="momento === '{{ $clave }}' ? 'border-primary bg-primary text-white' : 'border-stroke dark:border-strokedark text-bodydark2 hover:border-primary'">
                                            {{ $clave === 'otra' ? 'Otra' : $etiqueta }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            <input type="text" name="descripcion" maxlength="255" value="{{ old('descripcion') }}" placeholder="Descripción (opcional): ej. rodillo desgastado" class="{{ $input }}">
                            <label class="flex items-center gap-2 rounded border border-stroke dark:border-strokedark px-3 py-2 cursor-pointer hover:border-primary transition">
                                <i class="fas fa-camera text-primary"></i>
                                <span class="text-xs text-black dark:text-white" x-text="cantidad ? cantidad + (cantidad === 1 ? ' foto seleccionada' : ' fotos seleccionadas') : 'Tomar o elegir fotos (hasta 12)'"></span>
                                <input type="file" name="fotos[]" accept="image/*" multiple required class="sr-only"
                                       @change="cantidad = $event.target.files.length">
                            </label>
                            <button type="submit" :disabled="!cantidad"
                                    class="w-full inline-flex items-center justify-center gap-1 rounded bg-primary py-1.5 px-3 text-[10px] font-bold uppercase text-white hover:bg-opacity-90 transition disabled:opacity-50">
                                <i class="fas fa-upload text-[9px]"></i> Subir
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Derecha: firmas ───────────────────────────────────── --}}
        <div class="col-span-12 lg:col-span-5 space-y-3">

            <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="border-b border-stroke py-2.5 px-4 dark:border-strokedark">
                    <h3 class="font-bold text-[10px] uppercase tracking-wider text-black dark:text-white flex items-center gap-1.5">
                        <i class="fas fa-signature text-primary"></i> Firmas
                    </h3>
                </div>

                <div class="p-4">
                    @if ($finalizado)
                        @if ($mantenimiento->esRegistroSinFirmas())
                            <p class="text-[11px] text-bodydark2">Registro anterior a las firmas: el reporte sale sin ellas.</p>
                        @else
                            <div class="grid grid-cols-2 gap-3">
                                @foreach ([['firma-tecnico', 'Técnico', $mantenimiento->realizador?->name, null, $mantenimiento->firma_tecnico_at],
                                           ['firma-cliente', 'Cliente', $mantenimiento->cliente_nombre, trim($mantenimiento->cliente_cargo . ($mantenimiento->cliente_documento ? ' · ' . $mantenimiento->cliente_documento : ''), ' ·'), $mantenimiento->firma_cliente_at]] as [$clave, $rol, $nombre, $detalle, $fecha])
                                    <div class="text-center">
                                        <div class="h-24 rounded border border-stroke dark:border-strokedark bg-white flex items-center justify-center">
                                            <img src="{{ $archivo($clave) }}" alt="Firma {{ $rol }}" class="max-h-20 max-w-full">
                                        </div>
                                        <p class="mt-1 text-[11px] font-bold text-black dark:text-white truncate">{{ $nombre }}</p>
                                        @if ($detalle)<p class="text-[9px] text-bodydark2 truncate">{{ $detalle }}</p>@endif
                                        <p class="text-[8px] font-bold uppercase text-bodydark2">{{ $rol }} · {{ $fecha?->format('d/m/Y H:i') }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @elseif ($puedeGestionar)
                        <form method="POST" action="{{ route('impresoras.mantenimientos.firmar', [$impresora, $mantenimiento]) }}" class="space-y-4"
                              x-data="{ tecnicoListo: false, clienteListo: false }"
                              @firma-cambio="$event.detail.campo === 'firma_tecnico' ? tecnicoListo = $event.detail.lista : clienteListo = $event.detail.lista"
                              onsubmit="return confirm('Al finalizar no se podrá modificar la evidencia ni las firmas. ¿Continuar?')">
                            @csrf

                            @if ($mantenimiento->evidencias->isEmpty())
                                <p class="rounded border border-meta-6/30 bg-meta-6/10 p-2 text-[10px] text-meta-6">
                                    <i class="fas fa-exclamation-triangle mr-0.5"></i> Aún no hay fotos de evidencia. Súbalas antes de firmar si las va a incluir en el reporte.
                                </p>
                            @endif

                            {{-- Técnico --}}
                            <div>
                                <label class="text-[9px] font-bold uppercase text-black dark:text-white block mb-1">
                                    Firma del técnico <span class="text-meta-1">*</span>
                                    <span class="font-normal normal-case text-bodydark2">— {{ $mantenimiento->realizador?->name }}</span>
                                </label>
                                @include('impresoras.mantenimientos._firma_pad', ['campo' => 'firma_tecnico'])
                            </div>

                            {{-- Cliente --}}
                            <div class="space-y-2">
                                <label class="text-[9px] font-bold uppercase text-black dark:text-white block">Recibe por el cliente</label>
                                <input type="text" name="cliente_nombre" required maxlength="150" value="{{ old('cliente_nombre') }}" placeholder="Nombre completo *" class="{{ $input }}">
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="text" name="cliente_cargo" maxlength="100" value="{{ old('cliente_cargo') }}" placeholder="Cargo" class="{{ $input }}">
                                    <input type="text" name="cliente_documento" maxlength="30" value="{{ old('cliente_documento') }}" placeholder="Documento" class="{{ $input }}">
                                </div>
                                <label class="text-[9px] font-bold uppercase text-black dark:text-white block pt-1">Firma del cliente <span class="text-meta-1">*</span></label>
                                @include('impresoras.mantenimientos._firma_pad', ['campo' => 'firma_cliente'])
                            </div>

                            <button type="submit" :disabled="!tecnicoListo || !clienteListo"
                                    class="w-full inline-flex items-center justify-center gap-1.5 rounded bg-meta-3 py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fas fa-check text-[9px]"></i> Firmar y finalizar
                            </button>
                        </form>
                    @else
                        <p class="text-[11px] text-bodydark2">Pendiente de la firma del técnico y del cliente.</p>
                    @endif
                </div>
            </div>

            {{-- Equipo atendido --}}
            <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="border-b border-stroke py-2.5 px-4 dark:border-strokedark">
                    <h3 class="font-bold text-[10px] uppercase tracking-wider text-black dark:text-white flex items-center gap-1.5">
                        <i class="fas fa-print text-primary"></i> Impresora
                    </h3>
                </div>
                <div class="p-4 space-y-1.5 text-xs">
                    @foreach ([['Empresa', $impresora->empresa?->nombre], ['Sede', $impresora->sede?->nombre], ['Modelo', $impresora->modelo?->nombre_completo], ['Serie', $impresora->numero_serie], ['Ubicación', $impresora->ubicacion_fisica]] as [$etiqueta, $valor])
                        <div class="flex justify-between gap-2">
                            <span class="text-[9px] font-bold text-bodydark2 uppercase">{{ $etiqueta }}</span>
                            <span class="text-black dark:text-white font-medium text-right truncate">{{ $valor ?? '—' }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
