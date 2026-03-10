<h2>Lista de Pacientes</h2>

<a href="index.php?controller=paciente&action=crear">Nuevo Paciente</a>

<table border="1">

<tr>
<th>ID</th>
<th>Nombre</th>
<th>Apellido</th>
<th>Cedula</th>
<th>Telefono</th>
<th>Email</th>
</tr>

<?php foreach($pacientes as $p): ?>

<tr>
<td><?= $p['id'] ?></td>
<td><?= $p['nombre'] ?></td>
<td><?= $p['apellido'] ?></td>
<td><?= $p['cedula'] ?></td>
<td><?= $p['telefono'] ?></td>
<td><?= $p['email'] ?></td>
</tr>

<?php endforeach; ?>

</table>