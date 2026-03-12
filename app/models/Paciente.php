<?php

require_once __DIR__ . '/../core/Model.php';

class Paciente extends Model {

    public function obtenerTodos(){
        $stmt = $this->db->prepare("SELECT * FROM pacientes ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function obtenerPorId($id){
        $sql = "SELECT * FROM pacientes WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($data){
        $sql = "INSERT INTO pacientes (nombre, apellido, cedula, telefono, email, fecha_nacimiento)
                VALUES (:nombre, :apellido, :cedula, :telefono, :email, :fecha_nacimiento)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre' => $data['nombre'],
            ':apellido' => $data['apellido'],
            ':cedula' => $data['cedula'],
            ':telefono' => $data['telefono'],
            ':email' => $data['email'],
            ':fecha_nacimiento' => $data['fecha_nacimiento'] ?: null
        ]);
    }

    public function actualizar($id, $data){
        $sql = "UPDATE pacientes SET nombre=:nombre, apellido=:apellido, cedula=:cedula,
                telefono=:telefono, email=:email, fecha_nacimiento=:fecha_nacimiento
                WHERE id=:id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':nombre' => $data['nombre'],
            ':apellido' => $data['apellido'],
            ':cedula' => $data['cedula'],
            ':telefono' => $data['telefono'],
            ':email' => $data['email'],
            ':fecha_nacimiento' => $data['fecha_nacimiento'] ?: null
        ]);
    }

    public function eliminar($id){
        $stmt = $this->db->prepare("DELETE FROM pacientes WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function existeCedula($cedula, $exceptId = null){
        $sql = "SELECT id FROM pacientes WHERE cedula = :cedula" . ($exceptId ? " AND id != :id" : "") . " LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $params = [':cedula' => $cedula];
        if ($exceptId) $params[':id'] = $exceptId;
        $stmt->execute($params);
        return (bool)$stmt->fetch();
    }
}
