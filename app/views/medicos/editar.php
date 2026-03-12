<?php require_once __DIR__ . '/../layout/header.php'; ?>
<div class="form-card"><div class="form-header"><h2>Editar médico</h2><a href="index.php?controller=medico&action=index" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Volver</a></div>
<form method="POST" action="index.php?controller=medico&action=actualizar"><input type="hidden" name="id" value="<?= $medico['id'] ?>"><div class="form-grid">
<div class="form-group"><label>Nombre</label><input type="text" name="nombre" id='nombre' value="<?= htmlspecialchars($medico['nombre']) ?>" required></div>
<div class="form-group"><label>Apellido</label><input type="text" name="apellido" id='apellido' value="<?= htmlspecialchars($medico['apellido']) ?>" required></div>
<div class="form-group"><label>Especialidad</label><input type="text" name="especialidad" id='especialidad' value="<?= htmlspecialchars($medico['especialidad']) ?>" required></div>
<div class="form-group"><label>Teléfono</label><input type="text" name="telefono" id='telefono' maxlength="9" data-mask="telefono" value="<?= htmlspecialchars($medico['telefono']) ?>"></div>
<div class="form-group full"><label>Correo electrónico</label><input type="email" id='email' name="email" value="<?= htmlspecialchars($medico['email']) ?>" required></div>
<div class="form-group full"><label><input type="checkbox" name="reset_password" value="1"> Generar nueva contraseña temporal para el médico</label></div>
</div><div class="form-actions"><button class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Actualizar médico</button></div></form></div>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>

<script>
function soloLetras(valor) {
    return valor.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ ]/g, '').replace(/\s+/g, ' ');
}

function soloNumeros(valor) {
    return valor.replace(/\D/g, '');
}

function formatoTelefono(valor) {
    valor = soloNumeros(valor).slice(0, 8);
    if (valor.length > 4) {
        return valor.slice(0, 4) + '-' + valor.slice(4);
    }
    return valor;
}

const nombre = document.getElementById('nombre');
const apellido = document.getElementById('apellido');
const especialidad = document.getElementById('especialidad');
const telefono = document.getElementById('telefono');

if (nombre) {
    nombre.addEventListener('input', function () {
        this.value = soloLetras(this.value);
    });
}

if (apellido) {
    apellido.addEventListener('input', function () {
        this.value = soloLetras(this.value);
    });
}

if (especialidad) {
    especialidad.addEventListener('input', function () {
        this.value = soloLetras(this.value);
    });
}

if (telefono) {
    telefono.addEventListener('input', function () {
        this.value = formatoTelefono(this.value);
    });
}
</script>