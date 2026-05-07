**Vista:** `views/admin/productos.php`
**Controlador:** `controllers/admin/ProductoController.php` → acción `buscar`
**Modelo:** `models/Producto.php`

1. Input de búsqueda con oninput que ejecuta función JavaScript con debounce de 300ms para evitar múltiples peticiones.
2. JavaScript obtiene valor del input con trim(). If valor vacío ejecuta fetch a action=listar para mostrar todos. Else ejecuta fetch GET a action=buscar&q=valor.
3. Controlador obtiene q con trim() y ?? ''. Llama `buscar($q)` del modelo. Este hace SELECT con WHERE nombre LIKE :q OR talla LIKE :q OR color LIKE :q. Usa "%{$q}%" para búsqueda parcial. ORDER BY nombre ASC.
4. Retorna array de productos que coinciden. JavaScript renderiza tabla actualizando solo el tbody sin recargar página completa.
