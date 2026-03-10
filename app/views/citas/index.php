<h2>Lista de Citas</h2>

<a href="index.php?controller=cita&action=crear">Nueva Cita</a>

<table border="1">

<tr>
<th>ID</th>
<th>Paciente</th>
<th>Médico</th>
<th>Fecha</th>
<th>Inicio</th>
<th>Fin</th>
<th>Estado</th>
</tr>

<?php foreach($citas as $c): ?>

<tr>

<td><?= $c['id'] ?></td>
<td><?= $c['paciente'] ?></td>
<td><?= $c['medico'] ?></td>
<td><?= $c['fecha'] ?></td>
<td><?= $c['hora_inicio'] ?></td>
<td><?= $c['hora_fin'] ?></td>
<td><?= $c['estado'] ?></td>

</tr>

<?php endforeach; ?>

</table>