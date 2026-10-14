{{-- resources/views/equipos/index.blade.php --}}
<x-app-layout>
    <x-slot name="title">Inventario de Equipos</x-slot>

    @php
        $filtrosCount = collect($filtros ?? [])
            ->filter(fn($v) => $v !== null && $v !== '')
            ->count();

        // Empresa/sede elegidas: los chips de categoría y estado las conservan
        $ubicacion = collect($filtros ?? [])->only(['empresa_id', 'sede_id'])->all();
    @endphp

    {{-- Alert Flash Compactos --}}
    @if (session('success'))
        <div class="mb-3 flex w-full border-l-4 border-meta-3 bg-meta-3/10 px-3 py-2 shadow-sm rounded-sm">
            <i class="fas fa-check-circle text-meta-3 mr-2 mt-0.5 text-xs"></i>
            <p class="text-xs text-meta-3 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-3 flex w-full border-l-4 border-meta-1 bg-meta-1/10 px-3 py-2 shadow-sm rounded-sm">
            <i class="fas fa-exclamation-triangle text-meta-1 mr-2 mt-0.5 text-xs"></i>
            <p class="text-xs text-meta-1 font-medium">{{ session('error') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-[15rem_minmax(0,1fr)] gap-3 items-start">

    {{-- ── Resumen lateral: empresas → sedes → cantidad de equipos ────────── --}}
    @include('equipos.partials._resumen_ubicacion')

    <div class="min-w-0">

    {{-- ── Header + Buscador + Filtros Integrados (Ultracompacto) ───────────── --}}
    <div class="rounded-sm border border-stroke bg-white p-3 shadow-default dark:border-strokedark dark:bg-boxdark mb-3"
         x-data="{
             filtrosAbiertos: {{ $filtrosCount > 1 ? 'true' : 'false' }},
             empresa: @js((string) ($filtros['empresa_id'] ?? '')),
             sede: @js((string) ($filtros['sede_id'] ?? '')),
             sedesPorEmpresa: @js($sedesPorEmpresa),
             get sedesDeEmpresa() { return this.sedesPorEmpresa[this.empresa] ?? []; },
         }">

        <form method="GET" action="{{ route('equipos.index') }}">
            {{-- Fila Superior: Título + Buscador + Botones --}}
            <div class="flex items-center gap-2 flex-wrap lg:flex-nowrap">
                
                {{-- Título --}}
                <div class="flex items-center gap-1.5 shrink-0 mr-1">
                    <i class="fas fa-desktop text-primary text-sm"></i>
                    <h2 class="text-xs font-bold text-black dark:text-white uppercase tracking-wider whitespace-nowrap">
                        Equipos
                    </h2>
                    <span class="font-mono text-[10px] text-bodydark2">({{ $equipos->total() }})</span>
                </div>

                {{-- Input Buscador --}}
                <div class="relative flex-1 min-w-[200px]">
                    <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-bodydark2 pointer-events-none">
                        <i class="fas fa-search text-[11px]"></i>
                    </span>
                    <input type="text" name="q" value="{{ $filtros['q'] ?? '' }}"
                           placeholder="Buscar por nombre, marca, serie, ubicación..."
                           class="w-full rounded border border-stroke bg-transparent py-1 pl-8 pr-7 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                    @if (!empty($filtros['q']))
                        <a href="{{ route('equipos.index') }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-bodydark2 hover:text-meta-1">
                            <i class="fas fa-times text-[10px]"></i>
                        </a>
                    @endif
                </div>

                {{-- Acciones --}}
                <div class="flex items-center gap-1.5 shrink-0">
                    <button type="button" @click="filtrosAbiertos = !filtrosAbiertos"
                            class="relative inline-flex items-center gap-1 rounded border border-stroke py-1 px-2.5 text-xs font-medium text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition">
                        <i class="fas fa-sliders-h text-[10px]"></i>
                        <span x-text="filtrosAbiertos ? 'Menos' : 'Filtros'"></span>
                        @if ($filtrosCount > 1)
                            <span class="ml-0.5 rounded-full bg-meta-1 px-1 py-0.2 text-[9px] font-bold text-white leading-none">
                                {{ $filtrosCount - 1 }}
                            </span>
                        @endif
                    </button>

                    <button type="submit"
                            class="inline-flex items-center gap-1 rounded bg-primary py-1 px-3 text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm cursor-pointer">
                        <i class="fas fa-search text-[10px]"></i> Buscar
                    </button>

                    @can('equipos.crear')
                        <a href="{{ route('equipos.create') }}"
                           class="inline-flex items-center gap-1 rounded bg-meta-3 py-1 px-3 text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm whitespace-nowrap ml-1">
                            <i class="fas fa-plus-circle text-[10px]"></i> Nuevo
                        </a>
                    @endcan
                </div>
            </div>

            {{-- Panel Desplegable de Filtros Avanzados --}}
            <div x-show="filtrosAbiertos" x-cloak x-transition
                 class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-2.5 mt-2.5 pt-2.5 border-t border-stroke dark:border-strokedark">
                <div>
                    <label class="mb-1 block text-[9px] font-bold uppercase text-black dark:text-white">Categoría</label>
                    <select name="categoria" class="w-full rounded border border-stroke bg-transparent py-1 px-2 text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                        <option value="" class="bg-[#1c2434] text-gray-400">Todas las categorías</option>
                        @foreach (\App\Models\Equipo::CATEGORIAS as $key => $label)
                            <option value="{{ $key }}" @selected(($filtros['categoria'] ?? '') === $key) class="bg-[#1c2434] text-white">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-[9px] font-bold uppercase text-black dark:text-white">Estado</label>
                    <select name="estado" class="w-full rounded border border-stroke bg-transparent py-1 px-2 text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                        <option value="" class="bg-[#1c2434] text-gray-400">Todos los estados</option>
                        @foreach (\App\Models\Equipo::ESTADOS as $key => $label)
                            <option value="{{ $key }}" @selected(($filtros['estado'] ?? '') === $key) class="bg-[#1c2434] text-white">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-[9px] font-bold uppercase text-black dark:text-white">Empresa</label>
                    <select name="empresa_id" x-model="empresa" @change="sede = ''"
                            class="w-full rounded border border-stroke bg-transparent py-1 px-2 text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                        <option value="" class="bg-[#1c2434] text-gray-400">Todas las empresas</option>
                        @foreach ($empresas as $empresa)
                            <option value="{{ $empresa->id }}" @selected(($filtros['empresa_id'] ?? '') == $empresa->id) class="bg-[#1c2434] text-white">{{ $empresa->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Sede: depende de la empresa y se aplica al elegirla --}}
                <div>
                    <label class="mb-1 block text-[9px] font-bold uppercase text-black dark:text-white">Sede</label>
                    <select name="sede_id" x-model="sede" :disabled="!empresa"
                            @change="$nextTick(() => $el.form.requestSubmit())"
                            class="w-full rounded border border-stroke bg-transparent py-1 px-2 text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white disabled:cursor-not-allowed disabled:opacity-60">
                        <option value="" class="bg-[#1c2434] text-gray-400" x-text="empresa ? 'Todas las sedes' : 'Elija una empresa'"></option>
                        <template x-for="s in sedesDeEmpresa" :key="s.id">
                            <option :value="s.id" x-text="s.nombre" :selected="String(s.id) === sede" class="bg-[#1c2434] text-white"></option>
                        </template>
                        <option value="sin" :selected="sede === 'sin'" x-show="empresa" class="bg-[#1c2434] text-white">Sin sede asignada</option>
                    </select>
                </div>
            </div>
        </form>

        {{-- Chips Rápidos de Categoría/Estado en Fila Baja --}}
        <div class="mt-2 pt-2 border-t border-stroke dark:border-strokedark flex items-center gap-1 overflow-x-auto">
            <a href="{{ route('equipos.index') }}" 
               class="inline-flex items-center gap-1 rounded-sm py-0.5 px-2 text-[10px] font-bold uppercase tracking-wider transition whitespace-nowrap {{ empty($filtros) ? 'bg-primary text-white shadow-sm' : 'bg-gray-2 text-black hover:bg-stroke dark:bg-meta-4/50 dark:text-white dark:hover:bg-meta-4' }}">
                <i class="fas fa-list text-[9px]"></i> Todos
            </a>

            @foreach (\App\Models\Equipo::CATEGORIAS as $key => $label)
                <a href="{{ route('equipos.index', ['categoria' => $key] + $ubicacion) }}"
                   class="inline-flex items-center gap-1 rounded-sm py-0.5 px-2 text-[10px] font-bold uppercase tracking-wider transition whitespace-nowrap {{ ($filtros['categoria'] ?? '') === $key ? 'bg-primary text-white shadow-sm' : 'bg-gray-2 text-black hover:bg-stroke dark:bg-meta-4/50 dark:text-white dark:hover:bg-meta-4' }}">
                    <i class="fas {{ ['computadora' => 'fa-desktop', 'portatil' => 'fa-laptop', 'impresora' => 'fa-print'][$key] ?? 'fa-cube' }} text-[9px]"></i>
                    {{ $label }}
                </a>
            @endforeach

            <a href="{{ route('equipos.index', ['estado' => 'mantenimiento'] + $ubicacion) }}"
               class="inline-flex items-center gap-1 rounded-sm py-0.5 px-2 text-[10px] font-bold uppercase tracking-wider transition whitespace-nowrap {{ ($filtros['estado'] ?? '') === 'mantenimiento' ? 'bg-meta-6 text-white shadow-sm' : 'bg-gray-2 text-black hover:bg-stroke dark:bg-meta-4/50 dark:text-white dark:hover:bg-meta-4' }}">
                <i class="fas fa-wrench text-[9px]"></i> Mantenimiento
            </a>

            <a href="{{ route('equipos.index', ['estado' => 'dado_de_baja'] + $ubicacion) }}"
               class="inline-flex items-center gap-1 rounded-sm py-0.5 px-2 text-[10px] font-bold uppercase tracking-wider transition whitespace-nowrap {{ ($filtros['estado'] ?? '') === 'dado_de_baja' ? 'bg-meta-1 text-white shadow-sm' : 'bg-gray-2 text-black hover:bg-stroke dark:bg-meta-4/50 dark:text-white dark:hover:bg-meta-4' }}">
                <i class="fas fa-ban text-[9px]"></i> Baja
            </a>
        </div>
    </div>

    {{-- ── Tabla de Equipos de Alta Densidad ──────────────────────────────── --}}
    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark mb-6">
        <div class="max-w-full overflow-x-auto">
            <table class="w-full table-auto border-collapse">
                <thead>
                    <tr class="bg-gray-2 text-left dark:bg-meta-4">
                        <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white min-w-[180px]">Equipo / Serie</th>
                        <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white w-28">Categoría</th>
                        <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white min-w-[150px]">Empresa / Sede</th>
                        <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white min-w-[140px]">Responsable</th>
                        <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white text-center w-28">Estado</th>
                        <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white text-center w-12">Opciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke dark:divide-strokedark">
                    @forelse ($equipos as $equipo)
                        @php
                            $puedeGestionar = $equipo->puedeSerGestionadoPor(auth()->user());
                            $puedeEliminar  = auth()->user()->can('equipos.eliminar') && ! $equipo->tieneHistorial();
                            
                            $colorEst = match($equipo->estado) {
                                'activo'        => 'bg-meta-3/10 text-meta-3 border-meta-3/20',
                                'mantenimiento' => 'bg-meta-6/10 text-meta-6 border-meta-6/20',
                                'dado_de_baja'  => 'bg-meta-1/10 text-meta-1 border-meta-1/20',
                                default         => 'bg-primary/10 text-primary border-primary/20',
                            };
                        @endphp
                        <tr class="hover:bg-gray-2 dark:hover:bg-meta-4/40 transition">
                            
                            {{-- Equipo / Marca / Modelo / Serie --}}
                            <td class="py-1.5 px-3">
                                <a href="{{ route('equipos.show', $equipo) }}" class="font-bold text-xs text-black dark:text-white hover:text-primary transition truncate block max-w-[220px]">
                                    {{ $equipo->nombre }}
                                </a>
                                <div class="flex items-center gap-1.5 whitespace-nowrap text-[9px]">
                                    @if ($equipo->marca || $equipo->modelo)
                                        <span class="text-bodydark2 truncate max-w-[140px]">
                                            {{ trim(($equipo->marca ?? '') . ' ' . ($equipo->modelo ?? '')) }}
                                        </span>
                                    @endif
                                    @if ($equipo->numero_serie)
                                        <span class="font-mono text-primary font-bold">
                                            #{{ $equipo->numero_serie }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- Categoría --}}
                            <td class="py-1.5 px-3 whitespace-nowrap text-xs text-black dark:text-white font-medium">
                                {{ $equipo->label_categoria }}
                            </td>

                            {{-- Empresa / Sede / Ubicación --}}
                            <td class="py-1.5 px-3">
                                <span class="text-xs text-black dark:text-white font-bold truncate block max-w-[150px]">
                                    {{ $equipo->empresa?->nombre ?? '—' }}
                                </span>
                                <div class="flex items-center gap-1 text-[9px] text-bodydark2 whitespace-nowrap truncate max-w-[150px]">
                                    @if ($equipo->sede)
                                        <span>{{ $equipo->sede->nombre }}</span>
                                    @endif
                                    @if ($equipo->ubicacion)
                                        <span>· <i class="fas fa-map-marker-alt text-primary text-[8px]"></i>{{ $equipo->ubicacion }}</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Responsable --}}
                            <td class="py-1.5 px-3 whitespace-nowrap">
                                @if ($equipo->responsable)
                                    <div class="flex items-center gap-1.5">
                                        <div class="flex h-5 w-5 items-center justify-center rounded-full bg-primary/20 text-primary font-bold text-[9px] shrink-0">
                                            {{ substr($equipo->responsable->name, 0, 1) }}
                                        </div>
                                        <span class="text-xs text-black dark:text-white font-semibold truncate max-w-[120px]">
                                            {{ $equipo->responsable->name }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-[10px] text-bodydark2 italic">Sin asignar</span>
                                @endif
                            </td>

                            {{-- Estado --}}
                            <td class="py-1.5 px-3 text-center whitespace-nowrap">
                                <span class="inline-flex rounded-full px-2 py-0.5 text-[9px] font-bold uppercase border {{ $colorEst }}">
                                    {{ $equipo->label_estado }}
                                </span>
                            </td>

                            {{-- Dropdown Acciones (Teleportado) --}}
                            <td class="py-1.5 px-3 text-center whitespace-nowrap">
                                <div x-data="dropdownMenu()" class="relative inline-block">
                                    <button type="button" x-ref="trigger" @click="toggle()" class="text-bodydark2 hover:text-primary transition p-1" title="Opciones">
                                        <i class="fas fa-ellipsis-v text-xs"></i>
                                    </button>

                                    <template x-teleport="body">
                                        <div x-show="open" x-cloak @click.outside="open = false"
                                             :style="`top: ${top}px; left: ${left}px;`"
                                             class="fixed w-48 rounded-sm border border-stroke bg-white shadow-xl dark:border-strokedark dark:bg-boxdark z-[99999] py-1 text-left text-xs">

                                            @if (Route::has('equipos.show'))
                                                <a href="{{ route('equipos.show', $equipo) }}" class="flex items-center gap-2 px-3 py-1.5 text-black dark:text-white hover:bg-gray-2 dark:hover:bg-meta-4 transition">
                                                    <i class="fas fa-eye w-4 text-center text-primary"></i> Ver detalle
                                                </a>
                                                <div class="my-1 border-t border-stroke dark:border-strokedark"></div>
                                            @endif

                                            @can('equipos.editar')
                                                <a href="{{ route('mpreventivo.create', $equipo) }}" class="flex items-center gap-2 px-3 py-1.5 text-black dark:text-white hover:bg-gray-2 dark:hover:bg-meta-4 transition">
                                                    <i class="fas fa-tools w-4 text-center text-meta-3"></i> Mantenimiento
                                                </a>
                                            @endcan

                                            @can('equipos.editar')
                                                @if ($puedeGestionar)
                                                    <a href="{{ route('equipos.edit', $equipo) }}" class="flex items-center gap-2 px-3 py-1.5 text-black dark:text-white hover:bg-gray-2 dark:hover:bg-meta-4 transition">
                                                        <i class="fas fa-pen w-4 text-center text-bodydark2"></i> Editar
                                                    </a>

                                                    @if ($equipo->estado !== 'dado_de_baja')
                                                        <form method="POST" action="{{ route('equipos.cambiar-estado', $equipo) }}"
                                                              onsubmit="return confirm('¿Dar de baja el equipo «{{ $equipo->nombre }}»?')">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="estado" value="dado_de_baja">
                                                            <button type="submit" class="w-full text-left flex items-center gap-2 px-3 py-1.5 text-black dark:text-white hover:bg-gray-2 dark:hover:bg-meta-4 transition">
                                                                <i class="fas fa-ban w-4 text-center text-meta-1"></i> Dar de baja
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form method="POST" action="{{ route('equipos.cambiar-estado', $equipo) }}">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="estado" value="activo">
                                                            <button type="submit" class="w-full text-left flex items-center gap-2 px-3 py-1.5 text-black dark:text-white hover:bg-gray-2 dark:hover:bg-meta-4 transition">
                                                                <i class="fas fa-power-off w-4 text-center text-meta-3"></i> Reactivar
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif
                                            @endcan

                                            @can('equipos.eliminar')
                                                <div class="my-1 border-t border-stroke dark:border-strokedark"></div>
                                                @if ($puedeEliminar)
                                                    <form method="POST" action="{{ route('equipos.destroy', $equipo) }}"
                                                          onsubmit="return confirm('¿Eliminar PERMANENTEMENTE el equipo «{{ $equipo->nombre }}»?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="w-full text-left flex items-center gap-2 px-3 py-1.5 text-meta-1 hover:bg-meta-1/10 transition font-bold">
                                                            <i class="fas fa-trash-alt w-4 text-center"></i> Eliminar
                                                        </button>
                                                    </form>
                                                @else
                                                    <div class="px-3 py-1 text-[9px] text-bodydark2 italic">
                                                        <i class="fas fa-lock mr-1"></i> Con historial asociado
                                                    </div>
                                                @endif
                                            @endcan

                                        </div>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center">
                                <div class="flex flex-col items-center text-bodydark2">
                                    <i class="fas fa-desktop text-xl mb-1"></i>
                                    <span class="text-xs">
                                        {{ !empty($filtros) ? 'No coinciden equipos con los filtros aplicados.' : 'Aún no hay equipos registrados.' }}
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if ($equipos->hasPages())
            <div class="border-t border-stroke px-4 py-2 dark:border-strokedark">
                {{ $equipos->onEachSide(1)->links() }}
            </div>
        @endif
    </div>

    </div>{{-- /min-w-0 --}}
    </div>{{-- /grid --}}

    @push('scripts')
        @once
        <script>
            function dropdownMenu() {
                return {
                    open: false, top: 0, left: 0,
                    toggle() {
                        this.open = !this.open;
                        if (this.open) {
                            this.$nextTick(() => {
                                const rect = this.$refs.trigger.getBoundingClientRect();
                                const ancho = 192, margen = 8;
                                let left = rect.right - ancho;
                                if (left < margen) left = rect.left;
                                this.top = rect.bottom + 4;
                                this.left = left;
                            });
                        }
                    },
                };
            }
        </script>
        @endonce
    @endpush
</x-app-layout>