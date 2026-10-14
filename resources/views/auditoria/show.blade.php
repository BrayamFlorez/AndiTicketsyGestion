{{-- resources/views/auditoria/show.blade.php --}}
<x-app-layout>
    <x-slot name="title">Auditoría · {{ $instancia->codigo }}</x-slot>

    {{-- ── Cabecera ────────────────────────────────────────────────────── --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <span class="text-xs font-mono font-bold text-bodydark2 uppercase tracking-wider block">
                {{ $instancia->codigo }}
            </span>
            <h2 class="text-title-md2 font-bold text-black dark:text-white flex items-center gap-2 mt-0.5">
                <i class="fas fa-shield-alt text-primary"></i> {{ $instancia->titulo }}
            </h2>
            <p class="text-xs text-bodydark2 mt-1">
                {{ $registros->count() }} evento(s) registrado(s) · Rastro inmutable de auditoría
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('flujos.instancias.show', $instancia) }}" 
               class="inline-flex items-center justify-center gap-2 rounded border border-stroke py-2 px-4 text-center text-xs font-medium text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition shadow-sm">
                <i class="fas fa-file-alt text-[10px]"></i> Ver Documento
            </a>
            <a href="{{ route('auditoria.index') }}" 
               class="inline-flex items-center justify-center gap-2 rounded border border-stroke py-2 px-4 text-center text-xs font-medium text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition shadow-sm">
                <i class="fas fa-arrow-left text-[10px]"></i> Volver
            </a>
        </div>
    </div>

    {{-- ── Línea de Tiempo de Auditoría ──────────────────────────────── --}}
    <div class="rounded-sm border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark mb-6">
        <div class="space-y-6">
            @forelse ($registros as $registro)
                <div class="flex gap-4 pb-6 relative">
                    {{-- Conector Vertical --}}
                    @if (!$loop->last)
                        <span class="absolute left-[15px] top-8 bottom-0 w-0.5 bg-stroke dark:bg-strokedark"></span>
                    @endif

                    {{-- Ícono del Evento --}}
                    <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gray-2 border border-stroke dark:bg-meta-4 dark:border-strokedark flex items-center justify-center z-10 text-bodydark2 dark:text-white shadow-sm">
                        <i class="fas {{ $registro->accion->icono() }} text-xs"></i>
                    </div>

                    {{-- Contenido del Evento --}}
                    <div class="flex-1 min-w-0 pt-0.5">
                        <div class="flex items-center gap-2 flex-wrap">
                            @include('auditoria.partials._badge_accion', ['accion' => $registro->accion])
                            <span class="text-[10px] font-medium text-bodydark2">
                                {{ $registro->created_at->format('d/m/Y H:i:s') }}
                                ({{ $registro->created_at->diffForHumans() }})
                            </span>
                        </div>

                        <p class="text-xs text-black dark:text-white mt-1">
                            @if ($registro->user_name)
                                <span class="font-bold">{{ $registro->user_name }}</span>
                                <span class="text-bodydark2">· {{ $registro->user_email }}</span>
                            @else
                                <span class="text-bodydark2 italic">Acción del sistema</span>
                            @endif
                        </p>

                        <p class="text-[10px] text-bodydark2 mt-0.5 font-mono">
                            IP: {{ $registro->ip_address ?? '—' }}
                            @if ($registro->metodo_http)
                                · {{ $registro->metodo_http }}
                            @endif
                        </p>

                        {{-- Detalle Contexto --}}
                        @if (!empty($registro->contexto))
                            <div class="mt-2.5 rounded border border-stroke bg-gray-2 p-3 text-xs dark:border-strokedark dark:bg-meta-4/20">
                                <span class="text-[10px] font-bold text-black dark:text-white uppercase tracking-wider block mb-1 pb-1 border-b border-stroke dark:border-strokedark">
                                    Detalle del Evento
                                </span>
                                <div class="space-y-1">
                                    @foreach ($registro->contexto as $clave => $valor)
                                        <div class="text-[11px]">
                                            <span class="font-bold uppercase text-[9px] text-black dark:text-white">{{ $clave }}:</span>
                                            <span class="text-bodydark2">{{ is_array($valor) ? json_encode($valor) : $valor }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-12 text-center">
                    <div class="flex flex-col items-center text-bodydark2">
                        <i class="fas fa-shield-alt text-4xl mb-3"></i>
                        <p class="text-xs">Todavía no hay eventos de auditoría registrados para este documento.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>