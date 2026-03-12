<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Sistema de Citas Médicas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *{box-sizing:border-box} body{margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#0f172a,#1d4ed8);font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif}.login-card{width:100%;max-width:430px;background:#fff;border-radius:20px;padding:34px;box-shadow:0 20px 45px rgba(0,0,0,.18)}.login-header{text-align:center;margin-bottom:24px}.icon{width:70px;height:70px;margin:0 auto 16px;border-radius:18px;background:#dbeafe;color:#1d4ed8;display:flex;align-items:center;justify-content:center;font-size:28px}.form-group{margin-bottom:16px}label{display:block;margin-bottom:8px;font-size:14px;font-weight:600}input{width:100%;padding:12px 14px;border:1px solid #d1d5db;border-radius:12px;font-size:14px}.btn-login{width:100%;border:none;background:#2563eb;color:#fff;padding:13px;border-radius:12px;font-size:15px;cursor:pointer;font-weight:700}.alert{padding:12px 14px;border-radius:12px;margin-bottom:16px;font-size:14px}.alert-error{background:#fee2e2;color:#991b1b}.demo{margin-top:18px;padding:14px;border-radius:14px;background:#f8fafc;font-size:13px;color:#475569}
    </style>
</head>
<body>
<div class="login-card">
    <div class="login-header">
        <div class="icon"><i class="fa-solid fa-hospital"></i></div>
        <h2>Sistema de Citas Médicas</h2>
        <p>Inicia sesión para continuar</p>
    </div>
    <?php if (!empty($_SESSION['error'])): ?><div class="alert alert-error"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div><?php endif; ?>
    <?php if (!empty($_SESSION['success'])): ?><div class="alert" style="background:#dcfce7;color:#166534;"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div><?php endif; ?>
    <form method="POST" action="index.php?controller=auth&action=autenticar">
        <div class="form-group"><label for="email">Correo electrónico</label><input type="email" name="email" id="email" required></div>
        <div class="form-group"><label for="password">Contraseña</label><input type="password" name="password" id="password" required></div>
        <button type="submit" class="btn-login"><i class="fa-solid fa-right-to-bracket"></i> Iniciar sesión</button>
    </form>
    <div class="demo"><strong>Usuarios de prueba</strong><br>admin@admin.com<br>recepcion@clinica.com<br>dr.flores@hospital.com<br>Contraseña: <strong>123456</strong></div>
</div>
</body>
</html>
