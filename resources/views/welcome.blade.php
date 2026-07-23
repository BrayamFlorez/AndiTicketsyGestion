<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido — {{ env('APP_NAME', 'Service Desk') }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        body { 
            font-family: 'DM Sans', sans-serif; 
            background-color: #121212; 
            color: #cccccc; 
        }
        .font-serif-title { font-family: 'Playfair Display', serif; }
        
        /* Fondo tecnológico: Cuadrícula sutil con resplandor radial */
        .bg-grid {
            background-size: 40px 40px;
            background-image: 
                linear-gradient(to right, rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
            position: absolute;
            inset: 0;
            z-index: 0;
        }
        .bg-glow {
            position: absolute;
            top: -20%;
            left: 50%;
            transform: translateX(-50%);
            width: 80vw;
            height: 80vh;
            background: radial-gradient(circle, rgba(0, 122, 204, 0.15) 0%, rgba(18, 18, 18, 0) 70%);
            z-index: 0;
            pointer-events: none;
        }
    </style>
</head>
<body class="antialiased min-h-screen flex flex-col relative overflow-x-hidden">

    <div class="bg-grid"></div>
    <div class="bg-glow"></div>

    <header class="relative z-10 border-b border-gray-800 bg-[#121212]/80 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#1e1e1e] border border-[#007acc] text-white flex items-center justify-center shadow-lg shadow-[#007acc]/20">
                    <i class="fas fa-headset text-lg"></i>
                </div>
                <span class="font-serif-title text-xl text-white font-bold tracking-wide">
                    {{ env('APP_NAME', 'Service Desk') }}
                </span>
            </div>
            
            <div>
                @auth
                    <a href="{{ url('/dashboard') }}" class="text-sm font-bold text-white bg-[#1e1e1e] border border-gray-800 hover:border-[#007acc] px-5 py-2.5 rounded-lg transition-colors">
                        Ir al Panel
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-bold text-white bg-[#007acc] hover:bg-[#005f9e] px-5 py-2.5 rounded-lg transition-colors shadow-lg shadow-[#007acc]/20 flex items-center gap-2">
                        <i class="fas fa-sign-in-alt"></i> Iniciar sesión
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <main class="relative z-10 flex-1 flex flex-col justify-center items-center text-center px-6 py-20">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#007acc]/10 border border-[#007acc]/30 text-[#007acc] text-xs font-bold uppercase tracking-widest mb-8">
            <i class="fas fa-bolt text-[#007acc]"></i> Plataforma Activa
        </div>

        <h1 class="font-serif-title text-4xl md:text-6xl font-bold text-white leading-tight mb-6 max-w-4xl">
            Gestión inteligente de <em class="italic text-[#007acc]">tickets</em> <br class="hidden md:block"> y servicios de soporte.
        </h1>
        
        <p class="text-gray-400 text-lg md:text-xl max-w-2xl mb-12">
            {{ env('APP_DESC', 'Centraliza, administra y resuelve los requerimientos tecnológicos e incidentes de tu organización de forma eficiente y estructurada.') }}
        </p>

        @guest
            <a href="{{ route('login') }}" class="text-base font-bold text-white bg-[#1e1e1e] border border-gray-700 hover:border-[#007acc] hover:bg-[#007acc]/10 px-8 py-4 rounded-xl transition-all shadow-xl flex items-center gap-3">
                Acceder al portal de soporte <i class="fas fa-arrow-right"></i>
            </a>
        @endguest

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl w-full mt-24 text-left">
            
            <div class="bg-[#1e1e1e]/80 backdrop-blur border border-gray-800 p-6 rounded-2xl hover:border-[#007acc]/50 transition-colors">
                <div class="w-12 h-12 rounded-lg bg-[#007acc]/10 text-[#007acc] flex items-center justify-center text-xl mb-4">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <h3 class="text-white font-bold text-lg mb-2">Gestión de Tickets</h3>
                <p class="text-gray-400 text-sm leading-relaxed">
                    Registra incidentes o solicitudes de servicio con prioridades, categorías y asignación directa a técnicos capacitados.
                </p>
            </div>

            <div class="bg-[#1e1e1e]/80 backdrop-blur border border-gray-800 p-6 rounded-2xl hover:border-[#007acc]/50 transition-colors">
                <div class="w-12 h-12 rounded-lg bg-amber-500/10 text-amber-500 flex items-center justify-center text-xl mb-4">
                    <i class="fas fa-tasks"></i>
                </div>
                <h3 class="text-white font-bold text-lg mb-2">Seguimiento en Vivo</h3>
                <p class="text-gray-400 text-sm leading-relaxed">
                    Monitorea el ciclo de vida completo del requerimiento, desde su apertura hasta su resolución, manteniendo al usuario informado.
                </p>
            </div>

            <div class="bg-[#1e1e1e]/80 backdrop-blur border border-gray-800 p-6 rounded-2xl hover:border-[#007acc]/50 transition-colors">
                <div class="w-12 h-12 rounded-lg bg-green-500/10 text-green-500 flex items-center justify-center text-xl mb-4">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="text-white font-bold text-lg mb-2">Control Multi-Sede</h3>
                <p class="text-gray-400 text-sm leading-relaxed">
                    Administra servicios a través de diferentes empresas y ubicaciones geográficas desde un único panel centralizado.
                </p>
            </div>

        </div>
    </main>

    <footer class="relative z-10 border-t border-gray-800 bg-[#121212] py-8 mt-auto">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-gray-500 font-medium tracking-wide uppercase">
            <p>&copy; {{ date('Y') }} {{ env('APP_NAME', 'Service Desk') }}. Todos los derechos reservados.</p>
            <div class="flex gap-6">
                <span class="flex items-center gap-2"><i class="fas fa-envelope text-gray-600"></i> {{ env('APP_SUPPORT_EMAIL', 'soporte@empresa.com') }}</span>
            </div>
        </div>
    </footer>

</body>
</html>