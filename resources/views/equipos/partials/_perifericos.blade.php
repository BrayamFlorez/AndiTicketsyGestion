{{-- resources/views/equipos/partials/_perifericos.blade.php --}}
{{-- Periféricos del PC (pantalla, teclado, mouse...). Se revisan en cada mantenimiento. Recibe: $equipo --}}
@use('App\Models\EquipoPeriferico')

@php
    $puedeEditar = auth()->user()->can('equipos.editar') && $equipo->puedeSerGestionadoPor(auth()->user());
    $reabrir = old('_periferico');
    $input = 'w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white';
    $vacio = ['id' => null, 'tipo' => 'monitor', 'marca' => '', 'modelo' => '', 'numero_serie' => '', 'notas' => ''];
    $inicial = $reabrir ? array_merge($vacio, ['id' => $reabrir === 'nuevo' ? null : (int) $reabrir], collect(old())->only(['tipo', 'marca', 'modelo', 'numero_serie', 'notas'])->all()) : $vacio;
@endphp

<div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark"
     x-data="{ abierto: {{ $reabrir ? 'true' : 'false' }}, p: {{ Js::from($inicial) }}, vacio: {{ Js::from($vacio) }},
               editar(datos) { this.p = { ...datos }; this.abierto = true }, nuevo() { this.p = { ...this.vacio }; this.abierto = true } }">
    <div class="flex items-center justify-between border-b border-stroke py-2.5 px-4 dark:border-strokedark">
        <h3 class="font-bold text-[11px] uppercase tracking-wider text-black dark:text-white flex items-center gap-1.5">
            <i class="fas fa-keyboard text-primary"></i> Periféricos ({{ $equipo->perifericos->count() }})
        </h3>
        @if ($puedeEditar)
            <button type="button" @click="nuevo()" class="text-[10px] font-bold uppercase text-primary hover:underline flex items-center gap-1">
                <i class="fas fa-plus text-[8px]"></i> Agregar
            </button>
        @endif
    </div>

    <div class="p-3">
        @if ($equipo->perifericos->isEmpty())
            <p class="text-xs text-bodydark2 text-center py-2">Sin periféricos vinculados. Agregue pantalla, teclado, mouse… para incluirlos en los mantenimientos.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                @foreach ($equipo->perifericos as $per)
                    <div class="group flex items-center gap-2 rounded border border-stroke bg-gray-2 p-2 dark:border-strokedark dark:bg-meta-4/20">
                        <div class="flex h-7 w-7 items-center justify-center rounded bg-primary/10 text-primary shrink-0">
                            <i class="fas {{ $per->icono }} text-xs"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="text-[8px] font-bold text-bodydark2 uppercase block truncate">{{ $per->tipo === 'otro' && $per->notas ? $per->notas : $per->label_tipo }}</span>
                            <span class="text-[11px] font-bold text-black dark:text-white truncate block">{{ trim($per->marca . ' ' . $per->modelo) ?: 'Sin marca / modelo' }}</span>
                            @if ($per->numero_serie)<span class="text-[9px] font-mono text-bodydark2 truncate block">S/N {{ $per->numero_serie }}</span>@endif
                        </div>
                        @if ($puedeEditar)
                            <div class="flex items-center gap-1 shrink-0 opacity-60 group-hover:opacity-100 transition">
                                <button type="button" title="Editar" @click="editar({{ Js::from($per->only(['id', 'tipo', 'marca', 'modelo', 'numero_serie', 'notas'])) }})"
                                        class="flex h-6 w-6 items-center justify-center rounded border border-stroke text-meta-6 hover:border-meta-6 dark:border-strokedark"><i class="fas fa-pen text-[9px]"></i></button>
                                <form method="POST" action="{{ route('equipos.perifericos.destroy', [$equipo, $per]) }}" onsubmit="return confirm('¿Retirar este periférico del equipo?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" title="Retirar" class="flex h-6 w-6 items-center justify-center rounded border border-stroke text-meta-1 hover:border-meta-1 dark:border-strokedark"><i class="fas fa-trash text-[9px]"></i></button>
                                </form>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @if ($puedeEditar)
        <template x-teleport="body">
            <div x-show="abierto" x-cloak @keydown.escape.window="abierto = false" class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
                <div @click.outside="abierto = false" class="w-full max-w-md rounded-sm border border-stroke bg-white shadow-2xl dark:border-strokedark dark:bg-boxdark">
                    <div class="flex items-center justify-between px-4 py-2.5 border-b border-stroke dark:border-strokedark">
                        <h4 class="font-bold text-xs text-black dark:text-white uppercase tracking-wider" x-text="p.id ? 'Editar periférico' : 'Agregar periférico'"></h4>
                        <button type="button" @click="abierto = false" class="text-bodydark2 hover:text-black dark:hover:text-white"><i class="fas fa-times text-xs"></i></button>
                    </div>
                    <form method="POST" class="p-4 space-y-3"
                          :action="p.id ? '{{ route('equipos.perifericos.update', [$equipo, '__ID__']) }}'.replace('__ID__', p.id) : '{{ route('equipos.perifericos.store', $equipo) }}'">
                        @csrf
                        <template x-if="p.id"><input type="hidden" name="_method" value="PUT"></template>
                        <input type="hidden" name="_periferico" :value="p.id ?? 'nuevo'">

                        @if ($reabrir && $errors->any())
                            <div class="rounded border border-meta-1 bg-meta-1/10 p-2 text-[10px] text-meta-1">@foreach ($errors->all() as $e)<p>{{ $e }}</p>@endforeach</div>
                        @endif

                        <div>
                            <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Tipo *</label>
                            <select name="tipo" x-model="p.tipo" required class="{{ $input }}">
                                @foreach (EquipoPeriferico::TIPOS as $clave => [$etiqueta])
                                    <option value="{{ $clave }}" class="bg-[#1c2434] text-white">{{ $etiqueta }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div x-show="p.tipo === 'otro'" x-cloak>
                            <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">¿Qué periférico es? *</label>
                            <input type="text" name="notas" x-model="p.notas" maxlength="255" placeholder="Ej: Pad de firmas" class="{{ $input }}">
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Marca</label>
                                <input type="text" name="marca" x-model="p.marca" maxlength="100" placeholder="Ej: HP" class="{{ $input }}">
                            </div>
                            <div>
                                <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Modelo</label>
                                <input type="text" name="modelo" x-model="p.modelo" maxlength="100" placeholder="Ej: P24 G4" class="{{ $input }}">
                            </div>
                        </div>
                        <div>
                            <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Serial (opcional)</label>
                            <input type="text" name="numero_serie" x-model="p.numero_serie" maxlength="100" class="{{ $input }} font-mono">
                        </div>
                        <template x-if="p.tipo !== 'otro'"><input type="hidden" name="notas" value=""></template>

                        <div class="flex justify-end gap-2 pt-2 border-t border-stroke dark:border-strokedark">
                            <button type="button" @click="abierto = false" class="rounded border border-stroke py-1 px-3 text-[10px] font-bold uppercase hover:bg-gray dark:border-strokedark dark:hover:bg-meta-4 dark:text-white">Cancelar</button>
                            <button type="submit" class="rounded bg-primary py-1 px-3 text-[10px] font-bold uppercase text-white hover:bg-opacity-90"><i class="fas fa-check mr-0.5"></i> Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    @endif
</div>
