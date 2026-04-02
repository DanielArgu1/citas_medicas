<?php require_once __DIR__ . '/../layout/header.php'; ?>
<div class="table-card">
    <div class="table-header"><h2>Gestión de pacientes</h2><a href="index.php?controller=paciente&action=crear" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Nuevo paciente</a></div>
    <div class="toolbar"><div class="search-box"><i class="fa-solid fa-magnifying-glass"></i><input type="text" placeholder="Buscar por nombre, cédula, teléfono o email..." data-search-target="tabla-pacientes"></div></div>
    <?php if (!empty($pacientes)): ?>
    <div class="table-responsive"><table><thead><tr><th>ID</th><th>Nombre completo</th><th>Cédula</th><th>Teléfono</th><th>Email</th><th>Cuenta</th><th>Fecha nacimiento</th><th>Acciones</th></tr></thead><tbody id="tabla-pacientes">
    <?php foreach ($pacientes as $p): ?>
    <tr>
        <td><?= htmlspecialchars($p['id']) ?></td>
        <td><?= htmlspecialchars($p['nombre'].' '.$p['apellido']) ?></td>
        <td><?= htmlspecialchars($p['cedula']) ?></td>
        <td><?= htmlspecialchars($p['telefono']) ?></td>
        <td><?= htmlspecialchars($p['email']) ?></td>
        <td>
            <?php if (!empty($p['usuario_id'])): ?>
                <span class="account-chip <?= ($p['usuario_estado'] ?? 'activo') === 'activo' ? 'active' : 'inactive' ?>">
                    <i class="fa-solid fa-user-check"></i>
                    <?= ($p['usuario_estado'] ?? 'activo') === 'activo' ? 'Activa' : 'Inactiva' ?>
                </span>
            <?php else: ?>
                <span class="account-chip inactive"><i class="fa-solid fa-user-slash"></i> Sin cuenta</span>
            <?php endif; ?>
        </td>
        <td><?= htmlspecialchars($p['fecha_nacimiento']) ?></td>
        <td>
            <div class="actions">
                <a class="btn btn-info" href="index.php?controller=historial&action=ver&paciente_id=<?= $p['id'] ?>" title="Ver historial clínico">
                    <i class="fa-solid fa-file-medical"></i>
                </a>
                <a class="btn btn-secondary" href="index.php?controller=paciente&action=editar&id=<?= $p['id'] ?>">
                    <i class="fa-solid fa-pen"></i>
                </a>
                <a class="btn btn-danger" href="index.php?controller=paciente&action=eliminar&id=<?= $p['id'] ?>" data-confirm="¿Eliminar este paciente?">
                    <i class="fa-solid fa-rotate"></i>
                </a>
            </div>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody></table></div>
    <?php else: ?><div class="empty-box"><p>No hay pacientes registrados.</p></div><?php endif; ?>
</div>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
