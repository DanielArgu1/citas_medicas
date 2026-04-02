<?php require_once __DIR__ . '/../layout/header.php'; ?>
<div class="form-card"><div class="form-header"><div><h2>Editar paciente</h2><p style="margin:6px 0 0;color:#6b7280;">Los cambios también actualizan la cuenta del usuario paciente.</p></div><a href="index.php?controller=paciente&action=index" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Volver</a></div>
<form method="POST" action="index.php?controller=paciente&action=actualizar"><input type="hidden" name="id" value="<?= $paciente['id'] ?>"><div class="form-grid">
<div class="form-group"><label>Nombre</label><input type="text" name="nombre" data-only-letters value="<?= htmlspecialchars($paciente['nombre']) ?>" required></div>
<div class="form-group"><label>Apellido</label><input type="text" name="apellido" data-only-letters value="<?= htmlspecialchars($paciente['apellido']) ?>" required></div>
<div class="form-group"><label>Cédula</label><input type="text" name="cedula" maxlength="15" data-mask="cedula" value="<?= htmlspecialchars($paciente['cedula']) ?>" required></div>
<div class="form-group"><label>Teléfono</label><input type="text" name="telefono" maxlength="9" data-mask="telefono" value="<?= htmlspecialchars($paciente['telefono']) ?>"></div>
<div class="form-group"><label>Correo electrónico</label><input type="email" name="email" value="<?= htmlspecialchars($paciente['email']) ?>" required></div>
<div class="form-group"><label>Fecha de nacimiento</label><input type="date" name="fecha_nacimiento" value="<?= htmlspecialchars($paciente['fecha_nacimiento']) ?>"></div>
<div class="form-group full"><label><input type="checkbox" name="reset_password" value="1"> Generar nueva contraseña temporal para el paciente</label></div>
</div><div class="form-actions"><button class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Actualizar paciente</button></div></form></div>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
