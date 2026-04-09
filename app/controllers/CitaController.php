<?php

require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Cita.php';
require_once __DIR__ . '/../models/Paciente.php';
require_once __DIR__ . '/../models/Medico.php';
require_once __DIR__ . '/../helpers/Notifier.php';

class CitaController extends Controller {
    private $cita;

    public function __construct(){
        $this->cita = new Cita();
    }

    private function notify($tipo, $cita){
        if (!$cita || empty($cita['email'])) {
            return;
        }

        $asunto = "Notificación de cita médica";
        $mensaje = match ($tipo) {
            'creada' => "Hola {$cita['paciente']}, tu cita ha sido creada para el {$cita['fecha']} a las {$cita['hora_inicio']} con {$cita['medico']}",
            'actualizada' => "Hola {$cita['paciente']}, tu cita ha sido actualizada: {$cita['fecha']} a las {$cita['hora_inicio']} con {$cita['medico']}",
            'completada' => "Hola {$cita['paciente']}, tu cita del {$cita['fecha']} con {$cita['medico']} fue marcada como completada.",
            'cancelada' => "Hola {$cita['paciente']}, tu cita del {$cita['fecha']} con {$cita['medico']} fue cancelada.",
            default => null,
        };

        if ($mensaje) {
            Notifier::enviarCorreo($cita['email'], $asunto, $mensaje);
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

    private function buildPacienteData(){
        $horaInicio = $_POST['hora_inicio'] ?? '';
        $horaFin = date('H:i:s', strtotime($horaInicio . ' +30 minutes'));
        return [
            'paciente_id' => (int)current_paciente_id(),
            'medico_id' => (int)($_POST['medico_id'] ?? 0),
            'fecha' => $_POST['fecha'] ?? '',
            'hora_inicio' => $horaInicio,
            'hora_fin' => $horaFin,
            'motivo' => trim($_POST['motivo'] ?? ''),
            'estado' => 'pendiente',
        ];
    }

    private function validateData(array $data, $checkPastDateTime = true){
        if(!$data['paciente_id'] || !$data['medico_id'] || $data['fecha'] === '' || $data['hora_inicio'] === ''){
            return 'Debes completar paciente, médico, fecha y hora.';
        }

        if(!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['fecha'])){
            return 'La fecha ingresada no es válida.';
        }

        if(!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $data['hora_inicio'])){
            return 'La hora ingresada no es válida.';
        }

        $dateTime = strtotime($data['fecha'] . ' ' . $data['hora_inicio']);
        if ($dateTime === false) {
            return 'La fecha y hora de la cita no son válidas.';
        }

        if ($checkPastDateTime) {
            if(strtotime($data['fecha']) < strtotime(date('Y-m-d'))){
                return 'No se pueden crear citas en fechas pasadas.';
            }

            if ($dateTime < time()) {
                return 'No se pueden registrar citas en un horario pasado.';
            }
        }

        if(strlen($data['motivo']) > 250){
            return 'El motivo no puede superar 250 caracteres.';
        }

        return null;
    }

    private function createRedirectUrl(){
        return current_user_role() === 'paciente'
            ? 'index.php?controller=cita&action=crearPaciente'
            : 'index.php?controller=cita&action=crear';
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
        $this->logActivity('VIEW', 'citas', 'Consultó el listado de citas.');
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

    public function crearPaciente(){
        require_roles(['paciente']);
        $pacienteModel = new Paciente();
        $medicoModel = new Medico();
        $paciente = $pacienteModel->obtenerPorId((int)current_paciente_id());

        if (!$paciente) {
            flash('error', 'No se encontró tu perfil de paciente para agendar la cita.');
            header("Location: index.php?controller=dashboard&action=index");
            exit;
        }

        $medicos = $medicoModel->obtenerTodos();
        require_once __DIR__ . '/../views/citas/crear_paciente.php';
    }

    public function guardar(){
        require_roles(['admin', 'recepcion']);
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            header("Location: index.php?controller=cita&action=index"); exit;
        }
        $data = $this->buildData();
        $error = $this->validateData($data, true);
        if($error){
            flash('error', $error);
            header("Location: " . $this->createRedirectUrl()); exit;
        }
        $res = $this->cita->crear($data);
        if ($res === 'ocupado') {
            flash('error', 'El médico ya tiene una cita en ese horario.');
            header("Location: " . $this->createRedirectUrl()); exit;
        }
        $cita = $this->cita->obtenerPorId($res);
        $this->notify('creada', $cita);
        $this->logActivity('INSERT', 'citas', 'Creó la cita ID ' . $res . ' para paciente ' . ($cita['paciente'] ?? ('ID ' . $data['paciente_id'])) . ' con ' . ($cita['medico'] ?? ('médico ID ' . $data['medico_id'])) . '.');
        flash('success', 'Cita registrada y paciente notificado. Revisa app/logs/notificaciones.log');
        header("Location: index.php?controller=cita&action=index"); exit;
    }

    public function guardarPaciente(){
        require_roles(['paciente']);
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            header("Location: index.php?controller=cita&action=index"); exit;
        }

        $pacienteModel = new Paciente();
        $paciente = $pacienteModel->obtenerPorId((int)current_paciente_id());
        if (!$paciente) {
            flash('error', 'No se encontró tu perfil de paciente para agendar la cita.');
            header("Location: index.php?controller=dashboard&action=index"); exit;
        }

        $data = $this->buildPacienteData();
        $error = $this->validateData($data, true);
        if($error){
            flash('error', $error);
            header("Location: index.php?controller=cita&action=crearPaciente"); exit;
        }

        $res = $this->cita->crear($data);
        if ($res === 'ocupado') {
            flash('error', 'El médico ya tiene una cita en ese horario.');
            header("Location: index.php?controller=cita&action=crearPaciente"); exit;
        }

        $cita = $this->cita->obtenerPorId($res);
        $this->notify('creada', $cita);
        $this->logActivity('INSERT', 'citas', 'El paciente agendó su propia cita ID ' . $res . ' con ' . ($cita['medico'] ?? ('médico ID ' . $data['medico_id'])) . '.');
        flash('success', 'Tu cita fue agendada correctamente.');
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
        $error = $this->validateData($data, false);
        if($error){
            flash('error', $error);
            header("Location: index.php?controller=cita&action=editar&id={$id}"); exit;
        }
        $res = $this->cita->actualizar($id, $data);
        if ($res === 'ocupado') {
            flash('error', 'El médico ya tiene una cita en ese horario.');
            header("Location: index.php?controller=cita&action=editar&id={$id}"); exit;
        }
        $cita = $this->cita->obtenerPorId($id);
        $this->notify('actualizada', $cita);
        $this->logActivity('UPDATE', 'citas', 'Actualizó la cita ID ' . $id . ' a ' . ($cita['fecha'] ?? $data['fecha']) . ' ' . ($cita['hora_inicio'] ?? $data['hora_inicio']) . '.');
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
        $citaActualizada = $this->cita->obtenerPorId($id);
        $this->notify('completada', $citaActualizada);
        $this->logActivity('UPDATE', 'citas', 'Marcó como completada la cita ID ' . $id . '.');
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
        $citaActualizada = $this->cita->obtenerPorId($id);
        $this->notify('cancelada', $citaActualizada);
        $this->logActivity('DELETE', 'citas', 'Canceló la cita ID ' . $id . '.');
        flash('info', 'La cita fue cancelada y el paciente fue notificado.');
        header("Location: index.php?controller=cita&action=index"); exit;
    }

    public function eliminar(){
        require_roles(['admin']);
        $id = (int)($_GET['id'] ?? 0);
        if ($id) {
            $this->cita->eliminar($id);
            $this->logActivity('DELETE', 'citas', 'Eliminó la cita ID ' . $id . '.');
            flash('success', 'La cita fue eliminada correctamente.');
        }
        header("Location: index.php?controller=cita&action=index"); exit;
    }
}
