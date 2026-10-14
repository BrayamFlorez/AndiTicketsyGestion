{{-- resources/views/equipos/mantenimientos/partials/_form.blade.php --}}
{{-- Servicio técnico a un PC con los campos del formato. Recibe: $equipo, $mantenimiento, $tecnicos, $tickets, $unidades, $partes --}}

@use('App\Models\EquipoMantenimiento')

@php
    $editando = $mantenimiento->exists;
    $accion = $editando
        ? route('equipos.mantenimientos.update', [$equipo, $mantenimiento])
        : route('equipos.mantenimientos.store', $equipo);
    $valor = fn (string $campo, $defecto = null) => old($campo, $mantenimiento->{$campo} ?? $defecto);
    $fecha = fn (string $campo) => old($campo, $mantenimiento->{$campo}?->format('Y-m-d'));
    $hora = fn (string $campo) => old($campo, $mantenimiento->{$campo} ? substr($mantenimiento->{$campo}, 0, 5) : null);

    // Filas: lo que se envió (si hubo error) o lo que viene del equipo / del servicio
    $filasUnidades = old('unidades', collect($unidades)->map(fn ($u) => collect($u)->only(array_merge(['unidad', 'periferico_id'], array_keys(EquipoMantenimiento::ACCIONES))))->all());
    $filasPartes = old('partes', collect($partes)->map(fn ($p) => collect($p)->only(['numero_parte', 'nombre', 'serial_original', 'serial_reemplazo']))->all());

    $input = 'w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs font-medium outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white';
    $label = 'mb-1 block text-[10px] font-bold uppercase text-black dark:text-white';
    $error = fn (string $campo) => $errors->has($campo) ? ' border-meta-1' : '';
@endphp

<form method="POST" action="{{ $accion }}"
      x-data="formServicio({ unidades: {{ Js::from(array_values($filasUnidades)) }}, partes: {{ Js::from(array_values($filasPartes)) }}, acciones: {{ Js::from(array_keys(EquipoMantenimiento::ACCIONES)) }} })"
      class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
    @csrf
    @if ($editando) @method('PUT') @endif

    <div class="p-5 space-y-6">

        @if ($errors->any())
            <div class="flex items-start gap-2 rounded border border-meta-1/40 bg-meta-1/10 px-3 py-2 text-xs text-meta-1">
                <i class="fas fa-circle-exclamation mt-0.5"></i>
                <div>
                    <p class="font-bold">No se pudo guardar el servicio:</p>
                    <ul class="mt-0.5 list-disc pl-4 text-[11px]">
                        @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- ── Servicio ─────────────────────────────────────── --}}
        <section>
            <h3 class="mb-3 pb-2 border-b border-stroke dark:border-strokedark font-bold text-black dark:text-white uppercase tracking-wider text-xs flex items-center gap-1.5">
                <i class="fas fa-clipboard-list text-primary"></i> Servicio
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div>
                    <label class="{{ $label }}">Servicio <span class="text-meta-1">*</span></label>
                    <select name="tipo" required class="{{ $input }}{{ $error('tipo') }}">
                        @foreach (EquipoMantenimiento::TIPOS as $clave => $texto)
                            <option value="{{ $clave }}" @selected($valor('tipo') === $clave) class="bg-[#1c2434] text-white">{{ $texto }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="{{ $label }}">Modalidad / contrato</label>
                    <select name="modalidad" class="{{ $input }}{{ $error('modalidad') }}">
                        <option value="" class="bg-[#1c2434] text-gray-400">— Sin especificar —</option>
                        @foreach (EquipoMantenimiento::MODALIDADES as $clave => $texto)
                            <option value="{{ $clave }}" @selected($valor('modalidad') === $clave) class="bg-[#1c2434] text-white">{{ $texto }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="{{ $label }}">Técnico / Ing. <span class="text-meta-1">*</span></label>
                    <select name="tecnico_id" required class="{{ $input }}{{ $error('tecnico_id') }}">
                        @foreach ($tecnicos as $t)
                            <option value="{{ $t->id }}" @selected((string) $valor('tecnico_id') === (string) $t->id) class="bg-[#1c2434] text-white">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="{{ $label }}">Orden de trabajo (ticket)</label>
                    <select name="ticket_id" class="{{ $input }}{{ $error('ticket_id') }}">
                        <option value="" class="bg-[#1c2434] text-gray-400">— Sin ticket —</option>
                        @foreach ($tickets as $t)
                            <option value="{{ $t->id }}" @selected((string) $valor('ticket_id') === (string) $t->id) class="bg-[#1c2434] text-white">
                                #{{ $t->id }} · {{ \Illuminate\Support\Str::limit($t->titulo, 40) }} ({{ $t->label_estado }})
                            </option>
                        @endforeach
                    </select>
                    @if ($tickets->isEmpty())<p class="mt-0.5 text-[10px] text-bodydark2">Este equipo no tiene tickets.</p>@endif
                </div>

                <div>
                    <label class="{{ $label }}">Fecha de ingreso</label>
                    <input type="date" name="fecha_ingreso" value="{{ $fecha('fecha_ingreso') }}" max="{{ today()->toDateString() }}" class="{{ $input }}{{ $error('fecha_ingreso') }}">
                </div>
                <div>
                    <label class="{{ $label }}">Fecha de servicio <span class="text-meta-1">*</span></label>
                    <input type="date" name="fecha_servicio" required value="{{ $fecha('fecha_servicio') }}" max="{{ today()->toDateString() }}" class="{{ $input }}{{ $error('fecha_servicio') }}">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="{{ $label }}">Hora inicial</label>
                        <input type="time" name="hora_inicio" value="{{ $hora('hora_inicio') }}" class="{{ $input }}{{ $error('hora_inicio') }}">
                    </div>
                    <div>
                        <label class="{{ $label }}">Hora final</label>
                        <input type="time" name="hora_fin" value="{{ $hora('hora_fin') }}" class="{{ $input }}{{ $error('hora_fin') }}">
                    </div>
                </div>
                <div>
                    <label class="{{ $label }}">Depósito</label>
                    <input type="text" name="deposito" maxlength="100" value="{{ $valor('deposito') }}" placeholder="Bodega o lugar del equipo" class="{{ $input }}{{ $error('deposito') }}">
                </div>

                <div>
                    <label class="{{ $label }}">Contador inicial</label>
                    <input type="text" name="contador_inicial" maxlength="50" value="{{ $valor('contador_inicial') }}" placeholder="Opcional (horas de uso, etc.)" class="{{ $input }}">
                </div>
                <div>
                    <label class="{{ $label }}">Contador final</label>
                    <input type="text" name="contador_final" maxlength="50" value="{{ $valor('contador_final') }}" placeholder="Opcional" class="{{ $input }}">
                </div>
            </div>
            <p class="mt-1 text-[10px] text-bodydark2">La hora final se completa sola al firmar si la deja vacía.</p>
        </section>

        {{-- ── Falla ─────────────────────────────────────────── --}}
        <section>
            <label class="{{ $label }}">Falla / motivo del servicio <span class="text-meta-1">*</span></label>
            <textarea name="falla" rows="2" required minlength="5" maxlength="3000" placeholder="Ej: Mantenimiento preventivo programado · El equipo se reinicia solo"
                      class="{{ $input }}{{ $error('falla') }}">{{ $valor('falla') }}</textarea>
        </section>

        {{-- ── Unidades revisadas ────────────────────────────── --}}
        <section>
            <div class="mb-2 pb-2 border-b border-stroke dark:border-strokedark flex flex-wrap items-center justify-between gap-2">
                <h3 class="font-bold text-black dark:text-white uppercase tracking-wider text-xs flex items-center gap-1.5">
                    <i class="fas fa-tasks text-primary"></i> Unidades revisadas
                </h3>
                <div class="flex items-center gap-2">
                    <button type="button" @click="marcarTodas('limpieza')" class="text-[10px] font-bold uppercase text-bodydark2 hover:text-primary">Limpieza a todas</button>
                    <button type="button" @click="marcarTodas('revision')" class="text-[10px] font-bold uppercase text-bodydark2 hover:text-primary">Revisión a todas</button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[640px] text-xs">
                    <thead>
                        <tr class="text-[9px] font-bold uppercase text-bodydark2 bg-gray-2 dark:bg-meta-4/40">
                            <th class="py-1.5 px-2 text-left">Unidad</th>
                            @foreach (EquipoMantenimiento::ACCIONES as [$encabezado])
                                <th class="py-1.5 px-1 text-center w-16">{{ $encabezado }}</th>
                            @endforeach
                            <th class="w-8"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(fila, i) in unidades" :key="fila._k">
                            <tr class="border-b border-stroke dark:border-strokedark">
                                <td class="py-1 px-2">
                                    <input type="text" :name="`unidades[${i}][unidad]`" x-model="fila.unidad" maxlength="150" required
                                           class="w-full rounded border border-transparent hover:border-stroke focus:border-primary bg-transparent py-1 px-2 text-xs text-black dark:text-white outline-none dark:hover:border-strokedark">
                                    <input type="hidden" :name="`unidades[${i}][periferico_id]`" :value="fila.periferico_id ?? ''">
                                </td>
                                <template x-for="accion in acciones" :key="accion">
                                    <td class="py-1 px-1 text-center">
                                        <input type="checkbox" value="1" :name="`unidades[${i}][${accion}]`" x-model="fila[accion]"
                                               class="h-4 w-4 rounded border-stroke text-primary cursor-pointer">
                                    </td>
                                </template>
                                <td class="text-center">
                                    <button type="button" @click="unidades.splice(i, 1)" class="text-bodydark2 hover:text-meta-1" title="Quitar fila">
                                        <i class="fas fa-times text-[10px]"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            <div class="mt-2 flex flex-wrap items-center justify-between gap-2">
                <button type="button" @click="agregarUnidad()" class="inline-flex items-center gap-1 rounded border border-dashed border-stroke dark:border-strokedark py-1 px-3 text-[10px] font-bold uppercase text-bodydark2 hover:border-primary hover:text-primary">
                    <i class="fas fa-plus text-[9px]"></i> Agregar unidad
                </button>
                <p class="text-[9px] text-bodydark2">L Limpieza · A Ajuste · Lu Lubricación · C/I Cambio o instalación · Repar. Reparación · Rev. Revisión</p>
            </div>
            @if (! $editando && $equipo->perifericos->isEmpty())
                <p class="mt-1 text-[10px] text-meta-6"><i class="fas fa-info-circle"></i> El equipo no tiene periféricos registrados: puede agregarlos desde su ficha para que aparezcan aquí la próxima vez.</p>
            @endif
        </section>

        {{-- ── Observaciones ─────────────────────────────────── --}}
        <section>
            <label class="{{ $label }}">Observaciones</label>
            <textarea name="observaciones" rows="3" maxlength="5000" placeholder="Pruebas realizadas, estado en que queda el equipo, recomendaciones..."
                      class="{{ $input }}{{ $error('observaciones') }}">{{ $valor('observaciones') }}</textarea>
        </section>

        {{-- ── Solicitud y cambio de partes ──────────────────── --}}
        <section>
            <h3 class="mb-2 pb-2 border-b border-stroke dark:border-strokedark font-bold text-black dark:text-white uppercase tracking-wider text-xs flex items-center gap-1.5">
                <i class="fas fa-exchange-alt text-primary"></i> Solicitud y cambio de partes
            </h3>
            <template x-for="(parte, i) in partes" :key="parte._k">
                <div class="grid grid-cols-12 gap-2 mb-2">
                    <input type="text" :name="`partes[${i}][numero_parte]`" x-model="parte.numero_parte" maxlength="60" placeholder="N.º de parte" class="col-span-6 sm:col-span-2 {{ $input }}">
                    <input type="text" :name="`partes[${i}][nombre]`" x-model="parte.nombre" maxlength="150" required placeholder="Nombre de la parte *" class="col-span-6 sm:col-span-4 {{ $input }}">
                    <input type="text" :name="`partes[${i}][serial_original]`" x-model="parte.serial_original" maxlength="100" placeholder="Serial original" class="col-span-5 sm:col-span-3 {{ $input }} font-mono">
                    <input type="text" :name="`partes[${i}][serial_reemplazo]`" x-model="parte.serial_reemplazo" maxlength="100" placeholder="Serial reemplazo" class="col-span-5 sm:col-span-2 {{ $input }} font-mono">
                    <button type="button" @click="partes.splice(i, 1)" class="col-span-2 sm:col-span-1 text-bodydark2 hover:text-meta-1" title="Quitar"><i class="fas fa-trash text-[10px]"></i></button>
                </div>
            </template>
            <button type="button" @click="agregarParte()" class="inline-flex items-center gap-1 rounded border border-dashed border-stroke dark:border-strokedark py-1 px-3 text-[10px] font-bold uppercase text-bodydark2 hover:border-primary hover:text-primary">
                <i class="fas fa-plus text-[9px]"></i> Agregar parte
            </button>
        </section>
    </div>

    <div class="flex items-center justify-end gap-2 bg-gray-2 dark:bg-meta-4/40 px-5 py-3 border-t border-stroke dark:border-strokedark">
        <a href="{{ $editando ? route('equipos.mantenimientos.show', [$equipo, $mantenimiento]) : route('equipos.show', $equipo) }}"
           class="inline-flex items-center rounded border border-stroke py-1.5 px-4 text-xs font-medium text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition">
            Cancelar
        </a>
        <button type="submit" class="inline-flex items-center gap-1.5 rounded bg-primary py-1.5 px-5 text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm">
            <i class="fas fa-{{ $editando ? 'save' : 'arrow-right' }} text-[10px]"></i>
            {{ $editando ? 'Guardar cambios' : 'Guardar y continuar con fotos y firmas' }}
        </button>
    </div>
</form>

@push('scripts')
<script>
    function formServicio({ unidades, partes, acciones }) {
        let clave = 0;
        const conClave = fila => ({ ...fila, _k: ++clave });
        const aBooleano = v => v === true || v === 1 || v === '1' || v === 'true' || v === 'on';

        return {
            acciones,
            unidades: unidades.map(u => conClave({
                ...u,
                ...Object.fromEntries(acciones.map(a => [a, aBooleano(u[a])])),
            })),
            partes: partes.map(conClave),

            agregarUnidad() {
                this.unidades.push(conClave({ unidad: '', periferico_id: null, ...Object.fromEntries(acciones.map(a => [a, false])) }));
            },
            agregarParte() {
                this.partes.push(conClave({ numero_parte: '', nombre: '', serial_original: '', serial_reemplazo: '' }));
            },
            // Si todas ya la tienen, la quita
            marcarTodas(accion) {
                const todas = this.unidades.every(u => u[accion]);
                this.unidades.forEach(u => u[accion] = ! todas);
            },
        };
    }
</script>
@endpush
