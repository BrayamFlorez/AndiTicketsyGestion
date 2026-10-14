{{-- resources/views/welcome.blade.php --}}
<!DOCTYPE html>
<html lang="es" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ANDI — Gestión IT y flujos documentales</title>
    <meta name="description"
        content="Plataforma integral de soporte técnico, inventario de equipos y aprobaciones documentales para tu organización.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --bg: #0a0e14;
            --bg-2: #0f141c;
            --accent: #007acc;
            --accent-2: #22a3ff;
            --accent-3: #4dd0ff;
            --glow: rgba(0, 122, 204, 0.35);
            --border: #1c2530;
            --border-2: #2a3644;
            --text: #f2f5f8;
            --text-2: #9aa7b5;
            --text-3: #5c6874;
            --font-display: 'Space Grotesk', system-ui, sans-serif;
            --font-body: 'Inter', system-ui, sans-serif;
            --font-mono: 'JetBrains Mono', ui-monospace, monospace;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: var(--font-body);
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        /* ── Fondo: glow radial + malla de puntos ────────────────────── */
        .bg-glow {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            background:
                radial-gradient(ellipse 70% 60% at 75% 35%, rgba(0, 122, 204, 0.22), transparent 65%),
                radial-gradient(ellipse 50% 50% at 20% 20%, rgba(34, 163, 255, 0.10), transparent 70%),
                radial-gradient(ellipse 90% 50% at 50% 100%, rgba(0, 122, 204, 0.12), transparent 70%);
        }

        .bg-dots {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            background-image: radial-gradient(circle, rgba(0, 122, 204, 0.28) 1px, transparent 1px);
            background-size: 28px 28px;
            mask: radial-gradient(ellipse 75% 65% at 60% 45%, black 10%, transparent 80%);
            -webkit-mask: radial-gradient(ellipse 75% 65% at 60% 45%, black 10%, transparent 80%);
            animation: dots-drift 24s linear infinite;
        }

        @keyframes dots-drift {
            to {
                background-position: 28px 28px;
            }
        }

        .bg-grid-lines {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            background-image:
                linear-gradient(rgba(28, 37, 48, 0.5) 1px, transparent 1px),
                linear-gradient(90deg, rgba(28, 37, 48, 0.5) 1px, transparent 1px);
            background-size: 90px 90px;
            mask: radial-gradient(ellipse 80% 70% at 50% 40%, black, transparent 85%);
            -webkit-mask: radial-gradient(ellipse 80% 70% at 50% 40%, black, transparent 85%);
        }

        .shell {
            position: relative;
            z-index: 1;
        }

        .wrap {
            max-width: 1340px;
            margin: 0 auto;
            padding: 0 32px;
        }

        /* ── Navbar ──────────────────────────────────────────────────── */
        nav {
            padding: 20px 0;
            position: sticky;
            top: 0;
            z-index: 50;
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            background: rgba(10, 14, 20, 0.72);
            border-bottom: 1px solid transparent;
            transition: border-color .3s;
        }

        nav.scrolled {
            border-bottom-color: var(--border);
        }

        .nav-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .brand-mark {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--accent), var(--accent-3));
            display: grid;
            place-items: center;
            color: #fff;
            font-size: 16px;
            box-shadow: 0 0 0 1px rgba(255, 255, 255, .06), 0 8px 24px var(--glow);
        }

        .brand-text {
            line-height: 1.15;
        }

        .brand-name {
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 16px;
            letter-spacing: -.02em;
            color: var(--text);
        }

        .brand-sub {
            font-family: var(--font-mono);
            font-size: 9px;
            color: var(--text-3);
            letter-spacing: .16em;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 30px;
        }

        .nav-links a {
            color: var(--text-2);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            transition: color .18s;
        }

        .nav-links a:hover {
            color: var(--text);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            border-radius: 999px;
            font-family: var(--font-display);
            font-weight: 600;
            text-decoration: none;
            transition: all .2s cubic-bezier(.4, 0, .2, 1);
            white-space: nowrap;
            cursor: pointer;
            border: 1px solid transparent;
        }

        .btn-ghost {
            padding: 9px 18px;
            font-size: 13px;
            color: var(--text-2);
            border-color: var(--border-2);
            background: transparent;
        }

        .btn-ghost:hover {
            color: var(--text);
            border-color: var(--accent);
            background: rgba(0, 122, 204, .07);
        }

        .btn-solid {
            padding: 10px 22px;
            font-size: 13px;
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            color: #fff;
            box-shadow: 0 6px 20px var(--glow);
        }

        .btn-solid:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 30px var(--glow);
        }

        .btn-hero {
            padding: 15px 32px;
            font-size: 15px;
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            color: #fff;
            box-shadow: 0 10px 34px var(--glow);
        }

        .btn-hero:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 44px var(--glow);
        }

        .btn-hero i {
            transition: transform .25s;
        }

        .btn-hero:hover i {
            transform: translateX(4px);
        }

        .nav-toggle {
            display: none;
            background: none;
            border: 1px solid var(--border-2);
            color: var(--text);
            padding: 8px 11px;
            border-radius: 8px;
            cursor: pointer;
        }

        /* ── Hero ────────────────────────────────────────────────────── */
        .hero {
            display: grid;
            grid-template-columns: 1.05fr 1fr;
            gap: 48px;
            align-items: center;
            padding: 56px 0 40px;
            min-height: calc(100vh - 260px);
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 7px 16px 7px 7px;
            border-radius: 999px;
            background: rgba(0, 122, 204, .08);
            border: 1px solid rgba(0, 122, 204, .28);
            font-size: 12.5px;
            color: var(--text-2);
            text-decoration: none;
            margin-bottom: 28px;
            transition: all .2s;
        }

        .pill:hover {
            border-color: var(--accent);
            background: rgba(0, 122, 204, .14);
            color: var(--text);
        }

        .pill-tag {
            background: var(--accent);
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 999px;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        h1.hero-title {
            font-family: var(--font-display);
            font-weight: 600;
            font-size: clamp(38px, 4.4vw, 62px);
            line-height: 1.04;
            letter-spacing: -.035em;
            margin-bottom: 22px;
        }

        h1.hero-title .grad {
            background: linear-gradient(100deg, var(--accent-2) 10%, var(--accent-3) 55%, #8fe3ff 95%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-sub {
            color: var(--text-2);
            font-size: 16.5px;
            line-height: 1.65;
            max-width: 520px;
            margin-bottom: 34px;
        }

        .hero-sub strong {
            color: var(--text);
            font-weight: 600;
        }

        .hero-actions {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .hero-note {
            font-family: var(--font-mono);
            font-size: 11.5px;
            color: var(--text-3);
            letter-spacing: .02em;
        }

        /* ── Showcase de imágenes rotativas ──────────────────────────── */
        .showcase {
            position: relative;
            height: 520px;
            display: grid;
            place-items: center;
        }

        .showcase-halo {
            position: absolute;
            width: 480px;
            height: 480px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0, 122, 204, .30) 0%, rgba(0, 122, 204, .10) 42%, transparent 68%);
            filter: blur(8px);
            animation: halo-pulse 6s ease-in-out infinite;
        }

        @keyframes halo-pulse {

            0%,
            100% {
                transform: scale(1);
                opacity: .85;
            }

            50% {
                transform: scale(1.09);
                opacity: 1;
            }
        }

        .showcase-rings {
            position: absolute;
            inset: 0;
            display: grid;
            place-items: center;
            pointer-events: none;
        }

        .ring {
            position: absolute;
            border-radius: 50%;
            border: 1px solid rgba(0, 122, 204, .18);
        }

        .ring-1 {
            width: 340px;
            height: 340px;
            animation: ring-spin 30s linear infinite;
            border-style: dashed;
        }

        .ring-2 {
            width: 450px;
            height: 450px;
            animation: ring-spin 46s linear infinite reverse;
        }

        .ring-3 {
            width: 560px;
            height: 560px;
            border-color: rgba(0, 122, 204, .09);
        }

        @keyframes ring-spin {
            to {
                transform: rotate(360deg);
            }
        }

        .slides {
            position: relative;
            width: 100%;
            height: 100%;
        }

        .slide {
            position: absolute;
            inset: 0;
            display: grid;
            place-items: center;
            opacity: 0;
            transform: scale(.9) translateY(16px);
            transition: opacity .9s cubic-bezier(.4, 0, .2, 1), transform .9s cubic-bezier(.4, 0, .2, 1);
        }

        .slide.active {
            opacity: 1;
            transform: scale(1) translateY(0);
            animation: float-soft 7s ease-in-out infinite;
        }

        @keyframes float-soft {

            0%,
            100% {
                transform: translateY(0) scale(1);
            }

            50% {
                transform: translateY(-16px) scale(1.012);
            }
        }

        .slide img {
            max-width: 100%;
            max-height: 460px;
            object-fit: contain;
            filter: drop-shadow(0 26px 60px rgba(0, 122, 204, .38));
        }

        .slide-caption {
            position: absolute;
            bottom: -4px;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
            width: max-content;
            max-width: 90%;
        }

        .slide-caption-title {
            font-family: var(--font-display);
            font-weight: 600;
            font-size: 15px;
            color: var(--text);
            margin-bottom: 3px;
        }

        .slide-caption-desc {
            font-size: 12.5px;
            color: var(--text-3);
        }

        .dots-nav {
            position: absolute;
            bottom: -42px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 9px;
        }

        .dot {
            width: 7px;
            height: 7px;
            border-radius: 999px;
            background: var(--border-2);
            border: none;
            cursor: pointer;
            padding: 0;
            transition: all .3s;
        }

        .dot.active {
            width: 24px;
            background: var(--accent-2);
        }

        /* ── Franja de módulos ───────────────────────────────────────── */
        .modules {
            padding: 30px 0 46px;
            border-top: 1px solid var(--border);
        }

        .modules-label {
            text-align: center;
            font-family: var(--font-mono);
            font-size: 10.5px;
            letter-spacing: .18em;
            text-transform: uppercase;
            color: var(--text-3);
            margin-bottom: 26px;
        }

        .modules-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 14px;
        }

        .module {
            padding: 20px 18px;
            border-radius: 14px;
            background: rgba(15, 20, 28, .6);
            border: 1px solid var(--border);
            transition: all .25s cubic-bezier(.4, 0, .2, 1);
            text-decoration: none;
            display: block;
        }

        .module:hover {
            border-color: rgba(0, 122, 204, .45);
            background: rgba(0, 122, 204, .05);
            transform: translateY(-3px);
        }

        .module-icon {
            width: 36px;
            height: 36px;
            border-radius: 9px;
            background: rgba(0, 122, 204, .10);
            border: 1px solid rgba(0, 122, 204, .22);
            color: var(--accent-2);
            display: grid;
            place-items: center;
            font-size: 14px;
            margin-bottom: 13px;
            transition: all .25s;
        }

        .module:hover .module-icon {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
        }

        .module-name {
            font-family: var(--font-display);
            font-weight: 600;
            font-size: 13.5px;
            color: var(--text);
            margin-bottom: 5px;
        }

        .module-desc {
            font-size: 11.5px;
            color: var(--text-3);
            line-height: 1.5;
        }

        /* ── Footer ──────────────────────────────────────────────────── */
        footer {
            border-top: 1px solid var(--border);
            padding: 20px 0 26px;
        }

        .footer-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
            font-family: var(--font-mono);
            font-size: 11px;
            color: var(--text-3);
            letter-spacing: .03em;
        }

        .footer-row a {
            color: var(--text-3);
            text-decoration: none;
            transition: color .18s;
        }

        .footer-row a:hover {
            color: var(--accent-2);
        }

        /* ── Responsive ──────────────────────────────────────────────── */
        @media (max-width: 1100px) {
            .modules-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 940px) {
            .hero {
                grid-template-columns: 1fr;
                gap: 30px;
                padding-top: 36px;
                text-align: center;
            }

            .hero-sub {
                margin-left: auto;
                margin-right: auto;
            }

            .hero-actions {
                justify-content: center;
            }

            .showcase {
                height: 380px;
                order: -1;
            }

            .showcase-halo {
                width: 330px;
                height: 330px;
            }

            .ring-1 {
                width: 250px;
                height: 250px;
            }

            .ring-2 {
                width: 330px;
                height: 330px;
            }

            .ring-3 {
                display: none;
            }

            .slide img {
                max-height: 330px;
            }

            .slide-caption {
                bottom: -20px;
            }

            .dots-nav {
                bottom: -54px;
            }

            .nav-links {
                display: none;
            }

            .nav-toggle {
                display: inline-flex;
            }

            .modules {
                padding-top: 60px;
            }
        }

        @media (max-width: 620px) {
            .wrap {
                padding: 0 20px;
            }

            .modules-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .brand-sub {
                display: none;
            }
        }

        @media (prefers-reduced-motion: reduce) {

            *,
            .slide.active,
            .showcase-halo,
            .ring-1,
            .ring-2,
            .bg-dots {
                animation: none !important;
                transition-duration: .01ms !important;
            }
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body x-data="landing()" x-init="init()">

    <div class="bg-glow"></div>
    <div class="bg-grid-lines"></div>
    <div class="bg-dots"></div>

    <div class="shell">

        {{-- ══════════════════════════════════════ NAVBAR ══ --}}
        <nav :class="{ 'scrolled': scrolled }">
            <div class="wrap nav-row">
                <a href="{{ url('/') }}" class="brand">
                    <div class="brand-mark"
                        style="overflow: hidden; padding: 3px; display: flex; align-items: center; justify-content: center;">
                        <img src="{{ asset('img/andi.webp') }}" alt="{{ config('app.name', 'ANDI') }}"
                            style="width: 100%; height: 100%; object-fit: contain;">
                    </div>
                    <div class="brand-text">
                        <div class="brand-name">{{ config('app.name', env('APP_NAME', 'ANDI')) }}</div>
                        <div class="brand-sub">SOPORTE · FLUJOS · INVENTARIO</div>
                    </div>
                </a>

                <div class="nav-links">
                    <a href="#modulos">Módulos</a>
                    <a href="{{ url('/motor-flujos') }}">Motor de Flujos</a>
                </div>

                <div style="display:flex; align-items:center; gap:10px;">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-solid">
                            <i class="fas fa-columns" style="font-size:11px;"></i> Ir al panel
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-ghost">Iniciar sesión</a>
                    @endauth

                    <button class="nav-toggle" @click="menuAbierto = !menuAbierto" aria-label="Menú">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </nav>

        {{-- ══════════════════════════════════════ HERO ══ --}}
        <div class="wrap">
            <section class="hero">

                {{-- Columna texto --}}
                <div>
                    <a href="{{ url('/motor-flujos') }}" class="pill">
                        <span class="pill-tag">Nuevo</span>
                        Motor de flujos documentales disponible
                        <i class="fas fa-arrow-right" style="font-size:10px; color: var(--accent-2);"></i>
                    </a>

                    <h1 class="hero-title">
                        Tu operación técnica<br>
                        y documental en <span class="grad">un solo lugar</span>
                    </h1>

                    <p class="hero-sub">
                        ANDI centraliza <strong>tickets de soporte</strong>,
                        <strong>inventario de equipos</strong> y <strong>aprobaciones documentales</strong>
                        con trazabilidad completa. Cada solicitud, cada firma y cada archivo
                        quedan registrados de punta a punta.
                    </p>

                    <div class="hero-actions">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn btn-hero">
                                Entrar al panel <i class="fas fa-arrow-right"></i>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-hero">
                                Comenzar ahora <i class="fas fa-arrow-right"></i>
                            </a>
                        @endauth
                        <span class="hero-note">Acceso por rol · Auditoría inmutable</span>
                    </div>
                </div>

                {{-- Columna showcase --}}
                <div class="showcase">
                    <div class="showcase-halo"></div>
                    <div class="showcase-rings">
                        <div class="ring ring-1"></div>
                        <div class="ring ring-2"></div>
                        <div class="ring ring-3"></div>
                    </div>

                    <div class="slides">
                        <template x-for="(s, i) in slides" :key="i">
                            <div class="slide" :class="{ 'active': i === actual }">
                                <img :src="s.img" :alt="s.titulo" loading="lazy">
                            </div>
                        </template>
                    </div>

                    <div class="slide-caption">
                        <div class="slide-caption-title" x-text="slides[actual].titulo"></div>
                        <div class="slide-caption-desc" x-text="slides[actual].desc"></div>
                    </div>

                    <div class="dots-nav">
                        <template x-for="(s, i) in slides" :key="'d' + i">
                            <button class="dot" :class="{ 'active': i === actual }" @click="irA(i)"
                                :aria-label="s.titulo"></button>
                        </template>
                    </div>
                </div>

            </section>
        </div>

        {{-- ══════════════════════════════════════ MÓDULOS ══ --}}
        <div class="wrap">
            <section class="modules" id="modulos">
                <p class="modules-label">Todo lo que incluye la plataforma</p>

                <div class="modules-grid">
                    <div class="module">
                        <div class="module-icon"><i class="fas fa-ticket-alt"></i></div>
                        <div class="module-name">Mesa de ayuda</div>
                        <div class="module-desc">Tickets con prioridad, asignación por cobertura y ciclo completo hasta
                            el cierre.</div>
                    </div>

                    <a href="{{ url('/motor-flujos') }}" class="module">
                        <div class="module-icon"><i class="fas fa-project-diagram"></i></div>
                        <div class="module-name">Motor de flujos</div>
                        <div class="module-desc">Aprobaciones por etapas, firmas paralelas y políticas de rechazo
                            declaradas.</div>
                    </a>

                    <div class="module">
                        <div class="module-icon"><i class="fas fa-desktop"></i></div>
                        <div class="module-name">Inventario</div>
                        <div class="module-desc">Equipos por empresa y sede, con mantenimientos preventivos y hoja de
                            vida.</div>
                    </div>

                    <a href="{{ url('/docs/impresoras') }}" class="module">
                        <div class="module-icon"><i class="fas fa-print"></i></div>
                        <div class="module-name">Seguimiento de Impresoras</div>
                        <div class="module-desc">Organización y control de impresoras por sede y el seguimiento a sus consumibles.</div>
                    </a>

                    <div class="module">
                        <div class="module-icon"><i class="fas fa-shield-alt"></i></div>
                        <div class="module-name">Roles y permisos</div>
                        <div class="module-desc">Matriz granular por rol con auditoría de cada acción sobre el sistema.
                        </div>
                    </div>
                </div>
            </section>
        </div>

        {{-- ══════════════════════════════════════ FOOTER ══ --}}
        <footer>
            <div class="wrap footer-row">
                <div>ANDI &nbsp;·&nbsp; CÚCUTA, COLOMBIA</div>
                <div>
                    <a href="{{ url('/docs/flujos') }}">Documentación</a> &nbsp;·&nbsp;
                    <a href="{{ url('/motor-flujos') }}">Motor de flujos</a> &nbsp;·&nbsp;
                    © {{ date('Y') }}
                </div>
            </div>
        </footer>

    </div>

    <script>
        function landing() {
            return {
                scrolled: false,
                menuAbierto: false,
                actual: 0,
                intervalo: null,

                // Reemplaza las rutas por tus PNG sin fondo en public/img/showcase/
                slides: [
                    //{
                    //img: "{{ asset('img/showcase/tickets.png') }}",
                    //titulo: 'Mesa de ayuda',
                    //desc: 'Del reporte al cierre, con notificaciones en cada paso',
                    //},
                    //{
                    //  img: "{{ asset('img/showcase/flujos.png') }}",
                    //titulo: 'Motor de flujos',
                    //desc: 'Aprobaciones por etapas con trazabilidad completa',
                    //},
                    //{
                    //  img: "{{ asset('img/showcase/inventario.png') }}",
                    //titulo: 'Inventario de equipos',
                    //desc: 'Hoja de vida y mantenimientos por sede',
                    //},
                    {
                        img: "{{ asset('img/showcase/andi.webp') }}",
                        titulo: 'Panel por rol',
                        desc: 'Cada usuario ve las métricas que le importan',
                    },
                ],

                init() {
                    window.addEventListener('scroll', () => {
                        this.scrolled = window.scrollY > 12;
                    }, {
                        passive: true
                    });

                    this.arrancarAutoplay();

                    // Pausa el carrusel si la pestaña no está visible
                    document.addEventListener('visibilitychange', () => {
                        document.hidden ? this.pararAutoplay() : this.arrancarAutoplay();
                    });
                },

                arrancarAutoplay() {
                    this.pararAutoplay();
                    this.intervalo = setInterval(() => {
                        this.actual = (this.actual + 1) % this.slides.length;
                    }, 4200);
                },

                pararAutoplay() {
                    if (this.intervalo) clearInterval(this.intervalo);
                    this.intervalo = null;
                },

                irA(i) {
                    this.actual = i;
                    this.arrancarAutoplay(); // reinicia el temporizador al elegir manualmente
                },
            };
        }
    </script>

</body>

</html>
