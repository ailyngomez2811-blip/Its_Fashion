**Vista:** `views/admin/clientes.php`  
**Controlador:** `controllers/admin/ClienteController.php`  
**Modelo:** `models/Cliente.php`

Tabla renderizada con foreach de `$clientes` obtenidos con query: SELECT con LEFT JOIN venta y devoluciones, COUNT(DISTINCT) para contar compras y devoluciones, GROUP BY id_usuario. Cada fila tiene `data-search` y `data-estado`. Input búsqueda y select estado ejecutan `filterTable()` que itera filas ocultando las que no coinciden. NO usa DataTables, filtrado manual JavaScript. NO hay paginación.

Click en fila ejecuta `openDetalle()` pasando objeto JSON del cliente. Modal con tabs: Información, Compras, Devoluciones. Tabs ejecutan fetch a action=compras o action=devoluciones que retornan JSON renderizado con JavaScript. Toggle estado ejecuta fetch a action=cambiarEstado que hace UPDATE sin recargar página.
