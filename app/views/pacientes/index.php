<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="table-card">
    <div class="table-header">
        <h2>Gestión de pacientes</h2>
        <a href="index.php?controller=paciente&action=crear" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Nuevo paciente
        </a>
    </div>

    <?php if (!empty($pacientes)): ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre completo</th>
                        <th>Cédula</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Fecha nacimiento</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pacientes as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['id']); ?></td>
                            <td><?= htmlspecialchars($p['nombre'] . ' ' . $p['apellido']); ?></td>
                            <td><?= htmlspecialchars($p['cedula']); ?></td>
                            <td><?= htmlspecialchars($p['telefono']); ?></td>
                            <td><?= htmlspecialchars($p['email']); ?></td>
                            <td><?= htmlspecialchars($p['fecha_nacimiento']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty-box">
            <i class="fa-solid fa-user-plus" style="font-size:32px; margin-bottom:10px;"></i>
            <p>No hay pacientes registrados.</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>