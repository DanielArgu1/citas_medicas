<?php require_once __DIR__ . '/../layout/header.php'; ?>


<div class="profile-grid">
    <div>
        <div class="profile-hero">
            <h2><?= htmlspecialchars($paciente['nombre'] . ' ' . $paciente['apellido']) ?></h2>
            <p>Tu cuenta de paciente ya está vinculada al sistema para consultar citas e historial clínico.</p>
            <div class="profile-list">
                <div class="profile-item"><span>Cédula</span><strong><?= htmlspecialchars($paciente['cedula']) ?></strong></div>
                <div class="profile-item"><span>Teléfono</span><strong><?= htmlspecialchars($paciente['telefono'] ?: 'No registrado') ?></strong></div>
                <div class="profile-item"><span>Correo</span><strong><?= htmlspecialchars($paciente['email']) ?></strong></div>
                <div class="profile-item"><span>Fecha de nacimiento</span><strong><?= htmlspecialchars($paciente['fecha_nacimiento'] ?: 'No registrada') ?></strong></div>
            </div>
        </div>

        <div class="profile-card" style="margin-top:20px;">
            <div class="table-header">
                <h2>Accesos rápidos</h2>
            </div>
            <div class="form-actions">
                <a href="index.php?controller=cita&action=crearPaciente" class="btn btn-primary"><i class="fa-solid fa-calendar-plus"></i> Agendar cita</a>
                <a href="index.php?controller=cita&action=index" class="btn btn-secondary"><i class="fa-solid fa-calendar-check"></i> Mis citas</a>
                <a href="index.php?controller=historial&action=ver&paciente_id=<?= (int)$paciente['id'] ?>" class="btn btn-info"><i class="fa-solid fa-notes-medical"></i> Mi historial</a>
            </div>
        </div>
    </div>

    <div>
        <div class="profile-card">
            <div class="table-header"><h2>Próximas citas</h2></div>
            <?php if (!empty($proximasCitas)): ?>
                <div class="timeline-mini">
                    <?php foreach ($proximasCitas as $cita): ?>
                        <div class="entry">
                            <strong><?= htmlspecialchars($cita['medico']) ?></strong>
                            <div class="muted">Fecha: <?= htmlspecialchars($cita['fecha']) ?> · Hora: <?= htmlspecialchars($cita['hora_inicio']) ?></div>
                            <div><?= htmlspecialchars($cita['motivo'] ?: 'Sin motivo especificado') ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-box"><p>No tienes citas pendientes próximas.</p></div>
            <?php endif; ?>
        </div>

        <div class="profile-card" style="margin-top:20px;">
            <div class="table-header"><h2>Últimos registros clínicos</h2></div>
            <?php if (!empty($historial)): ?>
                <div class="timeline-mini">
                    <?php foreach (array_slice($historial, 0, 3) as $h): ?>
                        <div class="entry">
                            <strong><?= htmlspecialchars($h['diagnostico']) ?></strong>
                            <div class="muted"><?= htmlspecialchars($h['medico_nombre']) ?> · <?= htmlspecialchars($h['fecha_registro']) ?></div>
                            <div><?= htmlspecialchars($h['motivo_consulta']) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-box"><p>No hay historial clínico registrado todavía.</p></div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
