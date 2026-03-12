<?php require_once __DIR__ . '/../layout/header.php'; ?>

<style>
    .hc-page{
        display:flex;
        flex-direction:column;
        gap:24px;
    }

    .hc-hero{
        position:relative;
        overflow:hidden;
        border-radius:24px;
        padding:28px;
        background:linear-gradient(135deg,#0f172a 0%, #1e3a8a 45%, #2563eb 100%);
        color:#fff;
        box-shadow:0 18px 40px rgba(15,23,42,.20);
    }

    .hc-hero::before{
        content:"";
        position:absolute;
        top:-40px;
        right:-40px;
        width:180px;
        height:180px;
        border-radius:50%;
        background:rgba(255,255,255,.10);
    }

    .hc-hero::after{
        content:"";
        position:absolute;
        bottom:-55px;
        left:-30px;
        width:140px;
        height:140px;
        border-radius:50%;
        background:rgba(255,255,255,.08);
    }

    .hc-hero-content{
        position:relative;
        z-index:2;
        display:flex;
        justify-content:space-between;
        align-items:flex-start;
        gap:18px;
        flex-wrap:wrap;
    }

    .hc-hero h2{
        margin:0;
        font-size:31px;
        font-weight:800;
        letter-spacing:-.02em;
    }

    .hc-hero p{
        margin:10px 0 0;
        color:rgba(255,255,255,.88);
        font-size:15px;
    }

    .hc-hero .btn{
        border:none;
        background:rgba(255,255,255,.14);
        color:#fff;
        backdrop-filter:blur(6px);
    }

    .hc-patient-strip{
        display:grid;
        grid-template-columns:repeat(4, minmax(0,1fr));
        gap:14px;
        margin-top:22px;
        position:relative;
        z-index:2;
    }

    .hc-mini{
        background:rgba(255,255,255,.10);
        border:1px solid rgba(255,255,255,.14);
        border-radius:18px;
        padding:14px 16px;
        backdrop-filter:blur(8px);
    }

    .hc-mini span{
        display:block;
        font-size:12px;
        text-transform:uppercase;
        letter-spacing:.06em;
        color:rgba(255,255,255,.72);
        margin-bottom:6px;
        font-weight:700;
    }

    .hc-mini strong{
        font-size:15px;
        font-weight:700;
        color:#fff;
    }

    .hc-panel{
        background:#fff;
        border:1px solid #e9eef5;
        border-radius:24px;
        box-shadow:0 14px 34px rgba(15,23,42,.08);
        overflow:hidden;
    }

    .hc-panel-head{
        padding:22px 24px 16px;
        border-bottom:1px solid #eef2f7;
        background:linear-gradient(180deg,#ffffff 0%, #f8fbff 100%);
    }

    .hc-panel-head h3{
        margin:0;
        display:flex;
        align-items:center;
        gap:10px;
        font-size:19px;
        font-weight:800;
        color:#0f172a;
    }

    .hc-panel-head p{
        margin:8px 0 0;
        color:#64748b;
        font-size:14px;
        line-height:1.5;
    }

    .hc-history-wrap{
        padding:24px;
    }

    .hc-timeline{
        position:relative;
        display:flex;
        flex-direction:column;
        gap:20px;
        padding-left:18px;
    }

    .hc-timeline::before{
        content:"";
        position:absolute;
        left:9px;
        top:0;
        bottom:0;
        width:2px;
        background:linear-gradient(180deg,#bfdbfe 0%, #dbeafe 100%);
    }

    .hc-item{
        position:relative;
        background:#fff;
        border:1px solid #e8eef6;
        border-radius:22px;
        padding:20px;
        margin-left:14px;
        box-shadow:0 10px 24px rgba(15,23,42,.06);
    }

    .hc-item::before{
        content:"";
        position:absolute;
        left:-24px;
        top:28px;
        width:14px;
        height:14px;
        border-radius:50%;
        background:#2563eb;
        box-shadow:0 0 0 5px #dbeafe;
    }

    .hc-item-top{
        display:flex;
        justify-content:space-between;
        align-items:flex-start;
        gap:14px;
        flex-wrap:wrap;
        margin-bottom:16px;
    }

    .hc-diagnostico{
        display:flex;
        flex-direction:column;
        gap:8px;
    }

    .hc-diagnostico h4{
        margin:0;
        font-size:19px;
        color:#0f172a;
        font-weight:800;
    }

    .hc-badges{
        display:flex;
        flex-wrap:wrap;
        gap:8px;
    }

    .hc-pill,
    .hc-date{
        display:inline-flex;
        align-items:center;
        gap:8px;
        border-radius:999px;
        padding:8px 12px;
        font-size:12px;
        font-weight:700;
    }

    .hc-pill{
        background:#eff6ff;
        color:#1d4ed8;
        border:1px solid #dbeafe;
    }

    .hc-date{
        background:#f8fafc;
        color:#64748b;
        border:1px solid #e2e8f0;
        white-space:nowrap;
    }

    .hc-blocks{
        display:grid;
        grid-template-columns:repeat(2,minmax(0,1fr));
        gap:12px;
    }

    .hc-block{
        background:linear-gradient(180deg,#ffffff 0%, #fbfdff 100%);
        border:1px solid #edf2f7;
        border-radius:16px;
        padding:14px 15px;
    }

    .hc-block-full{
        grid-column:1 / -1;
    }

    .hc-label{
        display:block;
        margin-bottom:7px;
        font-size:11px;
        font-weight:800;
        text-transform:uppercase;
        letter-spacing:.08em;
        color:#2563eb;
    }

    .hc-block p{
        margin:0;
        line-height:1.65;
        font-size:14px;
        color:#1f2937;
    }

    .hc-empty{
        padding:44px 24px;
        text-align:center;
        color:#64748b;
    }

    @media (max-width: 900px){
        .hc-patient-strip{
            grid-template-columns:repeat(2,minmax(0,1fr));
        }

        .hc-blocks{
            grid-template-columns:1fr;
        }
    }

    @media (max-width: 600px){
        .hc-hero{
            padding:22px;
        }

        .hc-hero h2{
            font-size:26px;
        }

        .hc-patient-strip{
            grid-template-columns:1fr;
        }

        .hc-history-wrap{
            padding:18px;
        }

        .hc-panel-head{
            padding:18px 18px 14px;
        }

        .hc-item{
            padding:16px;
        }
    }
</style>

<div class="hc-page">
    <div class="hc-hero">
        <div class="hc-hero-content">
            <div>
                <h2><i class="fa-solid fa-notes-medical"></i> Historial clínico</h2>
                <p>Consulta diagnósticos, tratamientos y evolución médica del paciente.</p>
            </div>

            <a href="index.php?controller=paciente&action=index" class="btn btn-secondary">
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