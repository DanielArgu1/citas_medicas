<?php require_once __DIR__ . '/../layout/header.php'; ?>
<div class="table-card">
    <div class="table-header">
        <h2><?= current_user_role() === 'medico' ? 'Mis citas' : 'Gestión de citas' ?></h2>
        <?php if (in_array(current_user_role(), ['admin','recepcion'], true)): ?>
            <a href="index.php?controller=cita&action=crear" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Nueva cita
            </a>
        <?php endif; ?>
    </div>

    <div class="toolbar">
        <div class="search-box">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" placeholder="Buscar por paciente, médico o estado..." data-search-target="tabla-citas">
        </div>
    </div>

    <?php if (!empty($citas)): ?>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Paciente</th>
                    <th>Médico</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Motivo</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="tabla-citas">
                <?php foreach ($citas as $c): ?>
                <tr>
                    <td><?= htmlspecialchars($c['id']) ?></td>
                    <td><?= htmlspecialchars($c['paciente']) ?></td>
                    <td><?= htmlspecialchars($c['medico']) ?></td>
                    <td><?= htmlspecialchars($c['fecha']) ?></td>
                    <td><?= htmlspecialchars($c['hora_inicio']) ?> - <?= htmlspecialchars($c['hora_fin']) ?></td>
                    <td><?= htmlspecialchars($c['motivo']) ?></td>
                    <td><span class="badge badge-<?= htmlspecialchars($c['estado']) ?>"><?= htmlspecialchars(ucfirst($c['estado'])) ?></span></td>
                    <td>
                        <div class="actions">
                           <a class="btn btn-info" href="index.php?controller=historial&action=registrar&paciente_id=<?= $c['paciente_id'] ?>&cita_id=<?= $c['id'] ?>" title="Registrar evolución clínica">
                                <i class="fa-solid fa-notes-medical"></i>
                           </a>

                            <?php if (in_array(current_user_role(), ['admin','recepcion'], true)): ?>
                                <a class="btn btn-secondary" href="index.php?controller=cita&action=editar&id=<?= $c['id'] ?>">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                            <?php endif; ?>

                            <?php if ($c['estado'] !== 'completada'): ?>
                                <a class="btn btn-success" href="index.php?controller=cita&action=completar&id=<?= $c['id'] ?>">
                                    <i class="fa-solid fa-check"></i>
                                </a>
                            <?php endif; ?>

                            <?php if ($c['estado'] !== 'cancelada'): ?>
                                <a class="btn btn-warning" href="index.php?controller=cita&action=cancelar&id=<?= $c['id'] ?>" data-confirm="¿Cancelar esta cita?">
                                    <i class="fa-solid fa-ban"></i>
                                </a>
                            <?php endif; ?>

                            <?php if (current_user_role() === 'admin'): ?>
                                <a class="btn btn-danger" href="index.php?controller=cita&action=eliminar&id=<?= $c['id'] ?>" data-confirm="¿Eliminar definitivamente esta cita?">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <div class="empty-box"><p>No hay citas registradas.</p></div>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
