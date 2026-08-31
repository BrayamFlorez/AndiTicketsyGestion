{{-- resources/views/equipos/index.blade.php --}}
<x-app-layout>
    <x-slot name="title">Equipos</x-slot>

    {{-- ── Cabecera y Acción Principal ────────────────────────────────── --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-title-md2 font-bold text-black dark:text-white flex items-center gap-2">
                <i class="fas fa-desktop text-primary"></i> Inventario de Equipos
            </h2>
            <p class="text-xs text-bodydark2 mt-1">
                {{ $equipos->total() }} equipo(s) registrado(s) · Control de hardware e infraestructura
            </p>
        </div>

        @can('equipos.crear')
            <a href="{{ route('equipos.create') }}" 
               class="inline-flex items-center justify-center gap-2 rounded bg-primary py-2 px-4 text-center text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm">
                <i class="fas fa-plus text-[10px]"></i> Registrar Equipo
            </a>
        @endcan
    </div>

    {{-- Alert Flash --}}
    @if (session('success'))
        <div class="mb-6 flex w-full border-l-6 border-meta-3 bg-meta-3/10 px-7 py-3 shadow-md dark:bg-meta-3/20 dark:border-meta-3 rounded-sm">
            <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-meta-3 text-white shrink-0">
                <i class="fas fa-check"></i>
            </div>
            <div class="w-full">
                <h5 class="mb-1 font-bold text-meta-3">¡Éxito!</h5>
                <p class="text-sm text-meta-3">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 flex w-full border-l-6 border-meta-1 bg-meta-1/10 px-7 py-3 shadow-md dark:bg-meta-1/20 dark:border-meta-1 rounded-sm">
            <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-meta-1 text-white shrink-0">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="w-full">
                <h5 class="mb-1 font-bold text-meta-1">Atención</h5>
                <p class="text-sm text-meta-1">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    {{-- ── Chips de Filtro Rápido ────────────────────────────────────────── --}}
    <div class="flex flex-wrap items-center gap-2 mb-6">
        <a href="{{ route('equipos.index') }}" 
           class="inline-flex rounded-full py-1.5 px-4 text-xs font-medium transition {{ empty($filtros) ? 'bg-primary text-white' : 'bg-gray hover:bg-opacity-80 dark:bg-meta-4 dark:text-white' }}">
            <i class="fas fa-list mr-1.5"></i> Todos
        </a>

        @foreach (\App\Models\Equipo::CATEGORIAS as $key => $label)
            <a href="{{ route('equipos.index', ['categoria' => $key]) }}"
               class="inline-flex rounded-full py-1.5 px-4 text-xs font-medium transition {{ ($filtros['categoria'] ?? '') === $key ? 'bg-primary text-white' : 'bg-gray hover:bg-opacity-80 dark:bg-meta-4 dark:text-white' }}">
                <i class="fas {{ ['computadora' => 'fa-desktop', 'portatil' => 'fa-laptop', 'impresora' => 'fa-print'][$key] ?? 'fa-cube' }} mr-1.5"></i>
                {{ $label }}
            </a>
        @endforeach

        <a href="{{ route('equipos.index', ['estado' => 'mantenimiento']) }}"
           class="inline-flex rounded-full py-1.5 px-4 text-xs font-medium transition {{ ($filtros['estado'] ?? '') === 'mantenimiento' ? 'bg-meta-6 text-white' : 'bg-gray hover:bg-opacity-80 dark:bg-meta-4 dark:text-white' }}">
            <i class="fas fa-wrench mr-1.5 {{ ($filtros['estado'] ?? '') === 'mantenimiento' ? 'text-white' : 'text-meta-6' }}"></i> En Mantenimiento
        </a>

        <a href="{{ route('equipos.index', ['estado' => 'dado_de_baja']) }}"
           class="inline-flex rounded-full py-1.5 px-4 text-xs font-medium transition {{ ($filtros['estado'] ?? '') === 'dado_de_baja' ? 'bg-meta-1 text-white' : 'bg-gray hover:bg-opacity-80 dark:bg-meta-4 dark:text-white' }}">
            <i class="fas fa-ban mr-1.5 {{ ($filtros['estado'] ?? '') === 'dado_de_baja' ? 'text-white' : 'text-meta-1' }}"></i> Dados de Baja
        </a>
    </div>

    {{-- ── Formulario de Filtros Avanzados (Card TailAdmin) ─────────────── --}}
    <div class="rounded-sm border border-stroke bg-white px-5 pt-5 pb-4 shadow-default dark:border-strokedark dark:bg-boxdark mb-6">
        <form method="GET" action="{{ route('equipos.index') }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Búsqueda --}}
                <div>
                    <label for="q" class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Buscar</label>
                    <input type="text" id="q" name="q" value="{{ $filtros['q'] ?? '' }}"
                           placeholder="Nombre, marca, serie..."
                           class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                </div>

                {{-- Categoría --}}
                <div>
                    <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Categoría</label>
                    <div class="relative z-20">
                        <select id="categoria" name="categoria" class="w-full appearance-none rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white cursor-pointer">
                            <option value="" class="bg-[#1c2434] text-gray-400">Todas</option>
                            @foreach (\App\Models\Equipo::CATEGORIAS as $key => $label)
                                <option value="{{ $key }}" @selected(($filtros['categoria'] ?? '') === $key) class="bg-[#1c2434] text-white">{{ $label }}</option>
                            @endforeach
                        </select>
                        <span class="absolute top-1/2 right-3 -translate-y-1/2 z-30 pointer-events-none">
                            <i class="fas fa-chevron-down text-[10px] text-bodydark2"></i>
                        </span>
                    </div>
                </div>

                {{-- Estado --}}
                <div>
                    <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Estado</label>
                    <div class="relative z-20">
                        <select id="estado" name="estado" class="w-full appearance-none rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white cursor-pointer">
                            <option value="" class="bg-[#1c2434] text-gray-400">Todos</option>
                            @foreach (\App\Models\Equipo::ESTADOS as $key => $label)
                                <option value="{{ $key }}" @selected(($filtros['estado'] ?? '') === $key) class="bg-[#1c2434] text-white">{{ $label }}</option>
                            @endforeach
                        </select>
                        <span class="absolute top-1/2 right-3 -translate-y-1/2 z-30 pointer-events-none">
                            <i class="fas fa-chevron-down text-[10px] text-bodydark2"></i>
                        </span>
                    </div>
                </div>

                {{-- Empresa --}}
                <div>
                    <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Empresa</label>
                    <div class="relative z-20">
                        <select id="empresa_id" name="empresa_id" class="w-full appearance-none rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white cursor-pointer">
                            <option value="" class="bg-[#1c2434] text-gray-400">Todas</option>
                            @foreach ($empresas as $empresa)
                                <option value="{{ $empresa->id }}" @selected(($filtros['empresa_id'] ?? '') == $empresa->id) class="bg-[#1c2434] text-white">
                                    {{ $empresa->nombre }}
                                </option>
                            @endforeach
                        </select>
                        <span class="absolute top-1/2 right-3 -translate-y-1/2 z-30 pointer-events-none">
                            <i class="fas fa-chevron-down text-[10px] text-bodydark2"></i>
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 mt-4 border-t border-stroke pt-3 dark:border-strokedark">
                @if (!empty($filtros))
                    <a href="{{ route('equipos.index') }}" 
                       class="inline-flex items-center gap-1.5 rounded border border-stroke py-1.5 px-3 text-xs font-medium text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition">
                        <i class="fas fa-times text-[10px]"></i> Limpiar
                    </a>
                @endif
                <button type="submit" 
                        class="inline-flex items-center gap-1.5 rounded bg-primary py-1.5 px-4 text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm cursor-pointer">
                    <i class="fas fa-filter text-[10px]"></i> Filtrar
                </button>
            </div>
        </form>
    </div>

    {{-- ── Tabla de Equipos ────────────────────────────────────────────── --}}
    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark mb-6">
        <div class="max-w-full overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-gray-2 text-left dark:bg-meta-4">
                        <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase">Equipo / Serie</th>
                        <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase">Categoría</th>
                        <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase">Empresa / Sede</th>
                        <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase">Responsable</th>
                        <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase text-center w-28">Estado</th>
                        <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase text-center w-16">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke dark:divide-strokedark">
                    @forelse ($equipos as $equipo)
                        @php
                            $puedeGestionar = $equipo->puedeSerGestionadoPor(auth()->user());
                            $puedeEliminar  = auth()->user()->can('equipos.eliminar') && ! $equipo->tieneHistorial();
                            
                            $colorEst = match($equipo->estado) {
                                'activo' => 'bg-meta-3/10 text-meta-3',
                                'mantenimiento' => 'bg-meta-6/10 text-meta-6',
                                'dado_de_baja' => 'bg-meta-1/10 text-meta-1',
                                default => 'bg-primary/10 text-primary',
                            };
                        @endphp
                        <tr class="hover:bg-gray-2 dark:hover:bg-meta-4/50 transition">
                            
                            {{-- Equipo / Marca / Modelo / Serie --}}
                            <td class="py-3.5 px-4">
                                <a href="{{ route('equipos.show', $equipo) }}" class="font-bold text-black dark:text-white hover:text-primary transition text-xs block truncate max-w-[240px]">
                                    {{ $equipo->nombre }}
                                </a>
                                @if ($equipo->marca || $equipo->modelo)
                                    <span class="text-[10px] text-bodydark2 block truncate max-w-[240px]">
                                        {{ trim(($equipo->marca ?? '') . ' ' . ($equipo->modelo ?? '')) }}
                                    </span>
                                @endif
                                @if ($equipo->numero_serie)
                                    <span class="text-[10px] text-bodydark2 font-mono block">
                                        S/N: {{ $equipo->numero_serie }}
                                    </span>
                                @endif
                            </td>

                            {{-- Categoría --}}
                            <td class="py-3.5 px-4 text-xs text-black dark:text-white">
                                {{ $equipo->label_categoria }}
                            </td>

                            {{-- Empresa / Sede / Ubicación --}}
                            <td class="py-3.5 px-4">
                                <span class="text-xs text-black dark:text-white font-medium block truncate max-w-[150px]">
                                    {{ $equipo->empresa?->nombre ?? '—' }}
                                </span>
                                @if ($equipo->sede)
                                    <span class="text-[10px] text-bodydark2 block truncate max-w-[150px]">
                                        {{ $equipo->sede->nombre }}
                                    </span>
                                @endif
                                @if ($equipo->ubicacion)
                                    <span class="text-[10px] text-bodydark2 block truncate max-w-[150px]">
                                        <i class="fas fa-map-marker-alt text-primary mr-0.5"></i>{{ $equipo->ubicacion }}
                                    </span>
                                @endif
                            </td>

                            {{-- Responsable --}}
                            <td class="py-3.5 px-4">
                                @if ($equipo->responsable)
                                    <div class="flex items-center gap-2">
                                        <div class="flex h-6 w-6 items-center justify-center rounded-full bg-primary/20 text-primary font-bold text-[10px] shrink-0">
                                            {{ substr($equipo->responsable->name, 0, 1) }}
                                        </div>
                                        <span class="text-xs text-black dark:text-white truncate max-w-[120px]">
                                            {{ $equipo->responsable->name }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-[10px] text-bodydark2 font-mono">Sin responsable</span>
                                @endif
                            </td>

                            {{-- Estado --}}
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex rounded-full py-1 px-3 text-[10px] font-bold uppercase {{ $colorEst }}">
                                    {{ $equipo->label_estado }}
                                </span>
                            </td>

                            {{-- Dropdown Acciones --}}
                            <td class="py-3.5 px-4 text-center">
                                <div x-data="dropdownMenu()" class="relative inline-block">
                                    <button type="button" x-ref="trigger" @click="toggle()" class="hover:text-primary transition" title="Opciones">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>

                                    <template x-teleport="body">
                                        <div x-show="open" x-cloak @click.outside="open = false"
                                             :style="`top: ${top}px; left: ${left}px;`"
                                             class="absolute w-52 rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark z-[9999] py-2 text-left">

                                            @if (Route::has('equipos.show'))
                                                <a href="{{ route('equipos.show', $equipo) }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-black dark:text-white hover:bg-gray dark:hover:bg-meta-4 transition">
                                                    <i class="fas fa-eye w-4 text-center text-bodydark2"></i> Ver detalle
                                                </a>
                                                <div class="my-1 border-t border-stroke dark:border-strokedark"></div>
                                            @endif

                                            @can('equipos.editar')
                                                <a href="{{ route('mpreventivo.create', $equipo) }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-black dark:text-white hover:bg-gray dark:hover:bg-meta-4 transition">
                                                    <i class="fas fa-tools w-4 text-center text-bodydark2"></i> Mantenimiento preventivo
                                                </a>
                                            @endcan

                                            @can('equipos.editar')
                                                @if ($puedeGestionar)
                                                    <a href="{{ route('equipos.edit', $equipo) }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-black dark:text-white hover:bg-gray dark:hover:bg-meta-4 transition">
                                                        <i class="fas fa-pen w-4 text-center text-bodydark2"></i> Editar
                                                    </a>

                                                    @if ($equipo->estado !== 'dado_de_baja')
                                                        <form method="POST" action="{{ route('equipos.cambiar-estado', $equipo) }}"
                                                              onsubmit="return confirm('¿Dar de baja el equipo «{{ $equipo->nombre }}»?')">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="estado" value="dado_de_baja">
                                                            <button type="submit" class="w-full text-left flex items-center gap-2.5 px-4 py-2 text-xs text-black dark:text-white hover:bg-gray dark:hover:bg-meta-4 transition">
                                                                <i class="fas fa-ban w-4 text-center text-bodydark2"></i> Dar de baja
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form method="POST" action="{{ route('equipos.cambiar-estado', $equipo) }}">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="estado" value="activo">
                                                            <button type="submit" class="w-full text-left flex items-center gap-2.5 px-4 py-2 text-xs text-black dark:text-white hover:bg-gray dark:hover:bg-meta-4 transition">
                                                                <i class="fas fa-power-off w-4 text-center text-bodydark2"></i> Reactivar
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
                                                        <button type="submit" class="w-full text-left flex items-center gap-2.5 px-4 py-2 text-xs text-meta-1 hover:bg-meta-1/10 transition">
                                                            <i class="fas fa-trash-alt w-4 text-center"></i> Eliminar
                                                        </button>
                                                    </form>
                                                @else
                                                    <div class="px-4 py-1.5 text-[10px] text-bodydark2 italic">
                                                        <i class="fas fa-lock mr-1"></i> Tiene historial asociado
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
                            <td colspan="6" class="py-8 text-center">
                                <div class="flex flex-col items-center text-bodydark2">
                                    <i class="fas fa-desktop text-3xl mb-2"></i>
                                    <span class="text-xs">
                                        {{ !empty($filtros) ? 'No coinciden equipos con los filtros aplicados.' : 'Aún no hay equipos registrados.' }}
                                    </span>
                                    @if (!empty($filtros))
                                        <a href="{{ route('equipos.index') }}" class="mt-2 inline-flex items-center gap-1 rounded border border-stroke py-1 px-3 text-xs font-medium text-black dark:border-strokedark dark:text-white">
                                            Limpiar Filtros
                                        </a>
                                    @elseif ($equipos->total() === 0)
                                        @can('equipos.crear')
                                            <a href="{{ route('equipos.create') }}" class="mt-3 inline-flex items-center gap-1.5 rounded bg-primary py-1.5 px-4 text-xs font-bold uppercase text-white hover:bg-opacity-90">
                                                <i class="fas fa-plus"></i> Registrar Primer Equipo
                                            </a>
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if ($equipos->hasPages())
            <div class="border-t border-stroke py-3 px-4 dark:border-strokedark">
                {{ $equipos->onEachSide(1)->links() }}
            </div>
        @endif
    </div>

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
                                const ancho = 210, margen = 8;
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