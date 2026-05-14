**Vista:** `views/admin/productos.php`  
**Controlador:** `controllers/admin/ProductoController.php` → acción `crear`  
**Modelo:** `models/Producto.php`

Tabla renderizada con foreach de `$productos` obtenidos con query LEFT JOIN categoria. Cada fila tiene atributos: `data-search`, `data-cat`, `data-estado`, `data-stock`. Tres filtros: input búsqueda, select categoría, select estado. Función `filterTable()` itera filas y oculta las que no coinciden evaluando múltiples condiciones. NO usa DataTables, filtrado manual JavaScript. NO hay paginación.

Click "Nuevo producto" abre modal. Submit ejecuta fetch POST con action=crear. Controlador verifica rol admin, valida campos obligatorios y que precio_venta > precio_compra. Llama `crear()` que hace INSERT usando foreach para bindValue. Retorna JSON. JavaScript muestra toast y ejecuta `location.reload()` recargando página completa.
