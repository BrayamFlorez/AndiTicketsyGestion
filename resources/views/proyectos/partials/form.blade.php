{{-- resources/views/proyectos/partials/form.blade.php — Alta y edición de proyecto --}}
@php
    $editing = $proyecto->exists;
    $action  = $editing ? route('proyectos.update', $proyecto) : route('proyectos.store');
    $input   = 'w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white';
    $label   = 'block text-[10px] font-bold uppercase text-black dark:text-white mb-1';

    // Equipo: lo que venga de old() manda; si no, el equipo actual
    $equipoOld = old('equipo');
    $enEquipo  = fn ($id) => is_array($equipoOld)
        ? ! empty($equipoOld[$id]['activo'])
        : array_key_exists($id, $rolesEquipo);
    $rolDe     = fn ($id) => is_array($equipoOld) ? ($equipoOld[$id]['rol'] ?? '') : ($rolesEquipo[$id] ?? '');
@endphp

<div class="mb-4 flex items-center justify-between">
    <div>
        <h2 class="text-title-md2 font-bold text-black dark:text-white flex items-center gap-2">
            <i class="fas fa-diagram-project text-primary"></i>
            {{ $editing ? "Editar {$proyecto->codigo}" : 'Nuevo proyecto' }}
        </h2>
        <p class="text-xs text-bodydark2 mt-1">
            {{ $editing ? 'Datos generales, estado y equipo del proyecto.' : 'El código se genera solo al guardar (PRY-' . now()->year . '-000000).' }}
        </p>
    </div>
    <a href="{{ $editing ? route('proyectos.show', $proyecto) : route('proyectos.index') }}"
       class="inline-flex items-center gap-1.5 rounded border border-stroke py-2 px-4 text-xs font-medium hover:bg-gray dark:border-strokedark dark:hover:bg-meta-4 text-black dark:text-white">
        <i class="fas fa-arrow-left text-[10px]"></i> Cancelar
    </a>
</div>

@include('proyectos.partials._flash')

<form action="{{ $action }}" method="POST"
      class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
    @csrf
    @if ($editing) @method('PUT') @endif

    <div class="p-5 space-y-5">

        {{-- SECCIÓN 1: Datos generales --}}
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-primary mb-3 pb-2 border-b border-stroke dark:border-strokedark">
                <i class="fas fa-circle-info"></i> Datos generales
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="{{ $label }}">Nombre del proyecto <span class="text-meta-1">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre', $proyecto->nombre) }}" required maxlength="150"
                           class="{{ $input }}" placeholder="Ej: Sistema de agendamiento web">
                </div>

                <div>
                    <label class="{{ $label }}">
                        Cliente <span class="text-meta-1">*</span>
                        @can('clientes.gestionar')
                            <a href="{{ route('clientes.create', ['volver_a_proyecto' => 1]) }}" class="normal-case font-medium text-primary hover:underline ml-1">
                                + Nuevo cliente
                            </a>
                        @endcan
                    </label>
                    <select name="cliente_id" required class="{{ $input }}">
                        <option value="">— Seleccioná —</option>
                        @foreach ($clientes as $c)
                            <option value="{{ $c->id }}" @selected((string) old('cliente_id', $proyecto->cliente_id) === (string) $c->id)>
                                {{ $c->nombre }}{{ $c->numero_documento ? " · {$c->numero_documento}" : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="{{ $label }}">Descripción / alcance</label>
                    <textarea name="descripcion" rows="3" maxlength="5000" class="{{ $input }}"
                              placeholder="Qué incluye el proyecto, para qué sirve...">{{ old('descripcion', $proyecto->descripcion) }}</textarea>
                </div>
            </div>
        </div>

        {{-- SECCIÓN 2: Estado y fechas --}}
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-primary mb-3 pb-2 border-b border-stroke dark:border-strokedark">
                <i class="fas fa-calendar-days"></i> Estado y fechas
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div>
                    <label class="{{ $label }}">Estado <span class="text-meta-1">*</span></label>
                    <select name="estado" required class="{{ $input }}">
                        @foreach (\App\Models\Proyecto::ESTADOS as $clave => $cfg)
                            <option value="{{ $clave }}" @selected(old('estado', $proyecto->estado) === $clave)>{{ $cfg['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="{{ $label }}">Fecha de inicio <span class="text-meta-1">*</span></label>
                    <input type="date" name="fecha_inicio" required class="{{ $input }}"
                           value="{{ old('fecha_inicio', $proyecto->fecha_inicio?->format('Y-m-d')) }}">
                </div>
                <div>
                    <label class="{{ $label }}">Entrega estimada</label>
                    <input type="date" name="fecha_entrega_estimada" class="{{ $input }}"
                           value="{{ old('fecha_entrega_estimada', $proyecto->fecha_entrega_estimada?->format('Y-m-d')) }}">
                </div>
                @if ($editing && $proyecto->fecha_cierre)
                    <div>
                        <span class="{{ $label }}">Fecha de cierre</span>
                        <div class="py-1.5 text-xs font-mono text-bodydark2">{{ $proyecto->fecha_cierre->format('d/m/Y') }}</div>
                    </div>
                @endif
            </div>
            <p class="text-[10px] text-bodydark2 mt-1.5">
                <i class="fas fa-circle-info"></i> Al pasar a Finalizado o Cancelado se registra la fecha de cierre; si se reabre, se limpia.
            </p>
        </div>

        {{-- SECCIÓN 3: Equipo --}}
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-primary mb-3 pb-2 border-b border-stroke dark:border-strokedark">
                <i class="fas fa-users"></i> Equipo
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">
                <div>
                    <label class="{{ $label }}">Responsable <span class="text-meta-1">*</span></label>
                    <select name="responsable_id" required class="{{ $input }}">
                        @foreach ($usuarios as $u)
                            <option value="{{ $u->id }}" @selected((string) old('responsable_id', $proyecto->responsable_id) === (string) $u->id)>{{ $u->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-bodydark2 mt-1">Siempre queda incluido en el equipo.</p>
                </div>
            </div>

            <span class="{{ $label }}">Miembros del equipo</span>
            @if ($usuarios->isEmpty())
                <p class="text-xs text-bodydark2">No hay usuarios con permiso para ver proyectos. Asigná el permiso <code>proyectos.ver</code> a algún rol.</p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-1.5">
                    @foreach ($usuarios as $u)
                        <div x-data="{ on: {{ $enEquipo($u->id) ? 'true' : 'false' }} }"
                             class="flex items-center gap-2 rounded border px-2.5 py-1.5 transition"
                             :class="on ? 'border-primary bg-primary/5' : 'border-stroke dark:border-strokedark'">
                            <input type="hidden" name="equipo[{{ $u->id }}][activo]" value="0">
                            <input type="checkbox" id="eq{{ $u->id }}" name="equipo[{{ $u->id }}][activo]" value="1"
                                   x-model="on" class="rounded border-stroke shrink-0">
                            <label for="eq{{ $u->id }}" class="text-xs text-black dark:text-white cursor-pointer min-w-0 flex-1 truncate" title="{{ $u->email }}">
                                {{ $u->name }}
                            </label>
                            <input type="text" name="equipo[{{ $u->id }}][rol]" value="{{ $rolDe($u->id) }}" maxlength="60"
                                   placeholder="Rol (opcional)" :readonly="!on"
                                   class="w-28 shrink-0 rounded border border-stroke bg-transparent py-0.5 px-2 text-[11px] focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white"
                                   :class="!on && 'opacity-40'">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="flex justify-end gap-2 border-t border-stroke dark:border-strokedark px-5 py-3">
        <button type="submit" class="inline-flex items-center gap-1.5 rounded bg-primary py-2 px-5 text-xs font-bold uppercase text-white hover:bg-opacity-90 shadow-sm">
            <i class="fas fa-save text-[10px]"></i> {{ $editing ? 'Guardar cambios' : 'Crear proyecto' }}
        </button>
    </div>
</form>
