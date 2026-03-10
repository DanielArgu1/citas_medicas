<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="form-card">
    <div class="form-header">
        <h2>Registrar médico</h2>
        <a href="index.php?controller=medico&action=index" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Volver
        </a>
    </div>

    <form method="POST" action="index.php?controller=medico&action=guardar">
        <div class="form-grid">
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" required>
            </div>

            <div class="form-group">
                <label for="apellido">Apellido</label>
                <input type="text" name="apellido" id="apellido" required>
            </div>

            <div class="form-group">
                <label for="especialidad">Especialidad</label>
                <input type="text" name="especialidad" id="especialidad" required>
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="text" name="telefono" id="telefono">
            </div>

            <div class="form-group full">
                <label for="email">Correo electrónico</label>
                <input type="email" name="email" id="email">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-floppy-disk"></i> Guardar médico
            </button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>