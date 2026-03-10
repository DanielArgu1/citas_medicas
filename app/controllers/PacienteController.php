<?php

require_once __DIR__ . '/../models/Paciente.php';

class PacienteController {

    private $paciente;

    public function __construct(){
        $this->paciente = new Paciente();
    }

    public function index(){

        $pacientes = $this->paciente->obtenerTodos();

        require_once __DIR__ . '/../views/pacientes/index.php';
    }

    public function crear(){

        require_once __DIR__ . '/../views/pacientes/crear.php';
    }

    public function guardar(){

        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $data = [
                'nombre' => $_POST['nombre'],
                'apellido' => $_POST['apellido'],
                'cedula' => $_POST['cedula'],
                'telefono' => $_POST['telefono'],
                'email' => $_POST['email'],
                'fecha_nacimiento' => $_POST['fecha_nacimiento']
            ];

            $this->paciente->crear($data);

            header("Location: index.php?controller=paciente&action=index");
            exit;
        }

    }
}