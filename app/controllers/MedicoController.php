<?php

require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Medico.php';
require_once __DIR__ . '/../services/UserAccountService.php';
require_once __DIR__ . '/../config/database.php';

class MedicoController extends Controller {
    private Medico $medico;
    private UserAccountService $accountService;

    public function __construct(){
        $this->medico = new Medico();
        $this->accountService = new UserAccountService();
    }

    /**
     * Método Privado para Auditoría
     */
    private function registrarLog($accion, $detalles) {
        try {
            $conn = (new Database())->conectar();
            $stmt = $conn->prepare("INSERT INTO auditoria (usuario_id, accion, tabla_afectada, detalles) VALUES (:u, :a, :t, :d)");
            $stmt->execute([
                ':u' => current_user_id() ?? ($_SESSION['usuario_id'] ?? 1), 
                ':a' => $accion,
                ':t' => 'medicos',
                ':d' => $detalles
            ]);
        } catch (Throwable $e) {
            // Error silencioso para no interrumpir el flujo principal
        }
    }

    private function limpiarTexto($valor)
    {
        $valor = trim($valor ?? '');
        return preg_replace('/\s+/', ' ', $valor);
    }

    private function soloDigitos($valor)
    {
        return preg_replace('/\D/', '', $valor ?? '');
    }

    private function formatearTelefono($valor)
    {
        $valor = substr($this->soloDigitos($valor), 0, 8);
        return strlen($valor) > 4 ? substr($valor, 0, 4) . '-' . substr($valor, 4) : $valor;
    }

    private function validarSoloTexto($texto)
    {
        return preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/', $texto);
    }

    private function validarTelefono($telefono)
    {
        return preg_match('/^\d{4}-\d{4}$/', $telefono);
    }

    private function baseData(): array
    {
        return [
            'nombre' => $this->limpiarTexto($_POST['nombre'] ?? ''),
            'apellido' => $this->limpiarTexto($_POST['apellido'] ?? ''),
            'especialidad' => $this->limpiarTexto($_POST['especialidad'] ?? ''),
            'telefono' => $this->formatearTelefono($_POST['telefono'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
        ];
    }

    private function validate(array $data): ?string
    {
        if (in_array('', [$data['nombre'], $data['apellido'], $data['especialidad'], $data['telefono'], $data['email']], true)) {
            return 'Todos los campos son obligatorios.';
        }

        if (!$this->validarSoloTexto($data['nombre'])) return 'El nombre solo debe contener letras y espacios.';
        if (!$this->validarSoloTexto($data['apellido'])) return 'El apellido solo debe contener letras y espacios.';
        if (!$this->validarSoloTexto($data['especialidad'])) return 'La especialidad solo debe contener letras y espacios.';
        if (mb_strlen($data['nombre']) < 2) return 'El nombre es demasiado corto.';
        if (mb_strlen($data['apellido']) < 2) return 'El apellido es demasiado corto.';
        if (mb_strlen($data['especialidad']) < 2) return 'La especialidad es demasiado corta.';
        if (!$this->validarTelefono($data['telefono'])) return 'El teléfono debe tener formato 0000-0000.';
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) return 'El correo electrónico no es válido.';

        return null;
    }

    private function fullName(array $data): string
    {
        return trim($data['nombre'] . ' ' . $data['apellido']);
    }

    public function index(){
        require_roles(['admin']);
        $medicos = $this->medico->obtenerTodos();
        $this->render('medicos/index', compact('medicos'));
    }

    public function crear(){
        require_roles(['admin']);
        $this->render('medicos/crear');
    }

    public function guardar(){
        require_roles(['admin']);
        $this->ensurePost('index.php?controller=medico&action=index');

        $data = $this->baseData();
        $error = $this->validate($data);
        if ($error) {
            $this->redirectWithFlash('error', $error, 'index.php?controller=medico&action=crear');
        }

        if (!$this->accountService->emailDisponible($data['email'])) {
            $this->redirectWithFlash('error', 'Ese correo ya está siendo usado por otro usuario del sistema.', 'index.php?controller=medico&action=crear');
        }

        $conn = (new Database())->conectar();

        try {
            $conn->beginTransaction();
            $stmt = $conn->prepare("INSERT INTO medicos (nombre, apellido, especialidad, telefono, email)
                                    VALUES (:nombre, :apellido, :especialidad, :telefono, :email)");
            $stmt->execute([
                ':nombre' => $data['nombre'],
                ':apellido' => $data['apellido'],
                ':especialidad' => $data['especialidad'],
                ':telefono' => $data['telefono'],
                ':email' => $data['email'],
            ]);

            $medicoId = (int)$conn->lastInsertId();
            $passwordTemporal = $this->accountService->crearCuentaVinculada($data, 'medico', $medicoId, $conn);
            
            // AUDITORÍA: Creación de médico
            $this->registrarLog('INSERT', "Se registró al médico: {$data['nombre']} {$data['apellido']} (Especialidad: {$data['especialidad']})");

            $conn->commit();

            $this->redirectWithFlash('success', "Médico registrado. Usuario: {$data['email']} | Contraseña temporal: {$passwordTemporal} | Debe cambiarla al iniciar sesión.", 'index.php?controller=medico&action=index');
        } catch (Throwable $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            $this->redirectWithFlash('error', 'No se pudo registrar el médico. Verifica que el correo no esté duplicado.', 'index.php?controller=medico&action=crear');
        }
    }

    public function editar(){
        require_roles(['admin']);

        $id = (int)($_GET['id'] ?? 0);
        $medico = $this->medico->obtenerPorId($id);
        if (!$medico) {
            $this->redirectWithFlash('error', 'Médico no encontrado.', 'index.php?controller=medico&action=index');
        }

        $this->render('medicos/editar', compact('medico'));
    }

    public function actualizar(){
        require_roles(['admin']);
        $this->ensurePost('index.php?controller=medico&action=index');

        $id = (int)($_POST['id'] ?? 0);
        $data = $this->baseData();
        $error = $this->validate($data);
        if ($error) {
            $this->redirectWithFlash('error', $error, "index.php?controller=medico&action=editar&id={$id}");
        }

        $usuario = $this->accountService->obtenerUsuarioVinculado('medico', $id);
        if ($usuario && !$this->accountService->emailDisponible($data['email'], (int)$usuario['id'])) {
            $this->redirectWithFlash('error', 'Ese correo ya está siendo usado por otro usuario del sistema.', "index.php?controller=medico&action=editar&id={$id}");
        }

        if (!$this->medico->actualizar($id, $data)) {
            $this->redirectWithFlash('error', 'No se pudo actualizar el médico.', "index.php?controller=medico&action=editar&id={$id}");
        }

        if ($usuario) {
            $this->accountService->actualizarCuentaVinculada('medico', $id, $this->fullName($data), $data['email']);
            
            // AUDITORÍA: Actualización de datos
            $this->registrarLog('UPDATE', "Se actualizaron datos del médico ID: $id ({$data['nombre']} {$data['apellido']})");

            if (!empty($_POST['reset_password'])) {
                $newPass = $this->accountService->reiniciarPasswordTemporal((int)$usuario['id'], 'medico');
                
                // AUDITORÍA: Reset de password
                $this->registrarLog('UPDATE', "Se reinició contraseña para el médico ID: $id");

                $this->redirectWithFlash('success', "Médico actualizado. Nueva contraseña temporal: {$newPass}. Deberá cambiarla al iniciar sesión.", 'index.php?controller=medico&action=index');
            }
        }

        $this->redirectWithFlash('success', 'Médico actualizado correctamente.', 'index.php?controller=medico&action=index');
    }

    public function eliminar() {
        require_roles(['admin']); 
        $id = (int)($_GET['id'] ?? 0);

        if ($id) {
            $m = $this->medico->obtenerPorId($id); // Obtener datos para el log antes de borrar

            if ($this->medico->desactivar($id)) {
                $this->accountService->eliminarCuentaVinculada('medico', $id);

                // AUDITORÍA: Eliminación (Desactivación)
                $nombreM = $m ? "{$m['nombre']} {$m['apellido']}" : "ID: $id";
                $this->registrarLog('DELETE', "Se deshabilitó al médico: $nombreM");

                flash('success', 'Médico deshabilitado correctamente. Su historial de citas se ha preservado.');
            } else {
                flash('error', 'No se pudo deshabilitar al médico.');
            }
        }

        header("Location: index.php?controller=medico&action=index");
        exit;
    }
}