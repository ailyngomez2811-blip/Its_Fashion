**Vista:** `views/admin/proveedores.php`
**Controlador:** `controllers/admin/ProveedorController.php` → acción `buscar`
**Modelo:** `models/Proveedor.php`

1. Input de búsqueda con oninput ejecuta función con debounce. JavaScript obtiene valor y ejecuta fetch GET a action=buscar&q=valor.
2. Modelo hace SELECT con WHERE nombre LIKE :q OR contacto LIKE :q OR telefono LIKE :q. Usa "%{$q}%" para búsqueda parcial.
3. JavaScript renderiza tabla con resultados filtrados sin recargar.
