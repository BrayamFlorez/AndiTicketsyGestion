<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — {{ config('app.name', env('APP_NAME', 'Andi Tickets')) }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* Animación suave de Iris/Orb para el panel lateral de TailAdmin */
        .tech-iris-container {
            position: absolute; right: -15%; top: 10%; width: 600px; height: 600px;
            opacity: 0.12; pointer-events: none;
        }
        .tech-iris {
            width: 100%; height: 100%; border-radius: 50%;
            border: 2px dashed #3C50E0;
            position: relative; animation: spin 60s linear infinite;
        }
        .tech-iris::before {
            content: ''; position: absolute; inset: 10%; border-radius: 50%;
            border: 4px solid transparent; border-top-color: #3C50E0; border-bottom-color: #3C50E0;
            animation: spin 30s linear infinite reverse;
        }
        .tech-pupil {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
            width: 20%; height: 20%; background: #3C50E0; border-radius: 50%;
            box-shadow: 0 0 50px #3C50E0;
        }
        @keyframes spin { 100% { transform: rotate(360deg); } }
    </style>
</head>
<body class="bg-whiten dark:bg-black-2 text-body dark:text-bodydark antialiased font-satoshi min-h-screen flex overflow-hidden">

    {{-- Panel Izquierdo: Presentación TailAdmin --}}
    <div class="hidden lg:flex lg:w-1/2 bg-boxdark border-r border-strokedark relative flex-col justify-between p-12 overflow-hidden">
        
        <div class="tech-iris-container">
            <div class="tech-iris"><div class="tech-pupil"></div></div>
        </div>

        <div class="relative z-10">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 border border-primary/30 text-primary text-xs font-bold uppercase tracking-widest mb-8">
                <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                Software de control de servicios y tickets
            </div>
            
            <h2 class="text-4xl font-extrabold text-white leading-tight mb-4">
                Servicio <em class="italic text-primary">ágil,</em><br>atención precisa.
            </h2>
            <p class="text-bodydark max-w-md text-sm leading-relaxed">
                {{ env('APP_DESC', 'Portal de acceso exclusivo para el personal autorizado de la institución.') }}
            </p>
        </div>

        <div class="relative z-10 space-y-3 text-xs text-bodydark">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-boxdark-2 flex items-center justify-center border border-strokedark text-primary">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <span>{{ env('APP_ADDRESS', 'Dirección no configurada') }}</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-boxdark-2 flex items-center justify-center border border-strokedark text-primary">
                    <i class="fas fa-phone-alt"></i>
                </div>
                <span>{{ env('APP_PHONE', 'Teléfono no configurado') }}</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-boxdark-2 flex items-center justify-center border border-strokedark text-primary">
                    <i class="fas fa-clock"></i>
                </div>
                <span>{{ env('APP_SCHEDULE', 'Horario no configurado') }}</span>
            </div>
        </div>
    </div>

    {{-- Panel Derecho: Formulario Login TailAdmin --}}
    <div class="w-full lg:w-1/2 flex flex-col justify-center items-center p-6 sm:p-12 relative">
        
        <div class="w-full max-w-md">
            
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-primary text-white shadow-lg shadow-primary/20 mb-3">
                    <i class="fas fa-ticket-alt text-2xl"></i>
                </div>
                <h1 class="text-2xl font-black text-black dark:text-white uppercase tracking-wide">
                    {{ config('app.name', env('APP_NAME', 'Andi Tickets')) }}
                </h1>
                <p class="text-bodydark2 text-xs mt-1">Ingresa tus credenciales para acceder a la plataforma</p>
            </div>

            {{-- Errores de Validación --}}
            @if ($errors->any())
                <div class="bg-meta-1/10 border border-meta-1/30 text-meta-1 p-3.5 rounded-sm flex items-center gap-3 mb-6 text-xs font-medium">
                    <i class="fas fa-exclamation-circle text-base shrink-0"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                {{-- Correo Electrónico --}}
                <div>
                    <label for="email" class="block text-xs font-bold text-black dark:text-white uppercase tracking-wider mb-2">
                        Correo Electrónico
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-bodydark">
                            <i class="fas fa-envelope text-sm"></i>
                        </span>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                               placeholder="usuario@correo.com" autocomplete="email"
                               class="w-full rounded-sm border border-stroke bg-gray py-3 pl-10 pr-4 text-xs text-black focus:border-primary focus-visible:outline-none dark:border-strokedark dark:bg-boxdark dark:text-white dark:focus:border-primary transition">
                    </div>
                </div>

                {{-- Contraseña --}}
                <div>
                    <label for="password" class="block text-xs font-bold text-black dark:text-white uppercase tracking-wider mb-2">
                        Contraseña
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-bodydark">
                            <i class="fas fa-lock text-sm"></i>
                        </span>
                        <input type="password" id="password" name="password" required autocomplete="current-password"
                               placeholder="••••••••"
                               class="w-full rounded-sm border border-stroke bg-gray py-3 pl-10 pr-10 text-xs text-black focus:border-primary focus-visible:outline-none dark:border-strokedark dark:bg-boxdark dark:text-white dark:focus:border-primary transition">
                        
                        <button type="button" id="passToggle" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-bodydark hover:text-black dark:hover:text-white transition">
                            <i class="fas fa-eye text-xs" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                {{-- Opciones Auxiliares --}}
                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 cursor-pointer text-bodydark select-none">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}
                               class="rounded border-stroke bg-gray dark:border-strokedark dark:bg-boxdark text-primary focus:ring-primary">
                        <span>Recordar sesión</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-primary hover:underline font-medium">
                            ¿Olvidaste tu contraseña?
                        </a>
                    @endif
                </div>

                {{-- Botón Iniciar Sesión --}}
                <button type="submit" 
                        class="w-full bg-primary hover:bg-opacity-90 text-white font-bold py-3 rounded-sm flex items-center justify-center gap-2 text-xs uppercase tracking-wider transition shadow-md shadow-primary/20 cursor-pointer">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-stroke dark:border-strokedark text-center text-[11px] text-bodydark2 leading-relaxed">
                Acceso restringido al personal autorizado.<br>
                ¿Problemas para acceder? Contacta a 
                <a href="mailto:{{ env('APP_SUPPORT_EMAIL', 'soporte@correo.com') }}" class="text-primary hover:underline">soporte técnico</a>.
            </div>

        </div>
    </div>

    {{-- Script de Visibilidad de Contraseña --}}
    <script>
        const passInput = document.getElementById('password');
        const passToggle = document.getElementById('passToggle');
        const eyeIcon = document.getElementById('eyeIcon');

        passToggle.addEventListener('click', () => {
            if (passInput.type === 'password') {
                passInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        });
    </script>
</body>
</html>