<h2>Registrar Médico</h2>

<form method="POST" action="index.php?controller=medico&action=guardar">

<label>Nombre</label>
<input type="text" name="nombre" required>

<br>

<label>Apellido</label>
<input type="text" name="apellido" required>

<br>

<label>Especialidad</label>
<input type="text" name="especialidad" required>

<br>

<label>Telefono</label>
<input type="text" name="telefono">

<br>

<label>Email</label>
<input type="email" name="email">

<br><br>

<button type="submit">Guardar</button>

</form>