<?php require_once __DIR__ . '/../layout/header.php'; ?>
<div class="form-card" style="max-width:760px;margin:0 auto;">
    <div class="form-header">
        <div>
            <h2>Cambiar contraseña</h2>
            <p style="margin:6px 0 0;color:#6b7280;">
                <?= !empty($forzado) ? 'Debes reemplazar la contraseña temporal para poder seguir usando el sistema.' : 'Actualiza tu contraseña para mantener segura tu cuenta.' ?>
            </p>
        </div>
    </div>

    <div class="alert alert-info">
        Tu nueva contraseña debe tener al menos 8 caracteres e incluir mayúscula, minúscula y número.
    </div>

    <form method="POST" action="index.php?controller=auth&action=actualizarPassword">
        <div class="form-grid">
            <div class="form-group full">
                <label>Contraseña actual</label>
                <input type="password" name="password_actual" required>
            </div>
            <div class="form-group">
                <label>Nueva contraseña</label>
                <input type="password" name="password_nueva" required>
            </div>
            <div class="form-group">
                <label>Confirmar nueva contraseña</label>
                <input type="password" name="password_confirmacion" required>
            </div>
        </div>
        <div class="form-actions">
            <button class="btn btn-primary"><i class="fa-solid fa-lock"></i> Guardar nueva contraseña</button>
            <?php if (empty($forzado)): ?>
                <a href="index.php?controller=dashboard&action=index" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Volver</a>
            <?php endif; ?>
        </div>
    </form>
</div>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
