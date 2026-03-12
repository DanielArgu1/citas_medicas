<?php

require_once __DIR__ . '/../models/HistorialClinico.php';
require_once __DIR__ . '/../models/Paciente.php';
require_once __DIR__ . '/../models/Cita.php';

class HistorialController {

    private function validarSesion(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['usuario_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
    }

    public function ver(){
        $this->validarSesion();

        if (!isset($_GET['paciente_id']) || empty($_GET['paciente_id'])) {
            exit('Paciente no especificado');
        }

        $pacienteId = (int) $_GET['paciente_id'];

        $historialModel = new HistorialClinico();
        $pacienteModel = new Paciente();

        $paciente = $pacienteModel->obtenerPorId($pacienteId);

        if (!$paciente) {
            exit('Paciente no encontrado');
        }

        $historial = $historialModel->obtenerPorPaciente($pacienteId);

        require_once __DIR__ . '/../views/historial/ver.php';
    }

    public function registrar(){
        $this->validarSesion();

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
        }

        require_once __DIR__ . '/../views/historial/registrar.php';
    }

    public function crear(){
        $this->validarSesion();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=cita&action=index');
            exit;
        }

        if (
            empty($_POST['paciente_id']) ||
            empty($_POST['medico_id']) ||
            empty($_POST['motivo_consulta']) ||
            empty($_POST['diagnostico'])
        ) {
            exit('Faltan campos obligatorios');
        }

        $historialModel = new HistorialClinico();

        $data = [
            'paciente_id' => (int) $_POST['paciente_id'],
            'medico_id' => (int) $_POST['medico_id'],
            'cita_id' => !empty($_POST['cita_id']) ? (int) $_POST['cita_id'] : null,
            'motivo_consulta' => trim($_POST['motivo_consulta']),
            'sintomas' => trim($_POST['sintomas'] ?? ''),
            'diagnostico' => trim($_POST['diagnostico']),
            'tratamiento' => trim($_POST['tratamiento'] ?? ''),
            'observaciones' => trim($_POST['observaciones'] ?? '')
        ];

        $historialModel->crear($data);

        if (!empty($data['cita_id'])) {
            $citaModel = new Cita();
            $citaModel->actualizarEstado($data['cita_id'], 'completada');
        }

        header('Location: index.php?controller=historial&action=registrar&paciente_id=' . $data['paciente_id'] . (!empty($data['cita_id']) ? '&cita_id=' . $data['cita_id'] : ''));
        exit;
    }
}