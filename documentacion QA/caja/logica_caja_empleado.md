**Vista:** `views/empleado/caja.php`  
**Controlador:** `controllers/admin/CajaController.php` (mismo que admin)  
**Modelo:** `models/Caja.php`

Empleado (rol 2) tiene acceso a mismas funciones de caja que admin: abrir, cerrar, movimientos. Controlador verifica sesión con `in_array((int)$_SESSION['user_rol'], [1, 2])` permitiendo ambos roles. Vista idéntica a admin/caja.php pero con menú lateral limitado. Empleado puede abrir caja con su id_usuario, registrar movimientos, cerrar caja que él abrió.
