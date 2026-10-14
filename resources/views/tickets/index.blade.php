{{-- resources/views/tickets/index.blade.php --}}
<x-app-layout>
    <x-slot name="title">Gestión de Tickets</x-slot>

    @php
        $partial = match ($modo) {
            'todos'     => 'index_admin',
            'asignados' => 'index_tecnico',
            'propios'   => 'index_usuario',
        };
    @endphp

    {{-- Alertas Compactas TailAdmin --}}
    @if (session('success'))
        <div class="mb-3 flex w-full border-l-4 border-meta-3 bg-meta-3/10 px-3 py-2 shadow-sm rounded-sm">
            <i class="fas fa-check-circle text-meta-3 mr-2 mt-0.5 text-xs"></i>
            <p class="text-xs text-meta-3 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error') || $errors->any())
        <div class="mb-3 flex w-full border-l-4 border-meta-1 bg-meta-1/10 px-3 py-2 shadow-sm rounded-sm">
            <i class="fas fa-exclamation-triangle text-meta-1 mr-2 mt-0.5 text-xs"></i>
            <p class="text-xs text-meta-1 font-medium">{{ session('error') ?? $errors->first() }}</p>
        </div>
    @endif

    {{-- Carga del Partial según el Rol / Modo --}}
    @include('tickets.partials.' . $partial)

    {{-- Script Helper Dropdown Teleportado --}}
    @push('scripts')
        @once
        <script>
            function dropdownMenu() {
                return {
                    open: false, top: 0, left: 0,
                    toggle() {
                        this.open = !this.open;
                        if (this.open) {
                            this.$nextTick(() => {
                                const rect = this.$refs.trigger.getBoundingClientRect();
                                const ancho = 200, margen = 8;
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