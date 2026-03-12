<?php

require_once __DIR__ . '/../core/Model.php';

class Reporte extends Model
{
    public function totalPacientes()
    {
        $stmt = $this->db->query("SELECT COUNT(*) AS total FROM pacientes");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    public function totalMedicos()
    {
        $stmt = $this->db->query("SELECT COUNT(*) AS total FROM medicos");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    public function totalCitas()
    {
        $stmt = $this->db->query("SELECT COUNT(*) AS total FROM citas");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    public function citasHoy()
    {
        $stmt = $this->db->query("SELECT COUNT(*) AS total FROM citas WHERE fecha = CURDATE()");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    public function totalHistoriales()
    {
        $stmt = $this->db->query("SELECT COUNT(*) AS total FROM historial_clinico");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    public function citasCompletadas()
    {
        $stmt = $this->db->query("SELECT COUNT(*) AS total FROM citas WHERE estado = 'completada'");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    public function citasCanceladas()
    {
        $stmt = $this->db->query("SELECT COUNT(*) AS total FROM citas WHERE estado = 'cancelada'");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    public function obtenerMedicos()
    {
        $stmt = $this->db->query("SELECT id, nombre, apellido FROM medicos ORDER BY nombre, apellido");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPacientes()
    {
        $stmt = $this->db->query("SELECT id, nombre, apellido FROM pacientes ORDER BY nombre, apellido");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerCitas($filters = [])
    {
        $sql = "SELECT 
                    c.*,
                    CONCAT(p.nombre, ' ', p.apellido) AS paciente,
                    CONCAT(m.nombre, ' ', m.apellido) AS medico
                FROM citas c
                INNER JOIN pacientes p ON c.paciente_id = p.id
                INNER JOIN medicos m ON c.medico_id = m.id
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

        $sql .= " ORDER BY c.fecha DESC, c.hora_inicio DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerHistoriales($filters = [])
    {
        $sql = "SELECT
                    h.*,
                    CONCAT(p.nombre, ' ', p.apellido) AS paciente,
                    CONCAT(m.nombre, ' ', m.apellido) AS medico
                FROM historial_clinico h
                INNER JOIN pacientes p ON h.paciente_id = p.id
                INNER JOIN medicos m ON h.medico_id = m.id
                WHERE 1=1";
        $params = [];

        if (!empty($filters['hist_medico_id'])) {
            $sql .= " AND h.medico_id = :hist_medico_id";
            $params[':hist_medico_id'] = $filters['hist_medico_id'];
        }

        if (!empty($filters['paciente_id'])) {
            $sql .= " AND h.paciente_id = :paciente_id";
            $params[':paciente_id'] = $filters['paciente_id'];
        }

        if (!empty($filters['fecha_desde_hist'])) {
            $sql .= " AND DATE(h.fecha_registro) >= :fecha_desde_hist";
            $params[':fecha_desde_hist'] = $filters['fecha_desde_hist'];
        }

        if (!empty($filters['fecha_hasta_hist'])) {
            $sql .= " AND DATE(h.fecha_registro) <= :fecha_hasta_hist";
            $params[':fecha_hasta_hist'] = $filters['fecha_hasta_hist'];
        }

        if (!empty($filters['diagnostico'])) {
            $sql .= " AND h.diagnostico LIKE :diagnostico";
            $params[':diagnostico'] = '%' . $filters['diagnostico'] . '%';
        }

        $sql .= " ORDER BY h.fecha_registro DESC, h.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function citasPorEstado()
    {
        $stmt = $this->db->query("SELECT estado, COUNT(*) AS total FROM citas GROUP BY estado ORDER BY total DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function diagnosticosFrecuentes()
    {
        $stmt = $this->db->query("
            SELECT diagnostico, COUNT(*) AS total
            FROM historial_clinico
            GROUP BY diagnostico
            ORDER BY total DESC, diagnostico ASC
            LIMIT 5
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function pacientesFrecuentes()
    {
        $stmt = $this->db->query("
            SELECT 
                CONCAT(p.nombre, ' ', p.apellido) AS paciente,
                COUNT(h.id) AS total
            FROM historial_clinico h
            INNER JOIN pacientes p ON h.paciente_id = p.id
            GROUP BY h.paciente_id
            ORDER BY total DESC, paciente ASC
            LIMIT 5
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function medicosFrecuentes()
    {
        $stmt = $this->db->query("
            SELECT 
                CONCAT(m.nombre, ' ', m.apellido) AS medico,
                COUNT(h.id) AS total
            FROM historial_clinico h
            INNER JOIN medicos m ON h.medico_id = m.id
            GROUP BY h.medico_id
            ORDER BY total DESC, medico ASC
            LIMIT 5
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function ultimosHistoriales()
    {
        $stmt = $this->db->query("
            SELECT
                CONCAT(p.nombre, ' ', p.apellido) AS paciente,
                CONCAT(m.nombre, ' ', m.apellido) AS medico,
                h.diagnostico,
                h.fecha_registro
            FROM historial_clinico h
            INNER JOIN pacientes p ON h.paciente_id = p.id
            INNER JOIN medicos m ON h.medico_id = m.id
            ORDER BY h.fecha_registro DESC
            LIMIT 5
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function diagnosticoMasFrecuente()
    {
        $stmt = $this->db->query("
            SELECT diagnostico, COUNT(*) AS total
            FROM historial_clinico
            GROUP BY diagnostico
            ORDER BY total DESC, diagnostico ASC
            LIMIT 1
        ");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function medicoMasActivo()
    {
        $stmt = $this->db->query("
            SELECT CONCAT(m.nombre, ' ', m.apellido) AS medico, COUNT(c.id) AS total
            FROM citas c
            INNER JOIN medicos m ON c.medico_id = m.id
            GROUP BY c.medico_id
            ORDER BY total DESC, medico ASC
            LIMIT 1
        ");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function pacienteMasConsultas()
    {
        $stmt = $this->db->query("
            SELECT CONCAT(p.nombre, ' ', p.apellido) AS paciente, COUNT(h.id) AS total
            FROM historial_clinico h
            INNER JOIN pacientes p ON h.paciente_id = p.id
            GROUP BY h.paciente_id
            ORDER BY total DESC, paciente ASC
            LIMIT 1
        ");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}