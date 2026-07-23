<section class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <header class="bg-gray-50 p-6 border-b border-gray-100 flex items-center space-x-4">
        <div class="w-10 h-10 bg-vscode-dark rounded-lg flex items-center justify-center text-white">
            <i class="fas fa-key text-[#007acc]"></i>
        </div>
        <div>
            <h2 class="text-lg font-bold text-[#1e1e1e] uppercase tracking-tighter">
                {{ __('Actualizar Contraseña') }}
            </h2>
            <p class="mt-1 text-xs font-medium text-gray-500 uppercase tracking-widest">
                {{ __('Asegúrese de usar una clave robusta para proteger el acceso clínico.') }}
            </p>
        </div>
    </header>

    <div class="p-8">
        <form method="post" action="{{ route('password.update') }}" class="max-w-xl space-y-6">
            @csrf
            @method('put')

            <div class="group">
                <label for="update_password_current_password" class="block text-[10px] font-black text-gray-400 uppercase tracking-[1px] mb-2 group-focus-within:text-[#007acc] transition-colors">
                    {{ __('Contraseña Actual') }}
                </label>
                <x-text-input id="update_password_current_password" name="current_password" type="password" 
                    class="mt-1 block w-full border-gray-200 focus:border-[#007acc] focus:ring-[#007acc] rounded-md shadow-sm transition" 
                    autocomplete="current-password" />
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 text-xs font-bold" />
            </div>

            <div class="group">
                <label for="update_password_password" class="block text-[10px] font-black text-gray-400 uppercase tracking-[1px] mb-2 group-focus-within:text-[#007acc] transition-colors">
                    {{ __('Nueva Contraseña') }}
                </label>
                <x-text-input id="update_password_password" name="password" type="password" 
                    class="mt-1 block w-full border-gray-200 focus:border-[#007acc] focus:ring-[#007acc] rounded-md shadow-sm transition" 
                    autocomplete="new-password" />
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-xs font-bold" />
            </div>

            <div class="group">
                <label for="update_password_password_confirmation" class="block text-[10px] font-black text-gray-400 uppercase tracking-[1px] mb-2 group-focus-within:text-[#007acc] transition-colors">
                    {{ __('Confirmar Nueva Contraseña') }}
                </label>
                <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" 
                    class="mt-1 block w-full border-gray-200 focus:border-[#007acc] focus:ring-[#007acc] rounded-md shadow-sm transition" 
                    autocomplete="new-password" />
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 text-xs font-bold" />
            </div>

            <div class="flex items-center gap-4 pt-4 border-t border-gray-50">
                <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-[#007acc] border border-transparent rounded-md font-bold text-xs text-white uppercase tracking-widest hover:bg-[#005a9e] active:bg-[#004a80] focus:outline-none focus:ring-2 focus:ring-[#007acc] focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
                    <i class="fas fa-save mr-2"></i> {{ __('Guardar Cambios') }}
                </button>

                @if (session('status') === 'password-updated')
                    <div
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform translate-x-4"
                        x-init="setTimeout(() => show = false, 3000)"
                        class="flex items-center text-sm font-bold text-green-600 bg-green-50 px-4 py-2 rounded-lg border border-green-100"
                    >
                        <i class="fas fa-check-circle mr-2"></i> {{ __('Actualizado con éxito.') }}
                    </div>
                @endif
            </div>
        </form>
    </div>
</section>