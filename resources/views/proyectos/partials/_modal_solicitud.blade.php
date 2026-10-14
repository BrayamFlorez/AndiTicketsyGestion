{{-- resources/views/proyectos/partials/_modal_solicitud.blade.php — Nueva / editar solicitud del cliente --}}
@php
    $input = 'w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white';
    $label = 'block text-[10px] font-bold uppercase text-black dark:text-white mb-1';
@endphp

<div x-show="modalSolicitud" style="display: none;" x-transition.opacity
     class="fixed inset-0 z-[9999] flex items-start justify-center overflow-y-auto bg-black/70 backdrop-blur-sm px-4 py-8"
     @keydown.escape.window="modalSolicitud = false">

    <form method="POST" :action="solicitud.accion" enctype="multipart/form-data"
          @click.outside="modalSolicitud = false"
          class="w-full max-w-2xl rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
        @csrf
        <input type="hidden" name="_method" :value="solicitud.editando ? 'PUT' : 'POST'">
        <input type="hidden" name="_modal" value="solicitud">
        <input type="hidden" name="_accion" :value="solicitud.accion">
        <input type="hidden" name="_editando" :value="solicitud.editando ? 1 : 0">

        <div class="flex items-center justify-between border-b border-stroke dark:border-strokedark px-4 py-3">
            <h3 class="text-sm font-bold text-black dark:text-white flex items-center gap-2">
                <i class="fas fa-comment-dots text-[#f97316]"></i>
                <span x-text="solicitud.editando ? 'Editar solicitud' : 'Nueva solicitud del cliente'"></span>
            </h3>
            <button type="button" @click="modalSolicitud = false" class="text-bodydark2 hover:text-black dark:hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="p-4 space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="{{ $label }}">Fecha de la solicitud <span class="text-meta-1">*</span></label>
                    <input type="date" name="fecha_solicitud" x-model="solicitud.fecha_solicitud" :max="hoy" required class="{{ $input }}">
                </div>
                <div>
                    <label class="{{ $label }}">Canal</label>
                    <select name="canal" x-model="solicitud.canal" class="{{ $input }}">
                        @foreach (\App\Models\ProyectoSolicitud::CANALES as $clave => $cfg)
                            <option value="{{ $clave }}">{{ $cfg['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="{{ $label }}">Prioridad</label>
                    <select name="prioridad" x-model="solicitud.prioridad" class="{{ $input }}">
                        @foreach (\App\Models\ProyectoSolicitud::PRIORIDADES as $clave => $cfg)
                            <option value="{{ $clave }}">{{ $cfg['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="{{ $label }}">Título <span class="text-meta-1">*</span></label>
                <input type="text" name="titulo" x-model="solicitud.titulo" required maxlength="150"
                       placeholder="Ej: Agregar filtro por sede en la agenda" class="{{ $input }}">
            </div>

            <div>
                <label class="{{ $label }}">Qué pidió el cliente <span class="text-meta-1">*</span></label>
                <textarea name="descripcion" x-model="solicitud.descripcion" rows="4" required maxlength="5000"
                          placeholder="Describí la solicitud con el mayor detalle posible" class="{{ $input }}"></textarea>
            </div>

            <div>
                <span class="{{ $label }}"><span x-text="solicitud.editando ? 'Agregar evidencias' : 'Evidencias'"></span></span>
                <label class="block rounded border-[1.5px] border-dashed border-stroke dark:border-strokedark px-3 py-3 text-center text-[11px] text-bodydark2 cursor-pointer hover:border-[#f97316] transition">
                    <i class="fas fa-paperclip text-[#f97316] text-lg block mb-0.5"></i>
                    Capturas, notas de voz, videos o documentos · máx. 20 MB por archivo
                    <input type="file" name="archivos[]" multiple class="hidden" x-ref="archivosSolicitud"
                           accept="image/*,audio/*,video/*,.pdf,.doc,.docx,.xls,.xlsx,.csv,.ppt,.pptx,.txt,.odt,.ods,.zip,.rar,.opus,.ogg,.heic"
                           @change="procesarArchivos($refs.archivosSolicitud, 'solicitud')">
                </label>
                <div class="mt-1.5 space-y-1">
                    <template x-for="(f, i) in archivos.solicitud" :key="f.nombre + i">
                        <div class="flex items-center gap-2 rounded border border-stroke dark:border-strokedark px-2 py-1 text-[11px] text-black dark:text-white">
                            <i class="fas text-bodydark2" :class="f.icono"></i>
                            <span class="truncate" x-text="f.nombre"></span>
                            <span class="ml-auto font-mono text-[10px] text-bodydark2" x-text="f.peso"></span>
                            <button type="button" @click="quitarArchivo($refs.archivosSolicitud, 'solicitud', i)" class="text-bodydark2 hover:text-meta-1">
                                <i class="fas fa-times text-[10px]"></i>
                            </button>
                        </div>
                    </template>
                </div>
                <p x-show="avisoArchivos.solicitud" x-text="avisoArchivos.solicitud" class="text-[10px] text-meta-1 mt-1"></p>
            </div>

            <div x-show="!solicitud.editando" class="rounded border border-primary/30 bg-primary/5 px-3 py-2 text-[11px] text-primary">
                <i class="fas fa-circle-info"></i>
                La solicitud entra como <b>Pendiente</b>. Cuando registres la etapa que la resuelve (una corrección o una entrega),
                la marcás ahí y pasa sola a <b>Atendida</b>.
            </div>
        </div>

        <div class="flex justify-end gap-2 border-t border-stroke dark:border-strokedark px-4 py-3">
            <button type="button" @click="modalSolicitud = false"
                    class="rounded border border-stroke py-1.5 px-4 text-xs font-medium hover:bg-gray dark:border-strokedark dark:hover:bg-meta-4 text-black dark:text-white">
                Cancelar
            </button>
            <button type="submit"
                    class="inline-flex items-center gap-1.5 rounded bg-[#f97316] py-1.5 px-4 text-xs font-bold uppercase text-white hover:bg-opacity-90 shadow-sm">
                <i class="fas fa-save text-[10px]"></i>
                <span x-text="solicitud.editando ? 'Guardar cambios' : 'Registrar solicitud'"></span>
            </button>
        </div>
    </form>
</div>
