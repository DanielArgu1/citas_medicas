<?php require_once __DIR__ . '/layout/header.php'; ?>

<h2 class="section-title">Resumen general</h2>

<div class="cards">
    <div class="card stat-card">
        <div class="stat-label">Total de pacientes</div>
        <p class="stat-value" data-count="<?= (int)$totalPacientes; ?>">0</p>
    </div>

    <div class="card stat-card">
        <div class="stat-label">Total de médicos</div>
        <p class="stat-value" data-count="<?= (int)$totalMedicos; ?>">0</p>
    </div>

    <div class="card stat-card">
        <div class="stat-label">Total de citas</div>
        <p class="stat-value" data-count="<?= (int)$totalCitas; ?>">0</p>
    </div>

    <div class="card stat-card">
        <div class="stat-label">Citas de hoy</div>
        <p class="stat-value" data-count="<?= (int)$citasHoy; ?>">0</p>
    </div>
</div>

<div class="table-card">
    <div class="table-header">
        <h2>Últimas citas registradas</h2>
        <a href="index.php?controller=cita&action=index" class="btn btn-primary">
            <i class="fa-solid fa-eye"></i> Ver todas
        </a>
    </div>

    <?php if (!empty($ultimasCitas)): ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Médico</th>
                        <th>Fecha</th>
                        <th>Hora inicio</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ultimasCitas as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['paciente']); ?></td>
                            <td><?= htmlspecialchars($c['medico']); ?></td>
                            <td><?= htmlspecialchars($c['fecha']); ?></td>
                            <td><?= htmlspecialchars($c['hora_inicio']); ?></td>
                            <td>
                                <?php if ($c['estado'] === 'pendiente'): ?>
                                    <span class="badge badge-pendiente">Pendiente</span>
                                <?php elseif ($c['estado'] === 'completada'): ?>
                                    <span class="badge badge-completada">Completada</span>
                                <?php else: ?>
                                    <span class="badge badge-cancelada">Cancelada</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty-box">
            <i class="fa-solid fa-calendar-xmark" style="font-size:32px; margin-bottom:10px;"></i>
            <p>No hay citas registradas todavía.</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>