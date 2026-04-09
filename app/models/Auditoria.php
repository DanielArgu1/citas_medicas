<?php
require_once __DIR__ . '/../config/database.php';

class Auditoria {
    private $db;

    public function __construct() {
        $this->db = (new Database())->conectar();
    }

    private function normalizarAccion(?string $accion): ?array
    {
        $accion = strtolower(trim((string)$accion));
        if ($accion === '') {
            return null;
        }

        return match ($accion) {
            'crear', 'insert', 'insertar' => ['INSERT'],
            'editar', 'update', 'actualizar' => ['UPDATE'],
            'eliminar', 'delete', 'borrar' => ['DELETE'],
            'login', 'acceso', 'ingreso' => ['LOGIN'],
            'logout', 'salida' => ['LOGOUT'],
            default => [strtoupper($accion)],
        };
    }

    public function obtenerTodos($filters = []) {
        $sql = "SELECT a.id,
                       a.fecha AS fecha_hora,
                       COALESCE(u.nombre, 'Usuario eliminado') AS usuario,
                       COALESCE(u.rol, 'N/D') AS rol,
                       a.accion,
                       a.tabla_afectada AS modulo,
                       a.detalles,
                       '" . ($_SERVER['REMOTE_ADDR'] ?? 'Desconocida') . "' AS ip
                FROM auditoria a
                LEFT JOIN usuarios u ON a.usuario_id = u.id
                WHERE 1=1";

        $params = [];

        if (!empty($filters['usuario_id'])) {
            $sql .= " AND a.usuario_id = :usuario_id";
            $params[':usuario_id'] = (int)$filters['usuario_id'];
        }

        $acciones = $this->normalizarAccion($filters['accion'] ?? null);
        if (!empty($acciones)) {
            $placeholders = [];
            foreach ($acciones as $i => $accion) {
                $ph = ':accion_' . $i;
                $placeholders[] = $ph;
                $params[$ph] = $accion;
            }
            $sql .= " AND UPPER(a.accion) IN (" . implode(', ', $placeholders) . ")";
        }

        if (!empty($filters['fecha_desde'])) {
            $sql .= " AND DATE(a.fecha) >= :fecha_desde";
            $params[':fecha_desde'] = $filters['fecha_desde'];
        }

        if (!empty($filters['fecha_hasta'])) {
            $sql .= " AND DATE(a.fecha) <= :fecha_hasta";
            $params[':fecha_hasta'] = $filters['fecha_hasta'];
        }

        $sql .= " ORDER BY a.fecha DESC, a.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerEstadisticas() {
        $stats = [];

        $stmt = $this->db->query("SELECT COUNT(*) FROM auditoria");
        $stats['total'] = (int)$stmt->fetchColumn();

        $stmt = $this->db->query("SELECT COUNT(*) FROM auditoria WHERE DATE(fecha) = CURDATE()");
        $stats['hoy'] = (int)$stmt->fetchColumn();

        $stmt = $this->db->query("SELECT COALESCE(u.nombre, 'Usuario eliminado') AS usuario
                                  FROM auditoria a
                                  LEFT JOIN usuarios u ON a.usuario_id = u.id
                                  GROUP BY a.usuario_id, u.nombre
                                  ORDER BY COUNT(*) DESC
                                  LIMIT 1");
        $stats['top_usuario'] = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['usuario' => 'Sin datos'];

        $stmt = $this->db->query("SELECT tabla_afectada AS modulo
                                  FROM auditoria
                                  GROUP BY tabla_afectada
                                  ORDER BY COUNT(*) DESC
                                  LIMIT 1");
        $stats['top_modulo'] = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['modulo' => 'Sin datos'];

        return $stats;
    }
}
