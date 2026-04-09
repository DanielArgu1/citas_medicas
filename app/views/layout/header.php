<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$currentController = $_GET['controller'] ?? 'dashboard';
$currentAction = $_GET['action'] ?? 'index';
$role = $_SESSION['usuario_rol'] ?? null;
function isActive($controller, $action = null) {
    global $currentController, $currentAction;
    return $currentController === $controller && ($action === null || $currentAction === $action) ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Citas Médicas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/css/intlTelInput.css">
    <link rel="stylesheet" href="assets/css/app.css">
<?php
$pageCss = [];
if ($currentController === 'auditoria') {
    $pageCss[] = 'assets/css/admin.css';
}
if ($currentController === 'reporte') {
    $pageCss[] = 'assets/css/reportes.css';
}
if ($currentController === 'historial' && $currentAction === 'registrar') {
    $pageCss[] = 'assets/css/historial-registrar.css';
}
if ($currentController === 'historial' && $currentAction === 'ver') {
    $pageCss[] = 'assets/css/historial-ver.css';
}
if ($currentController === 'paciente' && $currentAction === 'perfil') {
    $pageCss[] = 'assets/css/paciente-perfil.css';
}
foreach ($pageCss as $cssFile): ?>
    <link rel="stylesheet" href="<?= htmlspecialchars($cssFile) ?>">
<?php endforeach; ?>
    
</head>
<body>
<?php if ($role): ?>
<div class="app">
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-icon"><i class="fa-solid fa-hospital"></i></div>
            <div class="brand-text"><strong>Citas Médicas</strong><span>Panel profesional</span></div>
        </div>
        <div class="menu-title">Menú</div>
        <nav class="nav">
            <a class="nav-link <?= isActive('dashboard') ?>" href="index.php?controller=dashboard&action=index"><i class="fa-solid fa-chart-line"></i><span>Dashboard</span></a>

            <?php if (in_array($role, ['admin','recepcion'], true)): ?>
                <a class="nav-link <?= isActive('paciente', 'index') ?>" href="index.php?controller=paciente&action=index"><i class="fa-solid fa-users"></i><span>Pacientes</span></a>
                <a class="nav-link <?= isActive('cita') ?>" href="index.php?controller=cita&action=index"><i class="fa-solid fa-calendar-check"></i><span>Citas</span></a>
            <?php endif; ?>

            <?php if ($role === 'admin'): ?>
                <a class="nav-link <?= isActive('medico') ?>" href="index.php?controller=medico&action=index"><i class="fa-solid fa-user-doctor"></i><span>Médicos</span></a>
                <a class="nav-link <?= isActive('auditoria') ?>" href="index.php?controller=auditoria&action=index">
                    <i class="fa-solid fa-shield-halved"></i><span>Auditoría</span>
                </a>
            <?php endif; ?>

            <?php if (in_array($role, ['admin','recepcion'], true)): ?>
                <a class="nav-link <?= isActive('reporte') ?>" href="index.php?controller=reporte&action=index"><i class="fa-solid fa-file-waveform"></i><span>Reportes</span></a>
            <?php endif; ?>

            <?php if ($role === 'medico'): ?>
                <a class="nav-link <?= isActive('cita') ?>" href="index.php?controller=cita&action=index"><i class="fa-solid fa-stethoscope"></i><span>Mis citas</span></a>
            <?php endif; ?>

            <?php if ($role === 'paciente'): ?>
                <a class="nav-link <?= isActive('cita', 'crearPaciente') ?>" href="index.php?controller=cita&action=crearPaciente"><i class="fa-solid fa-calendar-plus"></i><span>Agendar cita</span></a>
                <a class="nav-link <?= isActive('cita', 'index') ?>" href="index.php?controller=cita&action=index"><i class="fa-solid fa-calendar-days"></i><span>Mis citas</span></a>
                <a class="nav-link <?= isActive('historial') ?>" href="index.php?controller=historial&action=ver&paciente_id=<?= (int)($_SESSION['paciente_id'] ?? 0) ?>"><i class="fa-solid fa-notes-medical"></i><span>Mi historial</span></a>
                <a class="nav-link <?= isActive('paciente', 'perfil') ?>" href="index.php?controller=paciente&action=perfil"><i class="fa-solid fa-address-card"></i><span>Mi perfil</span></a>
            <?php endif; ?>

            

            <?php if (in_array($role, ['medico', 'paciente'], true)): ?>
                <a class="nav-link <?= isActive('auth', 'cambiarPassword') ?>" href="index.php?controller=auth&action=cambiarPassword"><i class="fa-solid fa-key"></i><span>Cambiar contraseña</span></a>
            <?php endif; ?>
        </nav>
        <div class="sidebar-footer">
            <a class="logout-link" href="index.php?controller=auth&action=logout"><i class="fa-solid fa-right-from-bracket"></i><span>Cerrar sesión</span></a>
        </div>
    </aside>
    <main class="main">
        <header class="topbar">
            <h1 class="page-title"><?= ucfirst($currentController) ?></h1>
            <div class="user-box"><strong><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? '') ?></strong><span><?= htmlspecialchars(strtoupper($role)) ?></span></div>
        </header>
        <section class="content">
            <?php if (!empty($_SESSION['success'])): ?><div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div><?php endif; ?>
            <?php if (!empty($_SESSION['error'])): ?><div class="alert alert-error"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div><?php endif; ?>
            <?php if (!empty($_SESSION['info'])): ?><div class="alert alert-info"><?= htmlspecialchars($_SESSION['info']); unset($_SESSION['info']); ?></div><?php endif; ?>
        <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/intlTelInput.min.js"></script>
<?php endif; ?>
