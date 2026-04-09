<?php

require_once __DIR__ . '/../core/Model.php';

class Usuario extends Model {

    public function __construct() {
        parent::__construct();
        // Mantiene la base de datos actualizada automáticamente
        $this->asegurarEstructuraUsuarios();
    }

    // --- MÉTODOS DE ESTRUCTURA Y MANTENIMIENTO ---

    private function columnaExiste(string $tabla, string $columna): bool
    {
        $sql = "SELECT COUNT(*) FROM information_schema.COLUMNS 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = :tabla 
                AND COLUMN_NAME = :columna";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':tabla' => $tabla,
            ':columna' => $columna,
        ]);
        return (int)$stmt->fetchColumn() > 0;
    }

    private function asegurarEstructuraUsuarios(): void
    {
        try {
            if (!$this->columnaExiste('usuarios', 'paciente_id')) {
                $this->db->exec("ALTER TABLE usuarios ADD COLUMN paciente_id INT NULL AFTER medico_id");
            }

            if (!$this->columnaExiste('usuarios', 'debe_cambiar_password')) {
                $this->db->exec("ALTER TABLE usuarios ADD COLUMN debe_cambiar_password TINYINT(1) NOT NULL DEFAULT 0 AFTER estado");
            }
        } catch (Throwable $e) {
            // Silenciamos errores de estructura para evitar caídas en producción
        }
    }

    // --- MÉTODOS DE BÚSQUEDA ---

    public function buscarPorEmail($email){
        // IMPORTANTE: En el login, podrías filtrar solo por activos
        $sql = "SELECT * FROM usuarios WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    public function obtenerPorId($id){
        $sql = "SELECT * FROM usuarios WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
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

    // --- MÉTODOS DE ESCRITURA Y ACTUALIZACIÓN ---

    public function crear($data){
        $sql = "INSERT INTO usuarios (nombre, email, password, rol, medico_id, paciente_id, estado, debe_cambiar_password)
                VALUES (:nombre, :email, :password, :rol, :medico_id, :paciente_id, :estado, :debe_cambiar_password)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nombre' => $data['nombre'],
            ':email' => $data['email'],
            ':password' => $data['password'],
            ':rol' => $data['rol'],
            ':medico_id' => $data['medico_id'] ?? null,
            ':paciente_id' => $data['paciente_id'] ?? null,
            ':estado' => $data['estado'] ?? 'activo',
            ':debe_cambiar_password' => !empty($data['debe_cambiar_password']) ? 1 : 0,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function actualizarPassword($id, $hash, $debeCambiarPassword = null){
        $sql = "UPDATE usuarios SET password = :password";
        $params = [':password' => $hash, ':id' => $id];

        if ($debeCambiarPassword !== null) {
            $sql .= ", debe_cambiar_password = :debe_cambiar_password";
            $params[':debe_cambiar_password'] = $debeCambiarPassword ? 1 : 0;
        }

        $sql .= " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
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

    // --- MÉTODOS DE VALIDACIÓN ---

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

    // --- REFACTORIZACIÓN: BORRADO LÓGICO (INACTIVAR) ---

    /**
     * Inactiva la cuenta del usuario vinculado a un médico.
     */
    public function cambiarEstadoPorMedico(int $medicoId, string $estado): bool {
        $sql = "UPDATE usuarios SET estado = :estado WHERE medico_id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':estado' => $estado, 
            ':id'     => $medicoId
        ]);
    }

    /**
     * Inactiva la cuenta del usuario vinculado a un paciente.
     */
    public function cambiarEstadoPorPaciente(int $pacienteId, string $estado): bool {
        $sql = "UPDATE usuarios SET estado = :estado WHERE paciente_id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':estado' => $estado, 
            ':id'     => $pacienteId
        ]);
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