<?php

require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../models/Paciente.php';

class PacienteController {
    private $paciente;

    public function __construct(){
        $this->paciente = new Paciente();
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
        if ($data['nombre'] === '' || $data['apellido'] === '' || $data['cedula'] === '') {
            return 'Nombre, apellido y cédula son obligatorios.';
        }
        if (!preg_match('/^\d{4}-\d{4}-\d{5}$/', $data['cedula'])) {
            return 'La cédula debe tener formato 0000-0000-00000.';
        }
        if ($data['telefono'] !== '' && !preg_match('/^\d{4}-\d{4}$/', $data['telefono'])) {
            return 'El teléfono debe tener formato 0000-0000.';
        }
        if ($data['email'] !== '' && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return 'El correo electrónico no es válido.';
        }
        if ($this->paciente->existeCedula($data['cedula'], $exceptId)) {
            return 'Ya existe un paciente con esa cédula.';
        }
        return null;
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
        $data = [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'apellido' => trim($_POST['apellido'] ?? ''),
            'cedula' => $this->formatCedula($_POST['cedula'] ?? ''),
            'telefono' => $this->formatPhone($_POST['telefono'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? null,
        ];
        $error = $this->validate($data);
        if ($error) { flash('error', $error); header("Location: index.php?controller=paciente&action=crear"); exit; }
        $this->paciente->crear($data);
        flash('success', 'Paciente registrado correctamente.');
        header("Location: index.php?controller=paciente&action=index");
        exit;
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
        $data = [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'apellido' => trim($_POST['apellido'] ?? ''),
            'cedula' => $this->formatCedula($_POST['cedula'] ?? ''),
            'telefono' => $this->formatPhone($_POST['telefono'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? null,
        ];
        $error = $this->validate($data, $id);
        if ($error) { flash('error', $error); header("Location: index.php?controller=paciente&action=editar&id={$id}"); exit; }
        $this->paciente->actualizar($id, $data);
        flash('success', 'Paciente actualizado correctamente.');
        header("Location: index.php?controller=paciente&action=index"); exit;
    }

    public function eliminar(){
        require_roles(['admin', 'recepcion']);
        $id = (int)($_GET['id'] ?? 0);
        if ($id) {
            $this->paciente->eliminar($id);
            flash('success', 'Paciente eliminado correctamente.');
        }
        header("Location: index.php?controller=paciente&action=index"); exit;
    }
}
