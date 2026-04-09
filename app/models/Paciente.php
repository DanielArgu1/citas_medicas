<?php

require_once __DIR__ . '/../core/Model.php';

class Paciente extends Model {

   public function obtenerTodos(){
        $sql = "SELECT p.*, u.id AS usuario_id, u.estado AS usuario_estado
                FROM pacientes p
                LEFT JOIN usuarios u ON u.paciente_id = p.id AND u.rol = 'paciente'
                WHERE p.estado = 'activo' 
                ORDER BY p.id DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function obtenerPorId($id){
        $sql = "SELECT p.*, u.id AS usuario_id, u.estado AS usuario_estado
                FROM pacientes p
                LEFT JOIN usuarios u ON u.paciente_id = p.id AND u.rol = 'paciente'
                WHERE p.id = :id
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($data){
        $sql = "INSERT INTO pacientes (nombre, apellido, cedula, telefono, email, fecha_nacimiento)
                VALUES (:nombre, :apellido, :cedula, :telefono, :email, :fecha_nacimiento)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nombre' => $data['nombre'],
            ':apellido' => $data['apellido'],
            ':cedula' => $data['cedula'],
            ':telefono' => $data['telefono'],
            ':email' => $data['email'],
            ':fecha_nacimiento' => $data['fecha_nacimiento'] ?: null
        ]);
        return (int)$this->db->lastInsertId();
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

   public function desactivar($id) {
        $sql = "UPDATE pacientes SET estado = 'inactivo' WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function existeCedula($cedula, $exceptId = null){
        $sql = "SELECT id FROM pacientes WHERE cedula = :cedula" . ($exceptId ? " AND id != :id" : "") . " LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $params = [':cedula' => $cedula];
        if ($exceptId) {
            $params[':id'] = $exceptId;
        }
        $stmt->execute($params);
        return (bool)$stmt->fetch();
    }

    public function total(){
        return (int)$this->db->query("SELECT COUNT(*) total FROM pacientes")->fetch()['total'];
    }

    public function totalHistoriales($pacienteId){
        $stmt = $this->db->prepare("SELECT COUNT(*) total FROM historial_clinico WHERE paciente_id = :paciente_id");
        $stmt->execute([':paciente_id' => $pacienteId]);
        return (int)$stmt->fetch()['total'];
    }
}
