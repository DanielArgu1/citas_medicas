<?php

$controller = $_GET['controller'] ?? 'paciente';
$action     = $_GET['action'] ?? 'index';

switch ($controller) {

    case 'paciente':
        require_once __DIR__ . '/../app/controllers/PacienteController.php';
        $ctrl = new PacienteController();

        switch ($action) {
            case 'index':
                $ctrl->index();
                break;

            case 'crear':
                $ctrl->crear();
                break;

            case 'guardar':
                $ctrl->guardar();
                break;

            default:
                echo "Acción no encontrada";
        }
        break;

    case 'medico':

        require_once __DIR__ . '/../app/controllers/MedicoController.php';

        $ctrl = new MedicoController();

        switch ($action){

        case 'index':
        $ctrl->index();
        break;

        case 'crear':
        $ctrl->crear();
        break;

        case 'guardar':
        $ctrl->guardar();
        break;

        }

        break;

<<<<<<< HEAD
    default:
        echo "Controlador no encontrado";
=======
    case 'dashboard':

        require_once __DIR__ . '/../app/controllers/DashboardController.php';

        $ctrl = new DashboardController();

        switch($action){

        case 'index':
        $ctrl->index();
        break;

        }

    break;

>>>>>>> 07c8788 (se agregaron vistas a formularios)
}