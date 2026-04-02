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

        if ($this->usuario->emailEnUsoPorOtro($data['email'])) {
            flash('error', 'Ese correo ya está siendo usado por otro usuario del sistema.');
            header("Location: index.php?controller=medico&action=crear");
            exit;
        }

        $db = new Database();
        $conn = $db->conectar();

        try {
            $conn->beginTransaction();

            $stmt = $conn->prepare("INSERT INTO medicos (nombre, apellido, especialidad, telefono, email)
                                    VALUES (:nombre, :apellido, :especialidad, :telefono, :email)");
            $stmt->execute([
                ':nombre' => $data['nombre'],
                ':apellido' => $data['apellido'],
                ':especialidad' => $data['especialidad'],
                ':telefono' => $data['telefono'],
                ':email' => $data['email'],
            ]);

            $medicoId = (int)$conn->lastInsertId();
            $passwordTemporal = 'Med' . rand(1000, 9999) . '!';

            $stmtUsuario = $conn->prepare("INSERT INTO usuarios (nombre, email, password, rol, medico_id, paciente_id, estado)
                                           VALUES (:nombre, :email, :password, 'medico', :medico_id, NULL, 'activo')");
            $stmtUsuario->execute([
                ':nombre' => $data['nombre'] . ' ' . $data['apellido'],
                ':email' => $data['email'],
                ':password' => password_hash($passwordTemporal, PASSWORD_DEFAULT),
                ':medico_id' => $medicoId,
            ]);

            $conn->commit();

            flash('success', "Médico registrado. Usuario: {$data['email']} | Contraseña temporal: {$passwordTemporal}");
            header("Location: index.php?controller=medico&action=index");
            exit;
        } catch (Throwable $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            flash('error', 'No se pudo registrar el médico. Verifica que el correo no esté duplicado.');
            header("Location: index.php?controller=medico&action=crear");
            exit;
        }
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

        $usuario = $this->usuario->obtenerPorMedicoId($id);
        if ($usuario && $this->usuario->emailEnUsoPorOtro($data['email'], (int)$usuario['id'])) {
            flash('error', 'Ese correo ya está siendo usado por otro usuario del sistema.');
            header("Location: index.php?controller=medico&action=editar&id={$id}");
            exit;
        }

        $resultado = $this->medico->actualizar($id, $data);

        if (!$resultado) {
            flash('error', 'No se pudo actualizar el médico.');
            header("Location: index.php?controller=medico&action=editar&id={$id}");
            exit;
        }

        if ($usuario) {
            $nombreCompleto = $data['nombre'] . ' ' . $data['apellido'];
            $this->usuario->actualizarDatosPorMedicoId($id, $nombreCompleto, $data['email']);

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
