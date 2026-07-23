{{-- resources/views/empresas/partials/edit.blade.php --}}
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
                <h3>Editar Empresa</h3>
                <p>Modificando los datos de: <strong class="text-white">{{ $empresa->nombre }}</strong></p>
            </div>
            <button @click="editOpen = false" class="text-gray-400 hover:text-white">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('empresas.update', $empresa) }}">
            @csrf
            @method('PUT')
            
            {{-- Input oculto para que Laravel sepa cuál modal reabrir si hay errores --}}
            <input type="hidden" name="empresa_id" value="{{ $empresa->id }}">
            
            <div class="form-card-body space-y-4">
                <div class="form-group">
                    <label class="form-label">Nombre <span class="required">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre', $empresa->nombre) }}" class="form-input" required>
                    @error('nombre') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">NIT / Documento</label>
                        <input type="text" name="nit" value="{{ old('nit', $empresa->nit) }}" class="form-input">
                        @error('nit') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" value="{{ old('telefono', $empresa->telefono) }}" class="form-input">
                        @error('telefono') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Correo Electrónico</label>
                    <input type="email" name="email" value="{{ old('email', $empresa->email) }}" class="form-input">
                    @error('email') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group mt-2">
                    <label class="flex items-center gap-2" style="color: var(--db-text); font-size: 0.85rem; cursor: pointer;">
                        <input type="checkbox" name="activo" value="1" @checked(old('activo', $empresa->activo))>
                        <span>Empresa activa</span>
                    </label>
                </div>
            </div>

            <div class="form-card-footer">
                <button type="button" @click="editOpen = false" class="db-btn ghost">Cancelar</button>
                <button type="submit" class="db-btn solid">Actualizar empresa</button>
            </div>
        </form>
    </div>
</div>