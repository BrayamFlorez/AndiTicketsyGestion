<x-app-layout>
    <x-slot name="title">Ajustes del sistema</x-slot>

    @if (session('exito'))
        <div class="mb-6 rounded-sm border-l-6 border-meta-3 bg-meta-3/10 px-6 py-3 shadow-md">
            <p class="text-xs text-meta-3">{{ session('exito') }}</p>
        </div>
    @endif

    <div class="mb-6">
        <h2 class="text-title-md2 font-bold text-black dark:text-white flex items-center gap-2">
            <i class="fas fa-sliders-h text-primary"></i> Ajustes del sistema
        </h2>
        <p class="text-xs text-bodydark2 mt-1">
            Interruptores globales que afectan el comportamiento de módulos completos.
            Cambiar con precaución.
        </p>
    </div>

    @foreach ($ajustesPorCategoria as $categoria => $ajustes)
        <div class="mb-8">
            <h3 class="text-xs font-bold uppercase tracking-widest text-primary mb-3 flex items-center gap-2">
                <i class="fas fa-folder text-[10px]"></i> {{ ucfirst($categoria) }}
            </h3>

            <div class="space-y-3">
                @foreach ($ajustes as $ajuste)
                    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark p-5">
                        <form action="{{ route('configuracion.ajustes.update', $ajuste) }}" method="POST"
                              class="flex items-start justify-between gap-4">
                            @csrf
                            @method('PATCH')

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-mono text-xs text-primary">{{ $ajuste->clave }}</span>
                                    <span class="text-[9px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded bg-gray-2 text-bodydark2 dark:bg-meta-4">
                                        {{ $ajuste->tipo }}
                                    </span>
                                </div>
                                @if ($ajuste->descripcion)
                                    <p class="text-xs text-bodydark2 leading-relaxed">
                                        {{ $ajuste->descripcion }}
                                    </p>
                                @endif
                            </div>

                            <div class="flex items-center gap-3 shrink-0">
                                @if ($ajuste->tipo === 'bool')
                                    {{-- Toggle switch --}}
                                    <input type="hidden" name="valor" value="0">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="valor" value="1"
                                               @checked($ajuste->valor_tipado)
                                               class="sr-only peer"
                                               onchange="this.form.submit()">
                                        <div class="w-11 h-6 bg-gray rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                    </label>
                                @else
                                    <input type="text" name="valor"
                                           value="{{ $ajuste->valor }}"
                                           class="rounded border border-stroke bg-transparent py-1.5 px-3 text-xs w-64 dark:border-strokedark dark:bg-form-input">
                                    <button type="submit" class="rounded bg-primary py-1.5 px-3 text-xs font-bold text-white hover:bg-opacity-90">
                                        <i class="fas fa-save text-[10px]"></i>
                                    </button>
                                @endif
                            </div>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</x-app-layout>