<?php require_once __DIR__ . '/../layout/header.php'; ?>
<div class="form-card"><div class="form-header"><div><h2>Registrar médico</h2><p style="margin:6px 0 0;color:#6b7280;">Al guardar, el sistema generará una contraseña temporal y el médico deberá cambiarla al iniciar sesión.</p></div><a href="index.php?controller=medico&action=index" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Volver</a></div>
<form method="POST" action="index.php?controller=medico&action=guardar"><div class="form-grid">
<div class="form-group"><label>Nombre</label><input type="text" name="nombre" id="nombre" required></div>
<div class="form-group"><label>Apellido</label><input type="text" name="apellido" id="apellido" required></div>
<div class="form-group"><label>Especialidad</label><input type="text" name="especialidad" id="especialidad" required></div>
<div class="form-group"><label>Teléfono</label><input type="text" name="telefono" id="telefono" maxlength="9" data-mask="telefono" placeholder="0000-0000"></div>
<div class="form-group full"><label>Correo electrónico</label><input type="email" name="email" id="email" required></div>
</div><div class="form-actions"><button class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar médico</button></div></form></div>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>

