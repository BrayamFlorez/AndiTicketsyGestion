{{-- resources/views/impresoras/mantenimientos/_firma_pad.blade.php --}}
{{-- Lienzo para firmar con el dedo o el mouse. El PNG viaja en un input oculto. Recibe: $campo --}}

<div x-data="firmaPad('{{ $campo }}')" class="select-none">
    <div class="relative rounded border-2 border-dashed border-stroke dark:border-strokedark bg-white">
        <canvas x-ref="lienzo" class="block w-full h-36 touch-none cursor-crosshair"
                @pointerdown="empezar($event)" @pointermove="mover($event)" @pointerup="terminar()" @pointerleave="terminar()" @pointercancel="terminar()"></canvas>
        <span x-show="vacia" class="pointer-events-none absolute inset-0 flex items-center justify-center text-[11px] text-gray-400">Firme aquí</span>
        <span class="pointer-events-none absolute left-4 right-4 bottom-6 border-b border-gray-300"></span>
    </div>
    <div class="flex justify-end mt-1">
        <button type="button" @click="limpiar()" x-show="!vacia" class="text-[9px] font-bold uppercase text-bodydark2 hover:text-meta-1">
            <i class="fas fa-eraser"></i> Borrar
        </button>
    </div>
    <input type="hidden" name="{{ $campo }}" :value="valor">
</div>

@once
    @push('scripts')
        <script>
            function firmaPad(campo) {
                return {
                    vacia: true,
                    valor: '',
                    dibujando: false,

                    init() {
                        this.$nextTick(() => this.ajustar());
                        window.addEventListener('resize', () => this.ajustar());
                    },

                    // Tamaño real del canvas = tamaño en pantalla × densidad de píxeles (trazo nítido en celulares)
                    ajustar() {
                        const lienzo = this.$refs.lienzo;
                        const caja = lienzo.getBoundingClientRect();
                        if (!caja.width) return;
                        const previo = this.vacia ? null : lienzo.toDataURL('image/png');
                        const dpr = window.devicePixelRatio || 1;
                        lienzo.width = caja.width * dpr;
                        lienzo.height = caja.height * dpr;
                        const ctx = this.ctx();
                        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
                        ctx.lineWidth = 2.2;
                        ctx.lineCap = 'round';
                        ctx.lineJoin = 'round';
                        ctx.strokeStyle = '#111827';
                        if (previo) {
                            const img = new Image();
                            img.onload = () => ctx.drawImage(img, 0, 0, caja.width, caja.height);
                            img.src = previo;
                        }
                    },

                    ctx() { return this.$refs.lienzo.getContext('2d'); },

                    punto(e) {
                        const caja = this.$refs.lienzo.getBoundingClientRect();
                        return { x: e.clientX - caja.left, y: e.clientY - caja.top };
                    },

                    empezar(e) {
                        e.preventDefault();
                        this.dibujando = true;
                        this.$refs.lienzo.setPointerCapture?.(e.pointerId);
                        const p = this.punto(e);
                        const ctx = this.ctx();
                        ctx.beginPath();
                        ctx.moveTo(p.x, p.y);
                        ctx.lineTo(p.x + 0.1, p.y + 0.1);
                        ctx.stroke();
                    },

                    mover(e) {
                        if (!this.dibujando) return;
                        const p = this.punto(e);
                        const ctx = this.ctx();
                        ctx.lineTo(p.x, p.y);
                        ctx.stroke();
                    },

                    terminar() {
                        if (!this.dibujando) return;
                        this.dibujando = false;
                        this.vacia = false;
                        this.valor = this.$refs.lienzo.toDataURL('image/png');
                        this.$dispatch('firma-cambio', { campo, lista: true });
                    },

                    limpiar() {
                        const lienzo = this.$refs.lienzo;
                        this.ctx().clearRect(0, 0, lienzo.width, lienzo.height);
                        this.vacia = true;
                        this.valor = '';
                        this.$dispatch('firma-cambio', { campo, lista: false });
                    },
                };
            }
        </script>
    @endpush
@endonce
