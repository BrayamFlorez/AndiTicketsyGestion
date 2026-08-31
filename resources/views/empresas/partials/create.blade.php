{{-- resources/views/empresas/partials/create.blade.php --}}
<template x-teleport="body">
    <div x-show="createOpen" x-cloak 
         class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 text-left"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="w-full max-w-lg rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark relative" 
             @click.outside="createOpen = false">
            
            {{-- Header del Modal --}}
            <div class="border-b border-stroke py-4 px-6 dark:border-strokedark flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-black dark:text-white uppercase tracking-wider text-xs flex items-center gap-2">
                        <i class="fas fa-building text-primary"></i> Nueva Empresa
                    </h3>
                    <p class="text-[11px] text-bodydark2 mt-0.5">
                        Registra una nueva entidad dentro de la plataforma.
                    </p>
                </div>
                <button type="button" @click="createOpen = false" class="text-bodydark2 hover:text-primary transition">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            {{-- Formulario --}}
            <form method="POST" action="{{ route('empresas.store') }}">
                @csrf
                
                <div class="p-6 space-y-4">
                    {{-- Nombre --}}
                    <div>
                        <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">
                            Nombre <span class="text-meta-1">*</span>
                        </label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}" required autofocus
                               placeholder="Ej: Acme Corporation S.A.S."
                               class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white @error('nombre') border-meta-1 @enderror">
                        @error('nombre') <span class="mt-0.5 block text-[10px] font-medium text-meta-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- NIT y Teléfono --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">NIT / Documento</label>
                            <input type="text" name="nit" value="{{ old('nit') }}" placeholder="900123456-1"
                                   class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white @error('nit') border-meta-1 @enderror">
                            @error('nit') <span class="mt-0.5 block text-[10px] font-medium text-meta-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Teléfono</label>
                            <input type="text" name="telefono" value="{{ old('telefono') }}" placeholder="(601) 555-0123"
                                   class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white @error('telefono') border-meta-1 @enderror">
                            @error('telefono') <span class="mt-0.5 block text-[10px] font-medium text-meta-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Correo Electrónico --}}
                    <div>
                        <label class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">Correo Electrónico</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="contacto@empresa.com"
                               class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white @error('email') border-meta-1 @enderror">
                        @error('email') <span class="mt-0.5 block text-[10px] font-medium text-meta-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Estado Activo --}}
                    <div class="pt-1">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" name="activo" value="1" @checked(old('activo', true))
                                   class="rounded border-stroke text-primary focus:ring-primary dark:border-form-strokedark dark:bg-form-input">
                            <span class="text-xs font-medium text-black dark:text-white">Empresa activa (permite crear sedes y tickets)</span>
                        </label>
                    </div>
                </div>

                {{-- Footer del Modal --}}
                <div class="flex items-center justify-end gap-2 bg-gray-2 dark:bg-meta-4/40 px-6 py-3 border-t border-stroke dark:border-strokedark">
                    <button type="button" @click="createOpen = false" 
                            class="inline-flex items-center justify-center rounded border border-stroke py-1.5 px-4 text-center text-xs font-medium text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="inline-flex items-center gap-1.5 rounded bg-primary py-1.5 px-4 text-center text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm cursor-pointer">
                        <i class="fas fa-save text-[10px]"></i> Guardar Empresa
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>