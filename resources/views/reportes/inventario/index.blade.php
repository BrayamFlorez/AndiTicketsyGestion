<x-app-layout>
    <x-slot name="title">Reporte de inventario</x-slot>

    <div class="mb-4 flex items-center justify-between gap-3 flex-wrap">
        <div>
            <h2 class="text-title-md2 font-bold text-black dark:text-white flex items-center gap-2">
                <i class="fas fa-file-export text-primary"></i> Reporte de inventario
            </h2>
            <p class="text-xs text-bodydark2 mt-1">
                Elegí qué incluir y descargá el listado en Excel o el informe en PDF.
            </p>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-4 flex w-full border-l-4 border-meta-1 bg-meta-1/10 px-4 py-3 rounded-sm">
            <i class="fas fa-exclamation-triangle text-meta-1 mr-2 mt-0.5"></i>
            <ul class="text-xs text-meta-1 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="GET" action="{{ route('reportes.inventario.pdf') }}"
          class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark"
          x-data="reporteInventario({
              sedesPorEmpresa: {{ Js::from($sedesPorEmpresa) }},
              tiposIniciales: {{ Js::from(old('tipos', request('tipos', ['equipos', 'impresoras']))) }},
              empresaInicial: {{ Js::from((string) old('empresa_id', request('empresa_id', ''))) }},
              sedeInicial: {{ Js::from((string) old('sede_id', request('sede_id', ''))) }},
          })">

        <div class="p-6 space-y-6">

            {{-- 1. Qué incluir --}}
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-primary mb-3 pb-2 border-b border-stroke dark:border-strokedark">
                    <i class="fas fa-boxes-stacked"></i> ¿Qué inventario incluir?
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="checkbox" name="tipos[]" value="equipos" x-model="tipos" class="peer sr-only">
                        <div class="flex items-center gap-3 rounded border-2 border-stroke p-4 transition peer-checked:border-primary peer-checked:bg-primary/5 dark:border-strokedark">
                            <div class="flex h-10 w-10 items-center justify-center rounded bg-primary/10 text-primary">
                                <i class="fas fa-desktop"></i>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-black dark:text-white">Equipos</div>
                                <div class="text-[10px] text-bodydark2">Computadoras, portátiles y demás equipos</div>
                            </div>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="checkbox" name="tipos[]" value="impresoras" x-model="tipos" class="peer sr-only">
                        <div class="flex items-center gap-3 rounded border-2 border-stroke p-4 transition peer-checked:border-primary peer-checked:bg-primary/5 dark:border-strokedark">
                            <div class="flex h-10 w-10 items-center justify-center rounded bg-primary/10 text-primary">
                                <i class="fas fa-print"></i>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-black dark:text-white">Impresoras</div>
                                <div class="text-[10px] text-bodydark2">Con consumibles y mantenimientos</div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            {{-- 2. Filtros --}}
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-primary mb-3 pb-2 border-b border-stroke dark:border-strokedark">
                    <i class="fas fa-sliders-h"></i> Filtros
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-black dark:text-white mb-1">Empresa</label>
                        <select name="empresa_id" x-model="empresaSeleccionada"
                                class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                            <option value="">— Todas las de mi alcance —</option>
                            @foreach ($empresas as $empresa)
                                <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase text-black dark:text-white mb-1">Sede</label>
                        <select name="sede_id" x-model="sedeSeleccionada" :disabled="!empresaSeleccionada"
                                x-init="$nextTick(() => { if (sedeSeleccionada) $el.value = sedeSeleccionada })"
                                class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white disabled:opacity-50">
                            <option value="">— Todas las sedes —</option>
                            <template x-for="sede in sedesFiltradas" :key="sede.id">
                                <option :value="sede.id" x-text="sede.nombre"></option>
                            </template>
                        </select>
                    </div>

                    <div x-show="tipos.includes('equipos')" x-cloak>
                        <label class="block text-[10px] font-bold uppercase text-black dark:text-white mb-1">Categoría de equipo</label>
                        <select name="categoria"
                                class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                            <option value="">— Todas —</option>
                            @foreach ($categorias as $clave => $label)
                                <option value="{{ $clave }}" @selected(request('categoria') === $clave)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <label class="mt-4 flex items-center gap-2 cursor-pointer text-xs text-black dark:text-white">
                    <input type="hidden" name="incluir_bajas" value="0">
                    <input type="checkbox" name="incluir_bajas" value="1" class="rounded border-stroke"
                           @checked(request()->boolean('incluir_bajas'))>
                    Incluir equipos e impresoras dados de baja
                </label>
            </div>
        </div>

        <div class="flex items-center justify-between gap-2 flex-wrap border-t border-stroke bg-gray-2 px-6 py-3 dark:border-strokedark dark:bg-meta-4/40">
            <p class="text-[10px] text-bodydark2" x-show="tipos.length === 0" x-cloak>
                <i class="fas fa-info-circle"></i> Elegí al menos un tipo de inventario.
            </p>
            <div class="flex items-center gap-2 ml-auto">
                <button type="submit" formaction="{{ route('reportes.inventario.excel') }}"
                        :disabled="tipos.length === 0"
                        class="inline-flex items-center gap-1.5 rounded bg-meta-3 py-2 px-4 text-xs font-bold uppercase text-white hover:bg-opacity-90 disabled:opacity-40 disabled:cursor-not-allowed">
                    <i class="fas fa-file-excel"></i> Descargar Excel
                </button>
                <button type="submit" formaction="{{ route('reportes.inventario.pdf') }}"
                        :disabled="tipos.length === 0"
                        class="inline-flex items-center gap-1.5 rounded bg-primary py-2 px-4 text-xs font-bold uppercase text-white hover:bg-opacity-90 disabled:opacity-40 disabled:cursor-not-allowed">
                    <i class="fas fa-file-pdf"></i> Descargar PDF
                </button>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        function reporteInventario({ sedesPorEmpresa, tiposIniciales, empresaInicial, sedeInicial }) {
            return {
                sedesPorEmpresa: sedesPorEmpresa || {},
                tipos: tiposIniciales || [],
                empresaSeleccionada: empresaInicial || '',
                sedeSeleccionada: sedeInicial || '',

                get sedesFiltradas() {
                    return this.sedesPorEmpresa[this.empresaSeleccionada] ?? [];
                },

                init() {
                    // Al cambiar de empresa, la sede anterior deja de aplicar
                    let primeraVez = true;
                    this.$watch('empresaSeleccionada', () => {
                        if (primeraVez) { primeraVez = false; return; }
                        this.sedeSeleccionada = '';
                    });
                },
            };
        }
    </script>
    @endpush
</x-app-layout>