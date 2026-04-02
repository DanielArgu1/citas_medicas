<?php

require_once __DIR__ . '/../models/Usuario.php';

class AuthController {

    public function login(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['usuario_id'])) {
            header("Location: index.php?controller=dashboard&action=index");
            exit;
        }
        require_once __DIR__ . '/../views/login.php';
    }

    public function autenticar(){
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            $_SESSION['error'] = 'Debes completar correo y contraseña.';
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->buscarPorEmail($email);

        if(!$usuario || !password_verify($password, $usuario['password'])){
            $_SESSION['error'] = 'Credenciales incorrectas.';
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        if (($usuario['estado'] ?? 'activo') !== 'activo') {
            $_SESSION['error'] = 'Tu usuario está inactivo.';
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        $_SESSION['usuario_rol'] = $usuario['rol'];
        $_SESSION['medico_id'] = $usuario['medico_id'] ?? null;
        $_SESSION['paciente_id'] = $usuario['paciente_id'] ?? null;

        header("Location: index.php?controller=dashboard&action=index");
        exit;
    }

    public function logout(){
        if(session_status() === PHP_SESSION_NONE){
            session_start();
        }
        session_unset();
        session_destroy();
        header("Location: index.php?controller=auth&action=login");
        exit;
    }
}
