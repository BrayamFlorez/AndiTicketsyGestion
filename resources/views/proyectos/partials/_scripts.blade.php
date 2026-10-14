{{-- resources/views/proyectos/partials/_scripts.blade.php — Componente Alpine del detalle --}}
<script>
    function proyectoShow(cfg) {
        const MB = 1024 * 1024;

        const ICONOS = { imagen: 'fa-image', audio: 'fa-microphone', video: 'fa-video', documento: 'fa-file' };

        return {
            filtro: 'todo',
            hoy: cfg.hoy,
            tiposQueAtienden: cfg.tiposQueAtienden,
            modalEtapa: false,
            modalSolicitud: false,
            etapa: {},
            solicitud: {},
            archivos: { etapa: [], solicitud: [] },
            avisoArchivos: { etapa: '', solicitud: '' },

            iniciar() {
                this.etapa = this.etapaVacia('entrega');
                this.solicitud = this.solicitudVacia();

                // La validación falló: reabrir el modal con lo que se había escrito
                if (cfg.reabrir && cfg.old) {
                    const o = cfg.old;
                    const editando = o._editando === '1';

                    if (cfg.reabrir === 'etapa') {
                        this.etapa = Object.assign(this.etapaVacia(o.tipo || 'entrega'), o, {
                            editando,
                            accion: o._accion || cfg.urls.etapaStore,
                            atiende: (o.atiende || []).map(String),
                        });
                        this.modalEtapa = true;
                    }

                    if (cfg.reabrir === 'solicitud') {
                        this.solicitud = Object.assign(this.solicitudVacia(), o, {
                            editando,
                            accion: o._accion || cfg.urls.solicitudStore,
                        });
                        this.modalSolicitud = true;
                    }
                }
            },

            // ── Etapas ──────────────────────────────────────────────
            etapaVacia(tipo) {
                return {
                    editando: false,
                    accion: cfg.urls.etapaStore,
                    tipo,
                    fecha: this.hoy,
                    estado: 'completada',
                    titulo: '',
                    descripcion: '',
                    monto: '',
                    fecha_validez: '',
                    medio_pago: 'transferencia',
                    version: '',
                    ronda: '',
                    avance: '',
                    enlace: '',
                    atiende: [],
                };
            },

            abrirEtapa(tipo = 'entrega') {
                this.etapa = this.etapaVacia(tipo);
                this.sugerirNumeracion();
                this.limpiarArchivos(this.$refs.archivosEtapa, 'etapa');
                this.modalEtapa = true;
            },

            editarEtapa(datos) {
                this.etapa = Object.assign(this.etapaVacia(datos.tipo), datos, { editando: true, atiende: [] });
                this.limpiarArchivos(this.$refs.archivosEtapa, 'etapa');
                this.modalEtapa = true;
            },

            cambiarTipo(tipo) {
                this.etapa.tipo = tipo;
                if (! this.etapa.editando) {
                    this.sugerirNumeracion();
                }
            },

            // Numera sola la próxima entrega (v2, v3…) y la próxima ronda de correcciones
            sugerirNumeracion() {
                const siguiente = (cfg.conteoPorTipo[this.etapa.tipo] || 0) + 1;
                this.etapa.version = this.etapa.tipo === 'entrega' ? 'v' + siguiente : '';
                this.etapa.ronda = this.etapa.tipo === 'correccion' ? siguiente : '';
            },

            tituloPorDefecto() {
                if (this.etapa.tipo === 'entrega') return ('Entrega ' + (this.etapa.version || '')).trim();
                if (this.etapa.tipo === 'correccion') return ('Correcciones · Ronda ' + (this.etapa.ronda || '')).trim();
                return cfg.etiquetas[this.etapa.tipo] || 'Etapa';
            },

            // ── Solicitudes ─────────────────────────────────────────
            solicitudVacia() {
                return {
                    editando: false,
                    accion: cfg.urls.solicitudStore,
                    fecha_solicitud: this.hoy,
                    canal: 'whatsapp',
                    prioridad: 'media',
                    titulo: '',
                    descripcion: '',
                };
            },

            abrirSolicitud() {
                this.solicitud = this.solicitudVacia();
                this.limpiarArchivos(this.$refs.archivosSolicitud, 'solicitud');
                this.modalSolicitud = true;
            },

            editarSolicitud(datos) {
                this.solicitud = Object.assign(this.solicitudVacia(), datos, { editando: true });
                this.limpiarArchivos(this.$refs.archivosSolicitud, 'solicitud');
                this.modalSolicitud = true;
            },

            // ── Archivos ────────────────────────────────────────────
            tipoArchivo(f) {
                if (f.type.startsWith('image/')) return 'imagen';
                if (f.type.startsWith('audio/')) return 'audio';
                if (f.type.startsWith('video/')) return 'video';
                return 'documento';
            },

            pesoLegible(b) {
                return b >= MB ? (b / MB).toFixed(1) + ' MB' : Math.max(1, Math.round(b / 1024)) + ' KB';
            },

            /** Descarta los que superan 20 MB o exceden el máximo y refresca la lista visible */
            procesarArchivos(input, destino) {
                const dt = new DataTransfer();
                const rechazados = [];

                for (const f of input.files) {
                    if (f.size > cfg.maxBytes) {
                        rechazados.push(`"${f.name}" supera 20 MB`);
                    } else if (dt.files.length >= cfg.maxArchivos) {
                        rechazados.push(`"${f.name}" excede el máximo de ${cfg.maxArchivos} archivos`);
                    } else {
                        dt.items.add(f);
                    }
                }

                input.files = dt.files;
                this.avisoArchivos[destino] = rechazados.length ? 'No se adjuntó: ' + rechazados.join(', ') + '.' : '';
                this.refrescarLista(input, destino);
            },

            quitarArchivo(input, destino, indice) {
                const dt = new DataTransfer();
                Array.from(input.files).forEach((f, i) => { if (i !== indice) dt.items.add(f); });
                input.files = dt.files;
                this.refrescarLista(input, destino);
            },

            limpiarArchivos(input, destino) {
                if (input) input.value = '';
                this.archivos[destino] = [];
                this.avisoArchivos[destino] = '';
            },

            refrescarLista(input, destino) {
                this.archivos[destino] = Array.from(input.files).map(f => ({
                    nombre: f.name,
                    peso: this.pesoLegible(f.size),
                    icono: ICONOS[this.tipoArchivo(f)],
                }));
            },
        };
    }
</script>
