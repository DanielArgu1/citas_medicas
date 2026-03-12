<?php

require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../core/Model.php';

class DashboardController extends Model {
    public function index(){
        require_login();

        $totalPacientes = (int)$this->db->query("SELECT COUNT(*) total FROM pacientes")->fetch()['total'];
        $totalMedicos = (int)$this->db->query("SELECT COUNT(*) total FROM medicos")->fetch()['total'];
        $totalCitas = (int)$this->db->query("SELECT COUNT(*) total FROM citas")->fetch()['total'];
        $citasHoy = (int)$this->db->query("SELECT COUNT(*) total FROM citas WHERE fecha = CURDATE()")->fetch()['total'];

        if (current_user_role() === 'medico') {
            $sql = "SELECT CONCAT(p.nombre,' ',p.apellido) paciente, CONCAT(m.nombre,' ',m.apellido) medico,
                           c.fecha, c.hora_inicio, c.estado
                    FROM citas c
                    JOIN pacientes p ON c.paciente_id=p.id
                    JOIN medicos m ON c.medico_id=m.id
                    JOIN usuarios u ON u.medico_id = m.id
                    WHERE u.id = :usuario_id
                    ORDER BY c.fecha DESC, c.hora_inicio ASC LIMIT 5";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':usuario_id' => current_user_id()]);
            $ultimasCitas = $stmt->fetchAll();
        } else {
            $sql = "SELECT CONCAT(p.nombre,' ',p.apellido) paciente, CONCAT(m.nombre,' ',m.apellido) medico,
                           c.fecha, c.hora_inicio, c.estado
                    FROM citas c
                    JOIN pacientes p ON c.paciente_id=p.id
                    JOIN medicos m ON c.medico_id=m.id
                    ORDER BY c.id DESC LIMIT 5";
            $ultimasCitas = $this->db->query($sql)->fetchAll();
        }

        require_once __DIR__ . '/../views/dashboard.php';
    }
}
