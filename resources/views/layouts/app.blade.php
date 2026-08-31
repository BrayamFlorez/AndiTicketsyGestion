<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      x-data="{ sidebarToggle: false, darkMode: true }" 
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', env('APP_NAME', 'TailAdmin')) }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>

<body class="bg-whiten dark:bg-black-2 text-body dark:text-bodydark antialiased font-satoshi">

    {{-- Page Wrapper --}}
    <div class="flex h-screen overflow-hidden">

        {{-- TailAdmin Sidebar --}}
        @include('layouts.partials.sidebar')

        {{-- Backdrop / Overlay Móvil --}}
        <div x-show="sidebarToggle" 
            @click="sidebarToggle = false" 
            x-cloak
            class="fixed inset-0 z-[999] bg-black/60 backdrop-blur-sm lg:hidden">
        </div>

        {{-- Content Area --}}
        <div class="relative flex flex-1 flex-col overflow-y-auto overflow-x-hidden">

            {{-- Header / Navbar TailAdmin --}}
            @include('layouts.partials.navbar')

            {{-- Main Content --}}
            <main class="flex-1 p-4 md:p-6 2xl:p-10 flex flex-col justify-between">
                <div class="mx-auto max-w-screen-2xl w-full">
                    {{ $slot }}
                </div>
                
                {{-- Footer --}}
                <div class="mt-auto pt-6">
                    @include('layouts.partials.footer')
                </div>
            </main>

        </div>
    </div>

    {{-- SweetAlert2 Notificaciones --}}
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
                    confirmButtonColor: '#3C50E0',
                    confirmButtonText: 'Entendido'
                });
            @endif
        });
    </script>
    
    @stack('scripts')
</body>
</html>