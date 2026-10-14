{{-- resources/views/proyectos/partials/_panel.blade.php — Panel lateral del detalle --}}
{{-- Recibe: $economia, $resumenEtapas, $solicitudesAbiertas, $proyecto, $puede, $moneda --}}

{{-- ── Resumen económico ── --}}
<div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
    <div class="border-b border-stroke py-2.5 px-4 dark:border-strokedark">
        <h3 class="font-bold text-[11px] uppercase tracking-wider text-black dark:text-white flex items-center gap-1.5">
            <i class="fas fa-sack-dollar text-primary"></i> Resumen económico
        </h3>
    </div>
    <div class="p-4 text-xs">
        @if ($economia['cotizado'] == 0 && $economia['pagos']->isEmpty())
            <p class="text-bodydark2">Sin cotización ni pagos registrados. Registrá una etapa de <b>Cotización</b> para ver el saldo.</p>
        @else
            <div class="flex justify-between py-0.5">
                <span class="text-bodydark2">Valor cotizado</span>
                <span class="font-mono font-semibold text-black dark:text-white">{{ $moneda($economia['cotizado']) }}</span>
            </div>
            <div class="flex justify-between py-0.5">
                <span class="text-bodydark2">Pagado</span>
                <span class="font-mono font-semibold text-meta-3">{{ $moneda($economia['pagado']) }}</span>
            </div>
            <div class="flex justify-between py-0.5">
                <span class="text-bodydark2">Saldo pendiente</span>
                <span class="font-mono font-semibold text-meta-6">{{ $moneda($economia['saldo']) }}</span>
            </div>

            <div class="h-2 bg-gray-2 dark:bg-meta-4 rounded-full overflow-hidden mt-2.5">
                <div class="h-full bg-meta-3 rounded-full" style="width: {{ $economia['porcentaje'] }}%"></div>
            </div>
            <div class="text-[10px] text-bodydark2 mt-1">
                {{ $economia['porcentaje'] }}% pagado
                @if ($economia['cotizacion'])
                    · según cotización del {{ $economia['cotizacion']->fecha->format('d/m/Y') }}
                @endif
            </div>

            @if ($economia['pagos']->isNotEmpty())
                <div class="mt-2.5 pt-2 border-t border-dashed border-stroke dark:border-strokedark space-y-1">
                    @foreach ($economia['pagos'] as $pago)
                        <div class="flex justify-between text-[11px] text-bodydark2">
                            <span class="truncate mr-2">{{ $pago->fecha->format('d/m/Y') }} · {{ $pago->titulo }}</span>
                            <span class="font-mono shrink-0">{{ $moneda($pago->monto) }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
    </div>
</div>

{{-- ── Mapa de etapas ── --}}
<div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
    <div class="border-b border-stroke py-2.5 px-4 dark:border-strokedark">
        <h3 class="font-bold text-[11px] uppercase tracking-wider text-black dark:text-white flex items-center gap-1.5">
            <i class="fas fa-layer-group text-primary"></i> Etapas
        </h3>
    </div>
    <div class="p-3">
        <div class="grid grid-cols-2 gap-1.5">
            @foreach (\App\Models\ProyectoEtapa::TIPOS as $clave => $cfg)
                @php $r = $resumenEtapas[$clave]; @endphp
                <button type="button"
                        @if ($puede['registrar']) @click="abrirEtapa('{{ $clave }}')" title="Registrar {{ $cfg['label'] }}" @else disabled @endif
                        class="flex items-center gap-2 rounded border border-stroke dark:border-strokedark bg-gray-2/40 dark:bg-meta-4/30 px-2 py-1.5 text-left transition
                               {{ $puede['registrar'] ? 'hover:border-primary cursor-pointer' : 'cursor-default' }}
                               {{ $r['cantidad'] ? '' : 'opacity-50 hover:opacity-100' }}">
                    <span class="w-6 h-6 rounded grid place-items-center text-white text-[10px] shrink-0" style="background: {{ $cfg['color'] }}">
                        <i class="{{ $cfg['icono'] }}"></i>
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block text-[11px] font-semibold text-black dark:text-white leading-tight truncate">{{ $cfg['label'] }}</span>
                        <span class="block text-[9px] text-bodydark2 truncate">
                            {{ $r['ultima'] ? 'Última: ' . \Illuminate\Support\Carbon::parse($r['ultima'])->format('d/m/Y') : 'Sin registros' }}
                        </span>
                    </span>
                    @if ($r['cantidad'])
                        <span class="font-mono text-[10px] text-bodydark2">{{ $r['cantidad'] }}</span>
                    @endif
                </button>
            @endforeach
        </div>
        <p class="text-[10px] text-bodydark2 mt-2">
            <i class="fas fa-circle-info"></i> Registrá cualquier etapa en cualquier orden y tantas veces como haga falta.
        </p>
    </div>
</div>

{{-- ── Solicitudes abiertas ── --}}
<div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
    <div class="flex items-center justify-between border-b border-stroke py-2.5 px-4 dark:border-strokedark">
        <h3 class="font-bold text-[11px] uppercase tracking-wider text-black dark:text-white flex items-center gap-1.5">
            <i class="fas fa-comment-dots text-[#f97316]"></i> Solicitudes abiertas
        </h3>
        <button type="button" @click="filtro = 'solicitudes'" class="text-[10px] text-primary hover:underline">Ver todas</button>
    </div>
    <div class="px-4 py-1.5">
        @forelse ($solicitudesAbiertas as $s)
            <div class="flex items-start gap-2 py-1.5 border-b border-stroke dark:border-strokedark last:border-0">
                <i class="fas fa-flag text-[10px] mt-1 {{ $s->prioridad_config['clase'] }}" title="Prioridad {{ $s->prioridad_config['label'] }}"></i>
                <div class="min-w-0 flex-1">
                    <div class="text-xs font-semibold text-black dark:text-white truncate" title="{{ $s->titulo }}">{{ $s->titulo }}</div>
                    <div class="text-[10px] text-bodydark2">{{ $s->fecha_solicitud->format('d/m/Y') }} · {{ $s->canal_config['label'] }}</div>
                </div>
                <span class="inline-flex rounded-full px-2 py-0.5 text-[9px] font-bold uppercase border shrink-0 {{ $s->clase_estado }}">{{ $s->label_estado }}</span>
            </div>
        @empty
            <p class="text-xs text-bodydark2 py-2.5">No hay solicitudes pendientes.</p>
        @endforelse
    </div>
</div>

{{-- ── Equipo ── --}}
<div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
    <div class="border-b border-stroke py-2.5 px-4 dark:border-strokedark">
        <h3 class="font-bold text-[11px] uppercase tracking-wider text-black dark:text-white flex items-center gap-1.5">
            <i class="fas fa-users text-primary"></i> Equipo
        </h3>
    </div>
    <div class="px-4 py-2 space-y-1.5">
        @foreach ($proyecto->equipo->sortByDesc(fn ($u) => $u->id === $proyecto->responsable_id) as $miembro)
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-full grid place-items-center text-[10px] font-bold text-white shrink-0"
                     style="background: {{ \App\Support\Proyectos\Avatar::color($miembro->id) }}">
                    {{ \App\Support\Proyectos\Avatar::iniciales($miembro->name) }}
                </div>
                <div class="min-w-0">
                    <div class="text-xs font-semibold text-black dark:text-white truncate">
                        {{ $miembro->name }}
                        @if ($miembro->id === $proyecto->responsable_id)
                            <i class="fas fa-star text-[9px] text-meta-6" title="Responsable"></i>
                        @endif
                    </div>
                    <div class="text-[10px] text-bodydark2">{{ $miembro->pivot->rol ?: 'Miembro' }}</div>
                </div>
            </div>
        @endforeach
    </div>
</div>
