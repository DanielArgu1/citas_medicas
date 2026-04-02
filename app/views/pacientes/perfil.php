<?php require_once __DIR__ . '/../layout/header.php'; ?>
<style>
    .profile-grid{display:grid;grid-template-columns:1.1fr 1fr;gap:20px}.profile-hero{background:linear-gradient(135deg,#0f172a 0%,#1d4ed8 100%);color:#fff;padding:28px;border-radius:24px;box-shadow:0 18px 40px rgba(15,23,42,.18)}.profile-hero h2{margin:0 0 8px;font-size:30px}.profile-hero p{margin:0;color:rgba(255,255,255,.85)}.profile-card{background:#fff;border-radius:24px;padding:22px;box-shadow:0 12px 30px rgba(15,23,42,.08)}.profile-list{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;margin-top:22px}.profile-item{background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.18);border-radius:18px;padding:14px}.profile-item span,.profile-detail span{display:block;font-size:12px;text-transform:uppercase;letter-spacing:.05em;opacity:.78;margin-bottom:6px;font-weight:700}.profile-item strong{font-size:15px}.profile-detail{background:#f8fafc;border:1px solid #e2e8f0;border-radius:16px;padding:16px}.timeline-mini{display:grid;gap:12px}.timeline-mini .entry{padding:14px;border:1px solid #e5e7eb;border-radius:16px;background:#fff}.timeline-mini .entry strong{display:block;margin-bottom:6px}.muted{color:#64748b}@media (max-width:900px){.profile-grid,.profile-list{grid-template-columns:1fr}}
</style>

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
                <a href="index.php?controller=cita&action=index" class="btn btn-primary"><i class="fa-solid fa-calendar-check"></i> Mis citas</a>
                <a href="index.php?controller=historial&action=ver&paciente_id=<?= (int)$paciente['id'] ?>" class="btn btn-secondary"><i class="fa-solid fa-notes-medical"></i> Mi historial</a>
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
