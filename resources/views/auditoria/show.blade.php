<x-app-layout>
    <x-slot name="title">Auditoría · {{ $instancia->codigo }}</x-slot>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
        <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
    @endpush

    <div class="db-header mb-6 flex items-center justify-between flex-wrap gap-3">
        <div>
            <span class="text-[11px] font-mono text-gray-500 uppercase tracking-wider">{{ $instancia->codigo }}</span>
            <h2 class="text-2xl font-black text-white uppercase tracking-tighter mt-1">
                <i class="fas fa-shield-alt mr-2 text-[#007acc]"></i> {{ $instancia->titulo }}
            </h2>
            <p class="text-xs text-gray-400 font-medium uppercase tracking-widest">
                {{ $registros->count() }} evento(s) registrado(s) · rastro inmutable
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flujos.instancias.show', $instancia) }}" class="db-btn ghost">
                <i class="fas fa-file-alt mr-2"></i> Ver documento
            </a>
            <a href="{{ route('auditoria.index') }}" class="db-btn ghost">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
        </div>
    </div>

    <div class="db-card">
        <div class="form-card-body" style="padding: 1.25rem;">
            @forelse ($registros as $registro)
                <div class="flex gap-4 pb-6 relative">
                    @if (!$loop->last)
                        <span class="absolute left-[15px] top-8 bottom-0 w-px bg-gray-800"></span>
                    @endif

                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-[#1a1a1b] border border-gray-800 flex items-center justify-center z-10">
                        <i class="fas {{ $registro->accion->icono() }} text-[11px] text-gray-400"></i>
                    </div>

                    <div class="flex-1 min-w-0 pt-0.5">
                        <div class="flex items-center gap-2 flex-wrap">
                            @include('auditoria.partials._badge_accion', ['accion' => $registro->accion])
                            <span class="text-[10px] text-gray-500">
                                {{ $registro->created_at->format('d/m/Y H:i:s') }}
                                ({{ $registro->created_at->diffForHumans() }})
                            </span>
                        </div>

                        <p class="text-xs text-white mt-1">
                            @if ($registro->user_name)
                                <span class="font-bold">{{ $registro->user_name }}</span>
                                <span class="text-gray-500">{{ $registro->user_email }}</span>
                            @else
                                <span class="text-gray-500">Acción del sistema</span>
                            @endif
                        </p>

                        <p class="text-[10px] text-gray-500 mt-0.5 font-mono">
                            IP: {{ $registro->ip_address ?? '—' }}
                            @if ($registro->metodo_http)
                                · {{ $registro->metodo_http }}
                            @endif
                        </p>

                        @if (!empty($registro->contexto))
                            <div class="mt-2 bg-[#1a1a1b] border border-gray-800 rounded-lg p-2.5 text-[11px] text-gray-300">
                                @foreach ($registro->contexto as $clave => $valor)
                                    <p class="mb-0.5">
                                        <span class="text-gray-500 uppercase tracking-wider text-[10px]">{{ $clave }}:</span>
                                        {{ is_array($valor) ? implode(', ', $valor) : $valor }}
                                    </p>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="db-empty py-8 text-center">
                    <i class="fas fa-shield-alt text-4xl mb-3 text-gray-700"></i>
                    <p class="text-gray-400">Todavía no hay eventos de auditoría para este documento.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
