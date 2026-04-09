<?php

require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Paciente.php';
require_once __DIR__ . '/../models/Cita.php';

class DashboardController extends Controller {
    private $db;

    public function __construct(){
        $database = new Database();
        $this->db = $database->conectar();
    }
    public function index(){
        require_login();

        $role = current_user_role();
        $pacienteModel = new Paciente();
        $citaModel = new Cita();

        if ($role === 'paciente') {
            $pacienteId = (int)current_paciente_id();
            $paciente = $pacienteModel->obtenerPorId($pacienteId);

            $totalPacientes = 1;
            $totalMedicos = (int)$this->db->query("SELECT COUNT(*) total FROM medicos")->fetch()['total'];
            $totalCitas = $citaModel->totalPorPaciente($pacienteId);
            $stmtHoy = $this->db->prepare("SELECT COUNT(*) total FROM citas WHERE paciente_id = :paciente_id AND fecha = CURDATE()");
            $stmtHoy->execute([':paciente_id' => $pacienteId]);
            $citasHoy = (int)$stmtHoy->fetch()['total'];
            $citasPendientes = $citaModel->contarPendientesPorPaciente($pacienteId);
            $totalHistoriales = $pacienteModel->totalHistoriales($pacienteId);
            $ultimasCitas = $citaModel->obtenerPorPacienteUsuario(current_user_id());
            $ultimasCitas = array_slice($ultimasCitas, 0, 5);
            $this->logActivity('VIEW', 'dashboard', 'Ingresó al panel principal.');
            require_once __DIR__ . '/../views/dashboard.php';
            return;
        }

        $totalPacientes = (int)$this->db->query("SELECT COUNT(*) total FROM pacientes")->fetch()['total'];
        $totalMedicos = (int)$this->db->query("SELECT COUNT(*) total FROM medicos")->fetch()['total'];
        $totalCitas = (int)$this->db->query("SELECT COUNT(*) total FROM citas")->fetch()['total'];
        $citasHoy = (int)$this->db->query("SELECT COUNT(*) total FROM citas WHERE fecha = CURDATE()")->fetch()['total'];

        if ($role === 'medico') {
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

        $this->logActivity('VIEW', 'dashboard', 'Ingresó al panel principal.');
        require_once __DIR__ . '/../views/dashboard.php';
    }
}
