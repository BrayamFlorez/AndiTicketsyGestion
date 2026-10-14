{{-- resources/views/empresas/partials/_codigo_agente.blade.php --}}
{{-- Uso: @include('empresas.partials._codigo_agente', ['empresa' => $empresa]) --}}

<div x-data="{ copiado: false }"
     class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark p-4">
    <h3 class="mb-2 font-bold text-black dark:text-white uppercase tracking-wider text-xs flex items-center gap-1.5">
        <i class="fas fa-desktop text-primary"></i> Agente de inventario
    </h3>

    @if ($empresa->codigo_agente)
        <p class="text-[11px] text-bodydark2 mb-2">
            Código que se escribe en el agente de Windows para vincular los PCs de esta empresa.
        </p>

        <div class="flex items-center gap-2">
            <code class="flex-1 rounded border border-stroke dark:border-strokedark bg-gray-2 dark:bg-meta-4 px-3 py-1.5 text-sm font-mono font-bold tracking-widest text-black dark:text-white">{{ $empresa->codigo_agente }}</code>
            <button type="button"
                    @click="navigator.clipboard?.writeText(@js($empresa->codigo_agente)); copiado = true; setTimeout(() => copiado = false, 2000)"
                    class="rounded border border-stroke dark:border-strokedark py-1.5 px-3 text-xs text-black dark:text-white hover:bg-gray dark:hover:bg-meta-4">
                <i class="fas" :class="copiado ? 'fa-check text-success' : 'fa-copy'"></i>
            </button>
        </div>
    @else
        <p class="text-[11px] text-bodydark2 mb-2">
            Esta empresa aún no tiene código. Generalo para poder instalar el agente en sus PCs.
        </p>
    @endif

    <form method="POST" action="{{ route('empresas.codigo-agente', $empresa) }}" class="mt-3"
          @if ($empresa->codigo_agente)
              onsubmit="return confirm('¿Regenerar el código? El actual dejará de servir para vincular PCs nuevos. Los agentes ya instalados siguen funcionando.')"
          @endif>
        @csrf
        <button type="submit"
                class="inline-flex items-center gap-1.5 rounded {{ $empresa->codigo_agente ? 'border border-stroke dark:border-strokedark text-black dark:text-white hover:bg-gray dark:hover:bg-meta-4' : 'bg-primary text-white hover:bg-opacity-90' }} py-1.5 px-3 text-[10px] font-bold uppercase tracking-wider transition">
            <i class="fas {{ $empresa->codigo_agente ? 'fa-rotate' : 'fa-key' }}"></i>
            {{ $empresa->codigo_agente ? 'Regenerar código' : 'Generar código' }}
        </button>
    </form>
</div>
