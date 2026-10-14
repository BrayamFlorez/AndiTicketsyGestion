<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Noticias y Actualizaciones | ANDI Servicios</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    {{-- Alpine.js para el Modal dinámico --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        .gradient-text { 
            background: linear-gradient(to right, #15803d, #1d4ed8); 
            -webkit-background-clip: text; 
            -webkit-text-fill-color: transparent; 
        }
        .video-card:hover .play-button { 
            transform: scale(1.1); 
            background-color: #15803d; 
            color: white;
        }
        .video-card video { pointer-events: none; } /* Evita que el click del video interfiera con el del contenedor */
    </style>
</head>
<body class="bg-gray-50 text-gray-900" x-data="{ openModal: false, videoSrc: '' }">

    {{-- Navbar --}}
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-sm shadow-sm">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('img/logo.webp') }}" alt="Logo" class="h-12">
                    <span class="text-xl font-bold tracking-tight">ANDI <span class="text-green-700">SERVICIOS</span></span>
                </div>
                <div class="hidden md:flex items-center gap-8">
                    <a href="/" class="text-gray-700 hover:text-green-700 font-medium transition">Inicio</a>
                    <a href="{{ route('login') }}" class="bg-green-700 text-white px-6 py-2.5 rounded-full font-semibold">Iniciar Sesión</a>
                </div>
            </div>
        </div>
    </nav>

    {{-- Header --}}
    <header class="pt-40 pb-20 bg-gray-900 text-white relative overflow-hidden">
        <div class="absolute inset-0 opacity-20">
            <img src="https://images.unsplash.com/photo-1550751827-4bd374c3f58b?w=1600" class="w-full h-full object-cover">
        </div>
        <div class="max-w-7xl mx-auto px-6 relative z-10 text-center">
            <span class="bg-green-600 text-white px-4 py-1 rounded-full text-sm font-bold mb-4 inline-block uppercase tracking-wider">Centro de Aprendizaje</span>
            <h1 class="text-5xl md:text-6xl font-extrabold mb-6">Noticias y <span class="text-green-400">Actualizaciones</span></h1>
            <p class="text-xl text-gray-300 max-w-2xl mx-auto">
                Domina tu plataforma con nuestros últimos tutoriales y noticias de Bits Desarrollo.
            </p>
        </div>
    </header>

    {{-- Contenido Principal --}}
    <main class="max-w-7xl mx-auto py-16 px-6">

        {{-- Grid de Noticias --}}
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-10">
            @forelse($noticias as $noticia)
            <article class="video-card bg-white rounded-3xl overflow-hidden shadow-md hover:shadow-2xl transition-all duration-300 border border-gray-100 flex flex-col">
                <div class="relative group cursor-pointer overflow-hidden" 
                     @if($noticia->tipo == 'video') 
                        @click="openModal = true; videoSrc = '{{ asset('news/' . $noticia->archivo) }}'"
                     @endif>
                    
                    @if($noticia->tipo == 'video')
                        {{-- Preview del Video --}}
                        <div class="relative h-56 bg-black">
                            <video src="{{ asset('news/' . $noticia->archivo) }}" class="w-full h-full object-cover opacity-80" muted></video>
                            {{-- Overlay Play --}}
                            <div class="absolute inset-0 flex items-center justify-center bg-black/20 group-hover:bg-black/40 transition-all">
                                <div class="play-button w-16 h-16 bg-white/90 text-green-700 rounded-full flex items-center justify-center shadow-2xl transition-all duration-300">
                                    <i class="fas fa-play text-xl ml-1"></i>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Imagen Estática --}}
                        <img src="{{ asset('news/' . $noticia->archivo) }}" class="w-full h-56 object-cover transition-transform duration-500 group-hover:scale-110">
                    @endif
                </div>
                
                <div class="p-8 flex-1 flex flex-col">
                    <div class="flex items-center gap-2 mb-4 text-xs font-bold text-green-700 uppercase tracking-widest">
                        <i class="fas fa-calendar-day"></i> {{ $noticia->created_at->format('d M, Y') }}
                    </div>
                    <h3 class="text-xl font-bold mb-4 leading-tight group-hover:text-green-700 transition-colors">
                        {{ $noticia->titulo }}
                    </h3>
                    <p class="text-gray-600 text-sm mb-6 line-clamp-3">
                        {{ $noticia->descripcion }}
                    </p>
                    
                    @if($noticia->tipo == 'video')
                    <div class="mt-auto">
                        <span class="text-green-700 font-bold text-sm inline-flex items-center gap-2">
                            Reproducir video <i class="fas fa-circle-play"></i>
                        </span>
                    </div>
                    @endif
                </div>
            </article>
            @empty
                <div class="col-span-full text-center py-20 text-gray-500">
                    <i class="fas fa-folder-open text-5xl mb-4 opacity-20"></i>
                    <p>No hay noticias publicadas en este momento.</p>
                </div>
            @endforelse
        </div>
    </main>

    {{-- MODAL DE VIDEO (Optimizado) --}}
    <div x-show="openModal" 
         class="fixed inset-0 z-[100] flex items-center justify-center bg-black/95 p-4 md:p-10"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         x-cloak>
        
        {{-- Botón Cerrar --}}
        <button @click="openModal = false; videoSrc = ''" class="absolute top-6 right-6 text-white text-4xl hover:text-green-400 transition-colors z-50">
            <i class="fas fa-times"></i>
        </button>

        {{-- Contenedor del Video --}}
        <div class="w-full max-w-5xl aspect-video rounded-2xl overflow-hidden shadow-2xl bg-black" @click.away="openModal = false; videoSrc = ''">
            <template x-if="openModal">
                <video :src="videoSrc" controls autoplay class="w-full h-full"></video>
            </template>
        </div>
    </div>

    {{-- Footer --}}
    <footer class="bg-gray-900 text-gray-300 py-16 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-3 gap-12 mb-12">
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <img src="{{ asset('img/logo.webp') }}" alt="Logo" class="h-10">
                        <span class="text-white font-bold text-lg">OFICANON</span>
                    </div>
                    <p class="text-gray-400 leading-relaxed text-sm">Innovación y tecnología desde el corazón de Norte de Santander. Especialistas en Bits Desarrollo.</p>
                </div>
                <div>
                    <h3 class="text-white font-bold text-lg mb-4 italic underline decoration-green-500">Ubicación</h3>
                    <p class="flex items-start gap-3 text-sm">
                        <i class="fas fa-map-marker-alt text-green-400 mt-1"></i>
                        <span>Quinta Bosch, Av. 0 #1-119,<br>Barrio La ceiba, Cúcuta, N.S.</span>
                    </p>
                </div>
                <div>
                    <h3 class="text-white font-bold text-lg mb-4 italic underline decoration-green-500">Contacto</h3>
                    <p class="flex items-center gap-3 mb-3 text-sm">
                        <i class="fas fa-phone text-green-400"></i> (607) 5497500
                    </p>
                    <p class="flex items-center gap-3 text-sm">
                        <i class="fas fa-envelope text-green-400"></i> info@oficanon.com
                    </p>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center text-xs tracking-widest text-gray-500">
                &copy; {{ date('Y') }} OFICANON - BITS DESARROLLO. TODOS LOS DERECHOS RESERVADOS.
            </div>
        </div>
    </footer>

</body>
</html>