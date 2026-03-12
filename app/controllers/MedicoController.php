<?php

require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../models/Medico.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../config/database.php';

class MedicoController {
    private $medico;
    private $usuario;

    public function __construct(){
        $this->medico = new Medico();
        $this->usuario = new Usuario();
    }

    private function limpiarTexto($valor)
    {
        $valor = trim($valor ?? '');
        $valor = preg_replace('/\s+/', ' ', $valor);
        return $valor;
    }

    private function soloDigitos($valor)
    {
        return preg_replace('/\D/', '', $valor ?? '');
    }

    private function formatearTelefono($valor)
    {
        $valor = $this->soloDigitos($valor);
        $valor = substr($valor, 0, 8);

        if (strlen($valor) > 4) {
            return substr($valor, 0, 4) . '-' . substr($valor, 4);
        }

        return $valor;
    }

    private function validarSoloTexto($texto)
    {
        return preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/', $texto);
    }

    private function validarTelefono($telefono)
    {
        return preg_match('/^\d{4}-\d{4}$/', $telefono);
    }

    private function baseData()
    {
        return [
            'nombre' => $this->limpiarTexto($_POST['nombre'] ?? ''),
            'apellido' => $this->limpiarTexto($_POST['apellido'] ?? ''),
            'especialidad' => $this->limpiarTexto($_POST['especialidad'] ?? ''),
            'telefono' => $this->formatearTelefono($_POST['telefono'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
        ];
    }

    private function validate($data)
    {
        if (
            $data['nombre'] === '' ||
            $data['apellido'] === '' ||
            $data['especialidad'] === '' ||
            $data['telefono'] === '' ||
            $data['email'] === ''
        ) {
            return 'Todos los campos son obligatorios.';
        }

        if (!$this->validarSoloTexto($data['nombre'])) {
            return 'El nombre solo debe contener letras y espacios.';
        }

        if (!$this->validarSoloTexto($data['apellido'])) {
            return 'El apellido solo debe contener letras y espacios.';
        }

        if (!$this->validarSoloTexto($data['especialidad'])) {
            return 'La especialidad solo debe contener letras y espacios.';
        }

        if (mb_strlen($data['nombre']) < 2) {
            return 'El nombre es demasiado corto.';
        }

        if (mb_strlen($data['apellido']) < 2) {
            return 'El apellido es demasiado corto.';
        }

        if (mb_strlen($data['especialidad']) < 2) {
            return 'La especialidad es demasiado corta.';
        }

        if (!$this->validarTelefono($data['telefono'])) {
            return 'El teléfono debe tener formato 0000-0000.';
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return 'El correo electrónico no es válido.';
        }

        return null;
    }

    public function index(){
        require_roles(['admin']);
        $medicos = $this->medico->obtenerTodos();
        require_once __DIR__ . '/../views/medicos/index.php';
    }

    public function crear(){
        require_roles(['admin']);
        require_once __DIR__ . '/../views/medicos/crear.php';
    }

    public function guardar(){
        require_roles(['admin']);

        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            header("Location: index.php?controller=medico&action=index");
            exit;
        }

        $data = $this->baseData();
        $error = $this->validate($data);

        if ($error) {
            flash('error', $error);
            header("Location: index.php?controller=medico&action=crear");
            exit;
        }

        $medicoId = $this->medico->crear($data);

        if (!$medicoId) {
            flash('error', 'No se pudo registrar el médico.');
            header("Location: index.php?controller=medico&action=crear");
            exit;
        }

        $passwordTemporal = 'Med' . rand(1000, 9999) . '!';

        $this->usuario->crear([
            'nombre' => $data['nombre'] . ' ' . $data['apellido'],
            'email' => $data['email'],
            'password' => password_hash($passwordTemporal, PASSWORD_DEFAULT),
            'rol' => 'medico',
            'medico_id' => $medicoId,
            'estado' => 'activo'
        ]);

        flash('success', "Médico registrado. Usuario: {$data['email']} | Contraseña temporal: {$passwordTemporal}");
        header("Location: index.php?controller=medico&action=index");
        exit;
    }

    public function editar(){
        require_roles(['admin']);

        $id = (int)($_GET['id'] ?? 0);
        $medico = $this->medico->obtenerPorId($id);

        if (!$medico) {
            flash('error', 'Médico no encontrado.');
            header("Location: index.php?controller=medico&action=index");
            exit;
        }

        require_once __DIR__ . '/../views/medicos/editar.php';
    }

    public function actualizar(){
        require_roles(['admin']);

        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            header("Location: index.php?controller=medico&action=index");
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        $data = $this->baseData();
        $error = $this->validate($data);

        if ($error) {
            flash('error', $error);
            header("Location: index.php?controller=medico&action=editar&id={$id}");
            exit;
        }

        $resultado = $this->medico->actualizar($id, $data);

        if (!$resultado) {
            flash('error', 'No se pudo actualizar el médico.');
            header("Location: index.php?controller=medico&action=editar&id={$id}");
            exit;
        }

        $usuario = $this->usuario->obtenerPorMedicoId($id);

        if ($usuario) {
            $nombreCompleto = $data['nombre'] . ' ' . $data['apellido'];

            $db = new Database();
            $conn = $db->conectar();

            $stmt = $conn->prepare("UPDATE usuarios SET nombre = :nombre, email = :email WHERE medico_id = :medico_id");
            $stmt->execute([
                ':nombre' => $nombreCompleto,
                ':email' => $data['email'],
                ':medico_id' => $id
            ]);

            if (!empty($_POST['reset_password'])) {
                $newPass = 'Med' . rand(1000, 9999) . '!';
                $this->usuario->actualizarPassword($usuario['id'], password_hash($newPass, PASSWORD_DEFAULT));
                flash('success', "Médico actualizado. Nueva contraseña temporal: {$newPass}");
                header("Location: index.php?controller=medico&action=index");
                exit;
            }
        }

        flash('success', 'Médico actualizado correctamente.');
        header("Location: index.php?controller=medico&action=index");
        exit;
    }

    public function eliminar(){
        require_roles(['admin']);

        $id = (int)($_GET['id'] ?? 0);

        if ($id) {
            $this->usuario->eliminarPorMedicoId($id);
            $this->medico->eliminar($id);
            flash('success', 'Médico eliminado correctamente.');
        }

        header("Location: index.php?controller=medico&action=index");
        exit;
    }
}