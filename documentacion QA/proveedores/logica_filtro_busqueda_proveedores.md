**Vista:** `views/admin/proveedores.php`  
**Controlador:** `controllers/admin/ProveedorController.php` → acción `buscar`  
**Modelo:** `models/Proveedor.php`

Tabla renderizada con foreach de `$proveedores`. Input búsqueda con oninput ejecuta función con debounce. JavaScript ejecuta fetch GET a action=buscar&q=valor. Modelo hace SELECT WHERE nombre LIKE :q OR contacto LIKE :q OR telefono LIKE :q con "%{$q}%". Retorna JSON. JavaScript renderiza tabla con resultados sin recargar.
