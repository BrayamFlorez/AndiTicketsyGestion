<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — {{ env('APP_NAME', 'Sistema Clínico') }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        body { font-family: 'DM Sans', sans-serif; background-color: #121212; color: #cccccc; }
        h1, h2, .logo-name { font-family: 'Playfair Display', serif; }
        
        /* Efecto tecnológico del Iris para el panel izquierdo */
        .tech-iris-container {
            position: absolute; right: -15%; top: 10%; width: 600px; height: 600px;
            opacity: 0.15; pointer-events: none;
        }
        .tech-iris {
            width: 100%; height: 100%; border-radius: 50%;
            border: 2px dashed #007acc;
            position: relative; animation: spin 60s linear infinite;
        }
        .tech-iris::before {
            content: ''; position: absolute; inset: 10%; border-radius: 50%;
            border: 4px solid transparent; border-top-color: #007acc; border-bottom-color: #007acc;
            animation: spin 30s linear infinite reverse;
        }
        .tech-pupil {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
            width: 20%; height: 20%; background: #007acc; border-radius: 50%;
            box-shadow: 0 0 50px #007acc;
        }
        @keyframes spin { 100% { transform: rotate(360deg); } }
    </style>
</head>
<body class="antialiased min-h-screen flex overflow-hidden">

    <div class="hidden lg:flex lg:w-1/2 bg-[#1e1e1e] border-r border-gray-800 relative flex-col justify-between p-12 overflow-hidden">
        
        <div class="tech-iris-container">
            <div class="tech-iris"><div class="tech-pupil"></div></div>
        </div>

        <div class="relative z-10">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#007acc]/10 border border-[#007acc]/30 text-[#007acc] text-xs font-bold uppercase tracking-widest mb-8">
                <span class="w-2 h-2 rounded-full bg-[#007acc] animate-pulse"></span>
                Software de control de servicios y tickets
            </div>
            
            <h2 class="text-5xl font-bold text-white leading-tight mb-4">
                Servicio <em class="italic text-[#007acc]">agil,</em><br>atención precisa.
            </h2>
            <p class="text-gray-400 text-lg max-w-md">
                {{ env('APP_DESC', 'Portal de acceso exclusivo para el personal autorizado de la institución.') }}
            </p>
        </div>

        <div class="relative z-10 space-y-4 text-sm text-gray-400">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded bg-[#121212] flex items-center justify-center border border-gray-800 text-[#007acc]">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                {{ env('APP_ADDRESS', 'Dirección no configurada') }}
            </div>
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded bg-[#121212] flex items-center justify-center border border-gray-800 text-[#007acc]">
                    <i class="fas fa-phone-alt"></i>
                </div>
                {{ env('APP_PHONE', 'Teléfono no configurado') }}
            </div>
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded bg-[#121212] flex items-center justify-center border border-gray-800 text-[#007acc]">
                    <i class="fas fa-clock"></i>
                </div>
                {{ env('APP_SCHEDULE', 'Horario no configurado') }}
            </div>
        </div>
    </div>

    <div class="w-full lg:w-1/2 flex flex-col justify-center items-center p-8 sm:p-12 relative">
        
        <div class="w-full max-w-md">
            
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-[#1e1e1e] border-2 border-[#007acc] text-white shadow-lg shadow-[#007acc]/20 mb-4">
                    <i class="fas fa-eye text-2xl"></i>
                </div>
                <div class="logo-name text-2xl text-white font-bold tracking-wide">
                    {{ env('APP_NAME', 'Sistema Clínico') }}
                </div>
                <p class="text-gray-500 text-sm mt-1">Ingresa tus credenciales para continuar</p>
            </div>

            @if ($errors->any())
                <div class="bg-red-500/10 border border-red-500/30 text-red-400 p-4 rounded-lg flex items-center gap-3 mb-6 text-sm font-medium">
                    <i class="fas fa-exclamation-circle text-lg"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-bold text-gray-300 uppercase tracking-widest mb-1.5">Correo electrónico</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                            placeholder="usuario@correo.com" autocomplete="email"
                            class="w-full bg-[#1e1e1e] border border-gray-800 text-white rounded-lg py-3 pl-10 pr-4 focus:outline-none focus:border-[#007acc] focus:ring-1 focus:ring-[#007acc] transition-colors placeholder-gray-600">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-gray-300 uppercase tracking-widest mb-1.5">Contraseña</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                            <i class="fas fa-lock"></i>
                        </div>
                        <input type="password" id="password" name="password" required autocomplete="current-password"
                            placeholder="••••••••"
                            class="w-full bg-[#1e1e1e] border border-gray-800 text-white rounded-lg py-3 pl-10 pr-10 focus:outline-none focus:border-[#007acc] focus:ring-1 focus:ring-[#007acc] transition-colors placeholder-gray-600">
                        <button type="button" id="passToggle" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-white transition-colors focus:outline-none">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2 cursor-pointer text-gray-400 hover:text-gray-300 transition-colors">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}
                            class="rounded border-gray-800 bg-[#1e1e1e] text-[#007acc] focus:ring-[#007acc] focus:ring-offset-[#121212]">
                        <span>Recordar sesión</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-[#007acc] hover:text-white font-medium transition-colors">
                            ¿Olvidaste tu contraseña?
                        </a>
                    @endif
                </div>

                <button type="submit" class="w-full bg-[#007acc] hover:bg-[#005f9e] text-white font-bold py-3 rounded-lg flex items-center justify-center gap-2 transition-colors shadow-lg shadow-[#007acc]/20">
                    <i class="fas fa-sign-in-alt"></i> Iniciar sesión
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-gray-800 text-center text-xs text-gray-500 leading-relaxed">
                Acceso restringido al personal autorizado.<br>
                ¿Problemas de acceso? Contacta a 
                <a href="mailto:{{ env('APP_SUPPORT_EMAIL', 'soporte@correo.com') }}" class="text-[#007acc] hover:text-white transition-colors">soporte técnico</a>.
            </div>

        </div>
    </div>

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