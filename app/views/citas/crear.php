<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="form-card">
    <div class="form-header">
        <h2>Registrar nueva cita</h2>
        <a href="index.php?controller=cita&action=index" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Volver
        </a>
    </div>

    <form method="POST" action="index.php?controller=cita&action=guardar">
        <div class="form-grid">
            <div class="form-group">
                <label for="paciente_id">Paciente</label>
                <select name="paciente_id" id="paciente_id" required>
                    <option value="">Seleccione un paciente</option>
                    <?php foreach ($pacientes as $p): ?>
                        <option value="<?= $p['id']; ?>">
                            <?= htmlspecialchars($p['nombre'] . ' ' . $p['apellido']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="medico_id">Médico</label>
                <select name="medico_id" id="medico_id" required>
                    <option value="">Seleccione un médico</option>
                    <?php foreach ($medicos as $m): ?>
                        <option value="<?= $m['id']; ?>">
                            <?= htmlspecialchars($m['nombre'] . ' ' . $m['apellido']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

<<<<<<< HEAD
<option value="<?= $p['id'] ?>">
<?= $p['nombre'] ?> <?= $p['apellido'] ?>
</option>
=======
            <div class="form-group">
                <label for="fecha">Fecha</label>
                <input type="date" name="fecha" id="fecha" min="<?= date('Y-m-d'); ?>" required>
            </div>
>>>>>>> 07c8788 (se agregaron vistas a formularios)

            <div class="form-group">
                <label for="hora_inicio">Hora de inicio</label>
                <input type="time" name="hora_inicio" id="hora_inicio" required>
            </div>

            <div class="form-group full">
                <label for="motivo">Motivo</label>
                <textarea name="motivo" id="motivo" maxlength="200" placeholder="Describe el motivo de la cita"></textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-floppy-disk"></i> Guardar cita
            </button>

            <a href="index.php?controller=cita&action=index" class="btn btn-secondary">
                <i class="fa-solid fa-xmark"></i> Cancelar
            </a>
        </div>
    </form>
</div>

<<<<<<< HEAD
<?php foreach($medicos as $m): ?>

<option value="<?= $m['id'] ?>">
<?= $m['nombre'] ?> <?= $m['apellido'] ?>
</option>

<?php endforeach; ?>

</select>

<br><br>

<label>Fecha</label>
<input type="date" name="fecha" required>

<br><br>

<label>Hora Inicio</label>
<input type="time" name="hora_inicio" required>

<br><br>

<label>Motivo</label>
<textarea name="motivo"></textarea>

<br><br>

<button type="submit">Guardar Cita</button>

</form>
=======
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
>>>>>>> 07c8788 (se agregaron vistas a formularios)
