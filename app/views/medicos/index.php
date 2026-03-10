<h2>Lista de Médicos</h2>

<a href="index.php?controller=medico&action=crear">Nuevo Médico</a>

<table border="1">

<tr>
<th>ID</th>
<th>Nombre</th>
<th>Apellido</th>
<th>Especialidad</th>
<th>Telefono</th>
<th>Email</th>
</tr>

<?php foreach($medicos as $m): ?>

<tr>

<td><?= $m['id'] ?></td>
<td><?= $m['nombre'] ?></td>
<td><?= $m['apellido'] ?></td>
<td><?= $m['especialidad'] ?></td>
<td><?= $m['telefono'] ?></td>
<td><?= $m['email'] ?></td>

</tr>

<?php endforeach; ?>

</table>