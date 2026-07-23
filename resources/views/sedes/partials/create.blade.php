{{-- resources/views/sedes/partials/create.blade.php --}}
<div x-show="createOpen" class="fixed inset-0 z-50 flex items-center justify-center modal-backdrop p-4" x-cloak
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    
    <div class="form-card w-full max-w-lg bg-[#1e1e1e] shadow-2xl relative" @click.away="createOpen = false">
        
        <div class="form-card-header flex justify-between items-center">
            <div>
                <h3>Nueva Sede</h3>
                <p>Registra una nueva sucursal o ubicación.</p>
            </div>
            <button @click="createOpen = false" class="text-gray-400 hover:text-white">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('sedes.store') }}">
            @csrf
            
            <div class="form-card-body space-y-4">
                
                {{-- Selector Inteligente de Empresa --}}
                <div class="form-group">
                    <label class="form-label">Empresa a la que pertenece <span class="required">*</span></label>
                    
                    @if(isset($empresaDefecto))
                        {{-- Modo Inyectado: Se usa cuando llamamos el partial desde el perfil de Empresa --}}
                        <input type="hidden" name="empresa_id" value="{{ $empresaDefecto->id }}">
                        <input type="text" value="{{ $empresaDefecto->nombre }}" class="form-input" disabled>
                    @else
                        {{-- Modo Normal: Se usa en el listado general de sedes --}}
                        <select name="empresa_id" class="form-select" required>
                            <option value="">— Seleccionar empresa —</option>
                            @foreach ($empresas as $empresa)
                                <option value="{{ $empresa->id }}" @selected(old('empresa_id') == $empresa->id)>
                                    {{ $empresa->nombre }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                    @error('empresa_id') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Nombre de la Sede <span class="required">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" class="form-input" placeholder="Ej: Sede Principal, Sucursal Norte..." required>
                    @error('nombre') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">Ciudad</label>
                        <input type="text" name="ciudad" value="{{ old('ciudad') }}" class="form-input">
                        @error('ciudad') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" value="{{ old('telefono') }}" class="form-input">
                        @error('telefono') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="direccion" value="{{ old('direccion') }}" class="form-input">
                    @error('direccion') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group mt-2">
                    <label class="flex items-center gap-2" style="color: var(--db-text); font-size: 0.85rem; cursor: pointer;">
                        <input type="checkbox" name="activo" value="1" @checked(old('activo', true))>
                        <span>Sede activa (permite registrar tickets aquí)</span>
                    </label>
                </div>
            </div>

            <div class="form-card-footer">
                <button type="button" @click="createOpen = false" class="db-btn ghost">Cancelar</button>
                <button type="submit" class="db-btn solid">Guardar sede</button>
            </div>
        </form>
    </div>
</div>