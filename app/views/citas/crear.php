<?php require_once __DIR__ . '/../layout/header.php'; ?>
<div class="form-card"><div class="form-header"><h2>Registrar cita</h2><a href="index.php?controller=cita&action=index" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Volver</a></div>
<form method="POST" action="index.php?controller=cita&action=guardar"><div class="form-grid">
<div class="form-group"><label>Paciente</label><select name="paciente_id" required><option value="">Seleccione un paciente</option><?php foreach ($pacientes as $p): ?><option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre'].' '.$p['apellido']) ?></option><?php endforeach; ?></select></div>
<div class="form-group"><label>Médico</label><select name="medico_id" required><option value="">Seleccione un médico</option><?php foreach ($medicos as $m): ?><option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nombre'].' '.$m['apellido'].' - '.$m['especialidad']) ?></option><?php endforeach; ?></select></div>
<div class="form-group"><label>Fecha</label><input type="date" name="fecha" min="<?= date('Y-m-d') ?>" required></div>
<div class="form-group"><label>Hora de inicio</label><input type="time" name="hora_inicio" required></div>
<div class="form-group full"><label>Motivo</label><textarea name="motivo" maxlength="250" placeholder="Describe el motivo de la cita"></textarea></div>
</div><div class="form-actions"><button class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar cita</button></div></form></div>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
