{{-- resources/views/proyectos/index.blade.php — Bandeja de proyectos --}}
<x-app-layout>
    <x-slot name="title">Proyectos</x-slot>

    @php
        $select = 'rounded border border-stroke bg-transparent py-1.5 px-2 text-xs focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white';
    @endphp

    @include('proyectos.partials._flash')

    {{-- Cabecera + filtros en una sola fila --}}
    <form method="GET" action="{{ route('proyectos.index') }}" class="mb-3 flex items-center gap-2 flex-wrap">
        <div class="flex items-center gap-2 shrink-0 mr-1">
            <i class="fas fa-diagram-project text-primary"></i>
            <h2 class="text-sm font-bold text-black dark:text-white whitespace-nowrap">Proyectos</h2>
            <span class="text-[10px] text-bodydark2 font-mono">({{ $proyectos->total() }})</span>
        </div>

        <div class="relative flex-1 min-w-[220px]">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-bodydark2 text-xs pointer-events-none"></i>
            <input type="text" name="q" value="{{ $filtros['q'] ?? '' }}" placeholder="Código, nombre o cliente..."
                   class="w-full rounded border border-stroke bg-transparent py-1.5 pl-8 pr-3 text-xs focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
        </div>

        <select name="estado" onchange="this.form.submit()" class="{{ $select }}">
            <option value="">Todos los estados</option>
            @foreach (\App\Models\Proyecto::ESTADOS as $clave => $cfg)
                <option value="{{ $clave }}" @selected(($filtros['estado'] ?? '') === $clave)>{{ $cfg['label'] }}</option>
            @endforeach
        </select>

        <select name="cliente_id" onchange="this.form.submit()" class="{{ $select }} max-w-[200px]">
            <option value="">Todos los clientes</option>
            @foreach ($clientes as $c)
                <option value="{{ $c->id }}" @selected((string) ($filtros['cliente_id'] ?? '') === (string) $c->id)>{{ $c->nombre }}</option>
            @endforeach
        </select>

        <label class="inline-flex items-center gap-1.5 text-xs text-black dark:text-white cursor-pointer whitespace-nowrap">
            <input type="checkbox" name="mios" value="1" onchange="this.form.submit()" @checked($filtros['mios'] ?? false) class="rounded border-stroke">
            Solo mis proyectos
        </label>

        @if ($filtros)
            <a href="{{ route('proyectos.index') }}" class="text-[11px] text-bodydark2 hover:text-meta-1" title="Limpiar filtros">
                <i class="fas fa-xmark"></i> Limpiar
            </a>
        @endif

        <div class="flex items-center gap-1.5 ml-auto">
            @can('clientes.gestionar')
                <a href="{{ route('clientes.index') }}"
                   class="inline-flex items-center gap-1 rounded border border-stroke py-1.5 px-3 text-xs font-medium hover:bg-gray dark:border-strokedark dark:hover:bg-meta-4 text-black dark:text-white">
                    <i class="fas fa-address-book text-[10px]"></i> Clientes
                </a>
            @endcan
            @if ($puedeCrear)
                <a href="{{ route('proyectos.create') }}"
                   class="inline-flex items-center gap-1 rounded bg-primary py-1.5 px-3 text-xs font-bold uppercase text-white hover:bg-opacity-90 transition shadow-sm">
                    <i class="fas fa-plus text-[10px]"></i> Nuevo proyecto
                </a>
            @endif
        </div>
    </form>

    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark overflow-x-auto">
        <table class="w-full text-xs">
            <thead>
                <tr class="bg-gray-2 dark:bg-meta-4 text-left text-[10px] uppercase tracking-wider text-bodydark2">
                    <th class="py-2 px-3 font-bold">Código</th>
                    <th class="py-2 px-3 font-bold">Proyecto</th>
                    <th class="py-2 px-3 font-bold">Cliente</th>
                    <th class="py-2 px-3 font-bold">Responsable</th>
                    <th class="py-2 px-3 font-bold text-center">Estado</th>
                    <th class="py-2 px-3 font-bold text-center" title="Solicitudes abiertas">Solic.</th>
                    <th class="py-2 px-3 font-bold">Última etapa</th>
                    <th class="py-2 px-3 font-bold">Entrega est.</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($proyectos as $p)
                    @php
                        $vencido = $p->fecha_entrega_estimada && ! $p->estaCerrado() && $p->fecha_entrega_estimada->isPast() && ! $p->fecha_entrega_estimada->isToday();
                    @endphp
                    <tr class="border-t border-stroke dark:border-strokedark hover:bg-gray-2/50 dark:hover:bg-meta-4/40 cursor-pointer"
                        onclick="window.location='{{ route('proyectos.show', $p) }}'">
                        <td class="py-1.5 px-3 whitespace-nowrap">
                            <span class="font-mono text-[11px] font-bold text-primary">{{ $p->codigo }}</span>
                        </td>
                        <td class="py-1.5 px-3 max-w-[280px]">
                            <span class="font-semibold text-black dark:text-white truncate block" title="{{ $p->nombre }}">{{ $p->nombre }}</span>
                        </td>
                        <td class="py-1.5 px-3 text-bodydark2 max-w-[200px] truncate">{{ $p->cliente?->nombre ?? '—' }}</td>
                        <td class="py-1.5 px-3 text-black dark:text-white whitespace-nowrap">{{ $p->responsable?->name ?? '—' }}</td>
                        <td class="py-1.5 px-3 text-center">
                            <span class="inline-flex rounded-full px-2 py-0.5 text-[9px] font-bold uppercase border {{ $p->clase_estado }}">{{ $p->label_estado }}</span>
                        </td>
                        <td class="py-1.5 px-3 text-center font-mono">
                            @if ($p->solicitudes_abiertas_count)
                                <span class="inline-flex min-w-[20px] justify-center rounded-full bg-[#f97316]/15 text-[#f97316] px-1.5 text-[10px] font-bold">{{ $p->solicitudes_abiertas_count }}</span>
                            @else
                                <span class="text-bodydark2">—</span>
                            @endif
                        </td>
                        <td class="py-1.5 px-3 font-mono text-[11px] text-bodydark2 whitespace-nowrap">
                            {{ $p->etapas_max_fecha ? \Illuminate\Support\Carbon::parse($p->etapas_max_fecha)->format('d/m/Y') : '—' }}
                        </td>
                        <td class="py-1.5 px-3 font-mono text-[11px] whitespace-nowrap {{ $vencido ? 'text-meta-1 font-bold' : 'text-bodydark2' }}">
                            {{ $p->fecha_entrega_estimada?->format('d/m/Y') ?? '—' }}
                            @if ($vencido) <i class="fas fa-triangle-exclamation text-[9px]" title="Fecha de entrega vencida"></i> @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-bodydark2">
                            <i class="fas fa-diagram-project text-xl mb-1 block"></i>
                            {{ $filtros ? 'No hay proyectos con esos filtros.' : 'Todavía no hay proyectos.' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">{{ $proyectos->links() }}</div>
</x-app-layout>
