<x-app-layout>
    <div class="max-w-5xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <header class="bg-[#1e1e1e] p-6 text-white flex justify-between items-center border-b-4 border-[#007acc]">
            <div class="flex items-center">
                <i class="fas fa-user-shield text-[#007acc] mr-4 text-xl"></i>
                <div>
                    <h2 class="font-bold uppercase tracking-tighter">Modificar Privilegios de Rol</h2>
                    <p class="text-[10px] text-gray-400 uppercase tracking-widest mt-1">Editando: <span class="text-[#007acc]">{{ $role->name }}</span></p>
                </div>
            </div>
            <a href="{{ route('roles.index') }}" class="text-xs font-bold text-gray-400 hover:text-white transition uppercase tracking-widest">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
        </header>

        <form method="POST" action="{{ route('roles.update', $role->id) }}" class="p-8">
            @csrf
            @method('PUT')
            
            <div class="mb-10 max-w-md">
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">Identificador del Rol</label>
                <input type="text" name="name" value="{{ old('name', $role->name) }}" 
                    class="w-full border-gray-200 focus:border-[#007acc] focus:ring-[#007acc] rounded-md shadow-sm font-mono text-sm uppercase" required>
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="mb-4">
                <div class="flex items-center mb-6 border-b border-gray-100 pb-2">
                    <h3 class="text-xs font-black text-[#1e1e1e] uppercase tracking-[2px]">Ajuste de Matriz de Permisos</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($permissions as $permission)
                        <label class="relative flex items-center p-4 rounded-xl border border-gray-100 hover:border-[#007acc]/30 hover:bg-blue-50/30 cursor-pointer transition-all group {{ in_array($permission->name, $rolePermissions) ? 'bg-blue-50 border-[#007acc]/20' : '' }}">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" 
                                    {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-[#007acc] focus:ring-[#007acc] w-5 h-5 transition-all">
                            </div>
                            <div class="ml-4">
                                <span class="text-[11px] font-black text-gray-600 group-hover:text-[#007acc] uppercase tracking-tighter transition-colors">
                                    {{ str_replace('.', ' › ', $permission->name) }}
                                </span>
                            </div>
                            
                            @if(in_array($permission->name, $rolePermissions))
                                <div class="absolute top-2 right-2">
                                    <i class="fas fa-check-circle text-[#007acc] text-[10px]"></i>
                                </div>
                            @endif
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="pt-8 mt-10 border-t border-gray-100 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center text-blue-600 bg-blue-50 px-5 py-3 rounded-lg border border-blue-100 max-w-md">
                    <i class="fas fa-info-circle mr-3 text-lg"></i>
                    <p class="text-[10px] font-bold uppercase tracking-tight leading-relaxed">
                        Nota: Los cambios en los permisos se aplicarán a todos los usuarios vinculados a este rol de forma inmediata.
                    </p>
                </div>

                <button type="submit" 
                    class="w-full md:w-auto inline-flex items-center justify-center bg-[#007acc] hover:bg-[#005a9e] text-white px-12 py-4 rounded-md font-black text-xs uppercase tracking-[2px] transition-all duration-300 shadow-[0_10px_20px_rgba(0,122,204,0.3)] hover:shadow-[0_15px_25px_rgba(0,122,204,0.4)] transform hover:-translate-y-1 active:scale-95">
                    <i class="fas fa-sync-alt mr-2 animate-hover"></i>
                    Actualizar Rol Institucional
                </button>
            </div>
        </form>
    </div>
</x-app-layout>