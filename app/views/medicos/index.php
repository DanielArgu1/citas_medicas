<?php require_once __DIR__ . '/../layout/header.php'; ?>
<div class="table-card">
    <div class="table-header"><h2>Gestión de médicos</h2><a href="index.php?controller=medico&action=crear" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Nuevo médico</a></div>
    <div class="toolbar"><div class="search-box"><i class="fa-solid fa-magnifying-glass"></i><input type="text" placeholder="Buscar por nombre, especialidad, teléfono o email..." data-search-target="tabla-medicos"></div></div>
    <?php if (!empty($medicos)): ?>
    <div class="table-responsive"><table><thead><tr><th>ID</th><th>Nombre completo</th><th>Especialidad</th><th>Teléfono</th><th>Email</th><th>Acciones</th></tr></thead><tbody id="tabla-medicos">
    <?php foreach ($medicos as $m): ?>
    <tr>
        <td><?= htmlspecialchars($m['id']) ?></td>
        <td><?= htmlspecialchars($m['nombre'].' '.$m['apellido']) ?></td>
        <td><?= htmlspecialchars($m['especialidad']) ?></td>
        <td><?= htmlspecialchars($m['telefono']) ?></td>
        <td><?= htmlspecialchars($m['email']) ?></td>
        <td><div class="actions"><a class="btn btn-secondary" href="index.php?controller=medico&action=editar&id=<?= $m['id'] ?>"><i class="fa-solid fa-pen"></i></a><a class="btn btn-danger" href="index.php?controller=medico&action=eliminar&id=<?= $m['id'] ?>" data-confirm="¿Eliminar este médico?"><i class="fa-solid fa-trash"></i></a></div></td>
    </tr>
    <?php endforeach; ?>
    </tbody></table></div>
    <?php else: ?><div class="empty-box"><p>No hay médicos registrados.</p></div><?php endif; ?>
</div>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
