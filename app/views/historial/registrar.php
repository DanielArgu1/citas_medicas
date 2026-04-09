<?php require_once __DIR__ . '/../layout/header.php'; ?>



<div class="hc-page">
    <div class="hc-header">
        <div class="hc-title-wrap">
            <h2>Registrar evolución clínica</h2>
            <p>Paciente: <strong><?= htmlspecialchars($paciente['nombre'] . ' ' . $paciente['apellido']) ?></strong></p>
        </div>

        <a href="index.php?controller=cita&action=index" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Volver a citas
        </a>
    </div>

    <div class="hc-layout">
        <div class="hc-left">
            <div class="hc-card">
                <div class="hc-card-head">
                    <h3><i class="fa-solid fa-file-circle-plus"></i> Nueva evolución clínica</h3>
                    <p>Registrá la atención médica actual del paciente.</p>
                </div>

                <form action="index.php?controller=historial&action=crear" method="POST" class="hc-form">
                    <input type="hidden" name="paciente_id" value="<?= htmlspecialchars($paciente['id']) ?>">
                    <input type="hidden" name="medico_id" value="1">
                    <input type="hidden" name="cita_id" value="<?= !empty($cita['id']) ? htmlspecialchars($cita['id']) : '' ?>">

                    <div class="hc-form-group">
                        <label>Motivo de consulta</label>
                        <textarea name="motivo_consulta" required placeholder="Ejemplo: dolor abdominal, control general, fiebre..."><?= !empty($cita['motivo']) ? htmlspecialchars($cita['motivo']) : '' ?></textarea>
                    </div>

                    <div class="hc-form-group">
                        <label>Síntomas</label>
                        <textarea name="sintomas" placeholder="Describí los síntomas reportados por el paciente"></textarea>
                    </div>

                    <div class="hc-form-group">
                        <label>Diagnóstico</label>
                        <textarea name="diagnostico" required placeholder="Diagnóstico médico"></textarea>
                    </div>

                    <div class="hc-form-group">
                        <label>Tratamiento</label>
                        <textarea name="tratamiento" placeholder="Medicamentos, indicaciones o procedimientos"></textarea>
                    </div>

                    <div class="hc-form-group">
                        <label>Observaciones</label>
                        <textarea name="observaciones" placeholder="Notas adicionales"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary hc-btn-full">
                        <i class="fa-solid fa-floppy-disk"></i> Guardar evolución
                    </button>
                </form>
            </div>
        </div>

        <div class="hc-right">
            <div class="hc-card">
                <div class="hc-card-head">
                    <h3><i class="fa-solid fa-notes-medical"></i> Historial previo</h3>
                    <p>Aquí podés revisar lo que otros médicos han documentado antes.</p>
                </div>

                <?php if (!empty($historial)): ?>
                    <div class="hc-timeline">
                        <?php foreach ($historial as $h): ?>
                            <div class="hc-item">
                                <div class="hc-item-top">
                                    <div>
                                        <h4><?= htmlspecialchars($h['diagnostico']) ?></h4>
                                        <span class="hc-pill">
                                            <i class="fa-solid fa-user-doctor"></i>
                                            <?= htmlspecialchars($h['medico_nombre']) ?>
                                        </span>
                                    </div>

                                    <span class="hc-date">
                                        <i class="fa-regular fa-calendar"></i>
                                        <?= htmlspecialchars($h['fecha_registro']) ?>
                                    </span>
                                </div>

                                <div class="hc-blocks">
                                    <div class="hc-block">
                                        <span class="hc-label">Motivo de consulta</span>
                                        <p><?= nl2br(htmlspecialchars($h['motivo_consulta'])) ?></p>
                                    </div>

                                    <?php if (!empty($h['sintomas'])): ?>
                                        <div class="hc-block">
                                            <span class="hc-label">Síntomas</span>
                                            <p><?= nl2br(htmlspecialchars($h['sintomas'])) ?></p>
                                        </div>
                                    <?php endif; ?>

                                    <div class="hc-block">
                                        <span class="hc-label">Diagnóstico</span>
                                        <p><?= nl2br(htmlspecialchars($h['diagnostico'])) ?></p>
                                    </div>

                                    <?php if (!empty($h['tratamiento'])): ?>
                                        <div class="hc-block">
                                            <span class="hc-label">Tratamiento</span>
                                            <p><?= nl2br(htmlspecialchars($h['tratamiento'])) ?></p>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($h['observaciones'])): ?>
                                        <div class="hc-block">
                                            <span class="hc-label">Observaciones</span>
                                            <p><?= nl2br(htmlspecialchars($h['observaciones'])) ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="hc-empty">
                        <p>No hay registros clínicos todavía para este paciente.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>