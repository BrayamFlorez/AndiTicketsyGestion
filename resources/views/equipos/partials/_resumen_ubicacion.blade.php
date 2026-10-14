{{-- resources/views/equipos/partials/_resumen_ubicacion.blade.php --}}
{{-- Árbol empresa → sedes con la cantidad de equipos. Cada fila es un enlace que aplica el filtro. --}}
{{-- Recibe: $resumen (EquipoController::resumenPorUbicacion), $filtros --}}

@php
    // Al elegir una ubicación se conservan la búsqueda, la categoría y el estado
    $base       = collect($filtros)->only(['q', 'categoria', 'estado'])->all();
    $empresaSel = (string) ($filtros['empresa_id'] ?? '');
    $sedeSel    = (string) ($filtros['sede_id'] ?? '');
    $totalGeneral = $resumen->sum('total');
    $hayFiltros   = ! empty($base);

    $fila   = 'flex items-center gap-1.5 rounded-sm px-2 py-1 text-[11px] transition';
    $activa = 'bg-primary/10 text-primary font-bold';
    $normal = 'text-black dark:text-white hover:bg-gray-2 dark:hover:bg-meta-4/50';
    $badge  = 'ml-auto shrink-0 rounded-full px-1.5 font-mono text-[9px] font-bold';
@endphp

<aside x-data="{ abierto: false, buscar: '' }"
       class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark lg:sticky lg:top-20">

    {{-- Encabezado: en pantallas chicas abre y cierra el panel --}}
    <button type="button" @click="abierto = !abierto"
            class="flex w-full items-center gap-1.5 border-b border-stroke dark:border-strokedark px-3 py-2 text-left lg:cursor-default">
        <i class="fas fa-sitemap text-primary text-xs"></i>
        <span class="text-[10px] font-bold uppercase tracking-wider text-black dark:text-white">Ubicaciones</span>
        <span class="ml-auto font-mono text-[10px] text-bodydark2">{{ $totalGeneral }} equipos</span>
        <i class="fas fa-chevron-down text-[9px] text-bodydark2 transition lg:hidden" :class="abierto && 'rotate-180'"></i>
    </button>

    <div class="p-2 lg:block lg:max-h-[calc(100vh-9rem)] lg:overflow-y-auto" :class="abierto ? 'block' : 'hidden'">

        @if ($hayFiltros)
            <p class="mb-1.5 px-2 text-[9px] text-bodydark2 leading-tight">
                <i class="fas fa-filter text-[8px]"></i> Cantidades según la búsqueda, categoría y estado aplicados.
            </p>
        @endif

        @if ($resumen->count() > 6)
            <div class="relative mb-1.5">
                <i class="fas fa-search absolute left-2 top-1/2 -translate-y-1/2 text-[9px] text-bodydark2"></i>
                <input type="text" x-model="buscar" placeholder="Filtrar empresa o sede..."
                       class="w-full rounded border border-stroke bg-transparent py-0.5 pl-6 pr-2 text-[11px] outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
            </div>
        @endif

        {{-- Todas --}}
        <a href="{{ route('equipos.index', $base) }}"
           class="{{ $fila }} {{ $empresaSel === '' ? $activa : $normal }}">
            <i class="fas fa-layer-group w-3.5 text-center text-[10px]"></i>
            <span>Todas las ubicaciones</span>
            <span class="{{ $badge }} {{ $empresaSel === '' ? 'bg-primary text-white' : 'bg-gray-2 text-bodydark2 dark:bg-meta-4' }}">{{ $totalGeneral }}</span>
        </a>

        <div class="mt-1 space-y-0.5">
            @forelse ($resumen as $empresa)
                @php
                    $esEmpresaSel  = $empresaSel === (string) $empresa['id'];
                    $soloEmpresa   = $esEmpresaSel && $sedeSel === '';
                    // Texto para el filtro rápido del panel
                    $textoBusqueda = mb_strtolower($empresa['nombre'] . ' ' . $empresa['sedes']->pluck('nombre')->implode(' '));
                @endphp

                <div x-data="{ expandida: @js($esEmpresaSel || $resumen->count() === 1) }"
                     x-show="! buscar || @js($textoBusqueda).includes(buscar.toLowerCase())">

                    {{-- Empresa --}}
                    <div class="flex items-center">
                        <button type="button" @click="expandida = !expandida"
                                class="flex h-5 w-5 shrink-0 items-center justify-center text-bodydark2 hover:text-primary"
                                :title="expandida ? 'Contraer' : 'Ver sedes'">
                            <i class="fas fa-chevron-right text-[8px] transition" :class="(expandida || buscar) && 'rotate-90'"></i>
                        </button>
                        <a href="{{ route('equipos.index', $base + ['empresa_id' => $empresa['id']]) }}"
                           class="{{ $fila }} flex-1 min-w-0 {{ $soloEmpresa ? $activa : $normal }} {{ $esEmpresaSel ? 'font-bold' : 'font-semibold' }}">
                            <i class="fas fa-building w-3.5 text-center text-[10px] {{ $esEmpresaSel ? 'text-primary' : 'text-bodydark2' }}"></i>
                            <span class="truncate" title="{{ $empresa['nombre'] }}">{{ $empresa['nombre'] }}</span>
                            <span class="{{ $badge }} {{ $soloEmpresa ? 'bg-primary text-white' : 'bg-gray-2 text-bodydark2 dark:bg-meta-4' }}">{{ $empresa['total'] }}</span>
                        </a>
                    </div>

                    {{-- Sedes --}}
                    <div x-show="expandida || buscar" x-transition.opacity.duration.150ms
                         class="ml-[18px] mt-0.5 space-y-0.5 border-l border-stroke dark:border-strokedark pl-1.5"
                         @if (! ($esEmpresaSel || $resumen->count() === 1)) style="display: none;" @endif>

                        @forelse ($empresa['sedes'] as $sede)
                            @php $esSedeSel = $esEmpresaSel && $sedeSel === (string) $sede['id']; @endphp
                            <a href="{{ route('equipos.index', $base + ['empresa_id' => $empresa['id'], 'sede_id' => $sede['id']]) }}"
                               class="{{ $fila }} {{ $esSedeSel ? $activa : $normal }} {{ $sede['total'] === 0 && ! $esSedeSel ? 'opacity-50' : '' }}">
                                <i class="fas fa-map-marker-alt w-3 text-center text-[9px] {{ $esSedeSel ? 'text-primary' : 'text-bodydark2' }}"></i>
                                <span class="truncate" title="{{ $sede['nombre'] }}">{{ $sede['nombre'] }}</span>
                                <span class="{{ $badge }} {{ $esSedeSel ? 'bg-primary text-white' : 'bg-gray-2 text-bodydark2 dark:bg-meta-4' }}">{{ $sede['total'] }}</span>
                            </a>
                        @empty
                            @if ($empresa['sin_sede'] === 0)
                                <p class="px-2 py-1 text-[10px] italic text-bodydark2">Sin sedes registradas</p>
                            @endif
                        @endforelse

                        @if ($empresa['sin_sede'] > 0)
                            @php $esSinSedeSel = $esEmpresaSel && $sedeSel === 'sin'; @endphp
                            <a href="{{ route('equipos.index', $base + ['empresa_id' => $empresa['id'], 'sede_id' => 'sin']) }}"
                               class="{{ $fila }} italic {{ $esSinSedeSel ? $activa : $normal }}">
                                <i class="fas fa-circle-question w-3 text-center text-[9px] {{ $esSinSedeSel ? 'text-primary' : 'text-meta-6' }}"></i>
                                <span class="truncate">Sin sede asignada</span>
                                <span class="{{ $badge }} {{ $esSinSedeSel ? 'bg-primary text-white' : 'bg-meta-6/15 text-meta-6' }}">{{ $empresa['sin_sede'] }}</span>
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <p class="px-2 py-3 text-center text-[11px] text-bodydark2">No tienes empresas asignadas.</p>
            @endforelse
        </div>
    </div>
</aside>
