<?php

require_once __DIR__ . '/../core/Model.php';

class Medico extends Model {

    public function obtenerTodos(){

        $sql = "SELECT * FROM medicos ORDER BY id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function crear($data){

        $sql = "INSERT INTO medicos
        (nombre,apellido,especialidad,telefono,email)
        VALUES
        (:nombre,:apellido,:especialidad,:telefono,:email)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':nombre'=>$data['nombre'],
            ':apellido'=>$data['apellido'],
            ':especialidad'=>$data['especialidad'],
            ':telefono'=>$data['telefono'],
            ':email'=>$data['email']
        ]);
    }

}