<?php

require_once __DIR__ . '/../core/Model.php';

class Paciente extends Model {

    public function obtenerTodos(){

        $sql = "SELECT * FROM pacientes ORDER BY id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function crear($data){

        $sql = "INSERT INTO pacientes
        (nombre,apellido,cedula,telefono,email,fecha_nacimiento)
        VALUES
        (:nombre,:apellido,:cedula,:telefono,:email,:fecha_nacimiento)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':nombre'=>$data['nombre'],
            ':apellido'=>$data['apellido'],
            ':cedula'=>$data['cedula'],
            ':telefono'=>$data['telefono'],
            ':email'=>$data['email'],
            ':fecha_nacimiento'=>$data['fecha_nacimiento']
        ]);
    }

}