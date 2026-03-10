<?php

require_once __DIR__ . '/../models/Medico.php';

class MedicoController {

    private $medico;

    public function __construct(){
        $this->medico = new Medico();
    }

    public function index(){

        $medicos = $this->medico->obtenerTodos();

        require_once __DIR__ . '/../views/medicos/index.php';
    }

    public function crear(){

        require_once __DIR__ . '/../views/medicos/crear.php';
    }

    public function guardar(){

        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $data = [
                'nombre'=>$_POST['nombre'],
                'apellido'=>$_POST['apellido'],
                'especialidad'=>$_POST['especialidad'],
                'telefono'=>$_POST['telefono'],
                'email'=>$_POST['email']
            ];

            $this->medico->crear($data);

            header("Location: index.php?controller=medico&action=index");
            exit;
        }

    }

}