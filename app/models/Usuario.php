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
        $sql = "INSERT INTO usuarios (nombre, email, password, rol, medico_id, paciente_id, estado)
                VALUES (:nombre, :email, :password, :rol, :medico_id, :paciente_id, :estado)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nombre' => $data['nombre'],
            ':email' => $data['email'],
            ':password' => $data['password'],
            ':rol' => $data['rol'],
            ':medico_id' => $data['medico_id'] ?? null,
            ':paciente_id' => $data['paciente_id'] ?? null,
            ':estado' => $data['estado'] ?? 'activo'
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function actualizarPassword($id, $hash){
        $stmt = $this->db->prepare("UPDATE usuarios SET password = :password WHERE id = :id");
        return $stmt->execute([':password' => $hash, ':id' => $id]);
    }

    public function actualizarDatosPorMedicoId($medicoId, $nombre, $email){
        $stmt = $this->db->prepare("UPDATE usuarios SET nombre = :nombre, email = :email WHERE medico_id = :medico_id");
        return $stmt->execute([
            ':nombre' => $nombre,
            ':email' => $email,
            ':medico_id' => $medicoId
        ]);
    }

    public function actualizarDatosPorPacienteId($pacienteId, $nombre, $email){
        $stmt = $this->db->prepare("UPDATE usuarios SET nombre = :nombre, email = :email WHERE paciente_id = :paciente_id");
        return $stmt->execute([
            ':nombre' => $nombre,
            ':email' => $email,
            ':paciente_id' => $pacienteId
        ]);
    }

    public function obtenerPorMedicoId($medicoId){
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE medico_id = :medico_id LIMIT 1");
        $stmt->execute([':medico_id' => $medicoId]);
        return $stmt->fetch();
    }

    public function obtenerPorPacienteId($pacienteId){
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE paciente_id = :paciente_id LIMIT 1");
        $stmt->execute([':paciente_id' => $pacienteId]);
        return $stmt->fetch();
    }

    public function emailEnUsoPorOtro($email, $excludeUserId = null){
        $sql = "SELECT id FROM usuarios WHERE email = :email";
        $params = [':email' => $email];

        if ($excludeUserId) {
            $sql .= " AND id != :id";
            $params[':id'] = $excludeUserId;
        }

        $sql .= " LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (bool)$stmt->fetch();
    }

    public function eliminarPorMedicoId($medicoId){
        $stmt = $this->db->prepare("DELETE FROM usuarios WHERE medico_id = :medico_id");
        return $stmt->execute([':medico_id' => $medicoId]);
    }

    public function eliminarPorPacienteId($pacienteId){
        $stmt = $this->db->prepare("DELETE FROM usuarios WHERE paciente_id = :paciente_id");
        return $stmt->execute([':paciente_id' => $pacienteId]);
    }
}
