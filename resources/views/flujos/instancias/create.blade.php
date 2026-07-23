<x-app-layout>
    <x-slot name="title">Nuevo Flujo</x-slot>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
        <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
    @endpush

    <div class="db-header mb-6 flex items-center justify-between flex-wrap gap-3">
        <div>
            <h2 class="text-2xl font-black text-white uppercase tracking-tighter">Iniciar Nuevo Flujo</h2>
            <p class="text-xs text-gray-400 font-medium uppercase tracking-widest">
                Selecciona el tipo de documento y completa los datos del trámite
            </p>
        </div>
        <a href="{{ route('flujos.instancias.index') }}" class="db-btn ghost">
            <i class="fas fa-arrow-left mr-2"></i> Volver
        </a>
    </div>

    @if ($tipos->isEmpty())
        <div class="db-card">
            <div class="db-empty py-12 text-center px-6">
                <i class="fas fa-folder-open text-5xl mb-4 text-gray-700"></i>
                <p class="text-gray-400 mb-2">No hay tipos de documento configurados con una plantilla activa.</p>
                <p class="text-xs text-gray-600">Un administrador debe crear al menos un tipo de documento y asociarle una plantilla de flujo activa.</p>
            </div>
        </div>
    @else
        <form action="{{ route('flujos.instancias.store') }}" method="POST" class="db-card"
              x-data="iniciarFlujo({{ Js::from($tipos->map(fn ($t) => [
                    'id'       => $t->id,
                    'nombre'   => $t->nombre,
                    'icono'    => $t->icono,
                    'plantilla' => $t->template->nombre,
                    'etapas'   => $t->template->stages->sortBy('orden')->pluck('nombre')->values(),
              ])) }})">
            @csrf

            @if ($errors->any())
                <div class="mx-6 mt-6 px-4 py-3 rounded-lg bg-red-500/10 border border-red-500/30 text-red-300 text-sm">
                    <div class="font-bold mb-2"><i class="fas fa-exclamation-triangle mr-1"></i> Revisa los campos:</div>
                    <ul class="list-disc list-inside space-y-1 text-xs">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="form-card-body" style="padding: 1.5rem;">

                {{-- Tipo de documento --}}
                <div class="form-group">
                    <label class="form-label">Tipo de Documento <span class="text-red-400">*</span></label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 mt-2">
                        @foreach ($tipos as $tipo)
                            <label class="cursor-pointer">
                                <input type="radio" name="document_type_id" value="{{ $tipo->id }}"
                                    class="peer sr-only" x-model.number="tipoElegido"
                                    @checked(old('document_type_id') == $tipo->id) required>
                                <div class="p-4 rounded-lg border-2 border-gray-800 bg-[#1a1a1b] transition-all
                                            peer-checked:border-[#007acc] peer-checked:bg-[#007acc]/10 hover:border-gray-700">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-[#007acc]/10 flex items-center justify-center text-[#007acc] flex-shrink-0">
                                            <i class="fas {{ $tipo->icono ?: 'fa-file-alt' }}"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-bold text-white">{{ $tipo->nombre }}</p>
                                            <p class="text-[10px] text-gray-500 mt-0.5 uppercase tracking-wider">
                                                {{ $tipo->template->nombre }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Preview de etapas del tipo elegido --}}
                <div class="mt-4 rounded-lg bg-[#232324] border border-gray-800 p-4" x-show="tipoElegido" style="display: none;">
                    <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest mb-2">
                        <i class="fas fa-stream mr-1"></i> Etapas que recorrerá el flujo:
                    </p>
                    <div class="flex flex-wrap items-center gap-2">
                        <template x-for="(etapa, idx) in etapasPreview()" :key="idx">
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 rounded-full bg-[#1a1a1b] border border-gray-800 text-xs text-gray-300">
                                    <span class="text-[#007acc] font-bold" x-text="`${idx + 1}.`"></span>
                                    <span x-text="etapa"></span>
                                </span>
                                <i class="fas fa-arrow-right text-gray-700 text-xs" x-show="idx < etapasPreview().length - 1"></i>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Título --}}
                <div class="form-group mt-6">
                    <label class="form-label" for="titulo">Título del Trámite <span class="text-red-400">*</span></label>
                    <input type="text" id="titulo" name="titulo" class="form-input"
                        value="{{ old('titulo') }}" required maxlength="200"
                        placeholder="Ej: Cotización cliente ACME — Servicios recurrentes 2026">
                </div>

                {{-- Descripción --}}
                <div class="form-group mt-4">
                    <label class="form-label" for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" rows="4" class="form-input"
                        maxlength="2000" placeholder="Contexto, monto, condiciones, referencias...">{{ old('descripcion') }}</textarea>
                </div>

            </div>

            <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-gray-800">
                <a href="{{ route('flujos.instancias.index') }}" class="db-btn ghost">Cancelar</a>
                <button type="submit" class="db-btn solid">
                    <i class="fas fa-play-circle mr-2"></i> Iniciar Flujo
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