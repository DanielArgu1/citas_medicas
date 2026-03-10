<?php

require_once __DIR__ . '/../models/Cita.php';
require_once __DIR__ . '/../models/Paciente.php';
require_once __DIR__ . '/../models/Medico.php';

class CitaController {

    private $cita;

    public function __construct(){
        $this->cita = new Cita();
    }

    public function index(){

        $citas = $this->cita->obtenerTodas();

        require_once __DIR__ . '/../views/citas/index.php';
    }

    public function crear(){

        $pacienteModel = new Paciente();
        $medicoModel = new Medico();

        $pacientes = $pacienteModel->obtenerTodos();
        $medicos = $medicoModel->obtenerTodos();

        require_once __DIR__ . '/../views/citas/crear.php';
    }

    public function guardar(){

        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $hora_inicio = $_POST['hora_inicio'];

            
            $hora_fin = date("H:i:s", strtotime($hora_inicio . " +30 minutes"));

            $data = [
                'paciente_id'=>$_POST['paciente_id'],
                'medico_id'=>$_POST['medico_id'],
                'fecha'=>$_POST['fecha'],
                'hora_inicio'=>$hora_inicio,
                'hora_fin'=>$hora_fin,
                'motivo'=>$_POST['motivo']
            ];

            $resultado = $this->cita->crear($data);

            if($resultado == "ocupado"){
                echo "El médico ya tiene una cita en ese horario.";
                return;
            }

            header("Location: index.php?controller=cita&action=index");
            exit;
        }
    }

}