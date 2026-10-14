{{-- resources/views/clientes/index.blade.php --}}
<x-app-layout>
    <x-slot name="title">Clientes</x-slot>

    @include('proyectos.partials._flash')

    {{-- Cabecera + buscador en una fila --}}
    <form method="GET" action="{{ route('clientes.index') }}" class="mb-3 flex items-center gap-2 flex-wrap">
        <div class="flex items-center gap-2 shrink-0 mr-1">
            <i class="fas fa-address-book text-primary"></i>
            <h2 class="text-sm font-bold text-black dark:text-white whitespace-nowrap">Clientes</h2>
            <span class="text-[10px] text-bodydark2 font-mono">({{ $clientes->total() }})</span>
        </div>

        <div class="relative flex-1 min-w-[220px]">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-bodydark2 text-xs pointer-events-none"></i>
            <input type="text" name="q" value="{{ $filtros['q'] ?? '' }}" placeholder="Nombre, documento, contacto o correo..."
                   class="w-full rounded border border-stroke bg-transparent py-1.5 pl-8 pr-3 text-xs focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
        </div>

        <select name="activo" onchange="this.form.submit()"
                class="rounded border border-stroke bg-transparent py-1.5 px-2 text-xs focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
            <option value="">Todos</option>
            <option value="1" @selected(($filtros['activo'] ?? '') === '1')>Activos</option>
            <option value="0" @selected(($filtros['activo'] ?? '') === '0')>Inactivos</option>
        </select>

        <button type="submit" class="rounded border border-stroke py-1.5 px-3 text-xs font-medium hover:bg-gray dark:border-strokedark dark:hover:bg-meta-4 text-black dark:text-white">
            Buscar
        </button>

        <a href="{{ route('clientes.create') }}"
           class="inline-flex items-center gap-1 rounded bg-primary py-1.5 px-3 text-xs font-bold uppercase text-white hover:bg-opacity-90 transition shadow-sm">
            <i class="fas fa-plus text-[10px]"></i> Nuevo cliente
        </a>
    </form>

    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark overflow-x-auto">
        <table class="w-full text-xs">
            <thead>
                <tr class="bg-gray-2 dark:bg-meta-4 text-left text-[10px] uppercase tracking-wider text-bodydark2">
                    <th class="py-2 px-3 font-bold">Cliente</th>
                    <th class="py-2 px-3 font-bold">Documento</th>
                    <th class="py-2 px-3 font-bold">Contacto</th>
                    <th class="py-2 px-3 font-bold">Teléfono / Correo</th>
                    <th class="py-2 px-3 font-bold text-center">Proyectos</th>
                    <th class="py-2 px-3 font-bold text-center">Estado</th>
                    <th class="py-2 px-3 font-bold text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($clientes as $cliente)
                    <tr class="border-t border-stroke dark:border-strokedark hover:bg-gray-2/50 dark:hover:bg-meta-4/40">
                        <td class="py-1.5 px-3">
                            <span class="font-semibold text-black dark:text-white">{{ $cliente->nombre }}</span>
                            @if ($cliente->ciudad)
                                <span class="text-[10px] text-bodydark2">· {{ $cliente->ciudad }}</span>
                            @endif
                        </td>
                        <td class="py-1.5 px-3 font-mono text-[11px] text-bodydark2 whitespace-nowrap">{{ $cliente->documento_completo ?? '—' }}</td>
                        <td class="py-1.5 px-3 text-black dark:text-white">
                            {{ $cliente->contacto_nombre ?? '—' }}
                            @if ($cliente->contacto_cargo)
                                <span class="text-[10px] text-bodydark2">· {{ $cliente->contacto_cargo }}</span>
                            @endif
                        </td>
                        <td class="py-1.5 px-3 text-bodydark2 whitespace-nowrap">
                            {{ $cliente->telefono ?? '' }}
                            @if ($cliente->telefono && $cliente->email) · @endif
                            {{ $cliente->email ?? '' }}
                            @if (! $cliente->telefono && ! $cliente->email) — @endif
                        </td>
                        <td class="py-1.5 px-3 text-center font-mono">
                            @if ($cliente->proyectos_count)
                                <a href="{{ route('proyectos.index', ['cliente_id' => $cliente->id]) }}" class="text-primary hover:underline">{{ $cliente->proyectos_count }}</a>
                            @else
                                <span class="text-bodydark2">0</span>
                            @endif
                        </td>
                        <td class="py-1.5 px-3 text-center">
                            <span class="inline-flex rounded-full px-2 py-0.5 text-[9px] font-bold uppercase border {{ $cliente->activo ? 'bg-meta-3/10 text-meta-3 border-meta-3/20' : 'bg-gray-2 text-bodydark2 border-stroke dark:bg-meta-4 dark:border-strokedark' }}">
                                {{ $cliente->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="py-1.5 px-3 text-right whitespace-nowrap">
                            <a href="{{ route('clientes.edit', $cliente) }}" class="text-bodydark2 hover:text-primary px-1" title="Editar">
                                <i class="fas fa-pen text-[11px]"></i>
                            </a>
                            @if (! $cliente->proyectos_count)
                                <form method="POST" action="{{ route('clientes.destroy', $cliente) }}" class="inline"
                                      onsubmit="return confirm('¿Eliminar el cliente {{ addslashes($cliente->nombre) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-bodydark2 hover:text-meta-1 px-1" title="Eliminar">
                                        <i class="fas fa-trash text-[11px]"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-bodydark2">
                            <i class="fas fa-address-book text-xl mb-1 block"></i>
                            No hay clientes registrados{{ ($filtros['q'] ?? false) ? ' con ese criterio' : '' }}.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">{{ $clientes->links() }}</div>
</x-app-layout>
