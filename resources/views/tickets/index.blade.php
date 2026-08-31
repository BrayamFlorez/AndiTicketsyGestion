{{-- resources/views/tickets/index.blade.php --}}
<x-app-layout>
    <x-slot name="title">Tickets</x-slot>

    @php
        $partial = match ($modo) {
            'todos'     => 'index_admin',
            'asignados' => 'index_tecnico',
            'propios'   => 'index_usuario',
        };
    @endphp

    {{-- Alertas estilo TailAdmin --}}
    @if (session('success'))
        <div class="mb-6 flex w-full border-l-6 border-meta-3 bg-meta-3/10 px-7 py-3 shadow-md dark:bg-meta-3/20 dark:border-meta-3 rounded-sm">
            <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-meta-3 text-white">
                <i class="fas fa-check"></i>
            </div>
            <div class="w-full">
                <h5 class="mb-1 font-bold text-meta-3">¡Éxito!</h5>
                <p class="text-sm text-meta-3">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if (session('error') || $errors->any())
        <div class="mb-6 flex w-full border-l-6 border-meta-1 bg-meta-1/10 px-7 py-3 shadow-md dark:bg-meta-1/20 dark:border-meta-1 rounded-sm">
            <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-meta-1 text-white">
                <i class="fas fa-exclamation"></i>
            </div>
            <div class="w-full">
                <h5 class="mb-1 font-bold text-meta-1">Atención</h5>
                <p class="text-sm text-meta-1">{{ session('error') ?? $errors->first() }}</p>
            </div>
        </div>
    @endif

    {{-- Carga del Partial --}}
    @include('tickets.partials.' . $partial)

    {{-- Helper Dropdown (Mantiene el script pero renderizará componentes de TailAdmin) --}}
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