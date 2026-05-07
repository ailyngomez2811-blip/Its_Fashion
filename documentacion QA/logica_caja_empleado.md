**Vista:** `views/empleado/caja.php`
**Controlador:** `controllers/admin/CajaController.php` (mismo que admin)
**Modelo:** `models/Caja.php`

1. Empleado (rol 2) tiene acceso a las mismas funciones de caja que admin: abrir, cerrar, movimientos.
2. Controlador verifica sesión con in_array((int)$_SESSION['user_rol'], [1, 2]) permitiendo ambos roles.
3. Vista es idéntica a admin/caja.php pero con menú lateral limitado. Empleado puede abrir caja con su id_usuario, registrar movimientos, cerrar caja que él abrió.
4. Historial de cajas muestra todas las cajas (no solo las del empleado) pero solo admin puede ver el historial completo desde su vista.
