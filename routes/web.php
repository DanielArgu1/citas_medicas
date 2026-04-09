<?php

require_once __DIR__ . '/../app/middleware/auth.php';

$controller = $_GET['controller'] ?? (isset($_SESSION['usuario_id']) ? 'dashboard' : 'auth');
$action = $_GET['action'] ?? (isset($_SESSION['usuario_id']) ? 'index' : 'login');

enforce_password_change($controller, $action);

switch($controller){
    case 'auth':
        require_once __DIR__ . '/../app/controllers/AuthController.php';
        $ctrl = new AuthController();
        break;
    case 'dashboard':
        require_once __DIR__ . '/../app/controllers/DashboardController.php';
        $ctrl = new DashboardController();
        break;
    case 'paciente':
        require_once __DIR__ . '/../app/controllers/PacienteController.php';
        $ctrl = new PacienteController();
        break;
    case 'medico':
        require_once __DIR__ . '/../app/controllers/MedicoController.php';
        $ctrl = new MedicoController();
        break;
    case 'cita':
        require_once __DIR__ . '/../app/controllers/CitaController.php';
        $ctrl = new CitaController();
        break;
    case 'reporte':
        require_once __DIR__ . '/../app/controllers/ReporteController.php';
        $ctrl = new ReporteController();
        break;
    case 'auditoria':
        require_once __DIR__ . '/../app/controllers/AuditoriaController.php';
        $ctrl = new AuditoriaController();
        break;
    case 'historial':
        require_once __DIR__ . '/../app/controllers/HistorialController.php';
        $ctrl = new HistorialController();
        break;
    default:
        http_response_code(404);
        exit('Controlador no encontrado');
}

if (!method_exists($ctrl, $action)) {
    http_response_code(404);
    exit('Acción no encontrada');
}

$ctrl->$action();