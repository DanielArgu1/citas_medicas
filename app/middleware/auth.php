<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function require_login()
{
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: index.php?controller=auth&action=login");
        exit;
    }
}

function current_user_role()
{
    return $_SESSION['usuario_rol'] ?? null;
}

function current_user_id()
{
    return $_SESSION['usuario_id'] ?? null;
}

function current_medico_id()
{
    return $_SESSION['medico_id'] ?? null;
}

function current_paciente_id()
{
    return $_SESSION['paciente_id'] ?? null;
}

function must_change_password()
{
    return !empty($_SESSION['debe_cambiar_password']);
}

function enforce_password_change($controller, $action)
{
    if (!isset($_SESSION['usuario_id']) || !must_change_password()) {
        return;
    }

    $allowedRoutes = [
        'auth' => ['cambiarPassword', 'actualizarPassword', 'logout'],
    ];

    if (isset($allowedRoutes[$controller]) && in_array($action, $allowedRoutes[$controller], true)) {
        return;
    }

    $_SESSION['info'] = 'Debes cambiar tu contraseña temporal para usar el sistema.';
    header("Location: index.php?controller=auth&action=cambiarPassword");
    exit;
}

function require_roles(array $roles)
{
    require_login();
    if (!in_array(current_user_role(), $roles, true)) {
        $_SESSION['error'] = 'No tienes permisos para acceder a esta sección.';
        header("Location: index.php?controller=dashboard&action=index");
        exit;
    }
}

function flash($type, $message)
{
    $_SESSION[$type] = $message;
}
