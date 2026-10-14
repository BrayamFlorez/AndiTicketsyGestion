{{-- resources/views/dashboard/partials/_flujo_row.blade.php --}}
<tr class="hover:bg-gray-2 dark:hover:bg-meta-4/50 transition">

    {{-- Col 1: Código interno (si aplica) --}}
    @if ($mostrarCodigoInterno ?? true)
        <td class="py-2 px-3 font-mono text-xs font-bold text-primary whitespace-nowrap">
            {{ $flujo->codigo }}
        </td>
    @endif

    {{-- Col 2: Trámite + Plantilla (Single Line con truncation) --}}
    <td class="py-2 px-3">
        <div class="flex items-center gap-1.5 whitespace-nowrap">
            <a href="{{ route('flujos.instancias.show', $flujo) }}" 
               class="font-bold text-xs text-black dark:text-white hover:text-primary transition truncate max-w-[280px]"
               title="{{ $flujo->titulo }}">
                {{ $flujo->titulo }}
            </a>
            <span class="text-[10px] text-bodydark2 shrink-0 opacity-80">
                · {{ $flujo->documentType?->nombre ?? ($flujo->template?->nombre ?? 'Sin Plantilla') }}
            </span>
        </div>
    </td>

    {{-- Col 3: Etapa Actual + SLA / Rev (Single Line) --}}
    <td class="py-2 px-3">
        <div class="flex items-center gap-2 whitespace-nowrap">
            <span class="text-xs font-semibold text-black dark:text-white truncate max-w-[180px]">
                {{ $flujo->etapaActual?->nombre ?? '—' }}
            </span>
            @if (isset($flujo->currentStep))
                @include('flujos.instancias.partials._sla_badge', ['step' => $flujo->currentStep])
            @else
                <span class="text-[10px] text-bodydark2 shrink-0">(Rev. {{ $flujo->revision }})</span>
            @endif
        </div>
    </td>

    {{-- Col 4: Estado --}}
    <td class="py-2 px-3 text-center whitespace-nowrap">
        @include('flujos.instancias.partials._badge_estado', ['estado' => $flujo->estado])
    </td>

    {{-- Col 5: Fecha --}}
    <td class="py-2 px-3 text-right font-mono text-[11px] text-bodydark2 whitespace-nowrap">
        {{ $flujo->created_at?->format('d/m/Y') }}
    </td>

    {{-- Col 6: Acción Ver --}}
    <td class="py-2 px-3 text-center whitespace-nowrap">
        <a href="{{ route('flujos.instancias.show', $flujo) }}" 
           class="inline-flex h-7 w-7 items-center justify-center rounded bg-primary/10 text-primary hover:bg-primary hover:text-white dark:bg-primary/20 dark:hover:bg-primary transition-all duration-200 mx-auto shadow-sm"
           title="Ver detalle del flujo">
            <i class="fas fa-eye text-xs"></i>
        </a>
    </td>
</tr>