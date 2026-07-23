<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ sidebarOpen: true }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', env('APP_NAME')) }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #333; border-radius: 10px; }
        
        /* Respaldo: Fuerza el fondo oscuro en todo el HTML */
        html, body {
            background-color: #121212 !important;
        }
    </style>
    @stack('styles')
</head>

<body class="bg-[#121212] text-gray-200 antialiased overflow-hidden">

    <div class="flex h-screen bg-[#121212]">
        @include('layouts.partials.sidebar')

        <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-[#121212]">
            @include('layouts.partials.navbar')

            <main class="flex-1 overflow-y-auto custom-scrollbar p-4 lg:p-8 flex flex-col justify-between">
                
                <div class="max-w-7xl mx-auto w-full">
                    {{ $slot }}
                </div>
                
                <div class="mt-auto pt-8">
                    @include('layouts.partials.footer')
                </div>

            </main>
        </div>

        <div x-show="!sidebarOpen" @click="sidebarOpen = true" class="fixed inset-0 bg-black/50 z-40 lg:hidden" x-cloak></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            @if(session('success'))
                Toast.fire({
                    icon: 'success',
                    title: "{!! session('success') !!}"
                });
            @endif

            @if(session('warning'))
                Toast.fire({
                    icon: 'warning',
                    title: "{!! session('warning') !!}"
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: '¡Acción denegada!',
                    text: "{!! session('error') !!}",
                    confirmButtonColor: '#007acc',
                    confirmButtonText: 'Entendido'
                });
            @endif
            
        });
    </script>
    
    @stack('scripts')
</body>
</html>