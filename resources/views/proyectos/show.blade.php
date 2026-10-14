{{-- resources/views/proyectos/show.blade.php — Detalle del proyecto con línea de tiempo --}}
<x-app-layout>
    <x-slot name="title">{{ $proyecto->codigo }} · {{ $proyecto->nombre }}</x-slot>

    @php
        $moneda = fn ($n) => '$ ' . number_format((float) $n, 0, ',', '.');

        // Configuración para el componente Alpine (modales)
        $config = [
            'hoy'            => now()->format('Y-m-d'),
            'conteoPorTipo'  => collect($resumenEtapas)->map(fn ($r) => $r['cantidad']),
            'tiposQueAtienden' => \App\Models\ProyectoEtapa::TIPOS_QUE_ATIENDEN,
            'etiquetas'      => collect(\App\Models\ProyectoEtapa::TIPOS)->map(fn ($t) => $t['label']),
            'maxBytes'       => \App\Models\ProyectoAdjunto::MAX_KB * 1024,
            'maxArchivos'    => \App\Models\ProyectoAdjunto::MAX_ARCHIVOS,
            'urls'           => [
                'etapaStore'     => route('proyectos.etapas.store', $proyecto),
                'solicitudStore' => route('proyectos.solicitudes.store', $proyecto),
            ],
            // Si la validación falló, se reabre el modal con lo que se había escrito
            'reabrir'        => old('_modal'),
            'old'            => old() ? collect(old())->except(['_token', 'archivos'])->all() : null,
        ];
    @endphp

    @include('proyectos.partials._flash')

    <div x-data="proyectoShow(@js($config))" x-init="iniciar()">

        {{-- ═════════════ CABECERA ═════════════ --}}
        <div class="mb-3 flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between border-b border-stroke pb-3 dark:border-strokedark">
            <div class="min-w-0">
                <div class="flex items-center gap-2 flex-wrap text-[11px]">
                    <span class="font-mono text-xs font-bold text-primary bg-primary/10 px-2 py-0.5 rounded border border-primary/20">{{ $proyecto->codigo }}</span>
                    <span class="inline-flex rounded-full px-2 py-0.5 text-[9px] font-bold uppercase border {{ $proyecto->clase_estado }}">{{ $proyecto->label_estado }}</span>
                    <span class="text-bodydark2">
                        Iniciado {{ $proyecto->fecha_inicio->format('d/m/Y') }}
                        @if ($proyecto->fecha_entrega_estimada)
                            · Entrega estimada
                            <span class="{{ $kpis['vencido'] ? 'text-meta-1 font-bold' : '' }}">{{ $proyecto->fecha_entrega_estimada->format('d/m/Y') }}</span>
                        @endif
                        @if ($proyecto->fecha_cierre)
                            · Cerrado {{ $proyecto->fecha_cierre->format('d/m/Y') }}
                        @endif
                    </span>
                </div>

                <h2 class="text-title-md font-bold text-black dark:text-white mt-1.5">{{ $proyecto->nombre }}</h2>

                <div class="text-xs text-bodydark2 mt-0.5">
                    <i class="fas fa-building text-primary mr-1"></i>
                    <span class="text-black dark:text-white font-medium">{{ $proyecto->cliente?->nombre }}</span>
                    @if ($proyecto->cliente?->documento_completo) · {{ $proyecto->cliente->documento_completo }} @endif
                    @if ($proyecto->cliente?->contacto_nombre) · Contacto: {{ $proyecto->cliente->contacto_nombre }} @endif
                    @if ($proyecto->cliente?->telefono) · {{ $proyecto->cliente->telefono }} @endif
                </div>

                @if ($proyecto->descripcion)
                    <p class="text-xs text-bodydark2 mt-1.5 max-w-3xl whitespace-pre-line">{{ $proyecto->descripcion }}</p>
                @endif
            </div>

            <div class="flex flex-col items-start lg:items-end gap-2 shrink-0">
                {{-- Avatares del equipo --}}
                <div class="flex items-center">
                    @foreach ($proyecto->equipo as $miembro)
                        <div class="w-7 h-7 rounded-full grid place-items-center text-[10px] font-bold text-white border-2 border-white dark:border-boxdark {{ $loop->first ? '' : '-ml-1.5' }}"
                             style="background: {{ \App\Support\Proyectos\Avatar::color($miembro->id) }}"
                             title="{{ $miembro->name }}{{ $miembro->pivot->rol ? ' · ' . $miembro->pivot->rol : '' }}">
                            {{ \App\Support\Proyectos\Avatar::iniciales($miembro->name) }}
                        </div>
                    @endforeach
                </div>

                <div class="flex items-center gap-1.5 flex-wrap">
                    <a href="{{ route('proyectos.index') }}"
                       class="inline-flex items-center gap-1 rounded border border-stroke py-1 px-2.5 text-xs font-medium hover:bg-gray dark:border-strokedark dark:hover:bg-meta-4 text-black dark:text-white">
                        <i class="fas fa-arrow-left text-[10px]"></i>
                    </a>
                    @if ($puede['editar'])
                        <a href="{{ route('proyectos.edit', $proyecto) }}"
                           class="inline-flex items-center gap-1 rounded border border-stroke py-1 px-3 text-xs font-bold uppercase hover:border-primary hover:text-primary dark:border-strokedark text-black dark:text-white">
                            <i class="fas fa-pen text-[10px]"></i> Editar
                        </a>
                    @endif
                    @if ($puede['eliminar'])
                        <form method="POST" action="{{ route('proyectos.destroy', $proyecto) }}" class="inline"
                              onsubmit="return confirm('¿Eliminar el proyecto {{ $proyecto->codigo }}? Se ocultará con todo su historial.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1 rounded border border-meta-1/40 py-1 px-2.5 text-xs text-meta-1 hover:bg-meta-1/10" title="Eliminar proyecto">
                                <i class="fas fa-trash text-[10px]"></i>
                            </button>
                        </form>
                    @endif
                    @if ($puede['registrar'])
                        <button type="button" @click="abrirSolicitud()"
                                class="inline-flex items-center gap-1 rounded border border-[#f97316]/40 bg-[#f97316]/10 py-1 px-3 text-xs font-bold uppercase text-[#f97316] hover:bg-[#f97316]/20">
                            <i class="fas fa-comment-dots text-[10px]"></i> Nueva solicitud
                        </button>
                        <button type="button" @click="abrirEtapa()"
                                class="inline-flex items-center gap-1 rounded bg-primary py-1 px-3 text-xs font-bold uppercase text-white hover:bg-opacity-90 shadow-sm">
                            <i class="fas fa-plus text-[10px]"></i> Registrar etapa
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- ═════════════ KPIs ═════════════ --}}
        <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 mb-3">
            <div class="rounded-sm border border-stroke bg-white dark:border-strokedark dark:bg-boxdark px-3 py-2.5 flex items-center gap-3">
                <div class="w-9 h-9 rounded grid place-items-center bg-primary/10 text-primary shrink-0"><i class="fas fa-calendar-day text-sm"></i></div>
                <div class="flex-1 min-w-0">
                    <div class="text-lg font-bold text-black dark:text-white leading-tight">
                        {{ $kpis['dias_transcurridos'] }}
                        @if ($kpis['dias_totales'])
                            <span class="text-xs font-medium text-bodydark2">de {{ $kpis['dias_totales'] }} días</span>
                        @else
                            <span class="text-xs font-medium text-bodydark2">días</span>
                        @endif
                    </div>
                    <div class="text-[9px] uppercase tracking-wider font-bold text-bodydark2">Tiempo transcurrido</div>
                    @if ($kpis['porcentaje_tiempo'] !== null)
                        <div class="h-1 bg-gray-2 dark:bg-meta-4 rounded mt-1 overflow-hidden">
                            <div class="h-full {{ $kpis['vencido'] ? 'bg-meta-1' : 'bg-primary' }}" style="width: {{ $kpis['porcentaje_tiempo'] }}%"></div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="rounded-sm border border-stroke bg-white dark:border-strokedark dark:bg-boxdark px-3 py-2.5 flex items-center gap-3">
                <div class="w-9 h-9 rounded grid place-items-center shrink-0" style="background: rgba(6,182,212,.12); color: #06b6d4"><i class="fas fa-box-open text-sm"></i></div>
                <div>
                    <div class="text-lg font-bold text-black dark:text-white leading-tight">{{ $kpis['entregas'] }}</div>
                    <div class="text-[9px] uppercase tracking-wider font-bold text-bodydark2">Entregas realizadas</div>
                </div>
            </div>

            <div class="rounded-sm border border-stroke bg-white dark:border-strokedark dark:bg-boxdark px-3 py-2.5 flex items-center gap-3">
                <div class="w-9 h-9 rounded grid place-items-center shrink-0 bg-[#f97316]/10 text-[#f97316]"><i class="fas fa-comment-dots text-sm"></i></div>
                <div>
                    <div class="text-lg font-bold text-black dark:text-white leading-tight">{{ $kpis['solicitudes_abiertas'] }}</div>
                    <div class="text-[9px] uppercase tracking-wider font-bold text-bodydark2">Solicitudes abiertas</div>
                </div>
            </div>

            <div class="rounded-sm border border-stroke bg-white dark:border-strokedark dark:bg-boxdark px-3 py-2.5 flex items-center gap-3">
                <div class="w-9 h-9 rounded grid place-items-center shrink-0 bg-meta-3/10 text-meta-3"><i class="fas fa-sack-dollar text-sm"></i></div>
                <div>
                    <div class="text-base font-bold font-mono text-black dark:text-white leading-tight">{{ $moneda($economia['saldo']) }}</div>
                    <div class="text-[9px] uppercase tracking-wider font-bold text-bodydark2">Saldo pendiente</div>
                </div>
            </div>
        </div>

        {{-- ═════════════ CUERPO ═════════════ --}}
        <div class="grid grid-cols-12 gap-3 items-start">
            <div class="col-span-12 xl:col-span-8">
                @include('proyectos.partials._timeline')
            </div>
            <div class="col-span-12 xl:col-span-4 space-y-3">
                @include('proyectos.partials._panel')
            </div>
        </div>

        {{-- ═════════════ MODALES ═════════════ --}}
        @if ($puede['registrar'])
            @include('proyectos.partials._modal_etapa')
            @include('proyectos.partials._modal_solicitud')
        @endif
    </div>

    @push('scripts')
        @include('proyectos.partials._scripts')
    @endpush
</x-app-layout>
