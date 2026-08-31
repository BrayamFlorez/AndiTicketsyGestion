{{-- resources/views/flujos/instancias/create.blade.php --}}
<x-app-layout>
    <x-slot name="title">Nuevo Flujo</x-slot>

    {{-- ── Cabecera y Acción Principal ────────────────────────────────── --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-title-md2 font-bold text-black dark:text-white flex items-center gap-2">
                <i class="fas fa-play-circle text-primary"></i> Iniciar Nuevo Flujo
            </h2>
            <p class="text-xs text-bodydark2 mt-1">
                Selecciona el tipo de documento y completa la información inicial del trámite
            </p>
        </div>

        <a href="{{ route('flujos.instancias.index') }}" 
           class="inline-flex items-center justify-center gap-2 rounded border border-stroke py-2 px-4 text-center text-xs font-medium text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition shadow-sm">
            <i class="fas fa-arrow-left text-[10px]"></i> Volver al Listado
        </a>
    </div>

    @if ($tipos->isEmpty())
        {{-- Sin Tipos de Documento Configurados --}}
        <div class="rounded-sm border border-stroke bg-white p-12 text-center shadow-default dark:border-strokedark dark:bg-boxdark">
            <div class="flex flex-col items-center text-bodydark2">
                <i class="fas fa-folder-open text-4xl mb-3"></i>
                <p class="text-sm font-bold text-black dark:text-white mb-1">
                    No hay tipos de documento configurados con una plantilla activa.
                </p>
                <p class="text-xs max-w-md">
                    Un administrador debe crear al menos un tipo de documento y asociarle una plantilla de flujo de trabajo activa.
                </p>
            </div>
        </div>
    @else
        <form action="{{ route('flujos.instancias.store') }}" method="POST" 
              class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark"
              x-data="iniciarFlujo({{ Js::from($tipos->map(fn ($t) => [
                    'id'        => $t->id,
                    'nombre'    => $t->nombre,
                    'icono'     => $t->icono,
                    'plantilla' => $t->template->nombre,
                    'etapas'    => $t->template->stages->sortBy('orden')->pluck('nombre')->values(),
                ])) }})">
            @csrf

            {{-- Errores de Validación --}}
            @if ($errors->any())
                <div class="mx-6 mt-6 flex w-full border-l-6 border-meta-1 bg-meta-1/10 px-6 py-4 shadow-md dark:bg-meta-1/20 dark:border-meta-1 rounded-sm">
                    <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-meta-1 text-white shrink-0">
                        <i class="fas fa-exclamation-triangle text-sm"></i>
                    </div>
                    <div>
                        <h5 class="mb-1 text-xs font-bold text-meta-1 uppercase tracking-wider">Revisa los siguientes campos:</h5>
                        <ul class="list-disc list-inside space-y-0.5 text-xs text-meta-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="p-6 space-y-6">

                {{-- SECCIÓN 1: Selección de Tipo de Documento --}}
                <div>
                    <div class="mb-3 pb-2 border-b border-stroke dark:border-strokedark">
                        <h3 class="font-bold text-black dark:text-white uppercase tracking-wider text-xs flex items-center gap-1.5">
                            <i class="fas fa-file-alt text-primary"></i> Tipo de Documento <span class="text-meta-1">*</span>
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach ($tipos as $tipo)
                            <label class="cursor-pointer">
                                <input type="radio" name="document_type_id" value="{{ $tipo->id }}"
                                       class="peer sr-only" x-model.number="tipoElegido"
                                       @checked(old('document_type_id') == $tipo->id) required>
                                
                                <div class="p-4 rounded border-2 border-stroke bg-gray-2 dark:border-strokedark dark:bg-meta-4/30 transition peer-checked:border-primary peer-checked:bg-primary/5 hover:border-primary/50 flex items-start gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded bg-primary/10 text-primary shrink-0">
                                        <i class="fas {{ $tipo->icono ?: 'fa-file-alt' }} text-sm"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h4 class="text-xs font-bold text-black dark:text-white truncate">{{ $tipo->nombre }}</h4>
                                        <p class="text-[10px] text-bodydark2 uppercase tracking-wider mt-0.5 truncate">
                                            {{ $tipo->template->nombre }}
                                        </p>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Preview de Etapas del Tipo Seleccionado --}}
                <div x-show="tipoElegido" x-cloak class="rounded border border-stroke bg-gray-2 p-4 dark:border-strokedark dark:bg-meta-4/30">
                    <p class="text-[10px] font-bold text-bodydark2 uppercase tracking-wider mb-2 flex items-center gap-1">
                        <i class="fas fa-stream text-primary"></i> Etapas que recorrerá el flujo:
                    </p>
                    <div class="flex flex-wrap items-center gap-2">
                        <template x-for="(etapa, idx) in etapasPreview()" :key="idx">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center rounded border border-stroke bg-white px-2.5 py-1 text-xs font-medium text-black dark:border-strokedark dark:bg-boxdark dark:text-white">
                                    <span class="text-primary font-bold mr-1" x-text="`${idx + 1}.`"></span>
                                    <span x-text="etapa"></span>
                                </span>
                                <i class="fas fa-arrow-right text-bodydark2 text-[10px]" x-show="idx < etapasPreview().length - 1"></i>
                            </div>
                        </template>
                    </div>
                </div>

                                {{-- SECCIÓN 2: Información del Trámite --}}
                <div>
                    <div class="mb-3 pb-2 border-b border-stroke dark:border-strokedark">
                        <h3 class="font-bold text-black dark:text-white uppercase tracking-wider text-xs flex items-center gap-1.5">
                            <i class="fas fa-align-left text-primary"></i> Información del Trámite
                        </h3>
                    </div>

                    <div class="space-y-4">

                        {{-- Referencia externa (índice personal del iniciador) --}}
                        <div>
                            <label for="referencia_externa" class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white flex items-center gap-1.5">
                                <i class="fas fa-hashtag text-primary text-[9px]"></i>
                                Código de Referencia
                                <span class="font-normal normal-case text-bodydark2">(opcional)</span>
                            </label>
                            <input type="text" id="referencia_externa" name="referencia_externa"
                                   maxlength="50"
                                   value="{{ old('referencia_externa') }}"
                                   placeholder="Ej: OT-2026-042 · PROY-ACME-Q3 · REF-INTERNO-15"
                                   style="text-transform: uppercase;"
                                   class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 font-mono text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white @error('referencia_externa') border-meta-1 @enderror">
                            <p class="mt-1 text-[10px] text-bodydark2">
                                <i class="fas fa-info-circle"></i>
                                Tu identificador para encontrar este trámite después.
                                Solo letras, números, guiones y barras. Sin espacios.
                            </p>
                            @error('referencia_externa')<p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $message }}</p>@enderror
                        </div>

                        {{-- Título --}}
                        <div>
                            <label for="titulo" class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">
                                Título del Trámite <span class="text-meta-1">*</span>
                            </label>
                            <input type="text" id="titulo" name="titulo" required maxlength="200"
                                   value="{{ old('titulo') }}"
                                   placeholder="Ej: Cotización Cliente ACME — Servicios Recurrentes 2026"
                                   class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white @error('titulo') border-meta-1 @enderror">
                            @error('titulo')<p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $message }}</p>@enderror
                        </div>

                        {{-- Descripción --}}
                        <div>
                            <label for="descripcion" class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">
                                Descripción Detallada (Opcional)
                            </label>
                            <textarea id="descripcion" name="descripcion" rows="4" maxlength="2000"
                                      placeholder="Agrega observaciones, contexto, referencias o montos iniciales..."
                                      class="w-full rounded border border-stroke bg-transparent py-2 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white @error('descripcion') border-meta-1 @enderror">{{ old('descripcion') }}</textarea>
                            @error('descripcion')<p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

            </div>

            {{-- Footer del Formulario --}}
            <div class="flex items-center justify-end gap-2 bg-gray-2 dark:bg-meta-4/40 px-6 py-3 border-t border-stroke dark:border-strokedark">
                <a href="{{ route('flujos.instancias.index') }}" 
                   class="inline-flex items-center justify-center rounded border border-stroke py-1.5 px-4 text-center text-xs font-medium text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition">
                    Cancelar
                </a>
                <button type="submit" 
                        class="inline-flex items-center gap-1.5 rounded bg-primary py-1.5 px-5 text-center text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm cursor-pointer">
                    <i class="fas fa-play-circle text-[10px]"></i> Iniciar Flujo
                </button>
            </div>
        </form>

        @push('scripts')
            <script>
                function iniciarFlujo(tipos) {
                    return {
                        tipos: tipos,
                        tipoElegido: null,
                        etapasPreview() {
                            const t = this.tipos.find(x => x.id === this.tipoElegido);
                            return t ? t.etapas : [];
                        }
                    };
                }
            </script>
        @endpush
    @endif
</x-app-layout>