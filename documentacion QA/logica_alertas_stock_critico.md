**Vista:** `views/admin/dashboard.php` y `views/admin/inventario.php`
**Modelo:** `models/Producto.php`

1. Dashboard ejecuta `totales()` que hace SELECT con SUM(stock=0) AS agotados, SUM(stock>0 AND stock<=stock_minimo) AS criticos FROM productos.
2. Widget muestra badge rojo con número de productos agotados y badge amarillo con productos en stock crítico.
3. Vista de inventario renderiza tabla con condicional: if (stock === 0) aplica clase CSS rojo y badge "Agotado". Else if (stock <= stock_minimo) aplica clase amarillo y badge "Stock bajo".
4. Click en widget redirige a inventario.php con parámetro filtro=critico. JavaScript filtra tabla mostrando solo productos con stock <= stock_minimo.
