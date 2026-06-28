<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cita Ya Confirmada</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; justify-content: center; align-items: center; padding: 20px; }
        .container { background: white; border-radius: 10px; padding: 50px 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); max-width: 460px; width: 100%; text-align: center; }
        .icon { font-size: 70px; margin-bottom: 20px; }
        h1 { color: #667eea; font-size: 26px; margin-bottom: 10px; }
        .subtitle { color: #666; margin-bottom: 30px; font-size: 15px; line-height: 1.6; }
        .btn { display: inline-block; background-color: #667eea; color: white; padding: 12px 30px; border-radius: 5px; text-decoration: none; font-weight: bold; font-size: 15px; transition: background-color 0.3s; }
        .btn:hover { background-color: #764ba2; }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">ℹ️</div>
        <h1>Esta cita ya fue confirmada</h1>
        <p class="subtitle">
            Este enlace de confirmación ya fue usado anteriormente.<br>
            Tu cita ya se encuentra confirmada en el sistema.
        </p>
        <a href="{{ route('dashboard') }}" class="btn">Ir al Dashboard</a>
    </div>
</body>
</html>
