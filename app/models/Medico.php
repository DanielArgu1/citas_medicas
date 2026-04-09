<?php

require_once __DIR__ . '/../core/Model.php';

class Medico extends Model {

    public function obtenerTodos() {
        $sql = "SELECT * FROM medicos 
                WHERE TRIM(LOWER(estado)) = 'activo' 
                ORDER BY nombre ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id){
        $stmt = $this->db->prepare("SELECT * FROM medicos WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function crear($data){
        $sql = "INSERT INTO medicos (nombre, apellido, especialidad, telefono, email)
                VALUES (:nombre, :apellido, :especialidad, :telefono, :email)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nombre' => $data['nombre'],
            ':apellido' => $data['apellido'],
            ':especialidad' => $data['especialidad'],
            ':telefono' => $data['telefono'],
            ':email' => $data['email']
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function actualizar($id, $data){
        $sql = "UPDATE medicos SET nombre=:nombre, apellido=:apellido, especialidad=:especialidad,
                telefono=:telefono, email=:email WHERE id=:id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':nombre' => $data['nombre'],
            ':apellido' => $data['apellido'],
            ':especialidad' => $data['especialidad'],
            ':telefono' => $data['telefono'],
            ':email' => $data['email']
        ]);
    }

    public function desactivar($id) {
        
        $sql = "UPDATE medicos SET estado = 'inactive' WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
