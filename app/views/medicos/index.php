<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="table-card">
    <div class="table-header">
        <h2>Gestión de médicos</h2>
        <a href="index.php?controller=medico&action=crear" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Nuevo médico
        </a>
    </div>

    <?php if (!empty($medicos)): ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre completo</th>
                        <th>Especialidad</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($medicos as $m): ?>
                        <tr>
                            <td><?= htmlspecialchars($m['id']); ?></td>
                            <td><?= htmlspecialchars($m['nombre'] . ' ' . $m['apellido']); ?></td>
                            <td><?= htmlspecialchars($m['especialidad']); ?></td>
                            <td><?= htmlspecialchars($m['telefono']); ?></td>
                            <td><?= htmlspecialchars($m['email']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty-box">
            <i class="fa-solid fa-user-doctor" style="font-size:32px; margin-bottom:10px;"></i>
            <p>No hay médicos registrados.</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>