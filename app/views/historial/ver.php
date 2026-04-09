<?php require_once __DIR__ . '/../layout/header.php'; ?>



<div class="hc-page">
    <div class="hc-hero">
        <div class="hc-hero-content">
            <div>
                <h2><i class="fa-solid fa-notes-medical"></i> Historial clínico</h2>
                <p>Consulta diagnósticos, tratamientos y evolución médica del paciente.</p>
            </div>

            <a href="<?= current_user_role() === 'paciente' ? 'index.php?controller=dashboard&action=index' : 'index.php?controller=paciente&action=index' ?>" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Volver
            </a>
        </div>

        <div class="hc-patient-strip">
            <div class="hc-mini">
                <span>Paciente</span>
                <strong><?= htmlspecialchars($paciente['nombre'] . ' ' . $paciente['apellido']) ?></strong>
            </div>

            <div class="hc-mini">
                <span>ID</span>
                <strong>#<?= htmlspecialchars($paciente['id']) ?></strong>
            </div>

            <div class="hc-mini">
                <span>Teléfono</span>
                <strong><?= !empty($paciente['telefono']) ? htmlspecialchars($paciente['telefono']) : 'No registrado' ?></strong>
            </div>

            <div class="hc-mini">
                <span>Email</span>
                <strong><?= !empty($paciente['email']) ? htmlspecialchars($paciente['email']) : 'No registrado' ?></strong>
            </div>
        </div>
    </div>

    <div class="hc-panel">
        <div class="hc-panel-head">
            <h3><i class="fa-solid fa-clock-rotate-left"></i> Registros clínicos</h3>
            <p>Aquí solo se muestra el historial médico del paciente.</p>
        </div>

        <div class="hc-history-wrap">
            <?php if (!empty($historial)): ?>
                <div class="hc-timeline">
                    <?php foreach ($historial as $h): ?>
                        <div class="hc-item">
                            <div class="hc-item-top">
                                <div class="hc-diagnostico">
                                    <h4><?= htmlspecialchars($h['diagnostico']) ?></h4>

                                    <div class="hc-badges">
                                        <span class="hc-pill">
                                            <i class="fa-solid fa-user-doctor"></i>
                                            <?= htmlspecialchars($h['medico_nombre']) ?>
                                        </span>

                                        <span class="hc-date">
                                            <i class="fa-regular fa-calendar"></i>
                                            <?= htmlspecialchars($h['fecha_registro']) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="hc-blocks">
                                <div class="hc-block hc-block-full">
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

<?php require_once __DIR__ . '/../layout/footer.php'; ?>