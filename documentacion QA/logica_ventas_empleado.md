**Vista:** `views/empleado/ventas.php`
**Controlador:** `controllers/admin/VentaController.php` (mismo que admin)
**Modelo:** `models/Venta.php`

1. Empleado (rol 2) tiene acceso completo a crear ventas. Controlador verifica con in_array((int)$_SESSION['user_rol'], [1, 2]).
2. Vista y funcionalidad idénticas a admin/ventas.php. Puede buscar clientes, seleccionar productos, registrar ventas en efectivo o transferencia.
3. Ventas quedan registradas con id_usuario del empleado en $_SESSION['user_id']. Historial muestra todas las ventas del sistema, no solo las del empleado.
