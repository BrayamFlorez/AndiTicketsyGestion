{{-- resources/views/equipos/partials/_mantenimientos.blade.php --}}
{{-- Historial de servicios técnicos (preventivos y correctivos) del PC. Recibe: $equipo --}}

@php
    $usuario = auth()->user();
    $puedeVer = $usuario->can('equipos.mantenimientos.ver');
    $puedeGestionar = $usuario->can('equipos.mantenimientos.gestionar') && $equipo->admiteMantenimiento();
    $puedeEliminar = $usuario->can('equipos.mantenimientos.eliminar');
@endphp

<div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
    <div class="border-b border-stroke py-2.5 px-4 dark:border-strokedark flex items-center justify-between">
        <h3 class="font-bold text-black dark:text-white text-[11px] uppercase tracking-wider flex items-center gap-1.5">
            <i class="fas fa-history text-primary"></i> Servicios técnicos ({{ $equipo->mantenimientos->count() }})
        </h3>
        @if ($puedeGestionar)
            <a href="{{ route('equipos.mantenimientos.create', $equipo) }}" class="text-[10px] font-bold uppercase text-primary hover:underline flex items-center gap-1">
                <i class="fas fa-plus text-[8px]"></i> Registrar servicio
            </a>
        @endif
    </div>

    @if (! $equipo->admiteMantenimiento())
        <p class="p-4 text-xs text-bodydark2">Las impresoras registran sus mantenimientos en el módulo de impresoras.</p>
    @else
        <div class="max-w-full overflow-x-auto">
            <table class="w-full table-auto border-collapse">
                <thead>
                    <tr class="bg-gray-2 text-left dark:bg-meta-4">
                        <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white w-28">Orden / Fecha</th>
                        <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white">Servicio</th>
                        <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white min-w-[130px]">Técnico</th>
                        <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white text-center w-28">Estado</th>
                        <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider text-black dark:text-white text-right w-24"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke dark:divide-strokedark">
                    @forelse ($equipo->mantenimientos as $m)
                        <tr class="hover:bg-gray-2 dark:hover:bg-meta-4/40 transition">
                            <td class="py-2 px-3 whitespace-nowrap">
                                <div class="font-mono text-xs font-bold text-primary">{{ $m->numero_orden }}</div>
                                <div class="text-[9px] font-mono text-bodydark2">{{ $m->fecha_servicio->format('d/m/Y') }}</div>
                            </td>
                            <td class="py-2 px-3">
                                <div class="text-xs font-bold text-black dark:text-white">{{ ucfirst($m->tipo) }}</div>
                                <div class="text-[10px] text-bodydark2 truncate max-w-[260px]" title="{{ $m->falla }}">{{ \Illuminate\Support\Str::limit($m->falla, 70) }}</div>
                            </td>
                            <td class="py-2 px-3 whitespace-nowrap text-xs text-black dark:text-white">{{ $m->tecnico?->name ?? '—' }}</td>
                            <td class="py-2 px-3 text-center whitespace-nowrap">
                                <span class="inline-flex rounded-full px-2 py-0.5 text-[8px] font-bold uppercase border {{ $m->estaFinalizado() ? 'bg-meta-3/10 text-meta-3 border-meta-3/20' : 'bg-meta-6/10 text-meta-6 border-meta-6/20' }}">
                                    {{ $m->label_estado }}
                                </span>
                            </td>
                            <td class="py-2 px-3 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    @if ($puedeVer)
                                        <a href="{{ route('equipos.mantenimientos.show', [$equipo, $m]) }}" title="Ver detalle"
                                           class="flex h-6 w-6 items-center justify-center rounded border border-stroke text-bodydark2 hover:border-primary hover:text-primary dark:border-strokedark transition"><i class="fas fa-eye text-[9px]"></i></a>
                                        @if ($m->estaFinalizado())
                                            <a href="{{ route('equipos.mantenimientos.pdf', [$equipo, $m]) }}" target="_blank" rel="noopener" title="Reporte PDF"
                                               class="flex h-6 w-6 items-center justify-center rounded border border-stroke text-meta-1 hover:border-meta-1 dark:border-strokedark transition"><i class="fas fa-file-pdf text-[9px]"></i></a>
                                        @endif
                                    @endif
                                    @if ($puedeGestionar && ! $m->estaFinalizado())
                                        <a href="{{ route('equipos.mantenimientos.edit', [$equipo, $m]) }}" title="Editar"
                                           class="flex h-6 w-6 items-center justify-center rounded border border-stroke text-meta-6 hover:border-meta-6 dark:border-strokedark transition"><i class="fas fa-pen text-[9px]"></i></a>
                                    @endif
                                    @if ($puedeEliminar)
                                        <form action="{{ route('equipos.mantenimientos.destroy', [$equipo, $m]) }}" method="POST" onsubmit="return confirm('¿Eliminar el servicio {{ $m->numero_orden }}? Se borran sus fotos y firmas.')" class="inline-block">
                                            @csrf @method('DELETE')
                                            <button type="submit" title="Eliminar" class="flex h-6 w-6 items-center justify-center rounded border border-stroke text-meta-1 hover:border-meta-1 dark:border-strokedark transition"><i class="fas fa-trash text-[9px]"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center">
                                <div class="flex flex-col items-center text-bodydark2">
                                    <i class="fas fa-clipboard-check text-xl mb-1"></i>
                                    <p class="text-xs">Sin servicios registrados.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>
