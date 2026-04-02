<?php require_once __DIR__ . '/layout/header.php'; ?>

<?php if (current_user_role() === 'paciente'): ?>
    <h2 class="section-title">Mi panel</h2>
    <div class="cards">
        <div class="card stat-card"><div class="stat-label">Mis citas</div><p class="stat-value" data-count="<?= (int)$totalCitas ?>">0</p></div>
        <div class="card stat-card"><div class="stat-label">Citas hoy</div><p class="stat-value" data-count="<?= (int)$citasHoy ?>">0</p></div>
        <div class="card stat-card"><div class="stat-label">Pendientes</div><p class="stat-value" data-count="<?= (int)$citasPendientes ?>">0</p></div>
        <div class="card stat-card"><div class="stat-label">Registros clínicos</div><p class="stat-value" data-count="<?= (int)$totalHistoriales ?>">0</p></div>
    </div>

    <div class="report-grid">
        <div class="table-card">
            <div class="table-header">
                <h2>Mis citas recientes</h2>
                <a href="index.php?controller=cita&action=index" class="btn btn-primary"><i class="fa-solid fa-eye"></i> Ver todas</a>
            </div>
            <?php if (!empty($ultimasCitas)): ?>
            <div class="table-responsive">
                <table>
                    <thead><tr><th>Médico</th><th>Fecha</th><th>Hora</th><th>Motivo</th><th>Estado</th></tr></thead>
                    <tbody>
                        <?php foreach ($ultimasCitas as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['medico']) ?></td>
                            <td><?= htmlspecialchars($c['fecha']) ?></td>
                            <td><?= htmlspecialchars($c['hora_inicio']) ?></td>
                            <td><?= htmlspecialchars($c['motivo']) ?></td>
                            <td><span class="badge badge-<?= htmlspecialchars($c['estado']) ?>"><?= htmlspecialchars(ucfirst($c['estado'])) ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?><div class="empty-box"><p>No tienes citas registradas.</p></div><?php endif; ?>
        </div>

        <div class="table-card">
            <div class="table-header"><h2>Mi información</h2></div>
            <?php if (!empty($paciente)): ?>
                <div class="mini-list">
                    <div><strong>Nombre:</strong><br><?= htmlspecialchars($paciente['nombre'] . ' ' . $paciente['apellido']) ?></div>
                    <div><strong>Cédula:</strong><br><?= htmlspecialchars($paciente['cedula']) ?></div>
                    <div><strong>Teléfono:</strong><br><?= htmlspecialchars($paciente['telefono'] ?: 'No registrado') ?></div>
                    <div><strong>Correo:</strong><br><?= htmlspecialchars($paciente['email']) ?></div>
                    <div><strong>Fecha de nacimiento:</strong><br><?= htmlspecialchars($paciente['fecha_nacimiento'] ?: 'No registrada') ?></div>
                </div>
                <div class="form-actions" style="margin-top:20px;">
                    <a href="index.php?controller=historial&action=ver&paciente_id=<?= (int)$paciente['id'] ?>" class="btn btn-secondary"><i class="fa-solid fa-notes-medical"></i> Mi historial</a>
                    <a href="index.php?controller=paciente&action=perfil" class="btn btn-primary"><i class="fa-solid fa-user"></i> Ver perfil</a>
                </div>
            <?php else: ?>
                <div class="empty-box"><p>No se encontró tu información de paciente.</p></div>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <h2 class="section-title">Resumen general</h2>
    <div class="cards">
        <div class="card stat-card"><div class="stat-label">Total de pacientes</div><p class="stat-value" data-count="<?= (int)$totalPacientes ?>">0</p></div>
        <div class="card stat-card"><div class="stat-label">Total de médicos</div><p class="stat-value" data-count="<?= (int)$totalMedicos ?>">0</p></div>
        <div class="card stat-card"><div class="stat-label">Total de citas</div><p class="stat-value" data-count="<?= (int)$totalCitas ?>">0</p></div>
        <div class="card stat-card"><div class="stat-label">Citas de hoy</div><p class="stat-value" data-count="<?= (int)$citasHoy ?>">0</p></div>
    </div>
    <div class="table-card">
        <div class="table-header">
            <h2><?= current_user_role() === 'medico' ? 'Mis próximas citas' : 'Últimas citas registradas' ?></h2>
            <a href="index.php?controller=cita&action=index" class="btn btn-primary"><i class="fa-solid fa-eye"></i> Ver citas</a>
        </div>
        <?php if (!empty($ultimasCitas)): ?>
        <div class="table-responsive">
            <table>
                <thead><tr><th>Paciente</th><th>Médico</th><th>Fecha</th><th>Hora</th><th>Estado</th></tr></thead>
                <tbody>
                    <?php foreach ($ultimasCitas as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['paciente']) ?></td>
                        <td><?= htmlspecialchars($c['medico']) ?></td>
                        <td><?= htmlspecialchars($c['fecha']) ?></td>
                        <td><?= htmlspecialchars($c['hora_inicio']) ?></td>
                        <td><span class="badge badge-<?= htmlspecialchars($c['estado']) ?>"><?= htmlspecialchars(ucfirst($c['estado'])) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?><div class="empty-box"><p>No hay citas recientes.</p></div><?php endif; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
