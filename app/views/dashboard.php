<?php require_once __DIR__ . '/layout/header.php'; ?>
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
<?php require_once __DIR__ . '/layout/footer.php'; ?>
