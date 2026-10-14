{{-- resources/views/equipos/show.blade.php --}}
<x-app-layout>
    <x-slot name="title">Detalle de Equipo: {{ $equipo->nombre }}</x-slot>

    {{-- Alert de éxito --}}
    @if (session('success'))
        <div class="mb-3 flex w-full border-l-4 border-meta-3 bg-meta-3/10 px-4 py-2.5 shadow-sm rounded-sm">
            <i class="fas fa-check-circle text-meta-3 mr-2 mt-0.5 text-xs"></i>
            <p class="text-xs text-meta-3 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    {{-- ── Cabecera Compacta & Acciones ────────────────────────────────────── --}}
    <div class="mb-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-stroke pb-2.5 dark:border-strokedark">
        <div class="flex items-center gap-2.5 flex-wrap min-w-0">
            <span class="font-mono text-xs font-bold text-primary bg-primary/10 px-2 py-0.5 rounded border border-primary/20">
                {{ $equipo->numero_serie ?? 'SIN-SERIE' }}
            </span>
            @if ($equipo->codigo_interno)
                <span class="font-mono text-[11px] text-bodydark2 bg-gray-2 dark:bg-meta-4 px-2 py-0.5 rounded border border-stroke dark:border-strokedark" title="Código interno">
                    #{{ $equipo->codigo_interno }}
                </span>
            @endif

            <h2 class="text-title-md font-bold text-black dark:text-white truncate" title="{{ $equipo->nombre }}">
                {{ $equipo->nombre }}
            </h2>

            @php
                $colorEst = match($equipo->estado) {
                    'activo'        => 'bg-meta-3/10 text-meta-3 border-meta-3/20',
                    'mantenimiento' => 'bg-meta-6/10 text-meta-6 border-meta-6/20',
                    'dado_de_baja'  => 'bg-meta-1/10 text-meta-1 border-meta-1/20',
                    default         => 'bg-primary/10 text-primary border-primary/20',
                };
            @endphp
            <span class="inline-flex rounded-full px-2 py-0.5 text-[9px] font-bold uppercase border {{ $colorEst }}">
                {{ $equipo->label_estado }}
            </span>
        </div>

        {{-- Acciones del Equipo --}}
        <div class="flex items-center gap-1.5 shrink-0">
            @if ($equipo->admiteMantenimiento())
                @can('equipos.mantenimientos.gestionar')
                    <a href="{{ route('equipos.mantenimientos.create', $equipo) }}"
                       class="inline-flex items-center gap-1 rounded bg-meta-3 py-1 px-3 text-xs font-bold uppercase text-white hover:bg-opacity-90 transition shadow-sm">
                        <i class="fas fa-tools text-[10px]"></i> Registrar servicio
                    </a>
                @endcan
            @endif

            @can('equipos.editar')
                <a href="{{ route('equipos.edit', $equipo) }}" 
                   class="inline-flex items-center gap-1 rounded border border-stroke py-1 px-3 text-xs font-bold text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition shadow-sm">
                    <i class="fas fa-edit text-[10px]"></i> Editar
                </a>
            @endcan

            <a href="{{ route('equipos.index') }}"
               class="inline-flex items-center gap-1 rounded border border-stroke py-1 px-3 text-xs font-bold text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition shadow-sm">
                <i class="fas fa-arrow-left text-[10px]"></i> Volver
            </a>
        </div>
    </div>

    {{-- ── Strip de Métricas Rápidas (4 KPIs Superiores) ─────────────────────── --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-3">
        <div class="rounded-sm border border-stroke bg-white p-2 shadow-default dark:border-strokedark dark:bg-boxdark flex items-center gap-2">
            <div class="flex h-7 w-7 items-center justify-center rounded bg-primary/10 text-primary shrink-0">
                <i class="fas fa-desktop text-xs"></i>
            </div>
            <div class="min-w-0">
                <span class="text-[8px] font-bold text-bodydark2 uppercase block">Categoría</span>
                <span class="text-[11px] font-bold text-black dark:text-white truncate block" title="{{ $equipo->label_categoria }}">
                    {{ $equipo->label_categoria }}
                </span>
            </div>
        </div>

        <div class="rounded-sm border border-stroke bg-white p-2 shadow-default dark:border-strokedark dark:bg-boxdark flex items-center gap-2">
            <div class="flex h-7 w-7 items-center justify-center rounded bg-meta-3/10 text-meta-3 shrink-0">
                <i class="fas fa-microchip text-xs"></i>
            </div>
            <div class="min-w-0">
                <span class="text-[8px] font-bold text-bodydark2 uppercase block">Marca / Modelo</span>
                <span class="text-[11px] font-bold text-black dark:text-white truncate block" title="{{ $equipo->marca }} {{ $equipo->modelo }}">
                    {{ $equipo->marca ?? 'Genérica' }} {{ $equipo->modelo ? '· '.$equipo->modelo : '' }}
                </span>
            </div>
        </div>

        <div class="rounded-sm border border-stroke bg-white p-2 shadow-default dark:border-strokedark dark:bg-boxdark flex items-center gap-2">
            <div class="flex h-7 w-7 items-center justify-center rounded bg-meta-6/10 text-meta-6 shrink-0">
                <i class="fas fa-user-shield text-xs"></i>
            </div>
            <div class="min-w-0">
                <span class="text-[8px] font-bold text-bodydark2 uppercase block">Responsable</span>
                <span class="text-[11px] font-bold text-black dark:text-white truncate block" title="{{ $equipo->responsable->name ?? 'Sin asignar' }}">
                    {{ $equipo->responsable->name ?? 'Sin Asignar' }}
                </span>
            </div>
        </div>

        <div class="rounded-sm border border-stroke bg-white p-2 shadow-default dark:border-strokedark dark:bg-boxdark flex items-center gap-2">
            <div class="flex h-7 w-7 items-center justify-center rounded bg-gray-2 text-bodydark2 dark:bg-meta-4 shrink-0">
                <i class="fas fa-history text-xs"></i>
            </div>
            <div class="min-w-0">
                <span class="text-[8px] font-bold text-bodydark2 uppercase block">Mantenimientos</span>
                <span class="text-[11px] font-bold font-mono text-black dark:text-white block">
                    {{ $equipo->mantenimientos->count() }} Registrados
                </span>
            </div>
        </div>
    </div>

    {{-- ── Grid Principal de Trabajo (8 Cols Izq / 4 Cols Der) ─────────────── --}}
    <div class="grid grid-cols-12 gap-3">

        {{-- COLUMNA IZQUIERDA: HUD Especificaciones & Mantenimientos (8 cols) --}}
        <div class="col-span-12 lg:col-span-8 space-y-3">

            {{-- Card Diagnóstico / Vectorial de Hardware --}}
            <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="flex items-center justify-between border-b border-stroke py-2.5 px-4 dark:border-strokedark">
                    <h3 class="font-bold text-[11px] uppercase tracking-wider text-black dark:text-white flex items-center gap-1.5">
                        <i class="fas fa-microchip text-primary"></i>
                        Especificaciones Técnicas del Hardware
                    </h3>
                    <span class="font-mono text-[9px] font-bold text-primary bg-primary/10 px-1.5 py-0.5 rounded">
                        DIAGNOSTIC-NODE // OK
                    </span>
                </div>

                <div class="p-3.5">
                    <div class="grid grid-cols-12 gap-3 items-center">

                        {{-- Esquema Vectorial de Estación / Servidor --}}
                        <div class="col-span-12 md:col-span-4 lg:col-span-3 border-b md:border-b-0 md:border-r border-stroke dark:border-strokedark pb-3 md:pb-0 md:pr-3 flex flex-col items-center justify-center text-center">
                            <div class="relative w-28 h-24 flex items-center justify-center">
                                <svg class="w-full h-full" viewBox="0 0 200 160" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <!-- Monitor / Pantalla -->
                                    <rect x="25" y="20" width="105" height="75" rx="4" class="fill-gray-2 dark:fill-meta-4 stroke-primary" stroke-width="2"/>
                                    <rect x="32" y="27" width="91" height="61" rx="2" class="fill-white dark:fill-boxdark stroke-stroke dark:stroke-strokedark" stroke-width="1"/>
                                    <path d="M70 95 L85 120 L65 120 Z" class="fill-gray-3 dark:fill-boxdark stroke-primary" stroke-width="1.5"/>
                                    <rect x="55" y="120" width="45" height="6" rx="1" class="fill-primary"/>
                                    <!-- Torre / CPU Chassis -->
                                    <rect x="140" y="25" width="40" height="100" rx="3" class="fill-gray-2 dark:fill-meta-4 stroke-primary" stroke-width="2"/>
                                    <circle cx="160" cy="40" r="4" class="fill-meta-3"/>
                                    <rect x="150" y="55" width="20" height="4" rx="1" class="fill-primary/40"/>
                                    <rect x="150" y="65" width="20" height="4" rx="1" class="fill-primary/40"/>
                                    <rect x="148" y="85" width="24" height="25" rx="2" class="fill-white dark:fill-boxdark stroke-stroke dark:stroke-strokedark" stroke-width="1"/>
                                    <!-- Teclado -->
                                    <rect x="20" y="132" width="115" height="12" rx="2" class="fill-gray-2 dark:fill-meta-4 stroke-stroke dark:stroke-strokedark" stroke-width="1"/>
                                </svg>
                            </div>

                            <span class="font-mono text-[9px] text-primary tracking-wider mt-1 uppercase block font-bold">
                                {{ $equipo->marca ?? 'SISTEMA IT' }}
                            </span>
                        </div>

                        {{-- Grilla de Especificaciones Dinámicas --}}
                        <div class="col-span-12 md:col-span-8 lg:col-span-9">
                            @if(!empty($equipo->especificaciones))
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                    @foreach($equipo->schema_especificaciones as $key => $schema)
                                        @if(isset($equipo->especificaciones[$key]) && $equipo->especificaciones[$key] !== null && $equipo->especificaciones[$key] !== '')
                                            <div class="rounded border border-stroke bg-gray-2 p-2 dark:border-strokedark dark:bg-meta-4/20">
                                                <span class="text-[8px] font-bold text-bodydark2 uppercase block truncate">{{ $schema['label'] }}</span>
                                                <div class="text-[11px] font-bold font-mono text-black dark:text-white truncate" title="{{ $equipo->especificaciones[$key] }}">
                                                    {{ $equipo->especificaciones[$key] }}
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4 border border-dashed border-stroke dark:border-strokedark rounded-sm">
                                    <i class="fas fa-box-open text-xl text-bodydark2 mb-1 block"></i>
                                    <p class="text-xs text-bodydark2">No hay especificaciones técnicas registradas.</p>
                                </div>
                            @endif
                        </div>

                    </div>
                </div>
            </div>

            {{-- Periféricos vinculados (pantalla, teclado, mouse...) --}}
            @includeWhen($equipo->admiteMantenimiento(), 'equipos.partials._perifericos')

            {{-- Servicios técnicos: preventivos y correctivos, con reporte PDF --}}
            @include('equipos.partials._mantenimientos')

        </div>

        {{-- COLUMNA DERECHA: Ficha de Ubicación, Asignación & Notas (4 cols) --}}
        <div class="col-span-12 lg:col-span-4 space-y-3">

            {{-- Ficha de Ubicación y Entidad --}}
            <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="border-b border-stroke py-2 px-3 dark:border-strokedark">
                    <h3 class="font-bold text-black dark:text-white text-[10px] uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fas fa-map-marker-alt text-primary"></i> Ubicación & Asignación
                    </h3>
                </div>
                <div class="p-3 space-y-2 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="text-[9px] font-bold text-bodydark2 uppercase">Empresa</span>
                        <span class="text-black dark:text-white font-bold truncate max-w-[140px]">{{ $equipo->empresa->nombre }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[9px] font-bold text-bodydark2 uppercase">Sede</span>
                        <span class="text-black dark:text-white font-semibold truncate max-w-[140px]">{{ $equipo->sede?->nombre ?? 'Sin sede' }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-bodydark2 uppercase block">Ubicación Física</span>
                        <span class="text-black dark:text-white font-medium block mt-0.5">{{ $equipo->ubicacion ?? '—' }}</span>
                    </div>
                    <div class="pt-2 border-t border-dashed border-stroke dark:border-strokedark flex justify-between items-center">
                        <span class="text-[9px] font-bold text-bodydark2 uppercase">Responsable</span>
                        <span class="text-black dark:text-white font-bold truncate max-w-[140px]">{{ $equipo->responsable->name ?? 'Sin Asignar' }}</span>
                    </div>
                </div>
            </div>

            {{-- Ficha de Control & Metadata --}}
            <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="border-b border-stroke py-2 px-3 dark:border-strokedark">
                    <h3 class="font-bold text-black dark:text-white text-[10px] uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fas fa-info-circle text-primary"></i> Datos del Registro
                    </h3>
                </div>
                <div class="p-3 space-y-1.5 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="text-[9px] font-bold text-bodydark2 uppercase">Registrado por</span>
                        <span class="text-black dark:text-white font-medium text-[10px] truncate max-w-[120px]">{{ $equipo->creador->name ?? 'Sistema' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[9px] font-bold text-bodydark2 uppercase">Fecha Registro</span>
                        <span class="text-bodydark2 font-mono text-[10px]">{{ $equipo->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>

            @include('tickets.partials._tickets_activo', ['activo' => $equipo])

            {{-- Card de Notas Adicionales --}}
            @if($equipo->notas)
                <div class="rounded-sm border border-stroke bg-white p-3 shadow-default dark:border-strokedark dark:bg-boxdark">
                    <h3 class="font-bold text-[10px] uppercase tracking-wider text-black dark:text-white border-b border-stroke pb-1 mb-1.5 dark:border-strokedark flex items-center gap-1.5">
                        <i class="fas fa-sticky-note text-primary"></i> Notas Adicionales
                    </h3>
                    <p class="text-xs text-black dark:text-white leading-relaxed whitespace-pre-line">{{ $equipo->notas }}</p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>