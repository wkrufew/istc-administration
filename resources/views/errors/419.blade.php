<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="4;url={{ route('login') }}">
    <title>Sesión expirada — ISTC</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: ui-sans-serif, system-ui, sans-serif;
            background: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,.08);
            padding: 48px 40px;
            max-width: 420px;
            width: 90%;
            text-align: center;
        }
        .icon { font-size: 48px; margin-bottom: 16px; }
        h1 { font-size: 1.4rem; font-weight: 700; margin-bottom: 8px; }
        p  { font-size: .95rem; color: #64748b; line-height: 1.6; }
        .redirect-note { margin-top: 20px; font-size: .85rem; color: #94a3b8; }
        a.btn {
            display: inline-block;
            margin-top: 24px;
            background: #1d4ed8;
            color: #fff;
            padding: 10px 28px;
            border-radius: 8px;
            text-decoration: none;
            font-size: .9rem;
            font-weight: 600;
        }
        a.btn:hover { background: #1e40af; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">⏱</div>
        <h1>Sesión expirada</h1>
        <p>Tu sesión ha expirado por inactividad. Serás redirigido al inicio de sesión automáticamente.</p>
        <p class="redirect-note">Redirigiendo en unos segundos...</p>
        <a class="btn" href="{{ route('login') }}">Ir al inicio de sesión</a>
    </div>
</body>
</html>
