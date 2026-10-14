{{-- resources/views/proyectos/partials/_timeline.blade.php --}}
{{-- Recibe: $timeline (grupos, conteos), $proyecto, $puede, $modificables --}}
<div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">

    <div class="flex items-center justify-between gap-2 flex-wrap border-b border-stroke py-2.5 px-4 dark:border-strokedark">
        <h3 class="font-bold text-[11px] uppercase tracking-wider text-black dark:text-white flex items-center gap-1.5">
            <i class="fas fa-stream text-primary"></i> Línea de tiempo
        </h3>

        <div class="flex items-center gap-1 flex-wrap">
            @foreach (\App\Services\Proyectos\TimelineService::FILTROS as $clave => $texto)
                <button type="button" @click="filtro = '{{ $clave }}'"
                        class="rounded-full border px-2.5 py-0.5 text-[10px] font-bold transition"
                        :class="filtro === '{{ $clave }}'
                            ? 'bg-primary border-primary text-white'
                            : 'border-stroke dark:border-strokedark text-bodydark2 hover:text-black dark:hover:text-white'">
                    {{ $texto }}
                    <span class="font-mono opacity-70 ml-0.5">{{ $timeline['conteos'][$clave] ?? 0 }}</span>
                </button>
            @endforeach
        </div>
    </div>

    <div class="p-4">
        @if ($timeline['grupos']->isEmpty())
            <div class="text-center py-10 border border-dashed border-stroke dark:border-strokedark rounded-sm">
                <i class="fas fa-stream text-2xl text-bodydark2 mb-2 block"></i>
                <p class="text-xs text-bodydark2">Todavía no hay registros. Empezá registrando una etapa o una solicitud del cliente.</p>
            </div>
        @else
            {{-- Mensaje cuando el filtro no tiene resultados --}}
            <p x-show="filtro !== 'todo' && !({{ Js::from($timeline['conteos']) }}[filtro] > 0)" x-cloak
               class="text-center text-xs text-bodydark2 py-8">No hay registros para este filtro.</p>

            @foreach ($timeline['grupos'] as $grupo)
                <div x-show="filtro === 'todo' || {{ Js::from($grupo['cats']) }}.includes(filtro)">
                    <div class="text-[10px] font-bold uppercase tracking-[.12em] text-bodydark2 mb-2 ml-12">{{ $grupo['label'] }}</div>

                    <div class="relative pl-12 mb-5">
                        {{-- Línea vertical --}}
                        <div class="absolute left-[17px] top-1 bottom-1 w-0.5 bg-stroke dark:bg-strokedark"></div>

                        @foreach ($grupo['items'] as $item)
                            <div x-show="filtro === 'todo' || {{ Js::from($item['cats']) }}.includes(filtro)" class="relative mb-3">
                                @include('proyectos.partials._evento', ['item' => $item])
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
