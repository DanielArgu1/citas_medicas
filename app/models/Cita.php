<?php

require_once __DIR__ . '/../core/Model.php';

class Cita extends Model {

    public function obtenerTodas($filters = []){
        $sql = "SELECT c.*, 
                       CONCAT(p.nombre,' ',p.apellido) AS paciente,
                       p.email AS paciente_email,
                       p.telefono AS paciente_telefono,
                       CONCAT(m.nombre,' ',m.apellido) AS medico
                FROM citas c
                JOIN pacientes p ON c.paciente_id = p.id
                JOIN medicos m ON c.medico_id = m.id
                WHERE 1=1";
        $params = [];
        if (!empty($filters['medico_id'])) {
            $sql .= " AND c.medico_id = :medico_id";
            $params[':medico_id'] = $filters['medico_id'];
        }
        if (!empty($filters['estado'])) {
            $sql .= " AND c.estado = :estado";
            $params[':estado'] = $filters['estado'];
        }
        if (!empty($filters['fecha_desde'])) {
            $sql .= " AND c.fecha >= :fecha_desde";
            $params[':fecha_desde'] = $filters['fecha_desde'];
        }
        if (!empty($filters['fecha_hasta'])) {
            $sql .= " AND c.fecha <= :fecha_hasta";
            $params[':fecha_hasta'] = $filters['fecha_hasta'];
        }
        $sql .= " ORDER BY c.fecha DESC, c.hora_inicio ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

  public function obtenerPorId($id){
        $sql = "SELECT 
                    c.*,
                    CONCAT(p.nombre, ' ', p.apellido) AS paciente,
                    p.email AS paciente_email,
                    p.telefono AS paciente_telefono,
                    CONCAT(m.nombre, ' ', m.apellido) AS medico
                FROM citas c
                INNER JOIN pacientes p ON c.paciente_id = p.id
                INNER JOIN medicos m ON c.medico_id = m.id
                WHERE c.id = :id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function cambiarEstado($id, $estado){
        $sql = "UPDATE citas SET estado = :estado WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function obtenerPorMedicoUsuario($usuarioId){
        $sql = "SELECT c.*, CONCAT(p.nombre,' ',p.apellido) AS paciente,
                       CONCAT(m.nombre,' ',m.apellido) AS medico
                FROM citas c
                JOIN pacientes p ON c.paciente_id = p.id
                JOIN medicos m ON c.medico_id = m.id
                JOIN usuarios u ON u.medico_id = m.id
                WHERE u.id = :usuario_id
                ORDER BY c.fecha DESC, c.hora_inicio ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':usuario_id' => $usuarioId]);
        return $stmt->fetchAll();
    }

    public function verificarDisponibilidad($medico_id, $fecha, $hora_inicio, $hora_fin, $exceptId = null){
        $sql = "SELECT COUNT(*) total FROM citas
                WHERE medico_id = :medico_id
                  AND fecha = :fecha
                  AND estado != 'cancelada'
                  AND hora_inicio < :hora_fin
                  AND hora_fin > :hora_inicio";
        $params = [
            ':medico_id' => $medico_id,
            ':fecha' => $fecha,
            ':hora_fin' => $hora_fin,
            ':hora_inicio' => $hora_inicio
        ];
        if ($exceptId) {
            $sql .= " AND id != :id";
            $params[':id'] = $exceptId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return ((int)$row['total']) === 0;
    }

    public function crear($data){
        $horaFin = date('H:i:s', strtotime($data['hora_inicio'] . ' +30 minutes'));

        if (!$this->verificarDisponibilidad($data['medico_id'], $data['fecha'], $data['hora_inicio'], $horaFin)) {
            return 'ocupado';
        }

        $sql = "INSERT INTO citas (paciente_id, medico_id, fecha, hora_inicio, hora_fin, motivo, estado)
                VALUES (:paciente_id, :medico_id, :fecha, :hora_inicio, :hora_fin, :motivo, :estado)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':paciente_id' => $data['paciente_id'],
            ':medico_id' => $data['medico_id'],
            ':fecha' => $data['fecha'],
            ':hora_inicio' => $data['hora_inicio'],
            ':hora_fin' => $horaFin,
            ':motivo' => $data['motivo'],
            ':estado' => $data['estado'] ?? 'pendiente'
        ]);

        return (int)$this->db->lastInsertId();
    }

   public function actualizar($id, $data){
        $horaFin = date('H:i:s', strtotime($data['hora_inicio'] . ' +30 minutes'));

        if (!$this->verificarDisponibilidad($data['medico_id'], $data['fecha'], $data['hora_inicio'], $horaFin, $id)) {
            return 'ocupado';
        }

        $sql = "UPDATE citas 
                SET paciente_id = :paciente_id,
                    medico_id = :medico_id,
                    fecha = :fecha,
                    hora_inicio = :hora_inicio,
                    hora_fin = :hora_fin,
                    motivo = :motivo,
                    estado = :estado
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':paciente_id' => $data['paciente_id'],
            ':medico_id' => $data['medico_id'],
            ':fecha' => $data['fecha'],
            ':hora_inicio' => $data['hora_inicio'],
            ':hora_fin' => $horaFin,
            ':motivo' => $data['motivo'],
            ':estado' => $data['estado']
        ]);

        return 'ok';
    }

    public function actualizarEstado($id, $estado){
        $stmt = $this->db->prepare("UPDATE citas SET estado = :estado WHERE id = :id");
        return $stmt->execute([':estado' => $estado, ':id' => $id]);
    }

   public function cancelarCitasVencidas(){

        require_once __DIR__ . '/../helpers/Notifier.php';

        $sql = "SELECT c.*, 
                    CONCAT(p.nombre, ' ', p.apellido) AS paciente,
                    p.email AS paciente_email,
                    p.telefono AS paciente_telefono
                FROM citas c
                INNER JOIN pacientes p ON c.paciente_id = p.id
                WHERE c.estado = 'pendiente'
                AND TIMESTAMP(c.fecha, c.hora_fin) < NOW()";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $citasVencidas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($citasVencidas as $cita) {

            $update = $this->db->prepare("UPDATE citas SET estado = 'cancelada' WHERE id = :id");
            $update->execute([
                ':id' => $cita['id']
            ]);

            $mensaje = "Hola {$cita['paciente']}, su cita médica programada para {$cita['fecha']} a las {$cita['hora_inicio']} fue cancelada automáticamente porque no fue atendida dentro del tiempo establecido. Por favor reprograme su cita.";

            if (!empty($cita['paciente_email'])) {
                Notifier::enviarCorreo(
                    $cita['paciente_email'],
                    'Cita cancelada automáticamente',
                    $mensaje
                );
            }
        }

        return true;
    }

    public function eliminar($id){
        $stmt = $this->db->prepare("DELETE FROM citas WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function total(){
        return (int)$this->db->query("SELECT COUNT(*) total FROM citas")->fetch()['total'];
    }

    public function totalHoy(){
        return (int)$this->db->query("SELECT COUNT(*) total FROM citas WHERE fecha = CURDATE()")->fetch()['total'];
    }

    public function contarPorEstado(){
        return $this->db->query("SELECT estado, COUNT(*) total FROM citas GROUP BY estado ORDER BY estado")->fetchAll();
    }
}
