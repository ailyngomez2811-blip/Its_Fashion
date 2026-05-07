**Vista:** `views/admin/proveedores.php`
**Controlador:** `controllers/admin/ProveedorController.php` → acción `crear`
**Modelo:** `models/Proveedor.php`

1. Click en "Nuevo proveedor" abre modal con inputs: nombre, contacto, telefono, email, direccion.
2. JavaScript valida campos obligatorios (nombre, telefono). Crea FormData con action='crear' y todos los campos. Ejecuta fetch POST.
3. Controlador obtiene datos con trim(): nombre, contacto, telefono, email, direccion. Valida if (empty($nombre) || empty($telefono)) retorna error.
4. Modelo hace INSERT INTO proveedor con todos los campos, estado='Activo'. Retorna boolean.
5. Fetch muestra toast y recarga tabla.
