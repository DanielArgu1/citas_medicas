<?php

require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Usuario.php';

class AuthController extends Controller {

    private function setSessionData(array $usuario): void
    {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        $_SESSION['usuario_rol'] = $usuario['rol'];
        $_SESSION['medico_id'] = $usuario['medico_id'] ?? null;
        $_SESSION['paciente_id'] = $usuario['paciente_id'] ?? null;
        $_SESSION['debe_cambiar_password'] = !empty($usuario['debe_cambiar_password']) ? 1 : 0;
    }

    private function validarNuevaPassword(string $newPassword, string $confirmPassword): ?string
    {
        if ($newPassword === '' || $confirmPassword === '') {
            return 'Debes completar la nueva contraseña y su confirmación.';
        }

        if ($newPassword !== $confirmPassword) {
            return 'La nueva contraseña y la confirmación no coinciden.';
        }

        if (strlen($newPassword) < 8) {
            return 'La nueva contraseña debe tener al menos 8 caracteres.';
        }

        if (!preg_match('/[A-Z]/', $newPassword) || !preg_match('/[a-z]/', $newPassword) || !preg_match('/\d/', $newPassword)) {
            return 'La nueva contraseña debe incluir mayúscula, minúscula y número.';
        }

        return null;
    }

    public function login(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['usuario_id'])) {
            if (!empty($_SESSION['debe_cambiar_password'])) {
                $this->redirect('index.php?controller=auth&action=cambiarPassword');
            }
            $this->redirect('index.php?controller=dashboard&action=index');
        }
        $this->render('login');
    }

    public function autenticar(){
        $this->ensurePost('index.php?controller=auth&action=login');

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            $_SESSION['error'] = 'Debes completar correo y contraseña.';
            $this->redirect('index.php?controller=auth&action=login');
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->buscarPorEmail($email);

        if(!$usuario || !password_verify($password, $usuario['password'])){
            $_SESSION['error'] = 'Credenciales incorrectas.';
            $this->redirect('index.php?controller=auth&action=login');
        }

        if (($usuario['estado'] ?? 'activo') !== 'activo') {
            $_SESSION['error'] = 'Tu usuario está inactivo.';
            $this->redirect('index.php?controller=auth&action=login');
        }

        $this->setSessionData($usuario);
        $this->logActivity('LOGIN', 'usuarios', 'Inicio de sesión exitoso: ' . ($usuario['nombre'] ?? $email));

        if (!empty($usuario['debe_cambiar_password'])) {
            $_SESSION['info'] = 'Por seguridad debes cambiar tu contraseña temporal antes de continuar.';
            $this->redirect('index.php?controller=auth&action=cambiarPassword');
        }

        $this->redirect('index.php?controller=dashboard&action=index');
    }

    public function cambiarPassword(){
        require_login();
        $forzado = !empty($_SESSION['debe_cambiar_password']);
        $this->render('auth/cambiar_password', compact('forzado'));
    }

    public function actualizarPassword(){
        require_login();
        $this->ensurePost('index.php?controller=auth&action=cambiarPassword');

        $currentPassword = trim($_POST['password_actual'] ?? '');
        $newPassword = trim($_POST['password_nueva'] ?? '');
        $confirmPassword = trim($_POST['password_confirmacion'] ?? '');

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->obtenerPorId((int)current_user_id());

        if (!$usuario) {
            $this->redirectWithFlash('error', 'No se encontró el usuario autenticado.', 'index.php?controller=auth&action=logout');
        }

        if (!password_verify($currentPassword, $usuario['password'])) {
            $this->redirectWithFlash('error', 'La contraseña actual no es correcta.', 'index.php?controller=auth&action=cambiarPassword');
        }

        $error = $this->validarNuevaPassword($newPassword, $confirmPassword);
        if ($error) {
            $this->redirectWithFlash('error', $error, 'index.php?controller=auth&action=cambiarPassword');
        }

        if (password_verify($newPassword, $usuario['password'])) {
            $this->redirectWithFlash('error', 'La nueva contraseña no puede ser igual a la actual.', 'index.php?controller=auth&action=cambiarPassword');
        }

        $usuarioModel->actualizarPassword((int)$usuario['id'], password_hash($newPassword, PASSWORD_DEFAULT), false);
        $_SESSION['debe_cambiar_password'] = 0;
        $this->logActivity('UPDATE', 'usuarios', 'Actualizó su contraseña.');
        $this->redirectWithFlash('success', 'Tu contraseña fue actualizada correctamente.', 'index.php?controller=dashboard&action=index');
    }

    public function logout(){
        if(session_status() === PHP_SESSION_NONE){
            session_start();
        }
        $nombre = $_SESSION['usuario_nombre'] ?? 'Usuario';
        $this->logActivity('LOGOUT', 'usuarios', 'Cierre de sesión: ' . $nombre);
        session_unset();
        session_destroy();
        $this->redirect('index.php?controller=auth&action=login');
    }
}
