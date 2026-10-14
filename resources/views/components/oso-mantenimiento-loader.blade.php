{{-- resources/views/components/oso-mantenimiento-loader.blade.php --}}
<template x-teleport="body">
    <div x-show="estadoMantenimiento !== 'inactivo'" x-cloak x-transition.opacity.duration.300ms
         class="fixed inset-0 z-[99999] flex flex-col items-center justify-center bg-[#0c101d]/95 backdrop-blur-md">

        <style>
            .oso-ojos-maint { animation: blink-maint 3.5s infinite; transform-origin: 100px 88px; }
            .oso-wrench-work { 
                animation: wrench-work 0.4s ease-in-out infinite alternate; 
                transform-origin: 127px 150px; 
            }
            
            @keyframes blink-maint {
                0%, 96%, 98% { transform: scaleY(1); }
                97% { transform: scaleY(0.1); }
            }
            @keyframes wrench-work {
                0% { transform: rotate(-10deg) translateY(0); }
                100% { transform: rotate(15deg) translateY(3px); }
            }
        </style>

        {{-- SVG DEL OSO ANDINO MECÁNICO / CYBER --}}
        <div class="relative w-52 h-52 mb-4">
            <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" class="w-full h-full drop-shadow-[0_0_20px_rgba(60,80,224,0.35)]">
                
                <!-- Anillos Tech de Fondo -->
                <circle cx="100" cy="100" r="92" fill="none" stroke="#1e293b" stroke-width="3" stroke-dasharray="8 8" class="animate-[spin_12s_linear_infinite]" />
                <circle cx="100" cy="100" r="82" fill="none" stroke="#3b82f6" stroke-width="1.5" opacity="0.25" />

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

                <!-- Ojos Interactivos + Destello -->
                <g class="oso-ojos-maint">
                    <circle cx="75" cy="88" r="6" :fill="estadoMantenimiento === 'exito' ? '#10b981' : '#38bdf8'" class="transition-colors duration-300" />
                    <circle cx="125" cy="88" r="6" :fill="estadoMantenimiento === 'exito' ? '#10b981' : '#38bdf8'" class="transition-colors duration-300" />
                    
                    <circle cx="73" cy="85" r="2.2" fill="#ffffff" />
                    <circle cx="123" cy="85" r="2.2" fill="#ffffff" />
                </g>

                <!-- Hocico Amigable -->
                <ellipse cx="100" cy="118" rx="20" ry="14" fill="#ffffff"/>
                <path d="M 93 112 Q 100 109 107 112 L 100 120 Z" fill="#0f172a"/>
                <line x1="100" y1="120" x2="100" y2="124" stroke="#0f172a" stroke-width="2"/>
                <path d="M 93 124 Q 100 129 107 124" fill="none" stroke="#0f172a" stroke-width="2" stroke-linecap="round"/>

                <!-- Llave Inglesa (Detrás de la Pata Derecha) -->
                <g :class="estadoMantenimiento === 'registrando' ? 'oso-wrench-work' : ''" class="transition-all duration-300">
                    <rect x="124" y="128" width="6" height="30" rx="3" fill="#1e293b" :stroke="estadoMantenimiento === 'exito' ? '#10b981' : '#38bdf8'" stroke-width="1.5" class="transition-colors duration-300"/>
                    <!-- Cabeza de la llave -->
                    <path d="M 119 128 C 119 120, 135 120, 135 128" fill="none" :stroke="estadoMantenimiento === 'exito' ? '#10b981' : '#38bdf8'" stroke-width="3" stroke-linecap="round" class="transition-colors duration-300"/>
                </g>

                <!-- Patitas Sosteniendo -->
                <rect x="54" y="146" width="30" height="38" rx="15" fill="#0f172a" stroke="#334155" stroke-width="2"/>
                <rect x="116" y="146" width="30" height="40" rx="15" fill="#0f172a" stroke="#334155" stroke-width="2"/>

                <!-- Engranaje Cyber Central -->
                <g class="transition-transform duration-300" :class="estadoMantenimiento === 'exito' ? 'translate-y-1' : ''">
                    
                    <!-- Engranaje Giratorio -->
                    <g :class="estadoMantenimiento === 'registrando' ? 'animate-[spin_4s_linear_infinite]' : ''" style="transform-origin: 100px 158px;">
                        <!-- Dientes del Engranaje -->
                        <path d="M 100 138 V 142 M 100 174 V 178 M 80 158 H 84 M 116 158 H 120 M 86 144 L 89 147 M 111 169 L 114 172 M 86 172 L 89 169 M 111 147 L 114 144" 
                              fill="none" :stroke="estadoMantenimiento === 'exito' ? '#10b981' : '#38bdf8'" stroke-width="3" stroke-linecap="round" class="transition-colors duration-300"/>
                        
                        <!-- Cuerpo del Engranaje -->
                        <circle cx="100" cy="158" r="16" fill="#1e293b" :stroke="estadoMantenimiento === 'exito' ? '#10b981' : '#38bdf8'" stroke-width="2" class="transition-colors duration-300"/>
                        <!-- Eje Hueco -->
                        <circle cx="100" cy="158" r="6" fill="#0f172a" :stroke="estadoMantenimiento === 'exito' ? '#10b981' : '#38bdf8'" stroke-width="1.5" class="transition-colors duration-300"/>
                    </g>
                    
                    <!-- Icono Éxito (Checkmark aparece sobre el engranaje) -->
                    <path x-show="estadoMantenimiento === 'exito'" d="M 94 158 L 98 163 L 108 152" fill="none" stroke="#10b981" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                </g>

            </svg>
        </div>

        {{-- Textos de la Terminal --}}
        <div class="text-center font-mono w-72">
            <h3 class="text-sm font-bold tracking-widest mb-1.5 transition-colors duration-300" 
                :class="estadoMantenimiento === 'exito' ? 'text-meta-3' : 'text-primary'">
                <span x-show="estadoMantenimiento === 'registrando'">REGISTRANDO MANTENIMIENTO...</span>
                <span x-show="estadoMantenimiento === 'exito'">¡MANTENIMIENTO GUARDADO!</span>
            </h3>
            
            <div class="h-1.5 w-full bg-stroke dark:bg-meta-4 rounded-full overflow-hidden mb-2.5">
                <div class="h-full bg-primary transition-all duration-[1000ms] ease-out rounded-full" 
                     :class="estadoMantenimiento === 'exito' ? '!bg-meta-3 w-full' : 'w-3/4'"></div>
            </div>

            <p class="text-[10px] text-bodydark2">
                <span x-show="estadoMantenimiento === 'registrando'" class="animate-pulse">Actualizando bitácora técnica // ANDI-NODE</span>
                <span x-show="estadoMantenimiento === 'exito'">Bitácora sincronizada con éxito.</span>
            </p>
        </div>

    </div>
</template>