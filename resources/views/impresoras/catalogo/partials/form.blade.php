@php
    $editing = isset($modelo) && $modelo->exists;
    $action  = $editing ? route('impresoras.catalogo.update', $modelo) : route('impresoras.catalogo.store');

    // Consumibles iniciales (edición o repoblado por old())
    $consumiblesIniciales = old('consumibles', $editing ? ($modelo->consumibles_sugeridos ?? []) : []);
@endphp

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-title-md2 font-bold text-black dark:text-white flex items-center gap-2">
            <i class="fas fa-layer-group text-primary"></i>
            {{ $editing ? "Editar {$modelo->nombre_completo}" : 'Nuevo modelo' }}
        </h2>
        <p class="text-xs text-bodydark2 mt-1">
            Definí la marca, modelo y los consumibles que trae. Se cargarán solos al registrar impresoras de este modelo.
        </p>
    </div>
    <a href="{{ route('impresoras.catalogo.index') }}"
       class="inline-flex items-center gap-1.5 rounded border border-stroke py-2 px-4 text-xs font-medium hover:bg-gray dark:border-strokedark dark:hover:bg-meta-4">
        <i class="fas fa-arrow-left text-[10px]"></i> Cancelar
    </a>
</div>

<form action="{{ $action }}" method="POST"
      class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark"
      x-data="modeloForm({
          consumiblesIniciales: {{ Js::from($consumiblesIniciales) }},
          tipos: {{ Js::from($tiposConsumible) }},
          colores: {{ Js::from($coloresConsumible) }},
      })">
    @csrf
    @if ($editing) @method('PUT') @endif

    @if ($errors->any())
        <div class="mx-6 mt-6 flex w-full border-l-4 border-meta-1 bg-meta-1/10 px-4 py-3 rounded-sm">
            <i class="fas fa-exclamation-triangle text-meta-1 mr-2 mt-0.5"></i>
            <ul class="text-xs text-meta-1 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="p-6 space-y-6">

        {{-- SECCIÓN 1: Datos del modelo --}}
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-primary mb-3 pb-2 border-b border-stroke dark:border-strokedark">
                <i class="fas fa-tag"></i> Datos del modelo
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold uppercase text-black dark:text-white mb-1">
                        Marca <span class="text-meta-1">*</span>
                    </label>
                    <input type="text" name="marca" required maxlength="80"
                           value="{{ old('marca', $editing ? $modelo->marca : '') }}"
                           placeholder="Ej: HP, Epson, Canon, Brother"
                           class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                </div>

                <div>
                    <label class="block text-[10px] font-bold uppercase text-black dark:text-white mb-1">
                        Modelo <span class="text-meta-1">*</span>
                    </label>
                    <input type="text" name="modelo" required maxlength="120"
                           value="{{ old('modelo', $editing ? $modelo->modelo : '') }}"
                           placeholder="Ej: LaserJet Pro M404dn"
                           class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                </div>

                <div>
                    <label class="block text-[10px] font-bold uppercase text-black dark:text-white mb-1">
                        Tecnología <span class="text-meta-1">*</span>
                    </label>
                    <select name="tipo_tecnologia" required
                            class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                        @foreach ($tecnologias as $key => $label)
                            <option value="{{ $key }}" @selected(old('tipo_tecnologia', $editing ? $modelo->tipo_tecnologia : 'laser') === $key)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-6 pb-1">
                    <label class="flex items-center gap-2 cursor-pointer text-xs text-black dark:text-white">
                        <input type="hidden" name="color" value="0">
                        <input type="checkbox" name="color" value="1" class="rounded border-stroke"
                               @checked(old('color', $editing ? $modelo->color : false))>
                        <i class="fas fa-palette text-meta-3 text-[10px]"></i> Color
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer text-xs text-black dark:text-white">
                        <input type="hidden" name="es_multifuncional" value="0">
                        <input type="checkbox" name="es_multifuncional" value="1" class="rounded border-stroke"
                               @checked(old('es_multifuncional', $editing ? $modelo->es_multifuncional : false))>
                        <i class="fas fa-copy text-primary text-[10px]"></i> Multifuncional
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer text-xs text-black dark:text-white">
                        <input type="hidden" name="activo" value="0">
                        <input type="checkbox" name="activo" value="1" class="rounded border-stroke"
                               @checked(old('activo', $editing ? $modelo->activo : true))>
                        <i class="fas fa-eye text-bodydark2 text-[10px]"></i> Activo
                    </label>
                </div>
            </div>
        </div>

        {{-- SECCIÓN 2: Consumibles sugeridos --}}
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-primary mb-3 pb-2 border-b border-stroke dark:border-strokedark">
                <i class="fas fa-fill-drip"></i> Consumibles sugeridos
            </h3>
            <p class="text-[10px] text-bodydark2 mb-3">
                Estos consumibles se crearán automáticamente al registrar una impresora de este modelo.
                Empezá con una plantilla y ajustá lo que haga falta.
            </p>

            {{-- Plantillas rápidas --}}
            <div class="flex flex-wrap gap-2 mb-4">
                <span class="text-[10px] font-bold text-bodydark2 uppercase self-center mr-1">Plantillas:</span>
                <button type="button" @click="aplicarPlantilla('laser_mono')"
                        class="rounded border border-stroke py-1 px-2.5 text-[10px] font-bold uppercase text-black dark:text-white hover:border-primary hover:text-primary dark:border-strokedark transition">
                    <i class="fas fa-print text-[9px]"></i> Láser mono
                </button>
                <button type="button" @click="aplicarPlantilla('laser_color')"
                        class="rounded border border-stroke py-1 px-2.5 text-[10px] font-bold uppercase text-black dark:text-white hover:border-primary hover:text-primary dark:border-strokedark transition">
                    <i class="fas fa-print text-[9px]"></i> Láser color
                </button>
                <button type="button" @click="aplicarPlantilla('inyeccion_mono')"
                        class="rounded border border-stroke py-1 px-2.5 text-[10px] font-bold uppercase text-black dark:text-white hover:border-primary hover:text-primary dark:border-strokedark transition">
                    <i class="fas fa-fill-drip text-[9px]"></i> Inyección mono
                </button>
                <button type="button" @click="aplicarPlantilla('inyeccion_color')"
                        class="rounded border border-stroke py-1 px-2.5 text-[10px] font-bold uppercase text-black dark:text-white hover:border-primary hover:text-primary dark:border-strokedark transition">
                    <i class="fas fa-fill-drip text-[9px]"></i> Inyección color
                </button>
                <button type="button" @click="consumibles = []"
                        class="rounded border border-stroke py-1 px-2.5 text-[10px] font-bold uppercase text-bodydark2 hover:border-meta-1 hover:text-meta-1 dark:border-strokedark transition"
                        title="Vaciar la lista">
                    <i class="fas fa-eraser text-[9px]"></i> Limpiar
                </button>
            </div>

            {{-- Editor de filas --}}
            <div class="space-y-2">
                <template x-for="(cons, i) in consumibles" :key="i">
                    <div class="flex items-center gap-2">
                        <select x-model="cons.tipo"
                                class="flex-1 rounded border border-stroke bg-transparent py-1.5 px-2 text-xs focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                            <template x-for="(label, key) in tipos" :key="key">
                                <option :value="key" x-text="label"></option>
                            </template>
                        </select>

                        <select x-model="cons.color"
                                class="flex-1 rounded border border-stroke bg-transparent py-1.5 px-2 text-xs focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                            <template x-for="(label, key) in colores" :key="key">
                                <option :value="key" x-text="label"></option>
                            </template>
                        </select>

                        <button type="button" @click="quitar(i)"
                                class="text-bodydark2 hover:text-meta-1 transition p-1.5" title="Quitar">
                            <i class="fas fa-times text-xs"></i>
                        </button>

                        {{-- Inputs hidden para el backend --}}
                        <input type="hidden" :name="`consumibles[${i}][tipo]`" :value="cons.tipo">
                        <input type="hidden" :name="`consumibles[${i}][color]`" :value="cons.color">
                    </div>
                </template>

                <p x-show="consumibles.length === 0" x-cloak class="text-[11px] text-bodydark2 italic py-2">
                    <i class="fas fa-info-circle"></i>
                    Sin consumibles definidos. Este modelo no cargará ninguno automáticamente.
                </p>
            </div>

            <button type="button" @click="agregar()"
                    class="mt-3 inline-flex items-center gap-1.5 rounded border border-primary py-1.5 px-3 text-[11px] font-bold uppercase text-primary hover:bg-primary/10 transition">
                <i class="fas fa-plus text-[9px]"></i> Agregar consumible
            </button>
        </div>
    </div>

    <div class="flex items-center justify-end gap-2 border-t border-stroke bg-gray-2 px-6 py-3 dark:border-strokedark dark:bg-meta-4/40">
        <a href="{{ route('impresoras.catalogo.index') }}"
           class="inline-flex items-center rounded border border-stroke py-1.5 px-4 text-xs font-medium hover:bg-gray dark:border-strokedark dark:hover:bg-meta-4">
            Cancelar
        </a>
        <button type="submit"
                class="inline-flex items-center gap-1.5 rounded bg-primary py-1.5 px-5 text-xs font-bold uppercase text-white hover:bg-opacity-90 shadow-sm">
            <i class="fas fa-save text-[10px]"></i>
            {{ $editing ? 'Guardar cambios' : 'Crear modelo' }}
        </button>
    </div>
</form>

@push('scripts')
<script>
    function modeloForm({ consumiblesIniciales, tipos, colores }) {
        return {
            tipos,
            colores,

            // Normalizar los iniciales
            consumibles: (consumiblesIniciales || []).map(c => ({
                tipo:  c.tipo  ?? 'toner',
                color: c.color ?? 'no_aplica',
            })),

            // Plantillas predefinidas
            plantillas: {
                laser_mono: [
                    { tipo: 'toner',  color: 'negro' },
                    { tipo: 'tambor', color: 'no_aplica' },
                ],
                laser_color: [
                    { tipo: 'toner',  color: 'negro' },
                    { tipo: 'toner',  color: 'cyan' },
                    { tipo: 'toner',  color: 'magenta' },
                    { tipo: 'toner',  color: 'amarillo' },
                    { tipo: 'tambor', color: 'no_aplica' },
                ],
                inyeccion_mono: [
                    { tipo: 'cartucho', color: 'negro' },
                ],
                inyeccion_color: [
                    { tipo: 'cartucho', color: 'negro' },
                    { tipo: 'cartucho', color: 'cyan' },
                    { tipo: 'cartucho', color: 'magenta' },
                    { tipo: 'cartucho', color: 'amarillo' },
                ],
            },

            aplicarPlantilla(nombre) {
                // Copia profunda para no compartir referencias
                this.consumibles = (this.plantillas[nombre] || []).map(c => ({ ...c }));
            },

            agregar() {
                this.consumibles.push({ tipo: 'toner', color: 'no_aplica' });
            },

            quitar(index) {
                this.consumibles.splice(index, 1);
            },
        };
    }
</script>
@endpush