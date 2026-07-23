<x-app-layout>
    <div class="max-w-5xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <header class="bg-vscode-dark p-6 text-white">
            <h2 class="font-bold uppercase tracking-tighter flex items-center">
                <i class="fas fa-user-shield text-vscode-blue mr-3"></i> Definir Nuevo Rol y Atribuciones
            </h2>
        </header>

        <form method="POST" action="{{ route('roles.store') }}" class="p-8">
            @csrf
            
            <div class="mb-8 max-w-md">
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">Identificador del Rol (Ej: auditor)</label>
                <input type="text" name="name" placeholder="nombre-del-rol" class="w-full border-gray-200 focus:border-vscode-blue focus:ring-vscode-blue rounded-md shadow-sm font-mono text-sm" required>
            </div>

            <div class="mb-4">
                <h3 class="text-sm font-bold text-gray-800 mb-4 border-b pb-2 uppercase tracking-tight">Matriz de Permisos</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($permissions as $permission)
                        <label class="relative flex items-center p-3 rounded-lg border border-gray-100 hover:bg-gray-50 cursor-pointer transition group">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" 
                                   class="rounded border-gray-300 text-vscode-blue focus:ring-vscode-blue w-5 h-5">
                            <span class="ml-3 text-xs font-bold text-gray-600 group-hover:text-vscode-blue uppercase tracking-tighter">
                                {{ str_replace('.', ' › ', $permission->name) }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="pt-8 mt-6 border-t border-gray-100 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center text-amber-600 bg-amber-50 px-4 py-2 rounded-lg border border-amber-100">
                    <i class="fas fa-exclamation-triangle mr-3 text-sm"></i>
                    <p class="text-[11px] font-bold uppercase tracking-tight italic">
                        Precaución: Asignar permisos con responsabilidad institucional.
                    </p>
                </div>

                <button type="submit" 
                    class="w-full md:w-auto inline-flex items-center justify-center bg-[#007acc] hover:bg-[#005a9e] text-white px-10 py-4 rounded-md font-black text-xs uppercase tracking-[2px] transition-all duration-300 shadow-[0_10px_20px_rgba(0,122,204,0.3)] hover:shadow-[0_15px_25px_rgba(0,122,204,0.4)] transform hover:-translate-y-1 active:scale-95">
                    <i class="fas fa-check-double mr-2"></i>
                    Confirmar y Crear Rol
                </button>
            </div>
        </form>
    </div>
</x-app-layout>