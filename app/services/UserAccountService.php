<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Usuario.php';

class UserAccountService
{
    private Usuario $usuario;

    public function __construct()
    {
        $this->usuario = new Usuario();
    }

    public function emailDisponible(string $email, ?int $excludeUserId = null): bool
    {
        return !$this->usuario->emailEnUsoPorOtro($email, $excludeUserId);
    }

    public function generarPasswordTemporal(string $prefix): string
    {
        return $prefix . random_int(1000, 9999) . '!' . random_int(10, 99);
    }

    public function crearCuentaVinculada(array $persona, string $rol, int $entityId, ?PDO $connection = null): string
    {
        $passwordTemporal = $this->generarPasswordTemporal($rol === 'medico' ? 'Med' : 'Pac');
        $conn = $connection ?: (new Database())->conectar();

        $stmt = $conn->prepare(
            "INSERT INTO usuarios (nombre, email, password, rol, medico_id, paciente_id, estado, debe_cambiar_password)
             VALUES (:nombre, :email, :password, :rol, :medico_id, :paciente_id, 'activo', 1)"
        );

        $stmt->execute([
            ':nombre' => trim(($persona['nombre'] ?? '') . ' ' . ($persona['apellido'] ?? '')),
            ':email' => $persona['email'],
            ':password' => password_hash($passwordTemporal, PASSWORD_DEFAULT),
            ':rol' => $rol,
            ':medico_id' => $rol === 'medico' ? $entityId : null,
            ':paciente_id' => $rol === 'paciente' ? $entityId : null,
        ]);

        return $passwordTemporal;
    }

    public function actualizarCuentaVinculada(string $rol, int $entityId, string $nombreCompleto, string $email): void
    {
        if ($rol === 'medico') {
            $this->usuario->actualizarDatosPorMedicoId($entityId, $nombreCompleto, $email);
            return;
        }

        $this->usuario->actualizarDatosPorPacienteId($entityId, $nombreCompleto, $email);
    }

    
    public function obtenerUsuarioVinculado(string $rol, int $entityId): ?array
    {
        $resultado = $rol === 'medico'
            ? $this->usuario->obtenerPorMedicoId($entityId)
            : $this->usuario->obtenerPorPacienteId($entityId);

        
        return $resultado ?: null; 
    }

    public function reiniciarPasswordTemporal(int $userId, string $rol): string
    {
        $newPassword = $this->generarPasswordTemporal($rol === 'medico' ? 'Med' : 'Pac');
        $this->usuario->actualizarPassword($userId, password_hash($newPassword, PASSWORD_DEFAULT), true);
        return $newPassword;
    }

    // --- REFACTORIZADO: AHORA DESHABILITA EN LUGAR DE BORRAR ---
    public function eliminarCuentaVinculada(string $rol, int $entityId): void
    {
        // En lugar de llamar a eliminar, llamamos a cambiarEstado
        if ($rol === 'medico') {
            $this->usuario->cambiarEstadoPorMedico($entityId, 'inactivo');
            return;
        }

        $this->usuario->cambiarEstadoPorPaciente($entityId, 'inactivo');
    }
}