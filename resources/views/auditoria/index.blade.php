<x-app-layout>
    <x-slot name="title">Registro de Auditoría</x-slot>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
        <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
    @endpush

    <div class="db-header mb-6 flex items-center justify-between flex-wrap gap-3">
        <div>
            <h2 class="text-2xl font-black text-white uppercase tracking-tighter">
                <i class="fas fa-shield-alt mr-2 text-[#007acc]"></i> Registro de Auditoría
            </h2>
            <p class="text-xs text-gray-400 font-medium uppercase tracking-widest">
            </p>
        </div>
    </div>

    @php
        $filtrosActivos = array_filter($filtros, fn ($v) => $v !== null && $v !== '');
    @endphp

    {{-- Formulario de búsqueda + filtros --}}
    <form method="GET" action="{{ route('auditoria.index') }}" class="db-card mb-4"
        x-data="{ expandido: {{ !empty($filtrosActivos) ? 'true' : 'false' }} }">
        <div class="form-card-body" style="padding: 1rem 1.25rem;">
            <div class="flex items-center gap-2 flex-wrap">
                <div class="relative flex-1 min-w-[240px]">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-xs"></i>
                    <input type="text" name="q" value="{{ $filtros['q'] ?? '' }}"
                        placeholder="Código, título del documento, usuario o correo..." class="form-input"
                        style="padding-left: 2.25rem;">
                </div>
                <button type="button" class="db-btn ghost" @click="expandido = !expandido">
                    <i class="fas fa-sliders-h mr-1"></i>
                    <span x-text="expandido ? 'Menos' : 'Más filtros'"></span>
                </button>
                <button type="submit" class="db-btn solid">
                    <i class="fas fa-search"></i> Buscar
                </button>
                @if (!empty($filtrosActivos))
                    <a href="{{ route('auditoria.index') }}" class="db-btn ghost" title="Limpiar filtros">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>

            <div x-show="expandido" x-transition
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mt-3 pt-3 border-t border-gray-800">
                <div class="form-group">
                    <label class="form-label text-[10px]">Acción</label>
                    <select name="accion" class="form-select text-xs">
                        <option value="">Todas</option>
                        @foreach ($acciones as $accionOpcion)
                            <option value="{{ $accionOpcion->value }}" @selected(($filtros['accion'] ?? '') === $accionOpcion->value)>
                                {{ $accionOpcion->etiqueta() }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label text-[10px]">Dirección IP</label>
                    <input type="text" name="ip" value="{{ $filtros['ip'] ?? '' }}" placeholder="192.168.1.10"
                        class="form-input text-xs">
                </div>
                <div class="form-group">
                    <label class="form-label text-[10px]">Desde</label>
                    <input type="date" name="desde" value="{{ $filtros['desde'] ?? '' }}" class="form-input text-xs">
                </div>
                <div class="form-group">
                    <label class="form-label text-[10px]">Hasta</label>
                    <input type="date" name="hasta" value="{{ $filtros['hasta'] ?? '' }}" class="form-input text-xs">
                </div>
            </div>

            @if (!empty($filtrosActivos))
                <div class="text-[10px] text-gray-500 mt-3 pt-3 border-t border-gray-800">
                    <i class="fas fa-info-circle mr-1"></i>
                    Mostrando resultados filtrados. {{ $registros->total() }} registro(s) encontrado(s).
                </div>
            @endif
        </div>
    </form>

    <div class="db-card">
        <div class="overflow-x-auto">
            <table class="db-table w-full">
                <thead>
                    <tr>
                        <th class="px-6 py-4">Fecha / Hora</th>
                        <th class="px-6 py-4">Documento</th>
                        <th class="px-6 py-4">Acción</th>
                        <th class="px-6 py-4">Usuario</th>
                        <th class="px-6 py-4">Dirección IP</th>
                        <th class="px-6 py-4 text-center">Detalle</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($registros as $registro)
                        <tr class="group transition-colors hover:bg-[var(--db-surface-2)]">
                            <td class="px-6 py-4">
                                <p class="text-xs text-white">{{ $registro->created_at->format('d/m/Y H:i:s') }}</p>
                                <p class="text-[10px] text-gray-500">{{ $registro->created_at->diffForHumans() }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-[10px] text-gray-500 font-mono uppercase tracking-wider">
                                    {{ $registro->documento_codigo ?? '—' }}
                                </p>
                                <a href="{{ route('auditoria.show', $registro->workflow_instance_id) }}"
                                    class="text-sm font-bold text-white hover:text-[#007acc] transition-colors">
                                    {{ $registro->documento_titulo ?? 'Documento #' . $registro->workflow_instance_id }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                @include('auditoria.partials._badge_accion', ['accion' => $registro->accion])
                            </td>
                            <td class="px-6 py-4">
                                @if ($registro->user_name)
                                    <div class="flex items-center gap-2">
                                        <div class="db-avatar text-xs w-7 h-7">
                                            {{ substr($registro->user_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-xs text-white leading-tight">{{ $registro->user_name }}</p>
                                            <p class="text-[10px] text-gray-500 leading-tight">{{ $registro->user_email }}</p>
                                        </div>
                                    </div>
                                @else
                                    <span class="db-sub">Sistema</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs text-gray-400 font-mono">{{ $registro->ip_address ?? '—' }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if (!empty($registro->contexto))
                                    <div class="relative inline-block" x-data="{ open: false }">
                                        <button type="button" class="db-btn outline" @click="open = !open" title="Ver contexto">
                                            <i class="fas fa-info-circle"></i>
                                        </button>
                                        <div x-show="open" @click.outside="open = false" x-transition
                                            class="absolute right-0 z-10 mt-2 w-72 text-left db-card p-3 text-[11px] text-gray-300"
                                            style="display: none;">
                                            @foreach ($registro->contexto as $clave => $valor)
                                                <p class="mb-1">
                                                    <span class="text-gray-500 uppercase tracking-wider text-[10px]">{{ $clave }}:</span>
                                                    {{ is_array($valor) ? implode(', ', $valor) : $valor }}
                                                </p>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <span class="db-sub">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="db-empty py-8 text-center">
                                    <i class="fas fa-shield-alt text-4xl mb-3 text-gray-700"></i>
                                    <p class="text-gray-400">No hay registros de auditoría con estos filtros.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($registros->hasPages())
            <div class="px-4 py-3 border-t border-gray-800">
                {{ $registros->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
