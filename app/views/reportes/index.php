<?php require_once __DIR__ . '/../layout/header.php'; ?>

<!-- Librerías -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>



<script id="reportes-data" type="application/json">
<?= json_encode([
    'porEstado' => array_values($porEstado),
    'diagnosticosFrecuentes' => array_values($diagnosticosFrecuentes),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
</script>

<h2 class="fade-in">Dashboard</h2>

<?php 
$porcentaje = $totalCitas > 0 ? round(($citasCompletadas / $totalCitas) * 100) : 0;
?>

<div class="report-cards fade-in">
    <div class="stat-card"><div class="stat-label">Pacientes</div><div class="stat-value"><?= $totalPacientes ?></div></div>
    <div class="stat-card"><div class="stat-label">Médicos</div><div class="stat-value"><?= $totalMedicos ?></div></div>
    <div class="stat-card"><div class="stat-label">Total citas</div><div class="stat-value"><?= $totalCitas ?></div></div>
    <div class="stat-card"><div class="stat-label">Citas hoy</div><div class="stat-value"><?= $citasHoy ?></div></div>
    <div class="stat-card"><div class="stat-label">Efectividad</div><div class="stat-value"><?= $porcentaje ?>%</div></div>
    <div class="stat-card"><div class="stat-label">Top diagnóstico</div><div class="stat-value stat-text"><?= $diagnosticoMasFrecuente['diagnostico'] ?? 'N/A' ?></div></div>
</div>

<div class="report-grid fade-in">

    <div class="report-left">

        <!-- TABLA CITAS -->
        <div class="table-card" id="tablaCitas">
            <div class="table-header">
                <h2>Últimas citas</h2>
                <div>
                    <button class="btn btn-print" data-report-action="print">Imprimir</button>
                    <button class="btn btn-excel" data-export="excel" data-target="tablaCitas" data-filename="Citas">Excel</button>
                    <button class="btn btn-pdf" data-export="pdf" data-target="tablaCitas" data-filename="Citas">PDF</button>
                </div>
            </div>
            <div class="table-responsive">
                <table>
                    <thead><tr><th>Paciente</th><th>Médico</th><th>Fecha</th><th>Estado</th></tr></thead>
                    <tbody>
                        <?php foreach($citas as $c): ?>
                        <tr>
                            <td><?= $c['paciente'] ?></td>
                            <td><?= $c['medico'] ?></td>
                            <td><?= $c['fecha'] ?></td>
                            <td><span class="badge <?= $c['estado'] ?>"><?= $c['estado'] ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- HISTORIAL -->
        <div class="table-card" id="tablaHistorial">
            <div class="table-header">
                <h2>Historial clínico</h2>
                <div>
                    <button class="btn btn-excel" data-export="excel" data-target="tablaHistorial" data-filename="Historial">Excel</button>
                    <button class="btn btn-pdf" data-export="pdf" data-target="tablaHistorial" data-filename="Historial">PDF</button>
                </div>
            </div>
            <div class="table-responsive">
                <table>
                    <thead><tr><th>Paciente</th><th>Diagnóstico</th><th>Fecha</th></tr></thead>
                    <tbody>
                        <?php foreach($ultimosHistoriales as $h): ?>
                        <tr>
                            <td><?= $h['paciente'] ?></td>
                            <td><?= $h['diagnostico'] ?></td>
                            <td><?= $h['fecha_registro'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- DERECHA GRAFICAS -->
    <div class="mini-list">
        <div class="table-card">
            <h2>Citas por estado</h2>
            <canvas id="chartEstados"></canvas>
        </div>
        <div class="table-card">
            <h2>Diagnósticos</h2>
            <canvas id="chartDiagnosticos"></canvas>
        </div>
    </div>

</div>


<?php require_once __DIR__ . '/../layout/footer.php'; ?>