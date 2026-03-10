<h2>Registrar Paciente</h2>

<form method="POST" action="index.php?controller=paciente&action=guardar">

<label>Nombre</label>
<input type="text" name="nombre" required>

<br>

<label>Apellido</label>
<input type="text" name="apellido" required>

<br>

<label>Cedula</label>
<input type="text" name="cedula" required>

<br>

<label>Telefono</label>
<input type="text" name="telefono">

<br>

<label>Email</label>
<input type="email" name="email">

<br>

<label>Fecha nacimiento</label>
<input type="date" name="fecha_nacimiento">

<br><br>

<button type="submit">Guardar</button>

</form>