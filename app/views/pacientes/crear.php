<?php require_once __DIR__ . '/../layout/header.php'; ?>
<div class="form-card"><div class="form-header"><h2>Registrar paciente</h2><a href="index.php?controller=paciente&action=index" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Volver</a></div>
<form method="POST" action="index.php?controller=paciente&action=guardar"><div class="form-grid">
<div class="form-group"><label>Nombre</label><input type="text" name="nombre" required></div>
<div class="form-group"><label>Apellido</label><input type="text" name="apellido" required></div>
<div class="form-group"><label>Cédula</label><input type="text" name="cedula" maxlength="15" data-mask="cedula" placeholder="0000-0000-00000" required></div>
<div class="form-group"><label>Teléfono</label><input type="text" name="telefono" maxlength="9" data-mask="telefono" placeholder="0000-0000"></div>
<div class="form-group"><label>Correo electrónico</label><input type="email" name="email"></div>
<div class="form-group"><label>Fecha de nacimiento</label><input type="date" name="fecha_nacimiento"></div>
</div><div class="form-actions"><button class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar paciente</button></div></form></div>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
