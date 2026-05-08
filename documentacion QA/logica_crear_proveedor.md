**Vista:** `views/admin/proveedores.php`  
**Controlador:** `controllers/admin/ProveedorController.php` → acción `crear`  
**Modelo:** `models/Proveedor.php`

Tabla renderizada con foreach de `$proveedores`. Click "Nuevo proveedor" abre modal con inputs: nombre, contacto, telefono, email, direccion. JavaScript valida campos obligatorios (nombre, telefono). Ejecuta fetch POST con action=crear. Controlador obtiene datos con trim(), valida nombre y telefono no vacíos. Modelo hace INSERT INTO proveedor con estado='Activo'. Retorna JSON. JavaScript muestra toast y recarga tabla.
