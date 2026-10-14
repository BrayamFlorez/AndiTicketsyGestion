{{-- resources/views/empresas/partials/_llave_agente.blade.php --}}
{{-- Uso en la ficha de la empresa: @include('empresas.partials._llave_agente', ['empresa' => $empresa]) --}}

@can('empresas.gestionar')
@php
    $llave = app(\App\Services\Agente\LlaveAgenteService::class)->revelar($empresa);
@endphp

<div x-data="{ visible: false, copiado: false }"
     class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark p-4">

    <h3 class="mb-1 font-bold text-black dark:text-white uppercase tracking-wider text-xs flex items-center gap-1.5">
        <i class="fas fa-key text-primary"></i> Token de instalación del agente
    </h3>

    @if ($llave)
        <p class="text-[11px] text-bodydark2 mb-2">
            Se pide en el instalador del agente para vincular los PCs a esta empresa. Compártalo solo con quien instala.
        </p>

        <div class="flex items-center gap-2">
            <code class="flex-1 min-w-0 truncate rounded border border-stroke dark:border-strokedark bg-gray-2 dark:bg-meta-4 px-3 py-1.5 text-xs font-mono text-black dark:text-white"
                  x-text="visible ? @js($llave) : @js(substr($llave, 0, 8) . str_repeat('•', 24) . substr($llave, -4))"></code>

            <button type="button" @click="visible = !visible" :title="visible ? 'Ocultar' : 'Mostrar'"
                    class="rounded border border-stroke dark:border-strokedark py-1.5 px-2.5 text-xs text-black dark:text-white hover:bg-gray dark:hover:bg-meta-4">
                <i class="fas" :class="visible ? 'fa-eye-slash' : 'fa-eye'"></i>
            </button>
            <button type="button" title="Copiar"
                    @click="navigator.clipboard?.writeText(@js($llave)); copiado = true; setTimeout(() => copiado = false, 2000)"
                    class="rounded border border-stroke dark:border-strokedark py-1.5 px-2.5 text-xs text-black dark:text-white hover:bg-gray dark:hover:bg-meta-4">
                <i class="fas" :class="copiado ? 'fa-check text-success' : 'fa-copy'"></i>
            </button>
        </div>

        <p class="mt-1 text-[10px] text-bodydark2">
            {{-- Parse: el modelo Empresa no castea esta columna a fecha --}}
            Generado el {{ \Illuminate\Support\Carbon::parse($empresa->agente_llave_generada_at)->format('d/m/Y H:i') }}
        </p>

        <div class="mt-3 flex flex-wrap gap-2">
            <form method="POST" action="{{ route('empresas.llave-agente.generar', $empresa) }}"
                  onsubmit="return confirm('¿Regenerar el token? El actual dejará de servir para instalar. Los PCs ya instalados siguen reportando.')">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded border border-stroke dark:border-strokedark py-1 px-3 text-[10px] font-bold uppercase text-black dark:text-white hover:bg-gray dark:hover:bg-meta-4 transition">
                    <i class="fas fa-rotate"></i> Regenerar
                </button>
            </form>

            <form method="POST" action="{{ route('empresas.llave-agente.revocar', $empresa) }}"
                  onsubmit="return confirm('¿Revocar el token? No se podrá instalar el agente en esta empresa hasta generar uno nuevo.')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded border border-meta-1/40 py-1 px-3 text-[10px] font-bold uppercase text-meta-1 hover:bg-meta-1 hover:text-white transition">
                    <i class="fas fa-ban"></i> Revocar
                </button>
            </form>
        </div>
    @else
        <p class="text-[11px] text-bodydark2 mb-3">
            Esta empresa no tiene token. Sin token no se puede instalar el agente en sus PCs.
        </p>

        <form method="POST" action="{{ route('empresas.llave-agente.generar', $empresa) }}">
            @csrf
            <button type="submit"
                    class="inline-flex items-center gap-1.5 rounded bg-primary py-1.5 px-3 text-[10px] font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition">
                <i class="fas fa-key"></i> Generar token
            </button>
        </form>
    @endif
</div>
@endcan
