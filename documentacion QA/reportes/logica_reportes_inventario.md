**Vista:** `views/admin/reportes.php` sección inventario  
**Modelo:** `models/Producto.php`, `models/Inventario.php`

KPIs: total productos, activos, agotados, críticos con `totales()`. Productos más vendidos: SELECT detalle_venta JOIN productos GROUP BY id_producto ORDER BY SUM(cantidad) DESC LIMIT 10. Productos menos rotación: ORDER BY SUM(cantidad) ASC. Gráfica movimientos: SELECT tipo_movimiento, COUNT(*) FROM inventario WHERE fecha BETWEEN GROUP BY tipo_movimiento. Muestra distribución Entradas vs Salidas. Botón exportar genera Excel o PDF con listado completo.
