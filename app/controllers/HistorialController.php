<?php

require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/HistorialClinico.php';
require_once __DIR__ . '/../models/Paciente.php';
require_once __DIR__ . '/../models/Cita.php';

class HistorialController extends Controller {

    public function ver(){
        require_login();

        if (!isset($_GET['paciente_id']) || empty($_GET['paciente_id'])) {
            exit('Paciente no especificado');
        }

        $pacienteId = (int) $_GET['paciente_id'];

        if (current_user_role() === 'paciente' && (int)current_paciente_id() !== $pacienteId) {
            flash('error', 'No puedes ver el historial de otro paciente.');
            header('Location: index.php?controller=dashboard&action=index');
            exit;
        }

        $historialModel = new HistorialClinico();
        $pacienteModel = new Paciente();

        $paciente = $pacienteModel->obtenerPorId($pacienteId);

        if (!$paciente) {
            exit('Paciente no encontrado');
        }

        $historial = $historialModel->obtenerPorPaciente($pacienteId);
        $this->logActivity('VIEW', 'historial_clinico', 'Consultó el historial del paciente ID ' . $pacienteId . '.');

        require_once __DIR__ . '/../views/historial/ver.php';
    }

    public function registrar(){
        require_roles(['medico']);

        if (!isset($_GET['paciente_id']) || empty($_GET['paciente_id'])) {
            exit('Paciente no especificado');
        }

        $pacienteId = (int) $_GET['paciente_id'];
        $citaId = isset($_GET['cita_id']) ? (int) $_GET['cita_id'] : null;

        $historialModel = new HistorialClinico();
        $pacienteModel = new Paciente();
        $citaModel = new Cita();

        $paciente = $pacienteModel->obtenerPorId($pacienteId);

        if (!$paciente) {
            exit('Paciente no encontrado');
        }

        $historial = $historialModel->obtenerPorPaciente($pacienteId);
        $cita = null;

        if (!empty($citaId)) {
            $cita = $citaModel->obtenerPorId($citaId);
            if (!$cita || (int)$cita['medico_id'] !== (int)current_medico_id()) {
                flash('error', 'No puedes registrar evolución para una cita que no te pertenece.');
                header('Location: index.php?controller=cita&action=index');
                exit;
            }
        }

        require_once __DIR__ . '/../views/historial/registrar.php';
    }

    public function crear(){
        require_roles(['medico']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=cita&action=index');
            exit;
        }

        if (
            empty($_POST['paciente_id']) ||
            empty($_POST['motivo_consulta']) ||
            empty($_POST['diagnostico'])
        ) {
            exit('Faltan campos obligatorios');
        }

        if (empty($_SESSION['medico_id'])) {
            exit('El usuario no tiene médico asignado');
        }

        $historialModel = new HistorialClinico();

        $data = [
            'paciente_id' => (int) $_POST['paciente_id'],
            'medico_id' => (int) $_SESSION['medico_id'],
            'cita_id' => !empty($_POST['cita_id']) ? (int) $_POST['cita_id'] : null,
            'motivo_consulta' => trim($_POST['motivo_consulta']),
            'sintomas' => trim($_POST['sintomas'] ?? ''),
            'diagnostico' => trim($_POST['diagnostico']),
            'tratamiento' => trim($_POST['tratamiento'] ?? ''),
            'observaciones' => trim($_POST['observaciones'] ?? '')
        ];

        $historialModel->crear($data);
        $this->logActivity('INSERT', 'historial_clinico', 'Registró evolución clínica para el paciente ID ' . $data['paciente_id'] . '.');

        if (!empty($data['cita_id'])) {
            $citaModel = new Cita();
            $citaModel->actualizarEstado($data['cita_id'], 'completada');
            $this->logActivity('UPDATE', 'citas', 'La cita ID ' . $data['cita_id'] . ' fue completada al registrar historial clínico.');
        }

        header('Location: index.php?controller=historial&action=registrar&paciente_id=' . $data['paciente_id'] . (!empty($data['cita_id']) ? '&cita_id=' . $data['cita_id'] : ''));
        exit;
    }
}
