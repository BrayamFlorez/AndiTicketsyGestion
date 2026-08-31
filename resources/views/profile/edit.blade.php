{{-- resources/views/profile/edit.blade.php --}}
<x-app-layout>
    <x-slot name="title">Configuración del Perfil</x-slot>

    {{-- Cabecera del Módulo --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-title-md2 font-bold text-black dark:text-white flex items-center gap-2">
                <i class="fas fa-user-cog text-primary"></i> {{ __('Configuración de la Cuenta') }}
            </h2>
            <p class="text-xs text-bodydark2 mt-1">
                {{ __('Gestione su información personal, credenciales de acceso y seguridad.') }}
            </p>
        </div>
    </div>

    {{-- Layout Apretado / Compacto en Ancho (Max 800px) --}}
    <div class="max-w-4xl mx-auto space-y-6">
        
        {{-- Formulario 1: Información del Perfil --}}
        <div>
            @include('profile.partials.update-profile-information-form')
        </div>

        {{-- Formulario 2: Cambiar Contraseña --}}
        <div>
            @include('profile.partials.update-password-form')
        </div>

        {{-- Formulario 3: Eliminar Cuenta (Si aplica) --}}
        @if (view()->exists('profile.partials.delete-user-form'))
            <div>
                @include('profile.partials.delete-user-form')
            </div>
        @endif

    </div>
</x-app-layout>