<?php

require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Reporte.php';

class ReporteController extends Controller
{
    public function index()
    {
        require_roles(['admin', 'recepcion']);

        $reporteModel = new Reporte();

        $filters = [
            'medico_id' => $_GET['medico_id'] ?? '',
            'estado' => $_GET['estado'] ?? '',
            'fecha_desde' => $_GET['fecha_desde'] ?? '',
            'fecha_hasta' => $_GET['fecha_hasta'] ?? '',
            'hist_medico_id' => $_GET['hist_medico_id'] ?? '',
            'paciente_id' => $_GET['paciente_id'] ?? '',
            'fecha_desde_hist' => $_GET['fecha_desde_hist'] ?? '',
            'fecha_hasta_hist' => $_GET['fecha_hasta_hist'] ?? '',
            'diagnostico' => trim($_GET['diagnostico'] ?? '')
        ];

        $totalPacientes = $reporteModel->totalPacientes();
        $totalMedicos = $reporteModel->totalMedicos();
        $totalCitas = $reporteModel->totalCitas();
        $citasHoy = $reporteModel->citasHoy();
        $totalHistoriales = $reporteModel->totalHistoriales();
        $citasCompletadas = $reporteModel->citasCompletadas();
        $citasCanceladas = $reporteModel->citasCanceladas();

        $medicos = $reporteModel->obtenerMedicos();
        $pacientes = $reporteModel->obtenerPacientes();

        $citas = $reporteModel->obtenerCitas($filters);
        $historiales = $reporteModel->obtenerHistoriales($filters);

        $porEstado = $reporteModel->citasPorEstado();
        $diagnosticosFrecuentes = $reporteModel->diagnosticosFrecuentes();
        $pacientesFrecuentes = $reporteModel->pacientesFrecuentes();
        $medicosFrecuentes = $reporteModel->medicosFrecuentes();
        $ultimosHistoriales = $reporteModel->ultimosHistoriales();

        $diagnosticoMasFrecuente = $reporteModel->diagnosticoMasFrecuente();
        $medicoMasActivo = $reporteModel->medicoMasActivo();
        $pacienteMasConsultas = $reporteModel->pacienteMasConsultas();

        $this->logActivity('VIEW', 'reportes', 'Consultó el módulo de reportes.');

        require_once __DIR__ . '/../views/reportes/index.php';
    }
}
