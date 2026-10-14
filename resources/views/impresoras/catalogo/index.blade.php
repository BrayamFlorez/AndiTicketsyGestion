<x-app-layout>
    <x-slot name="title">Catálogo de modelos</x-slot>

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

    {{-- Cabecera + buscador --}}
    <form method="GET" action="{{ route('impresoras.catalogo.index') }}" class="mb-4">
        <div class="flex items-center gap-3 flex-wrap">
            <div class="flex items-center gap-2 shrink-0">
                <i class="fas fa-layer-group text-primary"></i>
                <h2 class="text-sm font-bold text-black dark:text-white whitespace-nowrap">
                    Catálogo de modelos
                </h2>
            </div>

            <div class="relative flex-1 min-w-[240px]">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-bodydark2 text-xs pointer-events-none"></i>
                <input type="text" name="q" value="{{ $filtros['q'] ?? '' }}"
                       placeholder="Buscar por marca o modelo..."
                       class="w-full rounded border border-stroke bg-transparent py-2 pl-9 pr-3 text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
            </div>

            <select name="tipo" onchange="this.form.submit()"
                    class="rounded border border-stroke bg-transparent py-2 px-3 text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white shrink-0">
                <option value="">— Toda tecnología —</option>
                @foreach ($tecnologias as $key => $label)
                    <option value="{{ $key }}" @selected(($filtros['tipo'] ?? '') === $key)>{{ $label }}</option>
                @endforeach
            </select>

            <div class="flex items-center gap-2 shrink-0">
                <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded bg-primary py-2 px-3.5 text-xs font-bold uppercase text-white hover:bg-opacity-90">
                    <i class="fas fa-search text-[10px]"></i> Buscar
                </button>

                <a href="{{ route('impresoras.catalogo.create') }}"
                   class="inline-flex items-center gap-1.5 rounded bg-meta-3 py-2 px-3.5 text-xs font-bold uppercase text-white hover:bg-opacity-90 shadow-sm whitespace-nowrap">
                    <i class="fas fa-plus-circle text-[10px]"></i> Nuevo modelo
                </a>

                <a href="{{ route('impresoras.index') }}"
                   class="inline-flex items-center gap-1.5 rounded border border-stroke py-2 px-3.5 text-xs font-medium hover:bg-gray dark:border-strokedark dark:hover:bg-meta-4">
                    <i class="fas fa-arrow-left text-[10px]"></i> Impresoras
                </a>
            </div>
        </div>
    </form>

    {{-- Tabla --}}
    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="max-w-full overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-gray-2 text-left dark:bg-meta-4">
                        <th class="py-2.5 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white min-w-[200px]">Marca / Modelo</th>
                        <th class="py-2.5 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white w-32">Tecnología</th>
                        <th class="py-2.5 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white text-center w-20">Color</th>
                        <th class="py-2.5 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white text-center w-20">MFP</th>
                        <th class="py-2.5 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white text-center w-24">Consumibles</th>
                        <th class="py-2.5 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white text-center w-24">Impresoras</th>
                        <th class="py-2.5 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white text-center w-20">Estado</th>
                        <th class="py-2.5 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white text-right w-28">Acciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-stroke dark:divide-strokedark">
                    @forelse ($modelos as $m)
                        <tr class="hover:bg-gray-2 dark:hover:bg-meta-4/40 transition">
                            <td class="py-2 px-3">
                                <a href="{{ route('impresoras.catalogo.edit', $m) }}"
                                   class="text-xs font-medium text-black dark:text-white hover:text-primary transition">
                                    {{ $m->marca }} {{ $m->modelo }}
                                </a>
                            </td>
                            <td class="py-2 px-3">
                                <span class="text-[11px] text-bodydark2">
                                    <i class="fas {{ $m->tipo_tecnologia === 'laser' ? 'fa-print' : 'fa-fill-drip' }} text-primary mr-1 text-[10px]"></i>{{ $tecnologias[$m->tipo_tecnologia] ?? $m->tipo_tecnologia }}
                                </span>
                            </td>
                            <td class="py-2 px-3 text-center">
                                @if ($m->color)
                                    <i class="fas fa-palette text-meta-3 text-xs" title="Color"></i>
                                @else
                                    <i class="fas fa-circle text-bodydark2 text-[10px]" title="Monocromática"></i>
                                @endif
                            </td>
                            <td class="py-2 px-3 text-center">
                                @if ($m->es_multifuncional)
                                    <i class="fas fa-check text-meta-3 text-xs"></i>
                                @else
                                    <span class="text-bodydark2 text-[10px]">—</span>
                                @endif
                            </td>
                            <td class="py-2 px-3 text-center">
                                <span class="text-[11px] font-bold text-bodydark2">
                                    {{ count($m->consumibles_sugeridos ?? []) }}
                                </span>
                            </td>
                            <td class="py-2 px-3 text-center">
                                <span class="inline-flex rounded-full px-2 py-0.5 text-[9px] font-bold {{ $m->impresoras_count > 0 ? 'bg-primary/10 text-primary' : 'bg-gray-2 text-bodydark2 dark:bg-meta-4' }}">
                                    {{ $m->impresoras_count }}
                                </span>
                            </td>
                            <td class="py-2 px-3 text-center">
                                <form method="POST" action="{{ route('impresoras.catalogo.toggle', $m) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="inline-flex rounded-full px-2 py-0.5 text-[9px] font-bold uppercase {{ $m->activo ? 'bg-meta-3/10 text-meta-3' : 'bg-gray-2 text-bodydark2 dark:bg-meta-4' }}"
                                            title="{{ $m->activo ? 'Desactivar' : 'Activar' }}">
                                        {{ $m->activo ? 'Activo' : 'Inactivo' }}
                                    </button>
                                </form>
                            </td>
                            <td class="py-2 px-3 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('impresoras.catalogo.edit', $m) }}"
                                       class="text-bodydark2 hover:text-primary transition p-1" title="Editar">
                                        <i class="fas fa-pen text-[11px]"></i>
                                    </a>
                                    <form method="POST" action="{{ route('impresoras.catalogo.destroy', $m) }}" class="inline"
                                          onsubmit="return confirm('¿Eliminar {{ $m->marca }} {{ $m->modelo }} del catálogo?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-bodydark2 hover:text-meta-1 transition p-1" title="Eliminar">
                                            <i class="fas fa-trash text-[11px]"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-10 text-center">
                                <i class="fas fa-layer-group text-2xl text-bodydark2 mb-2 block"></i>
                                <p class="text-xs text-bodydark2">
                                    @if (! empty($filtros['q']))
                                        Sin resultados para "<strong>{{ $filtros['q'] }}</strong>".
                                    @else
                                        No hay modelos en el catálogo.
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($modelos->hasPages())
            <div class="border-t border-stroke px-4 py-2.5 dark:border-strokedark">
                {{ $modelos->links() }}
            </div>
        @endif
    </div>
</x-app-layout>