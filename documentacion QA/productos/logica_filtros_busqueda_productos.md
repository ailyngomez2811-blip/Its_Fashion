**Vista:** `views/admin/productos.php`  
**Modelo:** `models/Producto.php`

Tabla renderizada con foreach. Cada fila tiene atributos: `data-search`, `data-cat`, `data-estado`, `data-stock`. Input búsqueda, select categoría y select estado ejecutan `filterTable()` en oninput/onchange. Función obtiene valores de los 3 controles, itera filas con `querySelectorAll('#table-body tr')` y oculta las que no coinciden evaluando múltiples condiciones con dataset. NO usa DataTables, filtrado manual JavaScript. NO hay paginación.
