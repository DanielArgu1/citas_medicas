<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/auth.php';

class Controller
{
    protected function logActivity(string $accion, string $tabla, string $detalles): void
    {
        $usuarioId = current_user_id() ?? ($_SESSION['usuario_id'] ?? null);
        if (!$usuarioId) {
            return;
        }

        try {
            $db = (new Database())->conectar();
            $stmt = $db->prepare("INSERT INTO auditoria (usuario_id, accion, tabla_afectada, detalles) VALUES (?, ?, ?, ?)");
            $stmt->execute([$usuarioId, strtoupper(trim($accion)), trim($tabla), trim($detalles)]);
        } catch (Throwable $e) {
            error_log('Error de auditoría: ' . $e->getMessage());
        }
    }

    protected function render(string $viewPath, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require __DIR__ . '/../views/' . $viewPath . '.php';
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    protected function redirectWithFlash(string $type, string $message, string $url): void
    {
        flash($type, $message);
        $this->redirect($url);
    }

    protected function ensurePost(string $url): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirect($url);
        }
    }
}
