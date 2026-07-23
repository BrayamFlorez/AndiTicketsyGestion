{{-- resources/views/tickets/index.blade.php --}}
<x-app-layout>
    <x-slot name="title">Tickets</x-slot>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
        <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
    @endpush

    @php
        $partial = match ($modo) {
            'todos'     => 'index_admin',
            'asignados' => 'index_tecnico',
            'propios'   => 'index_usuario',
        };
    @endphp

    @if (session('success'))
        <div class="form-alert success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if (session('error') || $errors->any())
        <div class="form-alert error">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') ?? $errors->first() }}
        </div>
    @endif

    @include('tickets.partials.' . $partial)

    {{-- Dropdown helper: usado por las acciones de la tabla --}}
    @push('scripts')
        @once
        <script>
            function dropdownMenu() {
                return {
                    open: false,
                    top: 0,
                    left: 0,
                    toggle() {
                        this.open = !this.open;
                        if (this.open) {
                            this.$nextTick(() => {
                                const rect = this.$refs.trigger.getBoundingClientRect();
                                // Ancho aproximado del dropdown (220px). Si no cabe a la derecha, alinea a la izquierda del botón.
                                const ancho = 220;
                                const margen = 8;
                                let left = rect.right - ancho;
                                if (left < margen) left = rect.left;
                                this.top = rect.bottom + 4;
                                this.left = left;
                            });
                        }
                    },
                };
            }
        </script>
        @endonce
    @endpush
</x-app-layout>
