<?php require_once __DIR__ . '/../layout/header.php'; ?>

<style>
    .report-cards{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:16px;
    margin-bottom:20px;
}

.report-grid{
    display:grid;
    grid-template-columns:2fr 1fr;
    gap:20px;
    align-items:start;
}

.report-left{
    display:flex;
    flex-direction:column;
    gap:20px;
}

.table-card{
    background:#fff;
    border-radius:20px;
    padding:18px;
    box-shadow:0 10px 30px rgba(15,23,42,.06);
    border:1px solid #e5e7eb;
}

.table-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:12px;
    margin-bottom:16px;
}

.table-header h2{
    margin:0;
    font-size:20px;
    font-weight:800;
    color:#0f172a;
}

.filters-grid{
    display:grid;
    grid-template-columns:repeat(4,minmax(0,1fr));
    gap:12px;
    margin-bottom:16px;
}

.filters-grid-historial{
    grid-template-columns:repeat(5,minmax(0,1fr));
}

.filters select,
.filters input{
    width:100%;
    padding:12px 14px;
    border:1px solid #dbe2ea;
    border-radius:12px;
    outline:none;
    background:#fff;
    color:#0f172a;
}

.filters select:focus,
.filters input:focus{
    border-color:#2563eb;
    box-shadow:0 0 0 3px rgba(37,99,235,.10);
}

.filters-actions{
    grid-column:1/-1;
    display:flex;
    gap:10px;
    flex-wrap:wrap;
}

.table-responsive{
    width:100%;
    overflow:auto;
}

.table-card table{
    width:100%;
    border-collapse:collapse;
}

.table-card th,
.table-card td{
    text-align:left;
    padding:12px 10px;
    border-bottom:1px solid #e5e7eb;
    vertical-align:top;
}

.table-card th{
    font-size:13px;
    color:#64748b;
    font-weight:700;
}

.table-card td{
    color:#0f172a;
    font-size:14px;
}

.badge{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:6px 12px;
    border-radius:999px;
    font-size:12px;
    font-weight:700;
}

.badge.pendiente{
    background:#fef3c7;
    color:#92400e;
}

.badge.completada{
    background:#dcfce7;
    color:#166534;
}

.badge.cancelada{
    background:#fee2e2;
    color:#991b1b;
}

.stat-text{
    font-size:16px !important;
    line-height:1.4;
    font-weight:700;
}

.empty-cell{
    text-align:center !important;
    color:#64748b !important;
    padding:20px !important;
}

.mini-list{
    display:flex;
    flex-direction:column;
    gap:20px;
}

@media (max-width: 1200px){
    .report-grid{
        grid-template-columns:1fr;
    }
}

@media (max-width: 768px){
    .filters-grid,
    .filters-grid-historial{
        grid-template-columns:1fr;
    }

    .table-header{
        flex-direction:column;
        align-items:flex-start;
    }
}

@media print {

    /* ocultar sidebar y navegación */
    .sidebar,
    .menu,
    nav,
    .btn,
    .filters,
    .filters-grid,
    .filters-grid-historial,
    .table-header button {
        display: none !important;
    }

    /* quitar márgenes del layout */
    body {
        margin: 0;
        padding: 0;
        background: white;
    }

    /* hacer que el contenido use toda la página */
    .main-content,
    .content,
    .report-grid {
        margin: 0;
        padding: 0;
        width: 100%;
    }

    /* evitar sombras que se ven mal al imprimir */
    .card,
    .table-card {
        box-shadow: none !important;
        border: 1px solid #ddd;
    }

}
</style>

<h2 class="section-title">Reportes</h2>

<div class="cards report-cards">
    <div class="card stat-card">
        <div class="stat-label">Pacientes registrados</div>
        <p class="stat-value" data-count="<?= (int)$totalPacientes ?>">0</p>
    </div>

    <div class="card stat-card">
        <div class="stat-label">Médicos registrados</div>
        <p class="stat-value" data-count="<?= (int)$totalMedicos ?>">0</p>
    </div>

    <div class="card stat-card">
        <div class="stat-label">Total de citas</div>
        <p class="stat-value" data-count="<?= (int)$totalCitas ?>">0</p>
    </div>

    <div class="card stat-card">
        <div class="stat-label">Citas hoy</div>
        <p class="stat-value" data-count="<?= (int)$citasHoy ?>">0</p>
    </div>

    <div class="card stat-card">
        <div class="stat-label">Historiales clínicos</div>
        <p class="stat-value" data-count="<?= (int)$totalHistoriales ?>">0</p>
    </div>

    <div class="card stat-card">
        <div class="stat-label">Citas completadas</div>
        <p class="stat-value" data-count="<?= (int)$citasCompletadas ?>">0</p>
    </div>

    <div class="card stat-card">
        <div class="stat-label">Citas canceladas</div>
        <p class="stat-value" data-count="<?= (int)$citasCanceladas ?>">0</p>
    </div>

    <div class="card stat-card">
        <div class="stat-label">Diagnóstico más frecuente</div>
        <p class="stat-value stat-text">
            <?= htmlspecialchars($diagnosticoMasFrecuente['diagnostico'] ?? 'Sin datos') ?>
        </p>
    </div>

    <div class="card stat-card">
        <div class="stat-label">Paciente con más consultas</div>
        <p class="stat-value stat-text">
            <?= htmlspecialchars($pacienteMasConsultas['paciente'] ?? 'Sin datos') ?>
        </p>
    </div>

    <div class="card stat-card">
        <div class="stat-label">Médico con más citas</div>
        <p class="stat-value stat-text">
            <?= htmlspecialchars($medicoMasActivo['medico'] ?? 'Sin datos') ?>
        </p>
    </div>
</div>

<div class="report-grid">
    <div class="report-left">
        <div class="table-card">
            <div class="table-header">
                <h2>Reporte de citas</h2>
                <button class="btn btn-secondary" onclick="window.print()">
                    <i class="fa-solid fa-print"></i> Imprimir
                </button>
            </div>

            <form method="GET" class="filters filters-grid">
                <input type="hidden" name="controller" value="reporte">
                <input type="hidden" name="action" value="index">

                <select name="medico_id">
                    <option value="">Todos los médicos</option>
                    <?php foreach ($medicos as $m): ?>
                        <option value="<?= $m['id'] ?>" <?= ($filters['medico_id'] == $m['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($m['nombre'] . ' ' . $m['apellido']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="estado">
                    <option value="">Todos los estados</option>
                    <option value="pendiente" <?= $filters['estado'] === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                    <option value="completada" <?= $filters['estado'] === 'completada' ? 'selected' : '' ?>>Completada</option>
                    <option value="cancelada" <?= $filters['estado'] === 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
                </select>

                <input type="date" name="fecha_desde" value="<?= htmlspecialchars($filters['fecha_desde']) ?>">
                <input type="date" name="fecha_hasta" value="<?= htmlspecialchars($filters['fecha_hasta']) ?>">

                <div class="filters-actions">
                    <button class="btn btn-primary">
                        <i class="fa-solid fa-filter"></i> Filtrar
                    </button>
                    <a class="btn btn-secondary" href="index.php?controller=reporte&action=index">Limpiar</a>
                </div>
            </form>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Paciente</th>
                            <th>Médico</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($citas)): ?>
                            <?php foreach ($citas as $c): ?>
                                <tr>
                                    <td><?= htmlspecialchars($c['paciente']) ?></td>
                                    <td><?= htmlspecialchars($c['medico']) ?></td>
                                    <td><?= htmlspecialchars($c['fecha']) ?></td>
                                    <td><?= htmlspecialchars($c['hora_inicio']) ?> - <?= htmlspecialchars($c['hora_fin']) ?></td>
                                    <td>
                                        <span class="badge <?= htmlspecialchars($c['estado']) ?>">
                                            <?= htmlspecialchars(ucfirst($c['estado'])) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="empty-cell">No hay citas para mostrar.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="table-card" style="margin-top: 20px;">
            <div class="table-header">
                <h2>Reporte clínico</h2>
            </div>

            <form method="GET" class="filters filters-grid filters-grid-historial">
                <input type="hidden" name="controller" value="reporte">
                <input type="hidden" name="action" value="index">

                <select name="hist_medico_id">
                    <option value="">Todos los médicos</option>
                    <?php foreach ($medicos as $m): ?>
                        <option value="<?= $m['id'] ?>" <?= ($filters['hist_medico_id'] == $m['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($m['nombre'] . ' ' . $m['apellido']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="paciente_id">
                    <option value="">Todos los pacientes</option>
                    <?php foreach ($pacientes as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= ($filters['paciente_id'] == $p['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['nombre'] . ' ' . $p['apellido']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <input type="date" name="fecha_desde_hist" value="<?= htmlspecialchars($filters['fecha_desde_hist']) ?>">
                <input type="date" name="fecha_hasta_hist" value="<?= htmlspecialchars($filters['fecha_hasta_hist']) ?>">
                <input type="text" name="diagnostico" placeholder="Buscar diagnóstico" value="<?= htmlspecialchars($filters['diagnostico']) ?>">

                <div class="filters-actions">
                    <button class="btn btn-primary">
                        <i class="fa-solid fa-stethoscope"></i> Filtrar
                    </button>
                    <a class="btn btn-secondary" href="index.php?controller=reporte&action=index">Limpiar</a>
                </div>
            </form>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Paciente</th>
                            <th>Médico</th>
                            <th>Motivo</th>
                            <th>Diagnóstico</th>
                            <th>Tratamiento</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($historiales)): ?>
                            <?php foreach ($historiales as $h): ?>
                                <tr>
                                    <td><?= htmlspecialchars($h['paciente']) ?></td>
                                    <td><?= htmlspecialchars($h['medico']) ?></td>
                                    <td><?= htmlspecialchars($h['motivo_consulta']) ?></td>
                                    <td><?= htmlspecialchars($h['diagnostico']) ?></td>
                                    <td><?= htmlspecialchars($h['tratamiento'] ?? 'Sin tratamiento') ?></td>
                                    <td><?= htmlspecialchars($h['fecha_registro']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="empty-cell">No hay historiales clínicos para mostrar.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mini-list">
        <div class="table-card">
            <div class="table-header">
                <h2>Citas por estado</h2>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Estado</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($porEstado as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars(ucfirst($item['estado'])) ?></td>
                            <td><?= htmlspecialchars($item['total']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="table-card">
            <div class="table-header">
                <h2>Diagnósticos frecuentes</h2>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Diagnóstico</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($diagnosticosFrecuentes)): ?>
                        <?php foreach ($diagnosticosFrecuentes as $d): ?>
                            <tr>
                                <td><?= htmlspecialchars($d['diagnostico']) ?></td>
                                <td><?= htmlspecialchars($d['total']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2" class="empty-cell">Sin datos</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="table-card">
            <div class="table-header">
                <h2>Pacientes con más consultas</h2>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($pacientesFrecuentes)): ?>
                        <?php foreach ($pacientesFrecuentes as $p): ?>
                            <tr>
                                <td><?= htmlspecialchars($p['paciente']) ?></td>
                                <td><?= htmlspecialchars($p['total']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2" class="empty-cell">Sin datos</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="table-card">
            <div class="table-header">
                <h2>Médicos con más atenciones</h2>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Médico</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($medicosFrecuentes)): ?>
                        <?php foreach ($medicosFrecuentes as $m): ?>
                            <tr>
                                <td><?= htmlspecialchars($m['medico']) ?></td>
                                <td><?= htmlspecialchars($m['total']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2" class="empty-cell">Sin datos</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="table-card">
            <div class="table-header">
                <h2>Últimos historiales</h2>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Diagnóstico</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($ultimosHistoriales)): ?>
                        <?php foreach ($ultimosHistoriales as $u): ?>
                            <tr>
                                <td><?= htmlspecialchars($u['paciente']) ?></td>
                                <td><?= htmlspecialchars($u['diagnostico']) ?></td>
                                <td><?= htmlspecialchars($u['fecha_registro']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="empty-cell">Sin datos</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="table-card">
            <div class="table-header">
                <h2>Notificaciones</h2>
            </div>
            <p style="margin:0;color:#475569;line-height:1.6;">
                Cada vez que una cita se crea, actualiza o cancela, el sistema registra una notificación en
                <strong>app/logs/notificaciones.log</strong> y también intenta enviarla por correo con
                <strong>mail()</strong> si tu servidor PHP está configurado.
            </p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>