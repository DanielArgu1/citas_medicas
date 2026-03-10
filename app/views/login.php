<<<<<<< HEAD
=======
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Sistema de Citas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body{
            margin:0;
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            background:linear-gradient(135deg, #0f172a, #1d4ed8);
            font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-card{
            width:100%;
            max-width:420px;
            background:#fff;
            border-radius:20px;
            padding:34px;
            box-shadow:0 20px 45px rgba(0,0,0,.18);
        }

        .login-header{
            text-align:center;
            margin-bottom:24px;
        }

        .login-header .icon{
            width:70px;
            height:70px;
            margin:0 auto 16px;
            border-radius:18px;
            background:#dbeafe;
            color:#1d4ed8;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:28px;
        }

        .login-header h2{
            margin:0 0 6px;
        }

        .login-header p{
            margin:0;
            color:#6b7280;
        }

        .form-group{
            margin-bottom:16px;
        }

        label{
            display:block;
            margin-bottom:8px;
            font-size:14px;
            font-weight:600;
        }

        input{
            width:100%;
            padding:12px 14px;
            border:1px solid #d1d5db;
            border-radius:12px;
            font-size:14px;
            box-sizing:border-box;
        }

        input:focus{
            outline:none;
            border-color:#2563eb;
            box-shadow:0 0 0 4px rgba(37,99,235,.1);
        }

        .btn-login{
            width:100%;
            border:none;
            background:#2563eb;
            color:#fff;
            padding:13px;
            border-radius:12px;
            font-size:15px;
            cursor:pointer;
            font-weight:700;
        }

        .btn-login:hover{
            background:#1d4ed8;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <div class="icon">
            <i class="fa-solid fa-hospital"></i>
        </div>
        <h2>Sistema de Citas Médicas</h2>
        <p>Inicia sesión para continuar</p>
    </div>

    <form method="POST" action="index.php?controller=auth&action=autenticar">
        <div class="form-group">
            <label for="email">Correo electrónico</label>
            <input type="email" name="email" id="email" required>
        </div>

        <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" name="password" id="password" required>
        </div>

        <button type="submit" class="btn-login">
            <i class="fa-solid fa-right-to-bracket"></i> Iniciar sesión
        </button>
    </form>
</div>

</body>
</html>
>>>>>>> 07c8788 (se agregaron vistas a formularios)
