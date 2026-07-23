{{-- resources/views/sedes/partials/edit.blade.php --}}
<div x-show="editOpen" class="fixed inset-0 z-50 flex items-center justify-center modal-backdrop p-4 text-left" x-cloak
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    
    <div class="form-card w-full max-w-lg bg-[#1e1e1e] shadow-2xl relative" @click.away="editOpen = false">
        
        <div class="form-card-header flex justify-between items-center">
            <div>
                <h3>Editar Sede</h3>
                <p>Modificando: <strong class="text-white">{{ $sede->nombre }}</strong></p>
            </div>
            <button @click="editOpen = false" class="text-gray-400 hover:text-white">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('sedes.update', $sede) }}">
            @csrf
            @method('PUT')
            
            <input type="hidden" name="sede_id" value="{{ $sede->id }}">
            
            <div class="form-card-body space-y-4">
                
                <div class="form-group">
                    <label class="form-label">Empresa a la que pertenece <span class="required">*</span></label>
                    <select name="empresa_id" class="form-select" required>
                        @foreach (App\Models\Empresa::activas()->orderBy('nombre')->get(['id', 'nombre']) as $emp)
                            <option value="{{ $emp->id }}" @selected(old('empresa_id', $sede->empresa_id) == $emp->id)>
                                {{ $emp->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('empresa_id') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Nombre de la Sede <span class="required">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre', $sede->nombre) }}" class="form-input" required>
                    @error('nombre') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">Ciudad</label>
                        <input type="text" name="ciudad" value="{{ old('ciudad', $sede->ciudad) }}" class="form-input">
                        @error('ciudad') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" value="{{ old('telefono', $sede->telefono) }}" class="form-input">
                        @error('telefono') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="direccion" value="{{ old('direccion', $sede->direccion) }}" class="form-input">
                    @error('direccion') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group mt-2">
                    <label class="flex items-center gap-2" style="color: var(--db-text); font-size: 0.85rem; cursor: pointer;">
                        <input type="checkbox" name="activo" value="1" @checked(old('activo', $sede->activo))>
                        <span>Sede activa</span>
                    </label>
                </div>
            </div>

            <div class="form-card-footer">
                <button type="button" @click="editOpen = false" class="db-btn ghost">Cancelar</button>
                <button type="submit" class="db-btn solid">Actualizar sede</button>
            </div>
        </form>
    </div>
</div>