**Vista:** `views/admin/dashboard.php` y `views/admin/inventario.php`  
**Modelo:** `models/Producto.php`

Dashboard ejecuta `totales()` que hace SELECT con SUM(stock=0) AS agotados, SUM(stock>0 AND stock<=stock_minimo) AS criticos. Widget muestra badge rojo con agotados y amarillo con críticos. Vista inventario renderiza tabla con condicional: if stock === 0 aplica clase roja y badge "Agotado", else if stock <= stock_minimo aplica clase amarilla y badge "Stock bajo". Click en widget redirige a inventario.php con filtro=critico. JavaScript filtra tabla mostrando solo productos con stock <= stock_minimo.
