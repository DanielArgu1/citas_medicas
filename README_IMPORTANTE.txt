PASOS RÁPIDOS
1. Importa basedatos.sql en MySQL.
2. Ajusta app/config/database.php si tu usuario o contraseña de MySQL son diferentes.
3. Abre public/index.php en tu servidor local.

USUARIOS DE PRUEBA
- admin@admin.com
- recepcion@clinica.com
- dr.flores@hospital.com
Contraseña para todos: 123456

NOTIFICACIONES
- Cada cita creada, actualizada, cancelada o completada se registra en:
  app/logs/notificaciones.log
- También intenta enviarse por mail() si tu PHP tiene correo configurado.

MÉDICOS
- Cuando el admin registra un médico, se crea automáticamente su usuario del sistema.
- El mensaje de éxito muestra la contraseña temporal.
- Al editar un médico puedes marcar la opción para regenerar una nueva contraseña temporal.

VALIDACIONES
- Cédula hondureña: 0000-0000-00000
- Teléfono hondureño: 0000-0000
- Los campos aceptan solo números en esas entradas.
