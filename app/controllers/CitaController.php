<?php

require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../models/Cita.php';
require_once __DIR__ . '/../models/Paciente.php';
require_once __DIR__ . '/../models/Medico.php';

class CitaController {
    private $cita;

    public function __construct(){
        $this->cita = new Cita();
    }

    private function notify($tipo, $cita){
        $logDir = __DIR__ . '/../logs';
        if (!is_dir($logDir)) mkdir($logDir, 0777, true);
        $asunto = '';
        $mensaje = '';
        if ($tipo === 'creada') {
            $asunto = 'Nueva cita médica';
            $mensaje = "Hola {$cita['paciente']}, su cita quedó programada para el {$cita['fecha']} de {$cita['hora_inicio']} a {$cita['hora_fin']} con {$cita['medico']}. Estado: {$cita['estado']}";
        } elseif ($tipo === 'actualizada') {
            $asunto = 'Cita médica actualizada';
            $mensaje = "Hola {$cita['paciente']}, su cita fue actualizada para el {$cita['fecha']} de {$cita['hora_inicio']} a {$cita['hora_fin']} con {$cita['medico']}. Estado: {$cita['estado']}";
        } elseif ($tipo === 'cancelada') {
            $asunto = 'Cita médica cancelada';
            $mensaje = "Hola {$cita['paciente']}, su cita del {$cita['fecha']} con {$cita['medico']} fue cancelada.";
        } elseif ($tipo === 'completada') {
            $asunto = 'Cita médica completada';
            $mensaje = "Hola {$cita['paciente']}, su cita del {$cita['fecha']} con {$cita['medico']} fue marcada como completada.";
        }
        $line = '['.date('Y-m-d H:i:s')."] {$asunto} | PARA: {$cita['paciente_email']} | TEL: {$cita['paciente_telefono']} | {$mensaje}\n";
        file_put_contents($logDir . '/notificaciones.log', $line, FILE_APPEND);
        if (!empty($cita['paciente_email'])) {
            @mail($cita['paciente_email'], $asunto, $mensaje);
        }
    }

    private function buildData(){
        $horaInicio = $_POST['hora_inicio'] ?? '';
        $horaFin = date('H:i:s', strtotime($horaInicio . ' +30 minutes'));
        return [
            'paciente_id' => (int)($_POST['paciente_id'] ?? 0),
            'medico_id' => (int)($_POST['medico_id'] ?? 0),
            'fecha' => $_POST['fecha'] ?? '',
            'hora_inicio' => $horaInicio,
            'hora_fin' => $horaFin,
            'motivo' => trim($_POST['motivo'] ?? ''),
            'estado' => $_POST['estado'] ?? 'pendiente',
        ];
    }

    public function index(){
        require_login();
        if (current_user_role() === 'medico') {
            $citas = $this->cita->obtenerPorMedicoUsuario(current_user_id());
        } elseif (current_user_role() === 'paciente') {
            $citas = $this->cita->obtenerPorPacienteUsuario(current_user_id());
        } else {
            $citas = $this->cita->obtenerTodas();
        }
        require_once __DIR__ . '/../views/citas/index.php';
    }

    public function crear(){
        require_roles(['admin', 'recepcion']);
        $pacienteModel = new Paciente();
        $medicoModel = new Medico();
        $pacientes = $pacienteModel->obtenerTodos();
        $medicos = $medicoModel->obtenerTodos();
        require_once __DIR__ . '/../views/citas/crear.php';
    }

    public function guardar(){
        require_roles(['admin', 'recepcion']);
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            header("Location: index.php?controller=cita&action=index"); exit;
        }
        $data = $this->buildData();
        if(!$data['paciente_id'] || !$data['medico_id'] || $data['fecha'] === '' || $data['hora_inicio'] === ''){
            flash('error', 'Debes completar paciente, médico, fecha y hora.');
            header("Location: index.php?controller=cita&action=crear"); exit;
        }
        if(strtotime($data['fecha']) < strtotime(date('Y-m-d'))){
            flash('error', 'No se pueden crear citas en fechas pasadas.');
            header("Location: index.php?controller=cita&action=crear"); exit;
        }
        if(strlen($data['motivo']) > 250){
            flash('error', 'El motivo no puede superar 250 caracteres.');
            header("Location: index.php?controller=cita&action=crear"); exit;
        }
        $res = $this->cita->crear($data);
        if ($res === 'ocupado') {
            flash('error', 'El médico ya tiene una cita en ese horario.');
            header("Location: index.php?controller=cita&action=crear"); exit;
        }
        $cita = $this->cita->obtenerPorId($res);
        $this->notify('creada', $cita);
        flash('success', 'Cita registrada y paciente notificado. Revisa app/logs/notificaciones.log');
        header("Location: index.php?controller=cita&action=index"); exit;
    }

    public function editar(){
        require_roles(['admin', 'recepcion']);
        $id = (int)($_GET['id'] ?? 0);
        $cita = $this->cita->obtenerPorId($id);
        if (!$cita) { flash('error', 'Cita no encontrada.'); header("Location: index.php?controller=cita&action=index"); exit; }
        $pacienteModel = new Paciente();
        $medicoModel = new Medico();
        $pacientes = $pacienteModel->obtenerTodos();
        $medicos = $medicoModel->obtenerTodos();
        require_once __DIR__ . '/../views/citas/editar.php';
    }

    public function actualizar(){
        require_roles(['admin', 'recepcion']);
        $id = (int)($_POST['id'] ?? 0);
        $data = $this->buildData();
        $res = $this->cita->actualizar($id, $data);
        if ($res === 'ocupado') {
            flash('error', 'El médico ya tiene una cita en ese horario.');
            header("Location: index.php?controller=cita&action=editar&id={$id}"); exit;
        }
        $cita = $this->cita->obtenerPorId($id);
        $this->notify('actualizada', $cita);
        flash('success', 'Cita actualizada y paciente notificado.');
        header("Location: index.php?controller=cita&action=index"); exit;
    }

    public function completar(){
        require_login();
        $id = (int)($_GET['id'] ?? 0);
        $cita = $this->cita->obtenerPorId($id);
        if (!$cita) { flash('error', 'Cita no encontrada.'); header("Location: index.php?controller=cita&action=index"); exit; }

        if (current_user_role() === 'paciente') {
            flash('error', 'Un paciente no puede marcar citas como completadas.');
            header("Location: index.php?controller=cita&action=index"); exit;
        }

        if (current_user_role() === 'medico' && (int)current_medico_id() !== (int)$cita['medico_id']) {
            flash('error', 'No puedes modificar citas de otro médico.');
            header("Location: index.php?controller=cita&action=index"); exit;
        }
        $this->cita->actualizarEstado($id, 'completada');
        $this->notify('completada', $this->cita->obtenerPorId($id));
        flash('success', 'La cita fue marcada como completada.');
        header("Location: index.php?controller=cita&action=index"); exit;
    }

    public function cancelar(){
        require_login();
        $id = (int)($_GET['id'] ?? 0);
        $cita = $this->cita->obtenerPorId($id);
        if (!$cita) { flash('error', 'Cita no encontrada.'); header("Location: index.php?controller=cita&action=index"); exit; }

        if (current_user_role() === 'medico' && (int)current_medico_id() !== (int)$cita['medico_id']) {
            flash('error', 'No puedes cancelar citas de otro médico.');
            header("Location: index.php?controller=cita&action=index"); exit;
        }

        if (current_user_role() === 'paciente' && (int)current_paciente_id() !== (int)$cita['paciente_id']) {
            flash('error', 'No puedes cancelar citas de otro paciente.');
            header("Location: index.php?controller=cita&action=index"); exit;
        }

        $this->cita->actualizarEstado($id, 'cancelada');
        $this->notify('cancelada', $this->cita->obtenerPorId($id));
        flash('info', 'La cita fue cancelada y el paciente fue notificado.');
        header("Location: index.php?controller=cita&action=index"); exit;
    }

    public function eliminar(){
        require_roles(['admin']);
        $id = (int)($_GET['id'] ?? 0);
        if ($id) {
            $this->cita->eliminar($id);
            flash('success', 'La cita fue eliminada correctamente.');
        }
        header("Location: index.php?controller=cita&action=index"); exit;
    }
}
