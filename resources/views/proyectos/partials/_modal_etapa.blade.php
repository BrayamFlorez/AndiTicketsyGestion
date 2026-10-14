{{-- resources/views/proyectos/partials/_modal_etapa.blade.php — Registrar / editar etapa --}}
{{-- Recibe: $proyecto, $solicitudesAbiertas --}}
@php
    $input = 'w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white';
    $label = 'block text-[10px] font-bold uppercase text-black dark:text-white mb-1';
@endphp

<div x-show="modalEtapa" style="display: none;" x-transition.opacity
     class="fixed inset-0 z-[9999] flex items-start justify-center overflow-y-auto bg-black/70 backdrop-blur-sm px-4 py-8"
     @keydown.escape.window="modalEtapa = false">

    <form method="POST" :action="etapa.accion" enctype="multipart/form-data"
          @click.outside="modalEtapa = false"
          class="w-full max-w-2xl rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
        @csrf
        <input type="hidden" name="_method" :value="etapa.editando ? 'PUT' : 'POST'">
        {{-- Para reabrir el modal si la validación falla --}}
        <input type="hidden" name="_modal" value="etapa">
        <input type="hidden" name="_accion" :value="etapa.accion">
        <input type="hidden" name="_editando" :value="etapa.editando ? 1 : 0">
        <input type="hidden" name="tipo" :value="etapa.tipo">

        <div class="flex items-center justify-between border-b border-stroke dark:border-strokedark px-4 py-3">
            <h3 class="text-sm font-bold text-black dark:text-white flex items-center gap-2">
                <i class="fas" :class="etapa.editando ? 'fa-pen text-primary' : 'fa-plus text-primary'"></i>
                <span x-text="etapa.editando ? 'Editar etapa' : 'Registrar etapa'"></span>
            </h3>
            <button type="button" @click="modalEtapa = false" class="text-bodydark2 hover:text-black dark:hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="p-4 space-y-3">
            {{-- Tipo --}}
            <div>
                <span class="{{ $label }}">Tipo de etapa</span>
                <div class="grid grid-cols-3 sm:grid-cols-4 gap-1.5">
                    @foreach (\App\Models\ProyectoEtapa::TIPOS as $clave => $cfg)
                        <button type="button" @click="cambiarTipo('{{ $clave }}')"
                                class="rounded border px-1 py-1.5 text-center text-[10px] font-semibold transition"
                                :class="etapa.tipo === '{{ $clave }}'
                                    ? 'border-primary bg-primary/10 text-black dark:text-white'
                                    : 'border-stroke dark:border-strokedark text-bodydark2 hover:border-primary'">
                            <i class="{{ $cfg['icono'] }} block text-sm mb-0.5" style="color: {{ $cfg['color'] }}"></i>
                            {{ $cfg['label'] }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="{{ $label }}">Fecha del hecho <span class="text-meta-1">*</span></label>
                    <input type="date" name="fecha" x-model="etapa.fecha" required class="{{ $input }}">
                </div>
                <div>
                    <label class="{{ $label }}">Estado</label>
                    <select name="estado" x-model="etapa.estado" class="{{ $input }}">
                        @foreach (\App\Models\ProyectoEtapa::ESTADOS as $clave => $texto)
                            <option value="{{ $clave }}">{{ $texto }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="{{ $label }}">Título (opcional)</label>
                    <input type="text" name="titulo" x-model="etapa.titulo" maxlength="150" :placeholder="tituloPorDefecto()" class="{{ $input }}">
                </div>
            </div>

            {{-- Campos propios de cada tipo --}}
            <div x-show="etapa.tipo === 'cotizacion'" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="{{ $label }}">Valor cotizado</label>
                    <input type="number" name="monto" x-model="etapa.monto" min="0" step="1" placeholder="8500000" class="{{ $input }} font-mono" :disabled="etapa.tipo !== 'cotizacion'">
                </div>
                <div>
                    <label class="{{ $label }}">Válida hasta</label>
                    <input type="date" name="fecha_validez" x-model="etapa.fecha_validez" class="{{ $input }}" :disabled="etapa.tipo !== 'cotizacion'">
                </div>
            </div>

            <div x-show="etapa.tipo === 'pago'" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="{{ $label }}">Monto pagado</label>
                    <input type="number" name="monto" x-model="etapa.monto" min="0" step="1" placeholder="4250000" class="{{ $input }} font-mono" :disabled="etapa.tipo !== 'pago'">
                </div>
                <div>
                    <label class="{{ $label }}">Medio de pago</label>
                    <select name="medio_pago" x-model="etapa.medio_pago" class="{{ $input }}" :disabled="etapa.tipo !== 'pago'">
                        @foreach (\App\Models\ProyectoEtapa::MEDIOS_PAGO as $clave => $texto)
                            <option value="{{ $clave }}">{{ $texto }}</option>
                        @endforeach
                    </select>
                </div>
                <p class="sm:col-span-2 text-[10px] text-bodydark2 -mt-1">
                    <i class="fas fa-circle-info"></i> Solo los pagos en estado <b>Completada</b> suman al valor pagado. Usá <b>Planeada</b> para pagos programados.
                </p>
            </div>

            <div x-show="etapa.tipo === 'entrega'" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="{{ $label }}">Versión</label>
                    <input type="text" name="version" x-model="etapa.version" maxlength="30" class="{{ $input }}" :disabled="etapa.tipo !== 'entrega'">
                </div>
                <div class="sm:col-span-2">
                    <label class="{{ $label }}">Enlace (opcional)</label>
                    <input type="url" name="enlace" x-model="etapa.enlace" maxlength="500" placeholder="https://..." class="{{ $input }}" :disabled="etapa.tipo !== 'entrega'">
                </div>
            </div>

            <div x-show="etapa.tipo === 'correccion'">
                <label class="{{ $label }}">Ronda</label>
                <input type="number" name="ronda" x-model="etapa.ronda" min="1" max="999" class="{{ $input }} max-w-[120px]" :disabled="etapa.tipo !== 'correccion'">
            </div>

            <div x-show="etapa.tipo === 'desarrollo'" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="{{ $label }}">Avance (%)</label>
                    <input type="number" name="avance" x-model="etapa.avance" min="0" max="100" class="{{ $input }}" :disabled="etapa.tipo !== 'desarrollo'">
                </div>
                <div class="sm:col-span-2">
                    <label class="{{ $label }}">Enlace (opcional)</label>
                    <input type="url" name="enlace" x-model="etapa.enlace" maxlength="500" placeholder="https://..." class="{{ $input }}" :disabled="etapa.tipo !== 'desarrollo'">
                </div>
            </div>

            <div x-show="['diseno', 'pruebas'].includes(etapa.tipo)">
                <label class="{{ $label }}">Enlace (opcional)</label>
                <input type="url" name="enlace" x-model="etapa.enlace" maxlength="500" placeholder="https://figma.com/..." class="{{ $input }}" :disabled="!['diseno', 'pruebas'].includes(etapa.tipo)">
            </div>

            <div>
                <label class="{{ $label }}">Descripción <span class="text-meta-1">*</span></label>
                <textarea name="descripcion" x-model="etapa.descripcion" rows="3" required maxlength="5000"
                          placeholder="¿Qué pasó en esta etapa?" class="{{ $input }}"></textarea>
            </div>

            {{-- Solicitudes que atiende (solo al registrar) --}}
            @if ($solicitudesAbiertas->isNotEmpty())
                <div x-show="!etapa.editando && tiposQueAtienden.includes(etapa.tipo)">
                    <span class="{{ $label }}">¿Atiende alguna solicitud del cliente?</span>
                    <div class="rounded border border-stroke dark:border-strokedark p-2 space-y-1 max-h-40 overflow-y-auto">
                        @foreach ($solicitudesAbiertas as $s)
                            <label class="flex items-center gap-2 text-xs text-black dark:text-white cursor-pointer">
                                <input type="checkbox" name="atiende[]" value="{{ $s->id }}" x-model="etapa.atiende"
                                       :disabled="etapa.editando || !tiposQueAtienden.includes(etapa.tipo)" class="rounded border-stroke">
                                <i class="fas fa-flag text-[9px] {{ $s->prioridad_config['clase'] }}"></i>
                                <span class="truncate">{{ $s->titulo }}</span>
                                <span class="text-[10px] text-bodydark2 shrink-0">· {{ $s->fecha_solicitud->format('d/m/Y') }}</span>
                            </label>
                        @endforeach
                    </div>
                    <p class="text-[10px] text-bodydark2 mt-1">Las marcadas pasan a <b>Atendida</b> y quedan vinculadas a esta etapa.</p>
                </div>
            @endif

            {{-- Adjuntos --}}
            <div>
                <span class="{{ $label }}">
                    <span x-text="etapa.editando ? 'Agregar adjuntos' : 'Adjuntos'"></span>
                </span>
                <label class="block rounded border-[1.5px] border-dashed border-stroke dark:border-strokedark px-3 py-3 text-center text-[11px] text-bodydark2 cursor-pointer hover:border-primary transition">
                    <i class="fas fa-cloud-arrow-up text-primary text-lg block mb-0.5"></i>
                    Imagen, audio, video o documento · máx. 20 MB por archivo
                    <input type="file" name="archivos[]" multiple class="hidden" x-ref="archivosEtapa"
                           accept="image/*,audio/*,video/*,.pdf,.doc,.docx,.xls,.xlsx,.csv,.ppt,.pptx,.txt,.odt,.ods,.zip,.rar,.opus,.ogg,.heic"
                           @change="procesarArchivos($refs.archivosEtapa, 'etapa')">
                </label>
                <div class="mt-1.5 space-y-1">
                    <template x-for="(f, i) in archivos.etapa" :key="f.nombre + i">
                        <div class="flex items-center gap-2 rounded border border-stroke dark:border-strokedark px-2 py-1 text-[11px] text-black dark:text-white">
                            <i class="fas text-bodydark2" :class="f.icono"></i>
                            <span class="truncate" x-text="f.nombre"></span>
                            <span class="ml-auto font-mono text-[10px] text-bodydark2" x-text="f.peso"></span>
                            <button type="button" @click="quitarArchivo($refs.archivosEtapa, 'etapa', i)" class="text-bodydark2 hover:text-meta-1">
                                <i class="fas fa-times text-[10px]"></i>
                            </button>
                        </div>
                    </template>
                </div>
                <p x-show="avisoArchivos.etapa" x-text="avisoArchivos.etapa" class="text-[10px] text-meta-1 mt-1"></p>
            </div>
        </div>

        <div class="flex justify-end gap-2 border-t border-stroke dark:border-strokedark px-4 py-3">
            <button type="button" @click="modalEtapa = false"
                    class="rounded border border-stroke py-1.5 px-4 text-xs font-medium hover:bg-gray dark:border-strokedark dark:hover:bg-meta-4 text-black dark:text-white">
                Cancelar
            </button>
            <button type="submit"
                    class="inline-flex items-center gap-1.5 rounded bg-primary py-1.5 px-4 text-xs font-bold uppercase text-white hover:bg-opacity-90 shadow-sm">
                <i class="fas fa-save text-[10px]"></i>
                <span x-text="etapa.editando ? 'Guardar cambios' : 'Registrar'"></span>
            </button>
        </div>
    </form>
</div>
