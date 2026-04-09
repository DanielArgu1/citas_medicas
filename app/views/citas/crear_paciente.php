<?php require_once __DIR__ . '/../layout/header.php'; ?>
<div class="form-card">
    <div class="form-header">
        <h2>Agendar mi cita</h2>
        <a href="index.php?controller=cita&action=index" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>

    <div class="alert alert-info" style="margin-bottom:18px;">
        Estás agendando la cita para <strong><?= htmlspecialchars($paciente['nombre'] . ' ' . $paciente['apellido']) ?></strong>.
    </div>

    <form method="POST" action="index.php?controller=cita&action=guardarPaciente">
        <div class="form-grid">
            <div class="form-group full">
                <label>Paciente</label>
                <input type="text" value="<?= htmlspecialchars($paciente['nombre'] . ' ' . $paciente['apellido']) ?>" disabled>
            </div>

            <div class="form-group">
                <label>Médico</label>
                <select name="medico_id" required>
                    <option value="">Seleccione un médico</option>
                    <?php foreach ($medicos as $m): ?>
                        <option value="<?= (int)$m['id'] ?>"><?= htmlspecialchars($m['nombre'] . ' ' . $m['apellido'] . ' - ' . $m['especialidad']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Fecha</label>
                <input type="date" name="fecha" min="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="form-group">
                <label>Hora de inicio</label>
                <input type="time" name="hora_inicio" required>
            </div>

            <div class="form-group full">
                <label>Motivo de la consulta</label>
                <textarea name="motivo" maxlength="250" placeholder="Describe brevemente el motivo de tu cita"></textarea>
            </div>
        </div>

        <div class="form-actions">
            <button class="btn btn-primary"><i class="fa-solid fa-calendar-plus"></i> Agendar cita</button>
            <a href="index.php?controller=paciente&action=perfil" class="btn btn-secondary"><i class="fa-solid fa-user"></i> Ir a mi perfil</a>
        </div>
    </form>
</div>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
