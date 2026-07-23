<x-app-layout>
    @if (session('status') === 'rol-eliminado')
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
            class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 flex items-center shadow-sm rounded-r-lg">
            <i class="fas fa-check-circle mr-3"></i>
            <p class="text-xs font-bold uppercase tracking-widest text-balance">El rol ha sido revocado y eliminado del sistema correctamente.</p>
        </div>
    @endif

    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
            class="mb-6 p-4 bg-amber-50 border-l-4 border-amber-500 text-amber-700 flex items-center shadow-sm rounded-r-lg">
            <i class="fas fa-exclamation-triangle mr-3"></i>
            <p class="text-xs font-bold uppercase tracking-widest">{{ session('error') }}</p>
        </div>
    @endif
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-2xl font-black text-[#1e1e1e] uppercase tracking-tighter">Matriz de Roles</h2>
            <p class="text-xs text-gray-500 font-medium uppercase tracking-widest">Configuración de niveles de seguridad
            </p>
        </div>
        @can('roles.gestionar')
            <a href="{{ route('roles.create') }}"
                class="inline-flex items-center bg-[#1e1e1e] hover:bg-black text-white px-6 py-3 rounded-lg text-xs font-bold uppercase tracking-widest transition-all shadow-lg border-b-4 border-[#007acc] transform hover:-translate-y-0.5">
                <i class="fas fa-shield-alt mr-2 text-sm text-[#007acc]"></i> Definir Nuevo Rol
            </a>
        @endcan
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($roles as $role)
            <div
                class="bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition group overflow-hidden">
                <div class="p-5 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-black text-vscode-blue uppercase text-sm">{{ $role->name }}</h3>
                    <div class="flex space-x-2">
                        @can('roles.gestionar')
                            <a href="{{ route('roles.edit', $role) }}"
                                class="w-8 h-8 flex items-center justify-center rounded bg-gray-100 text-gray-500 hover:bg-vscode-blue hover:text-white transition-all duration-300 shadow-sm"
                                title="Editar Configuración">
                                <i class="fas fa-cog text-xs"></i>
                            </a>
                        @endcan

                        @can('roles.gestionar')
                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST"
                                onsubmit="return confirm('¿Está seguro de eliminar este rol? Esta acción podría afectar el acceso de los usuarios vinculados.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-8 h-8 flex items-center justify-center rounded bg-gray-100 text-gray-400 hover:bg-red-600 hover:text-white transition-all duration-300 shadow-sm"
                                    title="Eliminar Rol">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>

                <div class="p-5">
                    <p class="text-[10px] font-black text-gray-400 uppercase mb-3">Permisos Habilitados</p>
                    <div class="flex flex-wrap gap-2">
                        @forelse($role->permissions as $permission)
                            <span
                                class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-[9px] font-bold border border-gray-200">
                                {{ str_replace('.', ':', $permission->name) }}
                            </span>
                        @empty
                            <span class="text-xs text-gray-400 italic font-medium tracking-tighter">Sin permisos
                                asignados</span>
                        @endforelse
                    </div>
                </div>
                <div class="px-5 py-3 bg-white border-t border-gray-50 text-[10px] font-bold text-gray-400">
                    {{ $role->users->count() }} Usuarios vinculados
                </div>
            </div>
        @endforeach
    </div>
</x-app-layout>
