<?php

require_once __DIR__ . '/../core/Model.php';

class Usuario extends Model {

    public function buscarPorEmail($email){
        $sql = "SELECT * FROM usuarios WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    public function crear($data){
        $sql = "INSERT INTO usuarios (nombre, email, password, rol, medico_id, estado)
                VALUES (:nombre, :email, :password, :rol, :medico_id, :estado)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nombre' => $data['nombre'],
            ':email' => $data['email'],
            ':password' => $data['password'],
            ':rol' => $data['rol'],
            ':medico_id' => $data['medico_id'],
            ':estado' => $data['estado'] ?? 'activo'
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function actualizarPassword($id, $hash){
        $stmt = $this->db->prepare("UPDATE usuarios SET password = :password WHERE id = :id");
        return $stmt->execute([':password' => $hash, ':id' => $id]);
    }

    public function obtenerPorMedicoId($medicoId){
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE medico_id = :medico_id LIMIT 1");
        $stmt->execute([':medico_id' => $medicoId]);
        return $stmt->fetch();
    }

    public function eliminarPorMedicoId($medicoId){
        $stmt = $this->db->prepare("DELETE FROM usuarios WHERE medico_id = :medico_id");
        return $stmt->execute([':medico_id' => $medicoId]);
    }
}
