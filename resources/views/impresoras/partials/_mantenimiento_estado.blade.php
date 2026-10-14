{{-- resources/views/impresoras/partials/_mantenimiento_estado.blade.php --}}
{{-- Estado de firma y accesos (detalle / PDF) de un mantenimiento en el listado. Recibe: $impresora, $m --}}

<div class="flex items-center gap-2 flex-wrap mt-2 pt-2 border-t border-dashed border-stroke dark:border-strokedark">
    @if ($m->estaFinalizado())
        <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[9px] font-bold uppercase border bg-meta-3/10 text-meta-3 border-meta-3/20">
            <i class="fas fa-check-circle text-[8px]"></i> Finalizado
        </span>
    @else
        <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[9px] font-bold uppercase border bg-meta-6/10 text-meta-6 border-meta-6/20">
            <i class="fas fa-signature text-[8px]"></i> Pendiente de firma
        </span>
    @endif

    @php $fotos = $m->evidencias_count ?? $m->evidencias()->count(); @endphp
    @if ($fotos)
        <span class="text-[10px] text-bodydark2"><i class="fas fa-camera text-[9px]"></i> {{ $fotos }} {{ $fotos === 1 ? 'foto' : 'fotos' }}</span>
    @endif

    <div class="ml-auto flex items-center gap-1.5">
        <a href="{{ route('impresoras.mantenimientos.show', [$impresora, $m]) }}"
           class="inline-flex items-center gap-1 rounded border border-stroke dark:border-strokedark py-0.5 px-2 text-[9px] font-bold uppercase text-black dark:text-white hover:bg-gray dark:hover:bg-meta-4 transition">
            @if ($m->estaFinalizado())
                <i class="fas fa-eye text-[8px]"></i> Ver
            @else
                <i class="fas fa-signature text-[8px]"></i> Evidencia y firmas
            @endif
        </a>
        @if ($m->estaFinalizado())
            <a href="{{ route('impresoras.mantenimientos.pdf', [$impresora, $m]) }}" target="_blank" rel="noopener"
               class="inline-flex items-center gap-1 rounded bg-meta-1 py-0.5 px-2 text-[9px] font-bold uppercase text-white hover:bg-opacity-90 transition">
                <i class="fas fa-file-pdf text-[8px]"></i> PDF
            </a>
        @endif
    </div>
</div>
