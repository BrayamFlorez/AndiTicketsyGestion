<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="5;url={{ route('login') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesión Expirada</title>

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            background: linear-gradient(135deg, #0f172a, #1e3a8a);
            overflow:hidden;
        }

        .background-shape{
            position:absolute;
            border-radius:50%;
            background:rgba(255,255,255,0.05);
            backdrop-filter: blur(10px);
        }

        .shape-1{
            width:300px;
            height:300px;
            top:-100px;
            right:-100px;
        }

        .shape-2{
            width:250px;
            height:250px;
            bottom:-80px;
            left:-80px;
        }

        .card{
            position:relative;
            z-index:2;
            width:90%;
            max-width:550px;
            background:rgba(255,255,255,0.08);
            backdrop-filter: blur(12px);
            border:1px solid rgba(255,255,255,0.15);
            border-radius:20px;
            padding:40px;
            text-align:center;
            color:white;
            box-shadow:0 20px 40px rgba(0,0,0,.25);
        }

        .icon{
            font-size:70px;
            margin-bottom:20px;
        }

        .code{
            font-size:64px;
            font-weight:700;
            color:#60a5fa;
            margin-bottom:10px;
        }

        h2{
            margin-bottom:15px;
            font-weight:600;
        }

        p{
            color:#dbeafe;
            line-height:1.6;
        }

        .countdown{
            margin-top:25px;
            font-size:18px;
            font-weight:600;
        }

        .seconds{
            color:#93c5fd;
            font-size:28px;
        }

        .ticket-box{
            margin:25px auto;
            width:120px;
            height:120px;
            border-radius:16px;
            background:rgba(96,165,250,.15);
            display:flex;
            align-items:center;
            justify-content:center;
            border:1px solid rgba(96,165,250,.4);
        }

        .progress{
            width:100%;
            height:6px;
            background:rgba(255,255,255,.15);
            border-radius:10px;
            margin-top:25px;
            overflow:hidden;
        }

        .progress-bar{
            height:100%;
            width:100%;
            background:#60a5fa;
            animation: progress 5s linear forwards;
        }

        @keyframes progress {
            from { width:100%; }
            to { width:0%; }
        }
    </style>
</head>
<body>

    <div class="background-shape shape-1"></div>
    <div class="background-shape shape-2"></div>

    <div class="card">

        <div class="ticket-box">
            <div class="icon">🎫</div>
        </div>

        <div class="code">419</div>

        <h2>Sesión expirada</h2>

        <p>
            Por motivos de seguridad, tu sesión en la plataforma de gestión de tickets ha finalizado.
            Inicia sesión nuevamente para continuar administrando solicitudes, incidencias y seguimientos.
        </p>

        <div class="countdown">
            Redirigiendo al acceso en
            <span class="seconds" id="countdown">5</span>
            segundos...
        </div>

        <div class="progress">
            <div class="progress-bar"></div>
        </div>

    </div>

    <script>
        let seconds = 5;
        const countdown = document.getElementById('countdown');

        const interval = setInterval(() => {
            seconds--;
            countdown.textContent = seconds;

            if (seconds <= 0) {
                clearInterval(interval);
                window.location.href = "{{ route('login') }}";
            }
        }, 1000);
    </script>

</body>
</html>