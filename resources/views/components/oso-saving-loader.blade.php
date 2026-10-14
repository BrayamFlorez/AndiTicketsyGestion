{{-- resources/views/components/oso-saving-loader.blade.php --}}
<div x-show="estadoGuardado !== 'inactivo'" x-cloak x-transition.opacity.duration.300ms
     class="fixed inset-0 z-[99999] flex flex-col items-center justify-center bg-[#0c101d]/90 backdrop-blur-md">

    <style>
        .oso-ojos-saving { animation: blink-saving 3.5s infinite; transform-origin: 100px 88px; }
        .oso-write-scan { animation: write-scan 1.2s ease-in-out infinite alternate; }
        .oso-chip-pulse { animation: chip-pulse 1.5s ease-in-out infinite; }
        
        @keyframes blink-saving {
            0%, 96%, 98% { transform: scaleY(1); }
            97% { transform: scaleY(0.1); }
        }
        @keyframes write-scan {
            0% { transform: translateY(0); opacity: 1; }
            100% { transform: translateY(16px); opacity: 0.2; }
        }
        @keyframes chip-pulse {
            0%, 100% { opacity: 0.4; }
            50% { opacity: 1; }
        }
    </style>

    {{-- SVG DEL OSO ANDINO CON DISQUETE/CHIP CYBER --}}
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
            <g class="oso-ojos-saving">
                <circle cx="75" cy="88" r="6" :fill="estadoGuardado === 'exito' ? '#10b981' : '#38bdf8'" class="transition-colors duration-300" />
                <circle cx="125" cy="88" r="6" :fill="estadoGuardado === 'exito' ? '#10b981' : '#38bdf8'" class="transition-colors duration-300" />
                
                <circle cx="73" cy="85" r="2.2" fill="#ffffff" />
                <circle cx="123" cy="85" r="2.2" fill="#ffffff" />
            </g>

            <!-- Hocico Amigable -->
            <ellipse cx="100" cy="118" rx="20" ry="14" fill="#ffffff"/>
            <path d="M 93 112 Q 100 109 107 112 L 100 120 Z" fill="#0f172a"/>
            <line x1="100" y1="120" x2="100" y2="124" stroke="#0f172a" stroke-width="2"/>
            <path d="M 93 124 Q 100 129 107 124" fill="none" stroke="#0f172a" stroke-width="2" stroke-linecap="round"/>

            <!-- Patitas Sosteniendo el Disquete / Chip de Datos -->
            <rect x="54" y="146" width="30" height="38" rx="15" fill="#0f172a" stroke="#334155" stroke-width="2"/>
            <rect x="116" y="146" width="30" height="40" rx="15" fill="#0f172a" stroke="#334155" stroke-width="2"/>

            <!-- Disquete / Unidad de Memoria Cyber -->
            <g class="transition-transform duration-300" :class="estadoGuardado === 'exito' ? 'translate-y-1' : ''">
                <!-- Cuerpo del Disquete -->
                <rect x="74" y="142" width="52" height="42" rx="6" fill="#1e293b" :stroke="estadoGuardado === 'exito' ? '#10b981' : '#38bdf8'" stroke-width="2" class="transition-colors duration-300"/>
                
                <!-- Protector metálico superior -->
                <rect x="83" y="142" width="28" height="14" rx="2" fill="#0f172a" :stroke="estadoGuardado === 'exito' ? '#10b981' : '#38bdf8'" stroke-width="1.5"/>
                <rect x="100" y="145" width="6" height="8" rx="1" :fill="estadoGuardado === 'exito' ? '#10b981' : '#38bdf8'" class="transition-colors duration-300"/>

                <!-- Etiqueta de memoria -->
                <rect x="80" y="162" width="40" height="16" rx="3" fill="#0f172a"/>
                
                <!-- Láser de escritura de datos -->
                <line x-show="estadoGuardado === 'guardando'" x1="82" y1="164" x2="118" y2="164" stroke="#38bdf8" stroke-width="2" class="oso-write-scan" />
                
                <!-- LED de estado -->
                <circle cx="85" cy="170" r="2" :fill="estadoGuardado === 'exito' ? '#10b981' : '#38bdf8'" class="oso-chip-pulse"/>

                <!-- Icono Éxito (Checkmark) -->
                <path x-show="estadoGuardado === 'exito'" d="M 94 170 L 99 175 L 110 164" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            </g>

        </svg>
    </div>

    {{-- Textos de la Terminal --}}
    <div class="text-center font-mono w-72">
        <h3 class="text-sm font-bold tracking-widest mb-1.5 transition-colors duration-300" 
            :class="estadoGuardado === 'exito' ? 'text-meta-3' : 'text-primary'">
            <span x-show="estadoGuardado === 'guardando'">GUARDANDO CAMBIOS...</span>
            <span x-show="estadoGuardado === 'exito'">¡CAMBIOS GUARDADOS!</span>
        </h3>
        
        <div class="h-1.5 w-full bg-stroke dark:bg-meta-4 rounded-full overflow-hidden mb-2.5">
            <div class="h-full bg-primary transition-all duration-[1000ms] ease-out rounded-full" 
                 :class="estadoGuardado === 'exito' ? '!bg-meta-3 w-full' : 'w-3/4'"></div>
        </div>

        <p class="text-[10px] text-bodydark2">
            <span x-show="estadoGuardado === 'guardando'" class="animate-pulse">Escribiendo en base de datos // ANDI-NODE</span>
            <span x-show="estadoGuardado === 'exito'">Sincronización completada.</span>
        </p>
    </div>

</div>