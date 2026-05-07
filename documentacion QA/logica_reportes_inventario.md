**Vista:** `views/admin/reportes.php` sección inventario
**Modelo:** `models/Producto.php`, `models/Inventario.php`

1. Sección de reportes de inventario muestra KPIs: total de productos, productos activos, productos agotados, productos en stock crítico. Usa `totales()` del modelo Producto.
2. Tabla de productos más vendidos: SELECT de detalle_venta JOIN productos GROUP BY id_producto ORDER BY SUM(cantidad) DESC LIMIT 10. Muestra: producto, talla, color, cantidad vendida, stock actual.
3. Tabla de productos con menos rotación: SELECT similar pero ORDER BY SUM(cantidad) ASC. Identifica productos que no se venden.
4. Gráfica de movimientos de inventario por tipo: SELECT tipo_movimiento, COUNT(*) FROM inventario WHERE fecha BETWEEN GROUP BY tipo_movimiento. Muestra distribución de Entradas vs Salidas.
5. Botón exportar genera Excel o PDF con listado completo de productos y sus stocks.
