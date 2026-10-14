{{-- resources/views/components/oso-login-loader.blade.php --}}
<div x-show="estado !== 'inactivo'" x-cloak x-transition.opacity.duration.300ms
     class="fixed inset-0 z-[99999] flex flex-col items-center justify-center bg-[#0c101d]/95 backdrop-blur-md">

    <style>
        .oso-ojos { animation: blink 3.5s infinite; transform-origin: 100px 88px; }
        .oso-scan { animation: scan 1.2s ease-in-out infinite alternate; }
        .anim-shake { animation: shake-lock 0.4s cubic-bezier(.36,.07,.19,.97) both; }
        
        @keyframes blink {
            0%, 96%, 98% { transform: scaleY(1); }
            97% { transform: scaleY(0.1); }
        }
        @keyframes scan {
            0% { transform: translateY(0); opacity: 1; }
            100% { transform: translateY(18px); opacity: 0.2; }
        }
        @keyframes shake-lock {
            10%, 90% { transform: translate3d(-2px, 0, 0); }
            20%, 80% { transform: translate3d(3px, 0, 0); }
            30%, 50%, 70% { transform: translate3d(-5px, 0, 0); }
            40%, 60% { transform: translate3d(5px, 0, 0); }
        }
    </style>

    {{-- SVG DEL OSO ANDINO ADORABLE --}}
    <div class="relative w-52 h-52 mb-4">
        <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" class="w-full h-full drop-shadow-[0_0_20px_rgba(60,80,224,0.35)]">
            
            <!-- Anillos Tech de Fondo -->
            <circle cx="100" cy="100" r="92" fill="none" stroke="#1e293b" stroke-width="3" stroke-dasharray="8 8" class="animate-[spin_12s_linear_infinite]" />
            <circle cx="100" cy="100" r="82" fill="none" :stroke="estado === 'rechazado' ? '#ef4444' : '#3b82f6'" stroke-width="1.5" opacity="0.25" class="transition-colors duration-300"/>

            <!-- Orejas Redonditas -->
            <circle cx="50" cy="52" r="24" fill="#1e293b" stroke="#334155" stroke-width="2"/>
            <circle cx="50" cy="52" r="13" fill="#fef3c7" opacity="0.35"/>
            <circle cx="150" cy="52" r="24" fill="#1e293b" stroke="#334155" stroke-width="2"/>
            <circle cx="150" cy="52" r="13" fill="#fef3c7" opacity="0.35"/>

            <!-- Cabeza Principal -->
            <circle cx="100" cy="102" r="62" fill="#1e293b"/>

            <!-- Marcas Faciales / Gafas del Oso Andino -->
            <path d="M 52,88 
                     C 52,65 88,65 98,85 
                     C 100,87 100,87 102,85 
                     C 112,65 148,65 148,88 
                     C 148,114 128,135 100,135 
                     C 72,135 52,114 52,88 Z" 
                  fill="#fef3c7" opacity="0.95" />

            <!-- Rubor en Mejillas -->
            <circle cx="62" cy="106" r="8" fill="#f43f5e" opacity="0.2" />
            <circle cx="138" cy="106" r="8" fill="#f43f5e" opacity="0.2" />

            <!-- Parches Oscuros de los Ojos -->
            <circle cx="75" cy="88" r="15" fill="#0f172a"/>
            <circle cx="125" cy="88" r="15" fill="#0f172a"/>

            <!-- Ojos Interactivos + Destello de Brillo -->
            <g class="oso-ojos">
                <!-- Color depende del estado: Validando(Azul), Acceso(Verde), Rechazado(Rojo) -->
                <circle cx="75" cy="88" r="6" :fill="estado === 'acceso' ? '#10b981' : (estado === 'rechazado' ? '#ef4444' : '#38bdf8')" class="transition-colors duration-300" />
                <circle cx="125" cy="88" r="6" :fill="estado === 'acceso' ? '#10b981' : (estado === 'rechazado' ? '#ef4444' : '#38bdf8')" class="transition-colors duration-300" />
                
                <circle cx="73" cy="85" r="2.2" fill="#ffffff" />
                <circle cx="123" cy="85" r="2.2" fill="#ffffff" />
            </g>

            <!-- Hocico Amigable -->
            <ellipse cx="100" cy="118" rx="20" ry="14" fill="#ffffff"/>
            <path d="M 93 112 Q 100 109 107 112 L 100 120 Z" fill="#0f172a"/>
            <line x1="100" y1="120" x2="100" y2="124" stroke="#0f172a" stroke-width="2"/>
            
            <!-- Sonrisa o Tristeza dependiendo del estado -->
            <path :d="estado === 'rechazado' ? 'M 93 128 Q 100 124 107 128' : 'M 93 124 Q 100 129 107 124'" 
                  fill="none" stroke="#0f172a" stroke-width="2" stroke-linecap="round" class="transition-all duration-300"/>

            <!-- Patitas de Oso Sosteniendo Candado -->
            <rect x="54" y="146" width="30" height="38" rx="15" fill="#0f172a" stroke="#334155" stroke-width="2"/>
            <rect x="116" y="146" width="30" height="40" rx="15" fill="#0f172a" stroke="#334155" stroke-width="2"/>

            <!-- Candado Cyber Escáner -->
            <g class="transition-transform duration-300" :class="estado === 'acceso' ? 'translate-y-2' : (estado === 'rechazado' ? 'anim-shake' : '')">
                <rect x="75" y="148" width="50" height="36" rx="8" fill="#1e293b" :stroke="estado === 'acceso' ? '#10b981' : (estado === 'rechazado' ? '#ef4444' : '#38bdf8')" stroke-width="2" class="transition-colors duration-300"/>
                
                <!-- Arco -->
                <path :d="estado === 'acceso' ? 'M 85 148 V 134 A 10 10 0 0 1 105 134' : 'M 85 148 V 138 A 10 10 0 0 1 115 138 V 148'" 
                      fill="none" :stroke="estado === 'acceso' ? '#10b981' : (estado === 'rechazado' ? '#ef4444' : '#38bdf8')" stroke-width="3" stroke-linecap="round" class="transition-all duration-300"/>
                
                <!-- Láser de Escaneo -->
                <line x-show="estado === 'validando'" x1="78" y1="154" x2="122" y2="154" stroke="#38bdf8" stroke-width="2" class="oso-scan" />
                
                <!-- Icono Éxito (Check) -->
                <path x-show="estado === 'acceso'" d="M 90 166 L 97 172 L 110 158" fill="none" stroke="#10b981" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                
                <!-- Icono Rechazo (X) -->
                <path x-show="estado === 'rechazado'" d="M 92 158 L 108 174 M 108 158 L 92 174" fill="none" stroke="#ef4444" stroke-width="3" stroke-linecap="round"/>
            </g>

        </svg>
    </div>

    {{-- Textos de Terminal --}}
    <div class="text-center font-mono w-72">
        <h3 class="text-sm font-bold tracking-widest mb-1.5 transition-colors duration-300" 
            :class="estado === 'acceso' ? 'text-meta-3' : (estado === 'rechazado' ? 'text-meta-1' : 'text-primary')">
            <span x-show="estado === 'validando'">AUTENTICANDO...</span>
            <span x-show="estado === 'acceso'">¡ACCESO CONCEDIDO!</span>
            <span x-show="estado === 'rechazado'">ACCESO DENEGADO</span>
        </h3>
        
        <div class="h-1.5 w-full bg-stroke dark:bg-meta-4 rounded-full overflow-hidden mb-2.5">
            <div class="h-full bg-primary transition-all duration-[1200ms] ease-out rounded-full" 
                 :class="estado === 'acceso' ? '!bg-meta-3 w-full' : (estado === 'rechazado' ? '!bg-meta-1 w-full' : 'w-3/4')"></div>
        </div>

        <p class="text-[10px] text-bodydark2 h-4">
            <span x-show="estado === 'validando'" class="animate-pulse">Validando credenciales // ANDI-NODE</span>
            <span x-show="estado === 'acceso'">Estableciendo conexión segura...</span>
            <span x-show="estado === 'rechazado'">Credenciales inválidas. Abortando...</span>
        </p>
    </div>

</div>