{{-- resources/views/impresoras/show.blade.php --}}
<x-app-layout>
    <x-slot name="title">{{ $impresora->nombre_visible }}</x-slot>

    @if (session('exito'))
        <div class="mb-3 flex w-full border-l-4 border-meta-3 bg-meta-3/10 px-4 py-2.5 shadow-sm rounded-sm">
            <i class="fas fa-check-circle text-meta-3 mr-2 mt-0.5 text-xs"></i>
            <p class="text-xs text-meta-3 font-medium">{{ session('exito') }}</p>
        </div>
    @endif

    {{-- ── Cabecera Compacta ────────────────────────────────── --}}
    <div class="mb-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-stroke pb-2.5 dark:border-strokedark">
        <div class="flex items-center gap-2.5 flex-wrap min-w-0">
            <span class="font-mono text-xs font-bold text-primary bg-primary/10 px-2 py-0.5 rounded border border-primary/20">
                {{ $impresora->codigo }}
            </span>
            @if ($impresora->codigo_interno)
                <span class="font-mono text-[11px] text-bodydark2 bg-gray-2 dark:bg-meta-4 px-2 py-0.5 rounded border border-stroke dark:border-strokedark">
                    #{{ $impresora->codigo_interno }}
                </span>
            @endif

            <h2 class="text-title-md font-bold text-black dark:text-white truncate" title="{{ $impresora->nombre_visible }}">
                {{ $impresora->nombre_visible }}
            </h2>

            @php
                $cfgEstado = match ($impresora->estado) {
                    'activa'           => ['clase' => 'bg-meta-3/10 text-meta-3 border-meta-3/20', 'texto' => 'Activa'],
                    'en_mantenimiento' => ['clase' => 'bg-meta-6/10 text-meta-6 border-meta-6/20', 'texto' => 'En mantenimiento'],
                    'dada_de_baja'     => ['clase' => 'bg-meta-1/10 text-meta-1 border-meta-1/20', 'texto' => 'Dada de baja'],
                    default            => ['clase' => 'bg-gray-2 text-bodydark2 border-stroke', 'texto' => ucfirst($impresora->estado)],
                };
            @endphp
            <span class="inline-flex rounded-full px-2 py-0.5 text-[9px] font-bold uppercase border {{ $cfgEstado['clase'] }}">
                {{ $cfgEstado['texto'] }}
            </span>
        </div>

        {{-- Acciones --}}
        <div class="flex items-center gap-1.5 shrink-0">
            @if ($puede['editar'])
                <a href="{{ route('impresoras.edit', $impresora) }}"
                   class="inline-flex items-center gap-1 rounded bg-primary py-1 px-3 text-xs font-bold uppercase text-white hover:bg-opacity-90 transition shadow-sm">
                    <i class="fas fa-pen text-[10px]"></i> Editar
                </a>
            @endif

            @if ($puede['eliminar'])
                <form method="POST" action="{{ route('impresoras.destroy', $impresora) }}" class="inline"
                      onsubmit="return confirm('¿Eliminar la impresora {{ $impresora->codigo }}?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-1 rounded border border-meta-1 py-1 px-2 text-xs font-bold uppercase text-meta-1 hover:bg-meta-1/10 transition"
                            title="Eliminar Equipo">
                        <i class="fas fa-trash text-[10px]"></i>
                    </button>
                </form>
            @endif

            <a href="{{ route('impresoras.index') }}"
               class="inline-flex items-center gap-1 rounded border border-stroke py-1 px-3 text-xs font-bold text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition shadow-sm">
                <i class="fas fa-arrow-left text-[10px]"></i> Volver
            </a>
        </div>
    </div>

    {{-- ── Strip de Métricas Rápidas (4 KPIs) ─────────────────────── --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-3">
        <div class="rounded-sm border border-stroke bg-white p-2 shadow-default dark:border-strokedark dark:bg-boxdark flex items-center gap-2">
            <div class="flex h-7 w-7 items-center justify-center rounded bg-primary/10 text-primary shrink-0">
                <i class="fas {{ $impresora->modelo?->tipo_tecnologia === 'laser' ? 'fa-print' : 'fa-fill-drip' }} text-xs"></i>
            </div>
            <div class="min-w-0">
                <span class="text-[8px] font-bold text-bodydark2 uppercase block">Modelo</span>
                <span class="text-[11px] font-bold text-black dark:text-white truncate block" title="{{ $impresora->modelo?->nombre_completo ?? 'No especificado' }}">
                    {{ $impresora->modelo?->nombre_completo ?? 'Genérica' }}
                </span>
            </div>
        </div>

        {{-- Contador de páginas + modal para registrar lecturas --}}
        @include('impresoras.partials._contador')

        <div class="rounded-sm border border-stroke bg-white p-2 shadow-default dark:border-strokedark dark:bg-boxdark flex items-center gap-2">
            <div class="flex h-7 w-7 items-center justify-center rounded bg-meta-6/10 text-meta-6 shrink-0">
                <i class="fas fa-network-wired text-xs"></i>
            </div>
            <div class="min-w-0">
                <span class="text-[8px] font-bold text-bodydark2 uppercase block">Dirección IP</span>
                <span class="text-[11px] font-bold font-mono text-black dark:text-white truncate block">
                    {{ $impresora->ip_red ?? 'Sin IP' }}
                </span>
            </div>
        </div>

        <div class="rounded-sm border border-stroke bg-white p-2 shadow-default dark:border-strokedark dark:bg-boxdark flex items-center gap-2">
            <div class="flex h-7 w-7 items-center justify-center rounded bg-gray-2 text-bodydark2 dark:bg-meta-4 shrink-0">
                <i class="fas fa-barcode text-xs"></i>
            </div>
            <div class="min-w-0">
                <span class="text-[8px] font-bold text-bodydark2 uppercase block">N° de Serie</span>
                <span class="text-[11px] font-bold font-mono text-black dark:text-white truncate block" title="{{ $impresora->numero_serie }}">
                    {{ $impresora->numero_serie ?? '—' }}
                </span>
            </div>
        </div>
    </div>

    {{-- ── Grid Principal (8 Cols Izq / 4 Cols Der) ─────────────── --}}
    <div class="grid grid-cols-12 gap-3">

        {{-- COLUMNA IZQUIERDA: Consumibles & Mantenimientos (8 cols) --}}
        <div class="col-span-12 lg:col-span-8 space-y-3">

            @include('impresoras.partials._consumibles')

            @include('impresoras.partials._mantenimientos')

            @if ($impresora->observaciones)
                <div class="rounded-sm border border-stroke bg-white p-3 shadow-default dark:border-strokedark dark:bg-boxdark">
                    <h3 class="font-bold text-[10px] uppercase tracking-wider text-black dark:text-white border-b border-stroke pb-1 mb-1.5 dark:border-strokedark flex items-center gap-1.5">
                        <i class="fas fa-sticky-note text-primary"></i> Observaciones
                    </h3>
                    <p class="text-xs text-black dark:text-white leading-relaxed whitespace-pre-line">
                        {{ $impresora->observaciones }}
                    </p>
                </div>
            @endif
        </div>

        {{-- COLUMNA DERECHA: Ficha Técnica & Datos de Red (4 cols) --}}
        <div class="col-span-12 lg:col-span-4 space-y-3">

            <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="border-b border-stroke py-2 px-3 dark:border-strokedark">
                    <h3 class="font-bold text-black dark:text-white text-[10px] uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fas fa-map-marker-alt text-primary"></i> Ubicación & Asignación
                    </h3>
                </div>
                <div class="p-3 space-y-2 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="text-[9px] font-bold text-bodydark2 uppercase">Empresa</span>
                        <span class="text-black dark:text-white font-bold truncate max-w-[140px]">{{ $impresora->empresa?->nombre ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[9px] font-bold text-bodydark2 uppercase">Sede</span>
                        <span class="text-black dark:text-white font-semibold truncate max-w-[140px]">{{ $impresora->sede?->nombre ?? 'Sin sede' }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-bodydark2 uppercase block">Ubicación Física</span>
                        <span class="text-black dark:text-white font-medium block mt-0.5">{{ $impresora->ubicacion_fisica ?? '—' }}</span>
                    </div>
                </div>
            </div>

            <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark" x-data="{ mostrarPass: false }">
                <div class="border-b border-stroke py-2 px-3 dark:border-strokedark flex items-center justify-between">
                    <h3 class="font-bold text-black dark:text-white text-[10px] uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fas fa-network-wired text-primary"></i> Red & Panel Web
                    </h3>
                    @if ($impresora->ip_red)
                        <a href="http://{{ $impresora->ip_red }}" target="_blank" rel="noopener"
                           class="inline-flex items-center gap-1 text-[9px] font-bold text-primary hover:underline uppercase">
                            Panel <i class="fas fa-external-link-alt text-[8px]"></i>
                        </a>
                    @endif
                </div>

                <div class="p-3 space-y-2 text-xs">
                    <div class="grid grid-cols-2 gap-2 font-mono text-[10px]">
                        <div>
                            <span class="text-[8px] font-bold text-bodydark2 font-sans uppercase block">Hostname</span>
                            <span class="text-black dark:text-white truncate block">{{ $impresora->hostname ?? '—' }}</span>
                        </div>
                        <div>
                            <span class="text-[8px] font-bold text-bodydark2 font-sans uppercase block">MAC</span>
                            <span class="text-black dark:text-white truncate block">{{ $impresora->mac_address ?? '—' }}</span>
                        </div>
                    </div>

                    @if ($impresora->panel_web_usuario || $impresora->panel_web_password)
                        <div class="pt-2 border-t border-dashed border-stroke dark:border-strokedark">
                            <div class="bg-gray-2 dark:bg-meta-4/40 p-2 rounded border border-stroke dark:border-strokedark space-y-1 font-mono text-[10px]">
                                @if ($impresora->panel_web_usuario)
                                    <div class="flex items-center justify-between">
                                        <span class="text-bodydark2 font-sans">Usuario:</span>
                                        <span class="text-black dark:text-white font-bold">{{ $impresora->panel_web_usuario }}</span>
                                    </div>
                                @endif
                                @if ($impresora->panel_web_password)
                                    <div class="flex items-center justify-between">
                                        <span class="text-bodydark2 font-sans">Clave:</span>
                                        <div class="flex items-center gap-1">
                                            <span x-show="!mostrarPass" class="text-black dark:text-white">••••••••</span>
                                            <span x-show="mostrarPass" x-cloak class="text-black dark:text-white font-bold">{{ $impresora->panel_web_password }}</span>
                                            <button type="button" @click="mostrarPass = !mostrarPass" class="text-primary hover:text-opacity-80 ml-1">
                                                <i class="fas text-[9px]" :class="mostrarPass ? 'fa-eye-slash' : 'fa-eye'"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="border-b border-stroke py-2 px-3 dark:border-strokedark">
                    <h3 class="font-bold text-black dark:text-white text-[10px] uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fas fa-info-circle text-primary"></i> Registro
                    </h3>
                </div>
                <div class="p-3 space-y-1.5 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="text-[9px] font-bold text-bodydark2 uppercase">Adquisición</span>
                        <span class="text-black dark:text-white font-mono text-[10px]">{{ $impresora->fecha_adquisicion?->format('d/m/Y') ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[9px] font-bold text-bodydark2 uppercase">Creador</span>
                        <span class="text-black dark:text-white font-medium text-[10px] truncate max-w-[110px]">{{ $impresora->creador?->name ?? 'Sistema' }}</span>
                    </div>
                </div>
            </div>

            @include('tickets.partials._tickets_activo', ['activo' => $impresora])

        </div>
    </div>
</x-app-layout>