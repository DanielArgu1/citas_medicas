<?php

require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../models/Paciente.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Cita.php';
require_once __DIR__ . '/../models/HistorialClinico.php';
require_once __DIR__ . '/../config/database.php';

class PacienteController {
    private $paciente;
    private $usuario;

    public function __construct(){
        $this->paciente = new Paciente();
        $this->usuario = new Usuario();
    }

    private function digits($value){
        return preg_replace('/\D/', '', $value ?? '');
    }

    private function formatCedula($value){
        $d = substr($this->digits($value), 0, 13);
        if (strlen($d) <= 4) return $d;
        if (strlen($d) <= 8) return substr($d,0,4).'-'.substr($d,4);
        return substr($d,0,4).'-'.substr($d,4,4).'-'.substr($d,8);
    }

    private function formatPhone($value){
        $d = substr($this->digits($value), 0, 8);
        if (strlen($d) <= 4) return $d;
        return substr($d,0,4).'-'.substr($d,4);
    }

    private function validate($data, $exceptId = null){
        if ($data['nombre'] === '' || $data['apellido'] === '' || $data['cedula'] === '' || $data['email'] === '') {
            return 'Nombre, apellido, cédula y correo electrónico son obligatorios.';
        }
        if (!preg_match('/^\d{4}-\d{4}-\d{5}$/', $data['cedula'])) {
            return 'La cédula debe tener formato 0000-0000-00000.';
        }
        if ($data['telefono'] !== '' && !preg_match('/^\d{4}-\d{4}$/', $data['telefono'])) {
            return 'El teléfono debe tener formato 0000-0000.';
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return 'El correo electrónico no es válido.';
        }
        if ($this->paciente->existeCedula($data['cedula'], $exceptId)) {
            return 'Ya existe un paciente con esa cédula.';
        }
        return null;
    }

    private function payloadFromRequest(){
        return [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'apellido' => trim($_POST['apellido'] ?? ''),
            'cedula' => $this->formatCedula($_POST['cedula'] ?? ''),
            'telefono' => $this->formatPhone($_POST['telefono'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? null,
        ];
    }

    public function index(){
        require_roles(['admin', 'recepcion']);
        $pacientes = $this->paciente->obtenerTodos();
        require_once __DIR__ . '/../views/pacientes/index.php';
    }

    public function crear(){
        require_roles(['admin', 'recepcion']);
        require_once __DIR__ . '/../views/pacientes/crear.php';
    }

    public function guardar(){
        require_roles(['admin', 'recepcion']);
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            header("Location: index.php?controller=paciente&action=index"); exit;
        }

        $data = $this->payloadFromRequest();
        $error = $this->validate($data);

        if ($error) {
            flash('error', $error);
            header("Location: index.php?controller=paciente&action=crear");
            exit;
        }

        if ($this->usuario->emailEnUsoPorOtro($data['email'])) {
            flash('error', 'Ese correo ya está siendo usado por otro usuario del sistema.');
            header("Location: index.php?controller=paciente&action=crear");
            exit;
        }

        $db = new Database();
        $conn = $db->conectar();

        try {
            $conn->beginTransaction();

            $stmt = $conn->prepare("INSERT INTO pacientes (nombre, apellido, cedula, telefono, email, fecha_nacimiento)
                                    VALUES (:nombre, :apellido, :cedula, :telefono, :email, :fecha_nacimiento)");
            $stmt->execute([
                ':nombre' => $data['nombre'],
                ':apellido' => $data['apellido'],
                ':cedula' => $data['cedula'],
                ':telefono' => $data['telefono'],
                ':email' => $data['email'],
                ':fecha_nacimiento' => $data['fecha_nacimiento'] ?: null,
            ]);

            $pacienteId = (int)$conn->lastInsertId();
            $passwordTemporal = 'Pac' . rand(1000, 9999) . '!';

            $stmtUsuario = $conn->prepare("INSERT INTO usuarios (nombre, email, password, rol, medico_id, paciente_id, estado)
                                           VALUES (:nombre, :email, :password, 'paciente', NULL, :paciente_id, 'activo')");
            $stmtUsuario->execute([
                ':nombre' => $data['nombre'] . ' ' . $data['apellido'],
                ':email' => $data['email'],
                ':password' => password_hash($passwordTemporal, PASSWORD_DEFAULT),
                ':paciente_id' => $pacienteId,
            ]);

            $conn->commit();

            flash('success', "Paciente registrado. Usuario: {$data['email']} | Contraseña temporal: {$passwordTemporal}");
            header("Location: index.php?controller=paciente&action=index");
            exit;
        } catch (Throwable $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            flash('error', 'No se pudo registrar el paciente. Verifica que el correo y la cédula no estén duplicados.');
            header("Location: index.php?controller=paciente&action=crear");
            exit;
        }
    }

    public function editar(){
        require_roles(['admin', 'recepcion']);
        $id = (int)($_GET['id'] ?? 0);
        $paciente = $this->paciente->obtenerPorId($id);
        if (!$paciente) { flash('error', 'Paciente no encontrado.'); header("Location: index.php?controller=paciente&action=index"); exit; }
        require_once __DIR__ . '/../views/pacientes/editar.php';
    }

    public function actualizar(){
        require_roles(['admin', 'recepcion']);
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            header("Location: index.php?controller=paciente&action=index"); exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        $pacienteActual = $this->paciente->obtenerPorId($id);
        if (!$pacienteActual) {
            flash('error', 'Paciente no encontrado.');
            header("Location: index.php?controller=paciente&action=index");
            exit;
        }

        $data = $this->payloadFromRequest();
        $error = $this->validate($data, $id);
        if ($error) {
            flash('error', $error);
            header("Location: index.php?controller=paciente&action=editar&id={$id}");
            exit;
        }

        $usuario = $this->usuario->obtenerPorPacienteId($id);
        if ($usuario && $this->usuario->emailEnUsoPorOtro($data['email'], (int)$usuario['id'])) {
            flash('error', 'Ese correo ya está siendo usado por otro usuario del sistema.');
            header("Location: index.php?controller=paciente&action=editar&id={$id}");
            exit;
        }

        $resultado = $this->paciente->actualizar($id, $data);
        if (!$resultado) {
            flash('error', 'No se pudo actualizar el paciente.');
            header("Location: index.php?controller=paciente&action=editar&id={$id}");
            exit;
        }

        if ($usuario) {
            $nombreCompleto = $data['nombre'] . ' ' . $data['apellido'];
            $this->usuario->actualizarDatosPorPacienteId($id, $nombreCompleto, $data['email']);

            if (!empty($_POST['reset_password'])) {
                $newPass = 'Pac' . rand(1000, 9999) . '!';
                $this->usuario->actualizarPassword($usuario['id'], password_hash($newPass, PASSWORD_DEFAULT));
                flash('success', "Paciente actualizado. Nueva contraseña temporal: {$newPass}");
                header("Location: index.php?controller=paciente&action=index");
                exit;
            }
        }

        flash('success', 'Paciente actualizado correctamente.');
        header("Location: index.php?controller=paciente&action=index"); exit;
    }

    public function eliminar(){
        require_roles(['admin', 'recepcion']);
        $id = (int)($_GET['id'] ?? 0);
        if ($id) {
            $this->usuario->eliminarPorPacienteId($id);
            $this->paciente->eliminar($id);
            flash('success', 'Paciente eliminado correctamente.');
        }
        header("Location: index.php?controller=paciente&action=index"); exit;
    }

    public function perfil(){
        require_roles(['paciente']);
        $id = (int)current_paciente_id();
        $paciente = $this->paciente->obtenerPorId($id);

        if (!$paciente) {
            flash('error', 'No se encontró el perfil del paciente.');
            header("Location: index.php?controller=dashboard&action=index");
            exit;
        }

        $citaModel = new Cita();
        $historialModel = new HistorialClinico();
        $proximasCitas = $citaModel->proximasPorPaciente($id, 3);
        $historial = $historialModel->obtenerPorPaciente($id);

        require_once __DIR__ . '/../views/pacientes/perfil.php';
    }
}
