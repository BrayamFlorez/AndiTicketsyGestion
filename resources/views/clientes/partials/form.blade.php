{{-- resources/views/clientes/partials/form.blade.php --}}
@php
    $editing = $cliente->exists;
    $action  = $editing ? route('clientes.update', $cliente) : route('clientes.store');
    $input   = 'w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white';
    $label   = 'block text-[10px] font-bold uppercase text-black dark:text-white mb-1';
    $volver  = request('volver_a_proyecto') || old('volver_a_proyecto');
@endphp

<div class="mb-4 flex items-center justify-between">
    <div>
        <h2 class="text-title-md2 font-bold text-black dark:text-white flex items-center gap-2">
            <i class="fas fa-address-book text-primary"></i>
            {{ $editing ? "Editar {$cliente->nombre}" : 'Nuevo cliente' }}
        </h2>
        <p class="text-xs text-bodydark2 mt-1">Datos del cliente al que se le desarrollan proyectos.</p>
    </div>
    <a href="{{ $volver ? route('proyectos.create') : route('clientes.index') }}"
       class="inline-flex items-center gap-1.5 rounded border border-stroke py-2 px-4 text-xs font-medium hover:bg-gray dark:border-strokedark dark:hover:bg-meta-4 text-black dark:text-white">
        <i class="fas fa-arrow-left text-[10px]"></i> Cancelar
    </a>
</div>

@include('proyectos.partials._flash')

<form action="{{ $action }}" method="POST"
      class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
    @csrf
    @if ($editing) @method('PUT') @endif
    @if ($volver) <input type="hidden" name="volver_a_proyecto" value="1"> @endif

    <div class="p-5 space-y-5">
        {{-- Identificación --}}
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-primary mb-3 pb-2 border-b border-stroke dark:border-strokedark">
                <i class="fas fa-id-card"></i> Identificación
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div class="sm:col-span-2">
                    <label class="{{ $label }}">Nombre / Razón social <span class="text-meta-1">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre', $cliente->nombre) }}" required maxlength="150" class="{{ $input }}">
                </div>
                <div>
                    <label class="{{ $label }}">Tipo de documento</label>
                    <select name="tipo_documento" class="{{ $input }}">
                        @foreach (\App\Models\Cliente::TIPOS_DOCUMENTO as $clave => $texto)
                            <option value="{{ $clave }}" @selected(old('tipo_documento', $cliente->tipo_documento) === $clave)>{{ $texto }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="{{ $label }}">Número</label>
                    <input type="text" name="numero_documento" value="{{ old('numero_documento', $cliente->numero_documento) }}" maxlength="30" class="{{ $input }} font-mono" placeholder="900.123.456-7">
                </div>
            </div>
        </div>

        {{-- Contacto --}}
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-primary mb-3 pb-2 border-b border-stroke dark:border-strokedark">
                <i class="fas fa-user-tie"></i> Contacto
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div>
                    <label class="{{ $label }}">Nombre del contacto</label>
                    <input type="text" name="contacto_nombre" value="{{ old('contacto_nombre', $cliente->contacto_nombre) }}" maxlength="120" class="{{ $input }}">
                </div>
                <div>
                    <label class="{{ $label }}">Cargo</label>
                    <input type="text" name="contacto_cargo" value="{{ old('contacto_cargo', $cliente->contacto_cargo) }}" maxlength="80" class="{{ $input }}">
                </div>
                <div>
                    <label class="{{ $label }}">Teléfono</label>
                    <input type="text" name="telefono" value="{{ old('telefono', $cliente->telefono) }}" maxlength="30" class="{{ $input }}">
                </div>
                <div>
                    <label class="{{ $label }}">Correo</label>
                    <input type="email" name="email" value="{{ old('email', $cliente->email) }}" maxlength="150" class="{{ $input }}">
                </div>
                <div class="sm:col-span-2">
                    <label class="{{ $label }}">Dirección</label>
                    <input type="text" name="direccion" value="{{ old('direccion', $cliente->direccion) }}" maxlength="200" class="{{ $input }}">
                </div>
                <div>
                    <label class="{{ $label }}">Ciudad</label>
                    <input type="text" name="ciudad" value="{{ old('ciudad', $cliente->ciudad) }}" maxlength="80" class="{{ $input }}">
                </div>
                <div class="flex items-end pb-1.5">
                    <label class="inline-flex items-center gap-2 text-xs text-black dark:text-white cursor-pointer">
                        <input type="hidden" name="activo" value="0">
                        <input type="checkbox" name="activo" value="1" @checked(old('activo', $cliente->activo)) class="rounded border-stroke">
                        Cliente activo
                    </label>
                </div>
            </div>
        </div>

        <div>
            <label class="{{ $label }}">Observaciones</label>
            <textarea name="observaciones" rows="3" maxlength="2000" class="{{ $input }}">{{ old('observaciones', $cliente->observaciones) }}</textarea>
        </div>
    </div>

    <div class="flex justify-end gap-2 border-t border-stroke dark:border-strokedark px-5 py-3">
        <button type="submit" class="inline-flex items-center gap-1.5 rounded bg-primary py-2 px-5 text-xs font-bold uppercase text-white hover:bg-opacity-90 shadow-sm">
            <i class="fas fa-save text-[10px]"></i> {{ $editing ? 'Guardar cambios' : 'Crear cliente' }}
        </button>
    </div>
</form>
