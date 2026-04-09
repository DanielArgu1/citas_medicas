<?php require_once __DIR__ . '/../layout/header.php'; ?>
<div class="form-card"><div class="form-header"><div><h2>Nuevo paciente</h2><p style="margin:6px 0 0;color:#6b7280;">Al guardar, el sistema creará automáticamente su usuario con rol paciente y una contraseña temporal que deberá cambiar en su primer ingreso.</p></div><a href="index.php?controller=paciente&action=index" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Volver</a></div>
<form method="POST" action="index.php?controller=paciente&action=guardar"><div class="form-grid">
<div class="form-group"><label>Nombre</label><input type="text" name="nombre" data-only-letters required></div>
<div class="form-group"><label>Apellido</label><input type="text" name="apellido" data-only-letters required></div>
<div class="form-group"><label>Cédula</label><input type="text" name="cedula" maxlength="15" data-mask="cedula" required></div>
<div class="form-group"><label>Teléfono</label><input type="text" name="telefono" maxlength="9" data-mask="telefono"></div>
<div class="form-group"><label>Correo electrónico</label><input type="email" name="email" required></div>
<div class="form-group"><label>Fecha de nacimiento</label><input type="date" name="fecha_nacimiento"></div>
</div><div class="form-actions"><button class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar paciente</button></div></form></div>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
