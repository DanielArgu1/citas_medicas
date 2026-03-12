<?php require_once __DIR__ . '/../layout/header.php'; ?>

<style>
    .hc-page{
        display:flex;
        flex-direction:column;
        gap:24px;
    }

    .hc-header{
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:16px;
        flex-wrap:wrap;
    }

    .hc-title-wrap h2{
        margin:0;
        font-size:28px;
        font-weight:800;
        color:#0f172a;
    }

    .hc-title-wrap p{
        margin:6px 0 0;
        color:#64748b;
        font-size:14px;
    }

    .hc-layout{
        display:grid;
        grid-template-columns: 380px 1fr;
        gap:22px;
        align-items:start;
    }

    .hc-card{
        background:#ffffff;
        border:1px solid #e5e7eb;
        border-radius:20px;
        box-shadow:0 12px 30px rgba(15,23,42,.08);
        overflow:hidden;
    }

    .hc-card-head{
        padding:20px 22px 14px;
        border-bottom:1px solid #eef2f7;
        background:linear-gradient(180deg,#ffffff 0%, #f8fbff 100%);
    }

    .hc-card-head h3{
        margin:0;
        font-size:18px;
        font-weight:700;
        color:#0f172a;
        display:flex;
        align-items:center;
        gap:10px;
    }

    .hc-card-head p{
        margin:8px 0 0;
        font-size:14px;
        color:#64748b;
    }

    .hc-form{
        padding:20px 22px 22px;
    }

    .hc-form-group{
        margin-bottom:16px;
    }

    .hc-form-group label{
        display:block;
        margin-bottom:8px;
        font-size:14px;
        font-weight:700;
        color:#1e293b;
    }

    .hc-form-group textarea{
        width:100%;
        min-height:95px;
        resize:vertical;
        border:1px solid #dbe3ee;
        border-radius:14px;
        padding:12px 14px;
        font-size:14px;
        color:#0f172a;
        background:#fff;
        outline:none;
        transition:.2s ease;
        box-sizing:border-box;
    }

    .hc-form-group textarea:focus{
        border-color:#2563eb;
        box-shadow:0 0 0 4px rgba(37,99,235,.12);
    }

    .hc-btn-full{
        width:100%;
        display:inline-flex;
        justify-content:center;
        align-items:center;
        gap:8px;
    }

    .hc-timeline{
        padding:20px;
        display:flex;
        flex-direction:column;
        gap:16px;
    }

    .hc-item{
        border:1px solid #e7edf5;
        background:#fbfdff;
        border-radius:18px;
        padding:18px;
        transition:.2s ease;
    }

    .hc-item-top{
        display:flex;
        justify-content:space-between;
        align-items:flex-start;
        gap:12px;
        flex-wrap:wrap;
        margin-bottom:14px;
    }

    .hc-item-top h4{
        margin:0 0 8px;
        font-size:17px;
        font-weight:800;
        color:#0f172a;
    }

    .hc-pill{
        display:inline-flex;
        align-items:center;
        gap:8px;
        background:#eef4ff;
        color:#31528f;
        border-radius:999px;
        padding:7px 11px;
        font-size:13px;
        font-weight:600;
    }

    .hc-date{
        display:inline-flex;
        align-items:center;
        gap:8px;
        background:#f8fafc;
        border:1px solid #e2e8f0;
        color:#64748b;
        border-radius:999px;
        padding:7px 11px;
        font-size:13px;
        font-weight:600;
        white-space:nowrap;
    }

    .hc-blocks{
        display:grid;
        gap:12px;
    }

    .hc-block{
        background:#fff;
        border:1px solid #edf2f7;
        border-radius:14px;
        padding:12px 14px;
    }

    .hc-label{
        display:block;
        margin-bottom:7px;
        font-size:12px;
        font-weight:800;
        text-transform:uppercase;
        letter-spacing:.05em;
        color:#2563eb;
    }

    .hc-block p{
        margin:0;
        line-height:1.6;
        font-size:14px;
        color:#1f2937;
    }

    .hc-empty{
        padding:30px 22px;
        text-align:center;
        color:#64748b;
    }

    @media (max-width: 1100px){
        .hc-layout{
            grid-template-columns:1fr;
        }
    }
</style>

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