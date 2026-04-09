<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Auditoria.php';
require_once __DIR__ . '/../config/database.php';

class AuditoriaController extends Controller {
    private $auditoriaModel;

    public function __construct() {
        $this->auditoriaModel = new Auditoria();
    }

    public function index() {
        require_roles(['admin']);

        $filters = [
            'usuario_id' => trim($_GET['usuario_id'] ?? ''),
            'accion' => trim($_GET['accion'] ?? ''),
            'fecha_desde' => trim($_GET['fecha_desde'] ?? ''),
            'fecha_hasta' => trim($_GET['fecha_hasta'] ?? ''),
        ];

        $logsAuditoria = $this->auditoriaModel->obtenerTodos($filters);
        $stats = $this->auditoriaModel->obtenerEstadisticas();

        $db = (new Database())->conectar();
        $usuarios = $db->query("SELECT id, nombre, rol FROM usuarios ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);

        $this->logActivity('VIEW', 'auditoria', 'Consultó el módulo de auditoría' . ($this->hayFiltros($filters) ? ' con filtros aplicados' : ''));

        $this->render('admin/index', [
            'logsAuditoria' => $logsAuditoria,
            'totalAuditorias' => $stats['total'],
            'auditoriasHoy' => $stats['hoy'],
            'usuarioMasActivo' => $stats['top_usuario'],
            'moduloMasModificado' => $stats['top_modulo'],
            'usuarios' => $usuarios,
            'filters' => $filters,
        ]);
    }

    private function hayFiltros(array $filters): bool
    {
        foreach ($filters as $value) {
            if ($value !== '') {
                return true;
            }
        }
        return false;
    }
}
