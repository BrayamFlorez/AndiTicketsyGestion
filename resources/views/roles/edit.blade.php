{{-- resources/views/roles/edit.blade.php --}}
<x-app-layout>
    <x-slot name="title">Editar Rol</x-slot>

    {{-- Cabecera de la Página --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-title-md2 font-bold text-black dark:text-white flex items-center gap-2">
                <i class="fas fa-user-shield text-primary"></i> Modificar Privilegios de Rol
            </h2>
            <p class="text-xs text-bodydark2 mt-1">
                Editando: <strong class="text-primary font-mono uppercase">{{ $role->name }}</strong>
            </p>
        </div>

        <a href="{{ route('roles.index') }}" 
           class="inline-flex items-center justify-center gap-2 rounded border border-stroke py-2 px-4 text-center text-xs font-medium text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition shadow-sm">
            <i class="fas fa-arrow-left text-[10px]"></i> Volver
        </a>
    </div>

    {{-- Formulario Principal --}}
    <form method="POST" action="{{ route('roles.update', $role->id) }}" 
          class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
        @csrf
        @method('PUT')
        
        <div class="p-6 space-y-6">

            {{-- Identificador del Rol --}}
            <div class="max-w-md">
                <label for="name" class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">
                    Identificador del Rol <span class="text-meta-1">*</span>
                </label>
                <input type="text" id="name" name="name" value="{{ old('name', $role->name) }}" required
                       placeholder="EJ: TECNICO_SENIOR"
                       class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs font-mono uppercase outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white @error('name') border-meta-1 @enderror">
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>

            {{-- Matriz de Permisos --}}
            <div>
                <div class="mb-3 pb-2 border-b border-stroke dark:border-strokedark flex items-center justify-between">
                    <h3 class="font-bold text-black dark:text-white uppercase tracking-wider text-xs flex items-center gap-1.5">
                        <i class="fas fa-key text-primary"></i> Matriz de Permisos
                    </h3>
                    <span class="text-[10px] text-bodydark2 uppercase">
                        {{ count($rolePermissions) }} de {{ count($permissions) }} seleccionados
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($permissions as $permission)
                        @php
                            $isChecked = in_array($permission->name, $rolePermissions);
                        @endphp
                        <label class="relative flex items-center p-3.5 rounded border transition cursor-pointer select-none {{ $isChecked ? 'border-primary bg-primary/5 dark:bg-primary/10' : 'border-stroke bg-gray-2 dark:border-strokedark dark:bg-meta-4/20 hover:border-primary/50' }}">
                            <div class="flex h-5 items-center">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" 
                                       {{ $isChecked ? 'checked' : '' }}
                                       class="rounded border-stroke text-primary focus:ring-primary dark:border-form-strokedark dark:bg-form-input">
                            </div>
                            
                            <div class="ml-3 min-w-0 flex-1">
                                <span class="text-xs font-bold text-black dark:text-white block truncate">
                                    {{ str_replace('.', ' › ', $permission->name) }}
                                </span>
                            </div>

                            @if($isChecked)
                                <span class="text-primary text-xs ml-2">
                                    <i class="fas fa-check-circle"></i>
                                </span>
                            @endif
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Nota Informativa --}}
            <div class="rounded border border-primary/20 bg-primary/5 p-4 dark:bg-primary/10">
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary/20 text-primary shrink-0">
                        <i class="fas fa-info-circle text-sm"></i>
                    </div>
                    <p class="text-xs font-medium text-black dark:text-white">
                        <strong>Nota importante:</strong> Los cambios aplicados a esta matriz se reflejarán de manera inmediata en todos los usuarios activos que tengan asignado este rol.
                    </p>
                </div>
            </div>

        </div>

        {{-- Footer de Acciones --}}
        <div class="flex items-center justify-end gap-2 bg-gray-2 dark:bg-meta-4/40 px-6 py-3 border-t border-stroke dark:border-strokedark">
            <a href="{{ route('roles.index') }}" 
               class="inline-flex items-center justify-center rounded border border-stroke py-1.5 px-4 text-center text-xs font-medium text-black hover:bg-gray dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition">
                Cancelar
            </a>
            <button type="submit" 
                    class="inline-flex items-center gap-1.5 rounded bg-primary py-1.5 px-5 text-center text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm cursor-pointer">
                <i class="fas fa-sync-alt text-[10px]"></i> Actualizar Rol
            </button>
        </div>

    </form>
</x-app-layout>