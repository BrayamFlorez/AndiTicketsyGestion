{{-- resources/views/profile/partials/update-password-form.blade.php --}}
<div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
    
    <div class="border-b border-stroke py-3.5 px-6 dark:border-strokedark flex items-center gap-3">
        <div class="flex h-8 w-8 items-center justify-center rounded bg-primary/10 text-primary shrink-0">
            <i class="fas fa-key text-xs"></i>
        </div>
        <div>
            <h3 class="font-bold text-black dark:text-white uppercase tracking-wider text-xs">
                {{ __('Actualizar Contraseña') }}
            </h3>
            <p class="text-[10px] text-bodydark2">
                {{ __('Asegúrese de utilizar una clave robusta para mantener su cuenta protegida.') }}
            </p>
        </div>
    </div>

    <div class="p-6">
        <form method="post" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            @method('put')

            <div>
                <label for="update_password_current_password" class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">
                    {{ __('Contraseña Actual') }} <span class="text-meta-1">*</span>
                </label>
                <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password"
                       class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                @if ($errors->updatePassword->get('current_password'))
                    <p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $errors->updatePassword->first('current_password') }}</p>
                @endif
            </div>

            <div>
                <label for="update_password_password" class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">
                    {{ __('Nueva Contraseña') }} <span class="text-meta-1">*</span>
                </label>
                <input id="update_password_password" name="password" type="password" autocomplete="new-password"
                       class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                @if ($errors->updatePassword->get('password'))
                    <p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $errors->updatePassword->first('password') }}</p>
                @endif
            </div>

            <div>
                <label for="update_password_password_confirmation" class="mb-1 block text-[10px] font-bold uppercase text-black dark:text-white">
                    {{ __('Confirmar Contraseña') }} <span class="text-meta-1">*</span>
                </label>
                <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                       class="w-full rounded border border-stroke bg-transparent py-1.5 px-3 text-xs outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white">
                @if ($errors->updatePassword->get('password_confirmation'))
                    <p class="mt-0.5 text-[10px] text-meta-1 font-medium">{{ $errors->updatePassword->first('password_confirmation') }}</p>
                @endif
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" 
                        class="inline-flex items-center gap-1.5 rounded bg-primary py-1.5 px-4 text-center text-xs font-bold uppercase tracking-wider text-white hover:bg-opacity-90 transition shadow-sm cursor-pointer">
                    <i class="fas fa-lock text-[10px]"></i> {{ __('Actualizar Contraseña') }}
                </button>

                @if (session('status') === 'password-updated')
                    <span x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                          class="text-xs font-bold text-meta-3 bg-meta-3/10 px-2.5 py-1 rounded border border-meta-3/20">
                        <i class="fas fa-check-circle mr-1"></i> {{ __('Actualizado') }}
                    </span>
                @endif
            </div>
        </form>
    </div>
</div>