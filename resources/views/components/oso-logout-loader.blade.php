{{-- resources/views/components/oso-logout-loader.blade.php --}}
<template x-teleport="body">
    <div x-show="estadoLogout !== 'inactivo'" x-cloak x-transition.opacity.duration.300ms
         class="fixed inset-0 z-[99999] flex flex-col items-center justify-center bg-[#0c101d]/95 backdrop-blur-md">

        <style>
            .oso-wave-paw {
                animation: oso-wave 1s ease-in-out infinite alternate;
                transform-origin: 135px 145px;
            }
            .oso-sleep-eyes {
                animation: blink-logout 3.5s infinite;
                transform-origin: 100px 88px;
            }
            
            @keyframes oso-wave {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(-18deg); }
            }
            @keyframes blink-logout {
                0%, 96%, 98% { transform: scaleY(1); }
                97% { transform: scaleY(0.1); }
            }
        </style>

        {{-- SVG DEL OSO ANDINO DESPIDIÉNDOSE --}}
        <div class="relative w-52 h-52 mb-4">
            <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" class="w-full h-full drop-shadow-[0_0_20px_rgba(245,158,11,0.3)]">
                
                <!-- Anillos Tech de Fondo -->
                <circle cx="100" cy="100" r="92" fill="none" stroke="#1e293b" stroke-width="3" stroke-dasharray="8 8" class="animate-[spin_16s_linear_infinite]" />
                <circle cx="100" cy="100" r="82" fill="none" :stroke="estadoLogout === 'desconectando' ? '#f59e0b' : '#38bdf8'" stroke-width="1.5" opacity="0.3" class="transition-colors duration-300"/>

                <!-- Orejas Redonditas -->
                <circle cx="50" cy="52" r="24" fill="#1e293b" stroke="#334155" stroke-width="2"/>
                <circle cx="50" cy="52" r="13" fill="#fef3c7" opacity="0.35"/>
                
                <circle cx="150" cy="52" r="24" fill="#1e293b" stroke="#334155" stroke-width="2"/>
                <circle cx="150" cy="52" r="13" fill="#fef3c7" opacity="0.35"/>

                <!-- Cabeza Principal -->
                <circle cx="100" cy="102" r="62" fill="#1e293b"/>

                <!-- Marcas Faciales Curvas del Oso Andino -->
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

                <!-- Ojos Interactivos -->
                <g class="oso-sleep-eyes" x-show="estadoLogout !== 'desconectando'">
                    <circle cx="75" cy="88" r="6" fill="#38bdf8" />
                    <circle cx="125" cy="88" r="6" fill="#38bdf8" />
                    <circle cx="73" cy="85" r="2.2" fill="#ffffff" />
                    <circle cx="123" cy="85" r="2.2" fill="#ffffff" />
                </g>

                <!-- Ojos Guiñados al desconectar -->
                <g x-show="estadoLogout === 'desconectando'" opacity="0.9">
                    <path d="M 68 88 Q 75 82 82 88" fill="none" stroke="#f59e0b" stroke-width="3" stroke-linecap="round"/>
                    <path d="M 118 88 Q 125 82 132 88" fill="none" stroke="#f59e0b" stroke-width="3" stroke-linecap="round"/>
                </g>

                <!-- Hocico Amigable -->
                <ellipse cx="100" cy="118" rx="20" ry="14" fill="#ffffff"/>
                <path d="M 93 112 Q 100 109 107 112 L 100 120 Z" fill="#0f172a"/>
                <line x1="100" y1="120" x2="100" y2="124" stroke="#0f172a" stroke-width="2"/>
                <path d="M 93 124 Q 100 129 107 124" fill="none" stroke="#0f172a" stroke-width="2" stroke-linecap="round"/>

                <!-- Pata Izquierda -->
                <rect x="52" y="146" width="28" height="38" rx="14" fill="#0f172a" stroke="#334155" stroke-width="2"/>

                <!-- Pata Derecha Saludando -->
                <g class="oso-wave-paw">
                    <rect x="122" y="126" width="26" height="42" rx="13" fill="#0f172a" stroke="#334155" stroke-width="2"/>
                    <circle cx="135" cy="138" r="4" fill="#fef3c7" opacity="0.6"/>
                </g>

                <!-- Botón de Power -->
                <g class="transition-transform duration-300" :class="estadoLogout === 'desconectando' ? 'scale-110' : ''">
                    <circle cx="100" cy="162" r="16" fill="#1e293b" :stroke="estadoLogout === 'desconectando' ? '#f59e0b' : '#38bdf8'" stroke-width="2" class="transition-colors duration-300"/>
                    <path d="M 100 152 V 160 M 94 156 A 7 7 0 1 0 106 156" fill="none" :stroke="estadoLogout === 'desconectando' ? '#f59e0b' : '#38bdf8'" stroke-width="2.5" stroke-linecap="round" class="transition-colors duration-300"/>
                </g>

            </svg>
        </div>

        {{-- Textos de Terminal --}}
        <div class="text-center font-mono w-72">
            <h3 class="text-sm font-bold tracking-widest mb-1.5 transition-colors duration-300" 
                :class="estadoLogout === 'desconectando' ? 'text-amber-400' : 'text-primary'">
                <span x-show="estadoLogout === 'cerrando'">CERRANDO SESIÓN...</span>
                <span x-show="estadoLogout === 'desconectando'">¡HASTA PRONTO!</span>
            </h3>
            
            <div class="h-1.5 w-full bg-stroke dark:bg-meta-4 rounded-full overflow-hidden mb-2.5">
                <div class="h-full bg-primary transition-all duration-[1000ms] ease-out rounded-full" 
                     :class="estadoLogout === 'desconectando' ? '!bg-amber-400 w-full' : 'w-3/4'"></div>
            </div>

            <p class="text-[10px] text-bodydark2">
                <span x-show="estadoLogout === 'cerrando'" class="animate-pulse">Desconectando nodo de usuario // ANDI</span>
                <span x-show="estadoLogout === 'desconectando'">Sesión finalizada con éxito.</span>
            </p>
        </div>

    </div>
</template>