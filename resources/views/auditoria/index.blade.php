{{-- resources/views/auditoria/index.blade.php --}}
<x-app-layout>
    <x-slot name="title">Registro de Auditoría</x-slot>

    {{-- ── Cabecera ────────────────────────────────────────────────────── --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-title-md2 font-bold text-black dark:text-white flex items-center gap-2">
                <i class="fas fa-shield-alt text-primary"></i> Registro de Auditoría
            </h2>
            <p class="text-xs text-bodydark2 mt-1">
                Bitácora de eventos, trazabilidad y registro de actividad del sistema
            </p>
        </div>
    </div>

    @php
        $filtrosActivos = array_filter($filtros, fn ($v) => $v !== null && $v !== '');
    @endphp

    {{-- ── Formulario de búsqueda + Filtros ────────────────────────── --}}
    <div class="rounded-sm border border-stroke bg-white px-5 pt-5 pb-4 shadow-default dark:border-strokedark dark:bg-boxdark mb-6"
         x-data="{ expandido: {{ !empty($filtrosActivos) ? 'true' : 'false' }} }">
        <form method="GET" action="{{ route('auditoria.index') }}">
            <div class="flex items-center gap-2 flex-wrap">
                {{-- Campo Búsqueda --}}
                <div class="relative flex-1 min-w-[240px]">
                    <input type="text" name="q" value="{{ $filtros['q'] ?? '' }}"
                           placeholder="Buscar por código, título, usuario o correo..."
                           class="w-full rounded border border-stroke bg-transparent py-1.5 pl-9 pr-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 z-30 pointer-events-none">
                        <i class="fas fa-search text-xs text-bodydark2"></i>
                    </span>
                </div>

                {{-- Toggle Filtros --}}
                <button type="button" @click="expandido = !expandido"
                        class="inline-flex items-center gap-1.5 rounded border border-stroke py-1.5 px-3 text-xs font-medium text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition">
                    <i class="fas fa-sliders-h text-[10px]"></i>
                    <span x-text="expandido ? 'Menos filtros' : 'Más filtros'"></span>
                </button>

                {{-- Botón Buscar --}}
                <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded bg-primary py-1.5 px-4 text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm cursor-pointer">
                    <i class="fas fa-search text-[10px]"></i> Buscar
                </button>

                {{-- Limpiar Filtros --}}
                @if (!empty($filtrosActivos))
                    <a href="{{ route('auditoria.index') }}"
                       class="inline-flex h-8 w-8 items-center justify-center rounded border border-stroke text-bodydark2 hover:border-meta-1 hover:text-meta-1 dark:border-strokedark transition"
                       title="Limpiar filtros">
                        <i class="fas fa-times text-xs"></i>
                    </a>
                @endif
            </div>

            {{-- Panel Expandible de Filtros --}}
            <div x-show="expandido" x-cloak x-transition
                 class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-4 pt-4 border-t border-stroke dark:border-strokedark">
                
                {{-- Acción --}}
                <div>
                    <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Acción</label>
                    <div class="relative z-20">
                        <select name="accion" class="w-full appearance-none rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white cursor-pointer">
                            <option value="" class="bg-[#1c2434] text-gray-400">Todas las acciones</option>
                            @foreach ($acciones as $accionOpcion)
                                <option value="{{ $accionOpcion->value }}" @selected(($filtros['accion'] ?? '') === $accionOpcion->value) class="bg-[#1c2434] text-white">
                                    {{ $accionOpcion->etiqueta() }}
                                </option>
                            @endforeach
                        </select>
                        <span class="absolute top-1/2 right-3 -translate-y-1/2 z-30 pointer-events-none">
                            <i class="fas fa-chevron-down text-[10px] text-bodydark2"></i>
                        </span>
                    </div>
                </div>

                {{-- Dirección IP --}}
                <div>
                    <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Dirección IP</label>
                    <input type="text" name="ip" value="{{ $filtros['ip'] ?? '' }}" placeholder="Ej: 192.168.1.10"
                           class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs font-mono outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                </div>

                {{-- Desde --}}
                <div>
                    <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Desde</label>
                    <input type="date" name="desde" value="{{ $filtros['desde'] ?? '' }}"
                           class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                </div>

                {{-- Hasta --}}
                <div>
                    <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Hasta</label>
                    <input type="date" name="hasta" value="{{ $filtros['hasta'] ?? '' }}"
                           class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                </div>
            </div>

            @if (!empty($filtrosActivos))
                <div class="text-[10px] text-bodydark2 mt-3 pt-3 border-t border-stroke dark:border-strokedark flex items-center gap-1.5">
                    <i class="fas fa-info-circle text-primary"></i>
                    <span>Mostrando resultados filtrados. <strong>{{ $registros->total() }}</strong> registro(s) encontrado(s).</span>
                </div>
            @endif
        </form>
    </div>

    {{-- ── Tabla de Auditoría ────────────────────────────────────────── --}}
    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark mb-6">
        <div class="max-w-full overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-gray-2 text-left dark:bg-meta-4">
                        <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase min-w-[140px]">Fecha / Hora</th>
                        <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase min-w-[180px]">Documento</th>
                        <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase">Acción</th>
                        <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase min-w-[180px]">Usuario</th>
                        <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase w-32">Dirección IP</th>
                        <th class="py-3.5 px-4 font-bold text-black dark:text-white text-xs uppercase text-center w-24">Detalle</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke dark:divide-strokedark">
                    @forelse ($registros as $registro)
                        <tr class="hover:bg-gray-2 dark:hover:bg-meta-4/50 transition">
                            
                            {{-- Fecha / Hora --}}
                            <td class="py-3.5 px-4">
                                <span class="text-xs font-bold text-black dark:text-white block">{{ $registro->created_at->format('d/m/Y H:i:s') }}</span>
                                <span class="text-[10px] text-bodydark2 block mt-0.5">{{ $registro->created_at->diffForHumans() }}</span>
                            </td>

                            {{-- Documento --}}
                            <td class="py-3.5 px-4">
                                <span class="text-[10px] text-bodydark2 font-mono uppercase block">
                                    {{ $registro->documento_codigo ?? '—' }}
                                </span>
                                <a href="{{ route('auditoria.show', $registro->workflow_instance_id) }}"
                                   class="text-xs font-bold text-black dark:text-white hover:text-primary transition block truncate max-w-xs">
                                    {{ $registro->documento_titulo ?? 'Documento #' . $registro->workflow_instance_id }}
                                </a>
                            </td>

                            {{-- Acción --}}
                            <td class="py-3.5 px-4">
                                @include('auditoria.partials._badge_accion', ['accion' => $registro->accion])
                            </td>

                            {{-- Usuario --}}
                            <td class="py-3.5 px-4">
                                @if ($registro->user_name)
                                    <div class="flex items-center gap-2.5">
                                        <div class="flex h-7 w-7 items-center justify-center rounded-full bg-primary text-white text-[10px] font-bold shrink-0 uppercase">
                                            {{ substr($registro->user_name, 0, 1) }}
                                        </div>
                                        <div class="min-w-0">
                                            <span class="text-xs font-bold text-black dark:text-white block truncate">{{ $registro->user_name }}</span>
                                            <span class="text-[10px] text-bodydark2 block truncate">{{ $registro->user_email }}</span>
                                        </div>
                                    </div>
                                @else
                                    <span class="inline-flex rounded-full bg-gray-2 px-2.5 py-0.5 text-[10px] font-bold uppercase text-bodydark2 dark:bg-meta-4">
                                        Sistema
                                    </span>
                                @endif
                            </td>

                            {{-- Dirección IP --}}
                            <td class="py-3.5 px-4">
                                <span class="text-xs font-mono text-black dark:text-white">{{ $registro->ip_address ?? '—' }}</span>
                            </td>

                            {{-- Detalle Contexto Dropdown --}}
                            <td class="py-3.5 px-4 text-center">
                                @if (!empty($registro->contexto))
                                    <div class="relative inline-block" x-data="{ open: false }">
                                        <button type="button" @click="open = !open"
                                                class="flex h-7 w-7 items-center justify-center rounded border border-stroke text-bodydark2 hover:border-primary hover:text-primary dark:border-strokedark transition mx-auto"
                                                title="Ver detalle del contexto">
                                            <i class="fas fa-info-circle text-[10px]"></i>
                                        </button>
                                        
                                        <div x-show="open" @click.outside="open = false" x-cloak x-transition
                                             class="absolute right-0 z-50 mt-2 w-72 rounded border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark p-3 text-xs text-left">
                                            <span class="text-[10px] font-bold text-black dark:text-white uppercase tracking-wider block mb-2 pb-1 border-b border-stroke dark:border-strokedark">
                                                Contexto de la Acción
                                            </span>
                                            <div class="space-y-1.5 max-h-48 overflow-y-auto">
                                                @foreach ($registro->contexto as $clave => $valor)
                                                    <div class="text-[11px]">
                                                        <span class="font-bold text-black dark:text-white uppercase text-[9px]">{{ $clave }}:</span>
                                                        <span class="text-bodydark2 block break-words">{{ is_array($valor) ? json_encode($valor) : $valor }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-bodydark2 text-xs">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center">
                                <div class="flex flex-col items-center text-bodydark2">
                                    <i class="fas fa-shield-alt text-3xl mb-2"></i>
                                    <p class="text-xs">No se encontraron registros de auditoría que coincidan con los filtros.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if ($registros->hasPages())
            <div class="border-t border-stroke py-3 px-4 dark:border-strokedark">
                {{ $registros->links() }}
            </div>
        @endif
    </div>
</x-app-layout>