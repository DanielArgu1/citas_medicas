<?php

require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../core/Model.php';

class DashboardController extends Model{

public function index(){

$sql="SELECT COUNT(*) total FROM pacientes";
$totalPacientes=$this->db->query($sql)->fetch()['total'];

$sql="SELECT COUNT(*) total FROM medicos";
$totalMedicos=$this->db->query($sql)->fetch()['total'];

$sql="SELECT COUNT(*) total FROM citas";
$totalCitas=$this->db->query($sql)->fetch()['total'];

$sql="SELECT COUNT(*) total FROM citas WHERE fecha=CURDATE()";
$citasHoy=$this->db->query($sql)->fetch()['total'];

$sql="SELECT 
CONCAT(p.nombre,' ',p.apellido) paciente,
CONCAT(m.nombre,' ',m.apellido) medico,
c.fecha,
c.hora_inicio,
c.estado
FROM citas c
JOIN pacientes p ON c.paciente_id=p.id
JOIN medicos m ON c.medico_id=m.id
ORDER BY c.id DESC
LIMIT 5";

$ultimasCitas=$this->db->query($sql)->fetchAll();

require_once __DIR__.'/../views/dashboard.php';

}

}