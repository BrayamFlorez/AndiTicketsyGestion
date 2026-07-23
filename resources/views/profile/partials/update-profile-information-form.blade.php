<section class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <header class="bg-gray-50 p-6 border-b border-gray-100 flex items-center space-x-4">
        <div class="w-10 h-10 bg-vscode-dark rounded-lg flex items-center justify-center text-white">
            <i class="fas fa-id-card text-[#007acc]"></i>
        </div>
        <div>
            <h2 class="text-lg font-bold text-[#1e1e1e] uppercase tracking-tighter">
                {{ __('Información del Perfil') }}
            </h2>
            <p class="mt-1 text-xs font-medium text-gray-500 uppercase tracking-widest">
                {{ __("Gestione los datos básicos de su cuenta y dirección de correo.") }}
            </p>
        </div>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <div class="p-8">
        <form method="post" action="{{ route('profile.update') }}" class="max-w-xl space-y-6">
            @csrf
            @method('patch')

            <div class="group">
                <label for="name" class="block text-[10px] font-black text-gray-400 uppercase tracking-[1px] mb-2 group-focus-within:text-[#007acc] transition-colors">
                    {{ __('Nombre Completo') }}
                </label>
                <x-text-input id="name" name="name" type="text" 
                    class="mt-1 block w-full border-gray-200 focus:border-[#007acc] focus:ring-[#007acc] rounded-md shadow-sm transition" 
                    :value="old('name', $user->name)" required autofocus autocomplete="name" />
                <x-input-error class="mt-2 text-xs font-bold" :messages="$errors->get('name')" />
            </div>

            <div class="group">
                <label for="email" class="block text-[10px] font-black text-gray-400 uppercase tracking-[1px] mb-2 group-focus-within:text-[#007acc] transition-colors">
                    {{ __('Correo Institucional / Personal') }}
                </label>
                <x-text-input id="email" name="email" type="email" 
                    class="mt-1 block w-full border-gray-200 focus:border-[#007acc] focus:ring-[#007acc] rounded-md shadow-sm transition" 
                    :value="old('email', $user->email)" required autocomplete="username" />
                <x-input-error class="mt-2 text-xs font-bold" :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-4 p-4 bg-yellow-50 rounded-lg border border-yellow-100 text-yellow-800">
                        <p class="text-xs font-bold uppercase tracking-tight">
                            <i class="fas fa-exclamation-triangle mr-1"></i> {{ __('Su correo no ha sido verificado.') }}
                        </p>
                        <button form="send-verification" class="mt-2 underline text-xs font-semibold hover:text-yellow-600 transition">
                            {{ __('Presione aquí para re-enviar el enlace de verificación.') }}
                        </button>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 font-black text-[10px] text-green-600 uppercase">
                                <i class="fas fa-paper-plane mr-1"></i> {{ __('Se ha enviado un nuevo enlace a su correo.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-4 pt-4 border-t border-gray-50">
                <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-[#007acc] border border-transparent rounded-md font-bold text-xs text-white uppercase tracking-widest hover:bg-[#005a9e] active:bg-[#004a80] transition shadow-md">
                    <i class="fas fa-save mr-2"></i> {{ __('Guardar Información') }}
                </button>

                @if (session('status') === 'profile-updated')
                    <div
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform translate-x-4"
                        x-init="setTimeout(() => show = false, 3000)"
                        class="flex items-center text-xs font-bold text-green-600 bg-green-50 px-4 py-2 rounded-lg border border-green-100"
                    >
                        <i class="fas fa-check-circle mr-2"></i> {{ __('Datos actualizados.') }}
                    </div>
                @endif
            </div>
        </form>
    </div>
</section>