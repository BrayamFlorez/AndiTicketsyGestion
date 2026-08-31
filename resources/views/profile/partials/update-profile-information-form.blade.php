{{-- resources/views/profile/partials/update-profile-information-form.blade.php --}}
<div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
    
    {{-- Header de la Card --}}
    <div class="border-b border-stroke py-3.5 px-6 dark:border-strokedark flex items-center gap-3">
        <div class="flex h-8 w-8 items-center justify-center rounded bg-primary/10 text-primary shrink-0">
            <i class="fas fa-id-card text-xs"></i>
        </div>
        <div>
            <h3 class="font-bold text-black dark:text-white uppercase tracking-wider text-xs">
                {{ __('Información del Perfil') }}
            </h3>
            <p class="text-[10px] text-bodydark2">
                {{ __('Gestione los datos básicos de su cuenta y dirección de correo institucional.') }}
            </p>
        </div>
    </div>

    {{-- Formulario Oculto para Verificación --}}
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <div class="p-6">
        <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf
            @method('patch')

            {{-- Nombre Completo --}}
            <div>
                <label for="name" class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">
                    {{ __('Nombre Completo') }} <span class="text-meta-1">*</span>
                </label>
                <input id="name" name="name" type="text" 
                       value="{{ old('name', $user->name) }}" required autofocus autocomplete="name"
                       class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white @error('name') border-meta-1 @enderror">
                
                <x-input-error class="mt-1 text-[10px] text-meta-1 font-medium" :messages="$errors->get('name')" />
            </div>

            {{-- Correo Electrónico --}}
            <div>
                <label for="email" class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">
                    {{ __('Correo Institucional / Personal') }} <span class="text-meta-1">*</span>
                </label>
                <input id="email" name="email" type="email" 
                       value="{{ old('email', $user->email) }}" required autocomplete="username"
                       class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white @error('email') border-meta-1 @enderror">
                
                <x-input-error class="mt-1 text-[10px] text-meta-1 font-medium" :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-3 p-3 bg-meta-6/10 rounded-sm border border-meta-6/30 text-meta-6 text-xs">
                        <p class="font-bold uppercase tracking-tight flex items-center gap-1.5 text-[11px]">
                            <i class="fas fa-exclamation-triangle"></i> {{ __('Su correo no ha sido verificado.') }}
                        </p>
                        <button form="send-verification" class="mt-1 underline text-xs font-semibold hover:text-black dark:hover:text-white transition cursor-pointer">
                            {{ __('Presione aquí para re-enviar el enlace de verificación.') }}
                        </button>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-1.5 font-bold text-[10px] text-meta-3 uppercase flex items-center gap-1">
                                <i class="fas fa-paper-plane"></i> {{ __('Se ha enviado un nuevo enlace a su correo.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Botón y Notificación --}}
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" 
                        class="inline-flex items-center gap-1.5 rounded bg-primary py-1.5 px-4 text-center text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm cursor-pointer">
                    <i class="fas fa-save text-[10px]"></i> {{ __('Guardar Información') }}
                </button>

                @if (session('status') === 'profile-updated')
                    <span x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                          class="text-xs font-bold text-meta-3 bg-meta-3/10 px-2.5 py-1 rounded border border-meta-3/20">
                        <i class="fas fa-check-circle mr-1"></i> {{ __('Guardado correctamente') }}
                    </span>
                @endif
            </div>

        </form>
    </div>
</div>