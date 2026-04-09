<?php

require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Paciente.php';
require_once __DIR__ . '/../models/Cita.php';
require_once __DIR__ . '/../models/HistorialClinico.php';
require_once __DIR__ . '/../services/UserAccountService.php';
require_once __DIR__ . '/../config/database.php';

class PacienteController extends Controller {
    private Paciente $paciente;
    private UserAccountService $accountService;

    public function __construct(){
        $this->paciente = new Paciente();
        $this->accountService = new UserAccountService();
    }

    /**
     * Método Privado para Auditoría
     * Registra las acciones en la tabla 'auditoria'
     */
    private function registrarLog($accion, $detalles) {
        try {
            $conn = (new Database())->conectar();
            $stmt = $conn->prepare("INSERT INTO auditoria (usuario_id, accion, tabla_afectada, detalles) VALUES (:u, :a, :t, :d)");
            $stmt->execute([
                ':u' => current_user_id() ?? ($_SESSION['usuario_id'] ?? 1), // Ajustar según tu variable de sesión de usuario logueado
                ':a' => $accion,
                ':t' => 'pacientes',
                ':d' => $detalles
            ]);
        } catch (Throwable $e) {
            // Error silencioso para no interrumpir el flujo si falla la auditoría
        }
    }

    private function digits($value){
        return preg_replace('/\D/', '', $value ?? '');
    }

    private function formatCedula($value){
        $d = substr($this->digits($value), 0, 13);
        if (strlen($d) <= 4) return $d;
        if (strlen($d) <= 8) return substr($d,0,4).'-'.substr($d,4);
        return substr($d,0,4).'-'.substr($d,4,4).'-'.substr($d,8);
    }

    private function formatPhone($value){
        $d = substr($this->digits($value), 0, 8);
        return strlen($d) <= 4 ? $d : substr($d,0,4).'-'.substr($d,4);
    }

    private function validate($data, $exceptId = null){
        if ($data['nombre'] === '' || $data['apellido'] === '' || $data['cedula'] === '' || $data['email'] === '') {
            return 'Nombre, apellido, cédula y correo electrónico son obligatorios.';
        }
        if (!preg_match('/^\d{4}-\d{4}-\d{5}$/', $data['cedula'])) {
            return 'La cédula debe tener formato 0000-0000-00000.';
        }
        if ($data['telefono'] !== '' && !preg_match('/^\d{4}-\d{4}$/', $data['telefono'])) {
            return 'El teléfono debe tener formato 0000-0000.';
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return 'El correo electrónico no es válido.';
        }
        if ($this->paciente->existeCedula($data['cedula'], $exceptId)) {
            return 'Ya existe un paciente con esa cédula.';
        }
        return null;
    }

    private function payloadFromRequest(){
        return [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'apellido' => trim($_POST['apellido'] ?? ''),
            'cedula' => $this->formatCedula($_POST['cedula'] ?? ''),
            'telefono' => $this->formatPhone($_POST['telefono'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? null,
        ];
    }

    private function fullName(array $data): string
    {
        return trim($data['nombre'] . ' ' . $data['apellido']);
    }

    public function index(){
        require_roles(['admin', 'recepcion']);
        $pacientes = $this->paciente->obtenerTodos();
        $this->render('pacientes/index', compact('pacientes'));
    }

    public function crear(){
        require_roles(['admin', 'recepcion']);
        $this->render('pacientes/crear');
    }

    public function guardar(){
        require_roles(['admin', 'recepcion']);
        $this->ensurePost('index.php?controller=paciente&action=index');

        $data = $this->payloadFromRequest();
        $error = $this->validate($data);
        if ($error) {
            $this->redirectWithFlash('error', $error, 'index.php?controller=paciente&action=crear');
        }

        if (!$this->accountService->emailDisponible($data['email'])) {
            $this->redirectWithFlash('error', 'Ese correo ya está siendo usado por otro usuario del sistema.', 'index.php?controller=paciente&action=crear');
        }

        $conn = (new Database())->conectar();

        try {
            $conn->beginTransaction();
            $stmt = $conn->prepare("INSERT INTO pacientes (nombre, apellido, cedula, telefono, email, fecha_nacimiento)
                                    VALUES (:nombre, :apellido, :cedula, :telefono, :email, :fecha_nacimiento)");
            $stmt->execute([
                ':nombre' => $data['nombre'],
                ':apellido' => $data['apellido'],
                ':cedula' => $data['cedula'],
                ':telefono' => $data['telefono'],
                ':email' => $data['email'],
                ':fecha_nacimiento' => $data['fecha_nacimiento'] ?: null,
            ]);

            $pacienteId = (int)$conn->lastInsertId();
            $passwordTemporal = $this->accountService->crearCuentaVinculada($data, 'paciente', $pacienteId, $conn);
            
            // AUDITORÍA: Registro de creación
            $this->registrarLog('INSERT', "Nuevo paciente creado: {$data['nombre']} {$data['apellido']} (ID: $pacienteId)");

            $conn->commit();

            $this->redirectWithFlash('success', "Paciente registrado. Usuario: {$data['email']} | Contraseña temporal: {$passwordTemporal} | Debe cambiarla al iniciar sesión.", 'index.php?controller=paciente&action=index');
        } catch (Throwable $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            $this->redirectWithFlash('error', 'No se pudo registrar el paciente. Verifica que el correo y la cédula no estén duplicados.', 'index.php?controller=paciente&action=crear');
        }
    }

    public function editar(){
        require_roles(['admin', 'recepcion']);
        $id = (int)($_GET['id'] ?? 0);
        $paciente = $this->paciente->obtenerPorId($id);
        if (!$paciente) {
            $this->redirectWithFlash('error', 'Paciente no encontrado.', 'index.php?controller=paciente&action=index');
        }
        $this->render('pacientes/editar', compact('paciente'));
    }

    public function actualizar(){
        require_roles(['admin', 'recepcion']);
        $this->ensurePost('index.php?controller=paciente&action=index');

        $id = (int)($_POST['id'] ?? 0);
        $pacienteActual = $this->paciente->obtenerPorId($id);
        if (!$pacienteActual) {
            $this->redirectWithFlash('error', 'Paciente no encontrado.', 'index.php?controller=paciente&action=index');
        }
        
        $data = $this->payloadFromRequest();
        $error = $this->validate($data, $id);
        if ($error) {
            $this->redirectWithFlash('error', $error, "index.php?controller=paciente&action=editar&id={$id}");
        }
      
        $usuario = $this->accountService->obtenerUsuarioVinculado('paciente', $id);
          
        if ($usuario && !$this->accountService->emailDisponible($data['email'], (int)$usuario['id'])) {
            $this->redirectWithFlash('error', 'Ese correo ya está siendo usado por otro usuario.', "index.php?controller=paciente&action=editar&id={$id}");
        }
        
        if (!$this->paciente->actualizar($id, $data)) {
            $this->redirectWithFlash('error', 'No se pudo actualizar los datos personales.', "index.php?controller=paciente&action=editar&id={$id}");
        }
    
        if ($usuario) {
            $this->accountService->actualizarCuentaVinculada('paciente', $id, $this->fullName($data), $data['email']);
            
            // AUDITORÍA: Actualización
            $this->registrarLog('UPDATE', "Se actualizaron datos del paciente: {$data['nombre']} {$data['apellido']} (ID: $id)");

            if (!empty($_POST['reset_password'])) {
                $newPass = $this->accountService->reiniciarPasswordTemporal((int)$usuario['id'], 'paciente');
                $this->registrarLog('UPDATE', "Password reseteado para paciente ID: $id");
                $this->redirectWithFlash('success', "Datos actualizados. Nueva clave temporal generada: {$newPass}", 'index.php?controller=paciente&action=index');
            }
        } else {
            $newPass = $this->accountService->crearCuentaVinculada($data, 'paciente', $id);
            $this->registrarLog('INSERT', "Se creó cuenta de acceso para paciente ID: $id");
            $this->redirectWithFlash('success', "Paciente actualizado y cuenta de acceso CREADA. Clave temporal: <b>{$newPass}</b>", 'index.php?controller=paciente&action=index');
        }

        $this->redirectWithFlash('success', 'Paciente actualizado correctamente.', 'index.php?controller=paciente&action=index');
    }

    public function eliminar(){
        require_roles(['admin', 'recepcion']);
        $id = (int)($_GET['id'] ?? 0);
        if ($id) {
            $p = $this->paciente->obtenerPorId($id); // Obtenemos datos antes de desactivar para el log
            
            $this->accountService->eliminarCuentaVinculada('paciente', $id);
            $this->paciente->desactivar($id);

            // AUDITORÍA: Eliminación (Desactivación)
            $nombreComp = $p ? $p['nombre'].' '.$p['apellido'] : "ID: $id";
            $this->registrarLog('DELETE', "Se deshabilitó al paciente: $nombreComp");

            flash('success', 'Paciente Deshabilitado correctamente, Su historial de citas se ha preservado.');
        }
        $this->redirect('index.php?controller=paciente&action=index');
    }

    public function perfil(){
        require_roles(['paciente']);
        $id = (int)current_paciente_id();
        $paciente = $this->paciente->obtenerPorId($id);

        if (!$paciente) {
            $this->redirectWithFlash('error', 'No se encontró el perfil del paciente.', 'index.php?controller=dashboard&action=index');
        }

        $citaModel = new Cita();
        $historialModel = new HistorialClinico();
        $proximasCitas = $citaModel->proximasPorPaciente($id, 3);
        $historial = $historialModel->obtenerPorPaciente($id);

        $this->render('pacientes/perfil', compact('paciente', 'proximasCitas', 'historial'));
    }
}