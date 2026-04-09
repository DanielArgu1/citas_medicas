<?php require_once __DIR__ . '/../layout/header.php'; ?>



<h2 class="section-title">Auditoría del Sistema</h2>

<!-- TARJETAS ESTADÍSTICAS -->
<div class="cards report-cards">
    <div class="card stat-card">
        <div class="stat-label">Total de Registros</div>
        <p class="stat-value" data-count="<?= (int)($totalAuditorias ?? 0) ?>">0</p>
    </div>

    <div class="card stat-card">
        <div class="stat-label">Actividad Hoy</div>
        <p class="stat-value" data-count="<?= (int)($auditoriasHoy ?? 0) ?>">0</p>
    </div>

    <div class="card stat-card">
        <div class="stat-label">Usuario más activo</div>
        <p class="stat-value stat-text">
            <?= htmlspecialchars($usuarioMasActivo['usuario'] ?? 'Sin datos') ?>
        </p>
    </div>

    <div class="card stat-card">
        <div class="stat-label">Módulo más modificado</div>
        <p class="stat-value stat-text">
            <?= htmlspecialchars($moduloMasModificado['modulo'] ?? 'Sin datos') ?>
        </p>
    </div>
</div>

<!-- TABLA DE AUDITORÍA -->
<div class="table-card">
    <div class="table-header">
        <h2>Registro de Actividades</h2>
        <button type="button" id="admin-print-log">
            <i class="fa-solid fa-print"></i> Imprimir Log
        </button>
    </div>

    <!-- FILTROS -->
    <form method="GET" class="filters filters-grid">
        <input type="hidden" name="controller" value="auditoria">
        <input type="hidden" name="action" value="index">

        <select name="usuario_id">
            <option value="">Todos los usuarios</option>
            <?php foreach ($usuarios as $u): ?>
                <option value="<?= $u['id'] ?>" <?= (isset($filters['usuario_id']) && $filters['usuario_id'] == $u['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($u['nombre_usuario'] ?? $u['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="accion">
            <option value="">Todas las acciones</option>
            <option value="crear" <?= (isset($filters['accion']) && $filters['accion'] === 'crear') ? 'selected' : '' ?>>Crear / Insertar</option>
            <option value="editar" <?= (isset($filters['accion']) && $filters['accion'] === 'editar') ? 'selected' : '' ?>>Editar / Actualizar</option>
            <option value="eliminar" <?= (isset($filters['accion']) && $filters['accion'] === 'eliminar') ? 'selected' : '' ?>>Eliminar</option>
            <option value="login" <?= (isset($filters['accion']) && $filters['accion'] === 'login') ? 'selected' : '' ?>>Inicios de sesión</option>
        </select>

        <input type="date" name="fecha_desde" value="<?= htmlspecialchars($filters['fecha_desde'] ?? '') ?>" title="Fecha desde">
        <input type="date" name="fecha_hasta" value="<?= htmlspecialchars($filters['fecha_hasta'] ?? '') ?>" title="Fecha hasta">

        <div class="filters-actions">
            <button class="btn btn-primary">
                <i class="fa-solid fa-filter"></i> Filtrar
            </button>
            <a class="btn btn-secondary" href="index.php?controller=auditoria&action=index">Limpiar</a>
        </div>
    </form>

    <!-- TABLA -->
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Fecha y Hora</th>
                    <th>Usuario</th>
                    <th>Acción</th>
                    <th>Módulo / Tabla</th>
                    <th>Detalles</th>
                    <th>Dirección IP</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($logsAuditoria)): ?>
                    <?php foreach ($logsAuditoria as $log): ?>
                        <tr>
                            <td style="white-space: nowrap;"><?= htmlspecialchars($log['fecha_hora']) ?></td>
                            <td>
                                <strong><?= htmlspecialchars($log['usuario']) ?></strong><br>
                                <span style="font-size: 12px; color: #64748b;"><?= htmlspecialchars($log['rol'] ?? '') ?></span>
                            </td>
                            <td>
                                <span class="badge <?= strtolower(htmlspecialchars($log['accion'])) ?>">
                                    <?= htmlspecialchars(ucfirst($log['accion'])) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($log['modulo']) ?></td>
                            <td>
                                <?php if(!empty($log['detalles'])): ?>
                                    <span class="detalles-audit"><?= htmlspecialchars($log['detalles']) ?></span>
                                <?php else: ?>
                                    <span style="color:#94a3b8; font-style:italic;">Sin detalles</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($log['ip'] ?? 'Desconocida') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="empty-cell">
                            <i class="fa-solid fa-shield-halved" style="font-size: 28px; color: #cbd5e1; margin-bottom: 10px; display: block;"></i>
                            No hay registros de auditoría que coincidan con la búsqueda.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ANIMACIÓN DE NÚMEROS -->


<?php require_once __DIR__ . '/../layout/footer.php'; ?>