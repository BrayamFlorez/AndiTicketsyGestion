<x-app-layout>
    <x-slot name="title">Impresoras</x-slot>

    @php
        $filtrosCount = collect($filtros ?? [])
            ->filter(fn($v) => $v !== null && $v !== '')
            ->count();
    @endphp

    {{-- Alertas flash --}}
    @if (session('exito'))
        <div class="mb-4 flex w-full border-l-4 border-meta-3 bg-meta-3/10 px-4 py-3 rounded-sm">
            <i class="fas fa-check-circle text-meta-3 mr-2 mt-0.5"></i>
            <p class="text-xs text-meta-3">{{ session('exito') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 flex w-full border-l-4 border-meta-1 bg-meta-1/10 px-4 py-3 rounded-sm">
            <i class="fas fa-exclamation-triangle text-meta-1 mr-2 mt-0.5"></i>
            <p class="text-xs text-meta-1">{{ session('error') }}</p>
        </div>
    @endif

    {{-- Cabecera + Buscador + Filtros en una fila --}}
    <form method="GET" action="{{ route('impresoras.index') }}" x-data="{ filtrosAbiertos: {{ $filtrosCount > 1 ? 'true' : 'false' }} }" class="mb-4">

        <div class="flex items-center gap-3 flex-wrap">
            <div class="flex items-center gap-2 shrink-0">
                <i class="fas fa-print text-primary"></i>
                <h2 class="text-sm font-bold text-black dark:text-white whitespace-nowrap">
                    Impresoras
                </h2>
            </div>

            <div class="relative flex-1 min-w-[240px]">
                <i
                    class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-bodydark2 text-xs pointer-events-none"></i>
                <input type="text" name="q" value="{{ $filtros['q'] ?? '' }}"
                    placeholder="Código, nombre, ubicación, IP, hostname, serie..."
                    class="w-full rounded border border-stroke bg-transparent py-2 pl-9 pr-8 text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                @if (!empty($filtros['q']))
                    <a href="{{ route('impresoras.index') }}"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-bodydark2 hover:text-meta-1">
                        <i class="fas fa-times text-xs"></i>
                    </a>
                @endif
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <button type="submit"
                    class="inline-flex items-center gap-1.5 rounded bg-primary py-2 px-3.5 text-xs font-bold uppercase text-white hover:bg-opacity-90">
                    <i class="fas fa-search text-[10px]"></i> Buscar
                </button>

                <button type="button" @click="filtrosAbiertos = !filtrosAbiertos"
                    class="relative inline-flex items-center gap-1.5 rounded border border-stroke py-2 px-3.5 text-xs font-medium hover:bg-gray dark:border-strokedark dark:hover:bg-meta-4">
                    <i class="fas fa-sliders-h text-[10px]"></i> Filtros
                    @if ($filtrosCount > 1)
                        <span
                            class="absolute -top-1.5 -right-1.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-meta-1 px-1 text-[9px] font-bold text-white">
                            {{ $filtrosCount - 1 }}
                        </span>
                    @endif
                </button>

                @can('create', App\Models\Impresora::class)
                    <a href="{{ route('impresoras.create') }}"
                        class="inline-flex items-center gap-1.5 rounded bg-meta-3 py-2 px-3.5 text-xs font-bold uppercase text-white hover:bg-opacity-90 shadow-sm whitespace-nowrap">
                        <i class="fas fa-plus-circle text-[10px]"></i> Nueva
                    </a>
                @endcan
            </div>
        </div>

        <div x-show="filtrosAbiertos" x-cloak
            class="mt-3 grid grid-cols-1 sm:grid-cols-3 gap-2 rounded border border-stroke bg-gray-2 p-3 dark:border-strokedark dark:bg-meta-4/20">

            <select name="empresa_id"
                class="rounded border border-stroke bg-transparent py-1.5 px-2.5 text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                <option value="">— Todas las empresas —</option>
                @foreach ($empresas as $empresa)
                    <option value="{{ $empresa->id }}" @selected(($filtros['empresa_id'] ?? '') == $empresa->id)>
                        {{ $empresa->nombre }}
                    </option>
                @endforeach
            </select>

            <select name="estado"
                class="rounded border border-stroke bg-transparent py-1.5 px-2.5 text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                <option value="">— Todos los estados —</option>
                @foreach ($estados as $key => $label)
                    <option value="{{ $key }}" @selected(($filtros['estado'] ?? '') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </form>

    {{-- Tabla --}}
    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="max-w-full overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-gray-2 text-left dark:bg-meta-4">
                        <th
                            class="py-2.5 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white w-32">
                            Código</th>
                        <th
                            class="py-2.5 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white min-w-[200px]">
                            Nombre / Ubicación</th>
                        <th
                            class="py-2.5 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white w-40">
                            Modelo</th>
                        <th
                            class="py-2.5 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white w-40">
                            Empresa / Sede</th>
                        <th
                            class="py-2.5 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white w-28">
                            Red</th>
                        <th
                            class="py-2.5 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white text-center w-24">
                            Consumibles
                        </th>
                        <th
                            class="py-2.5 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white text-center w-28">
                            Estado</th>
                        <th
                            class="py-2.5 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white text-right w-12">
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-stroke dark:divide-strokedark">
                    @forelse ($impresoras as $imp)
                        @php
                            $cfgEstado = match ($imp->estado) {
                                'activa' => ['clase' => 'bg-meta-3/10 text-meta-3', 'texto' => 'Activa'],
                                'en_mantenimiento' => [
                                    'clase' => 'bg-meta-6/10 text-meta-6',
                                    'texto' => 'En mantenimiento',
                                ],
                                'dada_de_baja' => ['clase' => 'bg-meta-1/10 text-meta-1', 'texto' => 'Dada de baja'],
                                default => ['clase' => 'bg-gray-2 text-bodydark2', 'texto' => ucfirst($imp->estado)],
                            };
                        @endphp

                        <tr class="hover:bg-gray-2 dark:hover:bg-meta-4/40 transition">
                            <td class="py-2 px-3 whitespace-nowrap">
                                <a href="{{ route('impresoras.show', $imp) }}"
                                    class="font-mono text-[11px] text-primary hover:underline">
                                    {{ $imp->codigo }}
                                </a>
                                @if ($imp->codigo_interno)
                                    <div class="font-mono text-[9px] text-bodydark2">{{ $imp->codigo_interno }}</div>
                                @endif
                            </td>

                            <td class="py-2 px-3">
                                <a href="{{ route('impresoras.show', $imp) }}"
                                    class="text-xs font-medium text-black dark:text-white hover:text-primary truncate block max-w-[240px]"
                                    title="{{ $imp->nombre_visible }}">
                                    {{ $imp->nombre_visible }}
                                </a>
                                @if ($imp->ubicacion_fisica)
                                    <div class="text-[10px] text-bodydark2 truncate max-w-[240px]"
                                        title="{{ $imp->ubicacion_fisica }}">
                                        <i class="fas fa-map-marker-alt text-[9px]"></i> {{ $imp->ubicacion_fisica }}
                                    </div>
                                @endif
                            </td>

                            <td class="py-2 px-3">
                                @if ($imp->modelo)
                                    <span class="text-[11px] text-bodydark2 truncate block max-w-[140px]"
                                        title="{{ $imp->modelo->nombre_completo }}">
                                        <i
                                            class="fas {{ $imp->modelo->tipo_tecnologia === 'laser' ? 'fa-print' : 'fa-fill-drip' }} text-primary mr-1 text-[10px]"></i>{{ $imp->modelo->nombre_completo }}
                                    </span>
                                @else
                                    <span class="text-bodydark2 text-[10px]">—</span>
                                @endif
                            </td>

                            <td class="py-2 px-3">
                                <div class="text-[11px] text-black dark:text-white truncate max-w-[140px]">
                                    {{ $imp->empresa?->nombre ?? '—' }}
                                </div>
                                <div class="text-[10px] text-bodydark2 truncate max-w-[140px]">
                                    {{ $imp->sede?->nombre ?? 'Sin sede' }}
                                </div>
                            </td>

                            <td class="py-2 px-3">
                                @if ($imp->ip_red)
                                    <span class="font-mono text-[10px] text-bodydark2">{{ $imp->ip_red }}</span>
                                @else
                                    <span class="text-bodydark2 text-[10px]">—</span>
                                @endif
                            </td>

                            <td class="py-2 px-3 text-center">
                                @php
                                    $activos = $imp->consumiblesActivos ?? collect();
                                    $enAlerta = $activos
                                        ->filter(fn($c) => in_array($c->estado_nivel, ['critico', 'agotado']))
                                        ->count();
                                    $bajos = $activos->filter(fn($c) => $c->estado_nivel === 'bajo')->count();
                                @endphp

                                @if ($activos->isEmpty())
                                    <span class="text-bodydark2 text-[10px]">—</span>
                                @elseif ($enAlerta > 0)
                                    <span
                                        class="inline-flex items-center gap-0.5 rounded-full bg-meta-1/10 px-1.5 py-0.5 text-[9px] font-bold text-meta-1"
                                        title="{{ $enAlerta }} en estado crítico/agotado">
                                        <i class="fas fa-exclamation-triangle text-[8px]"></i> {{ $enAlerta }}
                                    </span>
                                @elseif ($bajos > 0)
                                    <span
                                        class="inline-flex items-center gap-0.5 rounded-full bg-meta-6/10 px-1.5 py-0.5 text-[9px] font-bold text-meta-6"
                                        title="{{ $bajos }} en nivel bajo">
                                        <i class="fas fa-battery-quarter text-[8px]"></i> {{ $bajos }}
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-0.5 rounded-full bg-meta-3/10 px-1.5 py-0.5 text-[9px] font-bold text-meta-3"
                                        title="Todos los consumibles OK">
                                        <i class="fas fa-check text-[8px]"></i> {{ $activos->count() }}
                                    </span>
                                @endif
                            </td>

                            <td class="py-2 px-3 text-center">
                                <span
                                    class="inline-flex rounded-full px-2 py-0.5 text-[9px] font-bold uppercase whitespace-nowrap {{ $cfgEstado['clase'] }}">
                                    {{ $cfgEstado['texto'] }}
                                </span>
                            </td>

                            <td class="py-2 px-3 text-right">
                                <a href="{{ route('impresoras.show', $imp) }}"
                                    class="text-bodydark2 hover:text-primary transition" title="Ver detalle">
                                    <i class="fas fa-arrow-right text-xs"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center">
                                <i class="fas fa-print text-2xl text-bodydark2 mb-2 block"></i>
                                <p class="text-xs text-bodydark2">
                                    @if (!empty($filtros['q']))
                                        Sin resultados para "<strong>{{ $filtros['q'] }}</strong>".
                                    @else
                                        No hay impresoras registradas en tu alcance.
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($impresoras->hasPages())
            <div class="border-t border-stroke px-4 py-2.5 dark:border-strokedark">
                {{ $impresoras->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
