<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Recurso no encontrado</title>

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            background:linear-gradient(135deg,#0f172a,#1e40af);
            overflow:hidden;
        }

        .shape{
            position:absolute;
            border-radius:50%;
            background:rgba(255,255,255,.05);
        }

        .shape-1{
            width:350px;
            height:350px;
            top:-120px;
            right:-120px;
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
            max-width:600px;
            padding:40px;
            text-align:center;
            border-radius:20px;
            background:rgba(255,255,255,.08);
            backdrop-filter:blur(12px);
            border:1px solid rgba(255,255,255,.15);
            box-shadow:0 20px 40px rgba(0,0,0,.25);
            color:#fff;
        }

        .icon{
            font-size:80px;
            margin-bottom:15px;
        }

        .code{
            font-size:80px;
            font-weight:700;
            color:#60a5fa;
            line-height:1;
        }

        h2{
            margin:20px 0 15px;
            font-weight:600;
        }

        p{
            color:#dbeafe;
            line-height:1.6;
        }

        .countdown{
            margin-top:25px;
            font-size:18px;
        }

        .seconds{
            color:#93c5fd;
            font-size:28px;
            font-weight:bold;
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
            width:100%;
            height:100%;
            background:#60a5fa;
            animation:progress 5s linear forwards;
        }

        .btn{
            display:inline-block;
            margin-top:25px;
            padding:12px 24px;
            border-radius:10px;
            text-decoration:none;
            color:white;
            background:#2563eb;
            transition:.3s;
        }

        .btn:hover{
            background:#1d4ed8;
        }

        @keyframes progress{
            from { width:100%; }
            to { width:0%; }
        }
    </style>
</head>
<body>

    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>

    <div class="card">

        <div class="icon">🔍</div>

        <div class="code">404</div>

        <h2>Ticket o recurso no encontrado</h2>

        <p>
            El recurso que intentas consultar no existe, fue eliminado
            o la URL ingresada no es válida.
        </p>

        <div class="countdown">
            Regresando a la página anterior en
            <span class="seconds" id="countdown">5</span>
            segundos...
        </div>

        <div class="progress">
            <div class="progress-bar"></div>
        </div>

        <a href="javascript:history.back()" class="btn">
            Volver ahora
        </a>

    </div>

    <script>
        let seconds = 5;
        const countdown = document.getElementById('countdown');

        const interval = setInterval(() => {
            seconds--;
            countdown.textContent = seconds;

            if (seconds <= 0) {
                clearInterval(interval);

                if (document.referrer) {
                    history.back();
                } else {
                    window.location.href = "/";
                }
            }
        }, 1000);
    </script>

</body>
</html>