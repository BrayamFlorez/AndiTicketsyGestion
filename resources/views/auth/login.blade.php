{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión — ANDI SOFTWARE</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body x-data="loginProceso()">

{{-- Componente Animado Cyber del Oso Andino --}}
<x-oso-login-loader />

<div class="bg-glow"></div>
<div class="bg-dots"></div>

{{-- ══════════════════════════════ PANEL IZQUIERDO ══ --}}
<div class="panel-brand">

    <div class="orb-wrap">
        <div class="orb-halo"></div>
        <div class="ring ring-1"></div>
        <div class="ring ring-2"></div>
        <div class="ring ring-3"></div>
        <div class="orb-core"><i class="fas fa-network-wired"></i></div>
    </div>

    <div class="brand-top">
        <a href="{{ url('/') }}" class="brand">
            <div class="brand-mark"><i class="fas fa-network-wired"></i></div>
            <div>
                <div class="brand-name">ANDI SOFTWARE</div>
                <div class="brand-sub">SOPORTE · FLUJOS · INVENTARIO</div>
            </div>
        </a>

        <div class="pill">
            <span class="pill-dot"></span>
            Plataforma interna
        </div>

        <h2 class="panel-title">
            Tu operación técnica<br>
            y documental en <span class="grad">un solo lugar</span>
        </h2>

        <p class="panel-desc">
            Tickets de soporte, inventario de equipos y aprobaciones documentales
            con trazabilidad completa de punta a punta.
        </p>
    </div>

    <div class="panel-features">
        <div class="feature">
            <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
            <span class="feature-text">Acceso segmentado por rol y cobertura</span>
        </div>
        <div class="feature">
            <div class="feature-icon"><i class="fas fa-stream"></i></div>
            <span class="feature-text">Historial inmutable de cada decisión</span>
        </div>
        <div class="feature">
            <div class="feature-icon"><i class="fas fa-bell"></i></div>
            <span class="feature-text">Notificaciones en cada cambio de estado</span>
        </div>
    </div>

</div>

{{-- ══════════════════════════════ PANEL DERECHO ══ --}}
<div class="panel-form">

    <a href="{{ url('/') }}" class="back-home">
        <i class="fas fa-arrow-left" style="font-size:10px;"></i>
        <span>Volver al inicio</span>
    </a>

    <div class="form-box">

        {{-- Logo visible solo en móvil --}}
        <div class="mobile-brand">
            <div class="brand-mark"><i class="fas fa-network-wired"></i></div>
            <div class="brand-name">ANDI SOFTWARE</div>
            <div class="brand-sub">SOPORTE · FLUJOS · INVENTARIO</div>
        </div>

        <div class="form-head">
            <h1>Iniciar sesión</h1>
            <p>Ingresá tus credenciales para acceder al panel.</p>
        </div>

        {{-- Mensaje de estado (ej: password restablecida) --}}
        @if (session('status'))
            <div class="alert alert-ok">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        {{-- Errores de validación --}}
        @if ($errors->any())
            <div class="alert">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Formulario con interceptor Alpine --}}
        <form method="POST" action="{{ route('login') }}" @submit.prevent="iniciarValidacion($event)">
            @csrf

            {{-- Email --}}
            <div class="field">
                <label class="field-label" for="email">Correo electrónico</label>
                <div class="field-wrap">
                    <i class="fas fa-envelope field-icon"></i>
                    <input type="email" id="email" name="email"
                           value="{{ old('email') }}"
                           required autofocus autocomplete="username"
                           placeholder="tu@empresa.com"
                           class="input @error('email') has-error @enderror">
                </div>
                @error('email')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Contraseña --}}
            <div class="field" x-data="{ visible: false }">
                <label class="field-label" for="password">Contraseña</label>
                <div class="field-wrap">
                    <i class="fas fa-lock field-icon"></i>
                    <input :type="visible ? 'text' : 'password'"
                           id="password" name="password"
                           required autocomplete="current-password"
                           placeholder="••••••••"
                           class="input @error('password') has-error @enderror"
                           style="padding-right: 44px;">
                    <button type="button" class="toggle-pass"
                            @click="visible = !visible"
                            :aria-label="visible ? 'Ocultar contraseña' : 'Mostrar contraseña'">
                        <i class="fas" :class="visible ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
                @error('password')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Recordar / Olvidé --}}
            <div class="form-row">
                <label class="check">
                    <input type="checkbox" name="remember" @checked(old('remember'))>
                    <span>Mantener sesión</span>
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="link">
                        ¿Olvidaste tu contraseña?
                    </a>
                @endif
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-submit">
                Entrar al panel
                <i class="fas fa-arrow-right"></i>
            </button>
        </form>

        <div class="form-foot">
            Acceso restringido al personal autorizado.
        </div>

    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('loginProceso', () => ({
            estado: 'inactivo', // inactivo | validando | acceso | rechazado

            async iniciarValidacion(e) {
                const form = e.target;
                this.estado = 'validando';

                try {
                    // Petición silenciosa a Laravel
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        // EXITOSO
                        this.estado = 'acceso';
                        setTimeout(() => {
                            window.location.href = response.redirected ? response.url : '/';
                        }, 1200);
                    } else {
                        // FALLIDO (Contraseña incorrecta, etc)
                        this.estado = 'rechazado';
                        
                        // Le damos 1.5 segundos al usuario para que vea al oso denegando el acceso
                        setTimeout(() => {
                            this.estado = 'inactivo';
                            
                            // Una vez oculto, forzamos el submit normal para que 
                            // Laravel recargue la página y muestre los mensajes rojos nativos
                            form.submit();
                        }, 1500);
                    }
                } catch (error) {
                    // Fallback en caso de error de conexión a internet
                    this.estado = 'rechazado';
                    setTimeout(() => {
                        this.estado = 'inactivo';
                        form.submit();
                    }, 1500);
                }
            }
        }));
    });
</script>

</body>
</html>