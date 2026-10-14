{{-- resources/views/impresoras/partials/_consumibles.blade.php --}}
{{-- Recibe: $impresora, $consumiblesActivos, $historialConsumibles, $tiposConsumible, $coloresConsumible, $puede --}}

<div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark"
     x-data="{ modalAgregar: false, editando: null, reemplazando: null, recargando: null }">

    {{-- Encabezado Estándar --}}
    <div class="flex items-center justify-between border-b border-stroke py-2.5 px-4 dark:border-strokedark">
        <h3 class="font-bold text-[11px] uppercase tracking-wider text-black dark:text-white flex items-center gap-1.5">
            <i class="fas fa-fill-drip text-primary"></i>
            Estado de Consumibles / Cartuchos ({{ $consumiblesActivos->count() }})
        </h3>

        @if ($puede['gestionar_consumibles'])
            <button type="button" @click="modalAgregar = true"
                    class="inline-flex items-center gap-1 rounded bg-primary py-1 px-2.5 text-[10px] font-bold uppercase text-white hover:bg-opacity-90 transition shadow-sm cursor-pointer">
                <i class="fas fa-plus text-[8px]"></i> Agregar Cartucho
            </button>
        @endif
    </div>

    <div class="p-3.5">
        @if ($consumiblesActivos->isEmpty())
            <div class="text-center py-6 border border-dashed border-stroke dark:border-strokedark rounded-sm">
                <i class="fas fa-fill-drip text-xl text-bodydark2 mb-1 block"></i>
                <p class="text-xs text-bodydark2">
                    No hay cartuchos ni consumibles registrados para este equipo.
                </p>
            </div>
        @else
            <div class="grid grid-cols-12 gap-3 items-center">

                {{-- ESQUEMA VECTORIAL IMPRESORA --}}
                <div class="col-span-12 md:col-span-4 lg:col-span-3 border-b md:border-b-0 md:border-r border-stroke dark:border-strokedark pb-3 md:pb-0 md:pr-3 flex flex-col items-center justify-center text-center">
                    <div class="relative w-28 h-24 flex items-center justify-center">
                        <svg class="w-full h-full" viewBox="0 0 200 160" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="20" y="70" width="160" height="70" rx="8" class="fill-gray-2 dark:fill-meta-4 stroke-primary" stroke-width="2"/>
                            <rect x="40" y="125" width="120" height="20" rx="2" class="fill-white dark:fill-boxdark stroke-stroke dark:stroke-strokedark" stroke-width="1.5"/>
                            <path d="M40 70 L60 30 L140 30 L160 70 Z" class="fill-gray-3 dark:fill-boxdark stroke-primary" stroke-width="1.5"/>
                            <rect x="65" y="20" width="70" height="25" class="fill-white dark:fill-boxdark stroke-primary" stroke-width="1" stroke-dasharray="2 2"/>
                            <rect x="130" y="80" width="35" height="20" rx="2" class="fill-primary/20 stroke-primary" stroke-width="1"/>
                            <circle cx="138" cy="90" r="2" class="fill-meta-3"/>
                            <line x1="144" y1="87" x2="160" y2="87" class="stroke-primary" stroke-width="1"/>
                            <line x1="144" y1="92" x2="155" y2="92" class="stroke-primary" stroke-width="1"/>
                            <rect x="35" y="80" width="85" height="35" rx="3" class="fill-white dark:fill-boxdark stroke-stroke dark:stroke-strokedark" stroke-width="1"/>
                        </svg>
                    </div>

                    <span class="font-mono text-[9px] text-primary tracking-wider mt-1 uppercase block font-bold">
                        {{ $impresora->modelo?->tipo_tecnologia === 'laser' ? 'Tecnología Láser' : 'Inyección de Tinta' }}
                    </span>
                </div>

                {{-- NIVELES DE CONSUMIBLES / CARTUCHOS --}}
                <div class="col-span-12 md:col-span-8 lg:col-span-9">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                        @foreach ($consumiblesActivos as $c)
                            @php
                                $hex = $c->color_hex ?: '#3c50e0';
                                $nivel = $c->nivel_estimado ?? 0;
                                $cfgNivel = match ($c->estado_nivel) {
                                    'agotado', 'critico' => 'bg-meta-1 text-meta-1',
                                    'bajo'               => 'bg-meta-6 text-meta-6',
                                    'normal'             => 'bg-meta-3 text-meta-3',
                                    default              => 'bg-bodydark2 text-bodydark2',
                                };
                            @endphp

                            <div class="bg-gray-2 dark:bg-meta-4/20 border border-stroke dark:border-strokedark rounded-sm p-2 flex flex-col justify-between relative">

                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-[10px] font-bold truncate text-black dark:text-white max-w-[85px]" title="{{ $c->descripcion_corta }}">
                                        {{ $c->descripcion_corta }}
                                    </span>

                                    @if ($puede['gestionar_consumibles'])
                                        <div class="relative" x-data="{ menu: false }">
                                            <button type="button" @click="menu = !menu" class="text-bodydark2 hover:text-primary transition px-1">
                                                <i class="fas fa-ellipsis-v text-[10px]"></i>
                                            </button>
                                            <div x-show="menu" x-cloak @click.outside="menu = false"
                                                 class="absolute right-0 mt-1 z-30 rounded border border-stroke bg-white dark:bg-boxdark dark:border-strokedark shadow-lg py-1 w-36 text-[9px]">
                                                <button type="button" @click="editando = {{ $c->id }}; menu = false"
                                                        class="w-full text-left px-2.5 py-1 hover:bg-gray-2 dark:hover:bg-meta-4 text-black dark:text-white font-bold uppercase">
                                                    <i class="fas fa-pen text-bodydark2 mr-1"></i> Editar
                                                </button>
                                                <button type="button" @click="recargando = {{ $c->id }}; menu = false"
                                                        class="w-full text-left px-2.5 py-1 hover:bg-gray-2 dark:hover:bg-meta-4 text-meta-3 font-bold uppercase">
                                                    <i class="fas fa-fill-drip mr-1"></i> Recargar porción
                                                </button>
                                                <button type="button" @click="reemplazando = {{ $c->id }}; menu = false"
                                                        class="w-full text-left px-2.5 py-1 hover:bg-gray-2 dark:hover:bg-meta-4 text-primary font-bold uppercase">
                                                    <i class="fas fa-sync mr-1"></i> Reemplazar
                                                </button>
                                                <form method="POST" action="{{ route('impresoras.consumibles.destroy', [$impresora, $c]) }}"
                                                      onsubmit="return confirm('¿Eliminar consumible?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="w-full text-left px-2.5 py-1 hover:bg-gray-2 dark:hover:bg-meta-4 text-meta-1 font-bold uppercase">
                                                        <i class="fas fa-trash mr-1"></i> Eliminar
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 my-1 bg-white dark:bg-boxdark p-1.5 rounded border border-stroke dark:border-strokedark">
                                    <div class="w-3.5 h-14 bg-gray-2 dark:bg-meta-4 rounded-full border border-stroke dark:border-strokedark relative overflow-hidden shrink-0 flex flex-col justify-end p-0.5">
                                        <div class="w-full rounded-b-full transition-all duration-500"
                                             style="height: {{ $nivel }}%; background-color: {{ $hex }};"></div>
                                    </div>

                                    <div class="flex-1 min-w-0 font-mono">
                                        <div class="text-[12px] font-bold text-black dark:text-white leading-none">
                                            {{ $nivel }}<span class="text-[9px] text-bodydark2">%</span>
                                        </div>

                                        <span class="inline-block mt-1 text-[8px] font-bold uppercase px-1 py-0.2 rounded bg-opacity-10 {{ $cfgNivel }}">
                                            {{ $c->estado_nivel }}
                                        </span>

                                        <div class="text-[8px] text-bodydark2 mt-1 truncate">
                                            @if ($c->dias_restantes !== null)
                                                {{ $c->dias_restantes > 0 ? "~{$c->dias_restantes}d rest." : 'Agotado' }}
                                            @else
                                                S/I
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                @if ($c->ultimaRecarga)
                                    <div class="text-[8px] font-mono text-meta-3 mb-0.5 truncate" title="Última recarga parcial: {{ $c->ultimaRecarga->nivel_antes + 0 }}% → {{ $c->ultimaRecarga->nivel_despues + 0 }}%">
                                        <i class="fas fa-fill-drip"></i> +{{ $c->ultimaRecarga->porcion_recargada + 0 }}% · {{ $c->ultimaRecarga->created_at->format('d/m') }}
                                    </div>
                                @endif

                                <div class="text-[8px] font-mono text-bodydark2 pt-1 border-t border-stroke dark:border-strokedark flex justify-between">
                                    <span>Est: {{ $c->dias_estimados }}d</span>
                                    <span>{{ $c->label_color }}</span>
                                </div>

                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        @endif
    </div>

    {{-- Historial de recargas parciales --}}
    @php $recargasParciales = $impresora->recargasConsumibles()->with(['consumible', 'registrador:id,name'])->latest()->limit(15)->get(); @endphp
    @if ($recargasParciales->isNotEmpty())
        <div class="border-t border-stroke dark:border-strokedark" x-data="{ abierto: false }">
            <button type="button" @click="abierto = !abierto"
                    class="w-full flex items-center justify-between px-4 py-1.5 text-[10px] font-bold text-bodydark2 hover:text-primary transition bg-gray-2/50 dark:bg-meta-4/20">
                <span><i class="fas" :class="abierto ? 'fa-chevron-down' : 'fa-chevron-right'"></i> Recargas parciales ({{ $recargasParciales->count() }})</span>
            </button>

            <div x-show="abierto" x-cloak class="p-2.5 space-y-1 bg-white dark:bg-boxdark">
                @foreach ($recargasParciales as $r)
                    <div class="rounded border border-stroke bg-gray-2 dark:border-strokedark dark:bg-meta-4/30 p-1.5 text-[10px] font-mono">
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-1.5 truncate">
                                <span class="w-2 h-2 rounded-full shrink-0" style="background: {{ $r->consumible?->color_hex }};"></span>
                                <span class="font-bold text-black dark:text-white truncate">{{ $r->consumible?->descripcion_corta }}</span>
                                <span class="text-meta-3 font-bold">{{ $r->nivel_antes + 0 }}% → {{ $r->nivel_despues + 0 }}%</span>
                            </div>
                            <span class="text-bodydark2 shrink-0">{{ $r->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="text-[9px] text-bodydark2 mt-0.5">
                            Estimado {{ round($r->nivel_estimado_sistema) }}%
                            · desvío {{ $r->desviacion_porcentaje > 0 ? '+' : '' }}{{ round($r->desviacion_porcentaje) }}% (≈{{ abs($r->desviacion_dias) }}d)
                            · duración {{ $r->recalibrado ? $r->dias_estimados_anteriores . '→' . $r->dias_estimados_nuevos . 'd' : $r->dias_estimados_nuevos . 'd sin cambio' }}
                            @if ($r->registrador) · {{ $r->registrador->name }} @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Historial Desplegable de Reemplazos --}}
    @if ($historialConsumibles->isNotEmpty())
        <div class="border-t border-stroke dark:border-strokedark" x-data="{ abierto: false }">
            <button type="button" @click="abierto = !abierto"
                    class="w-full flex items-center justify-between px-4 py-1.5 text-[10px] font-bold text-bodydark2 hover:text-primary transition bg-gray-2/50 dark:bg-meta-4/20">
                <span><i class="fas" :class="abierto ? 'fa-chevron-down' : 'fa-chevron-right'"></i> Historial de reemplazos ({{ $historialConsumibles->count() }})</span>
            </button>

            <div x-show="abierto" x-cloak class="p-2.5 space-y-1 bg-white dark:bg-boxdark">
                @foreach ($historialConsumibles as $h)
                    <div class="rounded border border-stroke bg-gray-2 dark:border-strokedark dark:bg-meta-4/30 p-1.5 text-[10px] font-mono flex items-center justify-between gap-2">
                        <div class="flex items-center gap-1.5 truncate">
                            <span class="w-2 h-2 rounded-full shrink-0" style="background: {{ $h->color_hex }};"></span>
                            <span class="font-bold text-black dark:text-white truncate">{{ $h->descripcion_corta }}</span>
                        </div>
                        <span class="text-bodydark2 shrink-0">
                            {{ $h->duracion_real_dias ?? '—' }}d reales · {{ $h->fecha_reemplazo?->format('d/m/Y') }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ── MODALES TELEPORTADOS AL BODY ── --}}
    @if ($puede['gestionar_consumibles'])

        {{-- Modal Agregar --}}
        <template x-teleport="body">
            <div x-show="modalAgregar" x-cloak
                 @keydown.escape.window="modalAgregar = false"
                 class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
                <div @click.outside="modalAgregar = false"
                     class="w-full max-w-md rounded-sm border border-stroke bg-white shadow-2xl dark:border-strokedark dark:bg-boxdark">
                    <div class="flex items-center justify-between px-4 py-2.5 border-b border-stroke dark:border-strokedark">
                        <h4 class="font-bold text-xs text-black dark:text-white uppercase tracking-wider">
                            <i class="fas fa-plus text-primary mr-1"></i> Agregar Cartucho / Consumible
                        </h4>
                        <button type="button" @click="modalAgregar = false" class="text-bodydark2 hover:text-black dark:hover:text-white">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('impresoras.consumibles.store', $impresora) }}" class="p-4 space-y-3">
                        @csrf

                        @if ($errors->any())
                            <div class="rounded border border-meta-1 bg-meta-1/10 p-2">
                                <ul class="text-[10px] text-meta-1 space-y-0.5 pl-4 list-disc">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[10px] font-bold uppercase text-black dark:text-white mb-1">Tipo *</label>
                                <select name="tipo" required class="w-full rounded border border-stroke bg-transparent py-1.5 px-2 text-xs focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                                    @foreach ($tiposConsumible as $key => $label)
                                        <option value="{{ $key }}" class="bg-[#1c2434] text-white">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold uppercase text-black dark:text-white mb-1">Color *</label>
                                <select name="color" required class="w-full rounded border border-stroke bg-transparent py-1.5 px-2 text-xs focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                                    @foreach ($coloresConsumible as $key => $label)
                                        <option value="{{ $key }}" @selected($key === 'no_aplica') class="bg-[#1c2434] text-white">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold uppercase text-black dark:text-white mb-1">Etiqueta Libre</label>
                            <input type="text" name="etiqueta" maxlength="80" placeholder="Ej: HP 83A" class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[10px] font-bold uppercase text-black dark:text-white mb-1">Días Estimados *</label>
                                <input type="number" name="dias_estimados" required min="1" max="3650" value="30" class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold uppercase text-black dark:text-white mb-1">Instalación</label>
                                <input type="date" name="fecha_instalacion" value="{{ now()->format('Y-m-d') }}" max="{{ now()->format('Y-m-d') }}" class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                            </div>
                        </div>

                        <div class="flex justify-end gap-2 pt-2 border-t border-stroke dark:border-strokedark">
                            <button type="button" @click="modalAgregar = false" class="rounded border border-stroke py-1 px-3 text-[10px] font-bold uppercase hover:bg-gray dark:border-strokedark dark:hover:bg-meta-4">
                                Cancelar
                            </button>
                            <button type="submit" class="rounded bg-primary py-1 px-3 text-[10px] font-bold uppercase text-white hover:bg-opacity-90">
                                Agregar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        {{-- Modales Editar, Recargar porción y Reemplazar por Consumible --}}
        @foreach ($consumiblesActivos as $c)
            @include('impresoras.partials._modal_recarga')

            <template x-teleport="body">
                <div x-show="editando === {{ $c->id }}" x-cloak
                     @keydown.escape.window="editando = null"
                     class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
                    <div @click.outside="editando = null" class="w-full max-w-md rounded-sm border border-stroke bg-white shadow-2xl dark:border-strokedark dark:bg-boxdark">
                        <div class="flex items-center justify-between px-4 py-2.5 border-b border-stroke dark:border-strokedark">
                            <h4 class="font-bold text-xs text-black dark:text-white uppercase tracking-wider">
                                Editar {{ $c->descripcion_corta }}
                            </h4>
                            <button type="button" @click="editando = null" class="text-bodydark2 hover:text-black dark:hover:text-white"><i class="fas fa-times text-xs"></i></button>
                        </div>

                        <form method="POST" action="{{ route('impresoras.consumibles.update', [$impresora, $c]) }}" class="p-4 space-y-3">
                            @csrf
                            @method('PUT')
                            <div>
                                <label class="block text-[10px] font-bold uppercase text-black dark:text-white mb-1">Etiqueta Libre</label>
                                <input type="text" name="etiqueta" maxlength="80" value="{{ $c->etiqueta }}" class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-[10px] font-bold uppercase text-black dark:text-white mb-1">Días Estimados *</label>
                                    <input type="number" name="dias_estimados" required min="1" max="3650" value="{{ $c->dias_estimados }}" class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase text-black dark:text-white mb-1">Instalación</label>
                                    <input type="date" name="fecha_instalacion" value="{{ $c->fecha_instalacion?->format('Y-m-d') }}" max="{{ now()->format('Y-m-d') }}" class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                                </div>
                            </div>
                            <div class="flex justify-end gap-2 pt-2 border-t border-stroke dark:border-strokedark">
                                <button type="button" @click="editando = null" class="rounded border border-stroke py-1 px-3 text-[10px] font-bold uppercase hover:bg-gray dark:border-strokedark dark:hover:bg-meta-4">Cancelar</button>
                                <button type="submit" class="rounded bg-primary py-1 px-3 text-[10px] font-bold uppercase text-white hover:bg-opacity-90">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>

            <template x-teleport="body">
                <div x-show="reemplazando === {{ $c->id }}" x-cloak
                     @keydown.escape.window="reemplazando = null"
                     class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
                    <div @click.outside="reemplazando = null" class="w-full max-w-md rounded-sm border border-stroke bg-white shadow-2xl dark:border-strokedark dark:bg-boxdark">
                        <div class="flex items-center justify-between px-4 py-2.5 border-b border-stroke dark:border-strokedark">
                            <h4 class="font-bold text-xs text-black dark:text-white uppercase tracking-wider">
                                <i class="fas fa-sync text-primary mr-1"></i> Reemplazar Consumible
                            </h4>
                            <button type="button" @click="reemplazando = null" class="text-bodydark2 hover:text-black dark:hover:text-white"><i class="fas fa-times text-xs"></i></button>
                        </div>

                        <form method="POST" action="{{ route('impresoras.consumibles.reemplazar', [$impresora, $c]) }}" class="p-4 space-y-3" x-data="{ recalibrar: false }">
                            @csrf
                            @php $duracionActual = $c->fecha_instalacion ? (int) $c->fecha_instalacion->diffInDays(now()) : null; @endphp
                            <div>
                                <label class="block text-[10px] font-bold uppercase text-black dark:text-white mb-1">Fecha Reemplazo *</label>
                                <input type="date" name="fecha_reemplazo" required value="{{ now()->format('Y-m-d') }}" max="{{ now()->format('Y-m-d') }}" class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                            </div>
                            @if ($duracionActual !== null)
                                <div class="rounded border border-primary/30 bg-primary/5 p-2">
                                    <label class="flex items-center gap-2 cursor-pointer text-xs text-black dark:text-white">
                                        <input type="checkbox" name="recalibrar" value="1" x-model="recalibrar" class="rounded border-stroke"> Recalibrar a {{ $duracionActual }}d reales
                                    </label>
                                </div>
                            @endif
                            <div>
                                <label class="block text-[10px] font-bold uppercase text-black dark:text-white mb-1">Días Estimados Nuevo *</label>
                                <input type="number" name="dias_estimados_nuevo" required min="1" max="3650" :value="recalibrar ? {{ $duracionActual ?? $c->dias_estimados }} : {{ $c->dias_estimados }}" :readonly="recalibrar" class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white read-only:opacity-50">
                            </div>
                            <div class="flex justify-end gap-2 pt-2 border-t border-stroke dark:border-strokedark">
                                <button type="button" @click="reemplazando = null" class="rounded border border-stroke py-1 px-3 text-[10px] font-bold uppercase hover:bg-gray dark:border-strokedark dark:hover:bg-meta-4">Cancelar</button>
                                <button type="submit" class="rounded bg-primary py-1 px-3 text-[10px] font-bold uppercase text-white hover:bg-opacity-90">Confirmar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>
        @endforeach
    @endif
</div>