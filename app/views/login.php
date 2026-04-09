<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Sistema de Citas Médicas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/login.css">
    
</head>
<body>
<div class="login-card">
    <div class="login-header">
        <div class="icon"><i class="fa-solid fa-hospital"></i></div>
        <h2>Sistema de Citas Médicas</h2>
        <p>Inicia sesión para continuar</p>
    </div>
    <?php if (!empty($_SESSION['error'])): ?><div class="alert alert-error"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div><?php endif; ?>
    <?php if (!empty($_SESSION['success'])): ?><div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div><?php endif; ?>
    <?php if (!empty($_SESSION['info'])): ?><div class="alert alert-info"><?= htmlspecialchars($_SESSION['info']); unset($_SESSION['info']); ?></div><?php endif; ?>
    <form method="POST" action="index.php?controller=auth&action=autenticar">
        <div class="form-group"><label for="email">Correo electrónico</label><input type="email" name="email" id="email" required></div>
        <div class="form-group"><label for="password">Contraseña</label><input type="password" name="password" id="password" required></div>
        <button type="submit" class="btn-login"><i class="fa-solid fa-right-to-bracket"></i> Iniciar sesión</button>
    </form>
    <div class="notice"><strong>Seguridad:</strong> si ingresas con una contraseña temporal, el sistema te obligará a cambiarla antes de poder usar el panel.</div>
    
</div>
</body>
</html>
