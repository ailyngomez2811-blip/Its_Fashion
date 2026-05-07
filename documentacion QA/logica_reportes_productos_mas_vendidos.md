**Vista:** `views/admin/reportes.php` o dashboard
**Modelo:** `models/Venta.php` con JOIN a detalle_venta

1. Consulta hace SELECT de productos JOIN detalle_venta JOIN venta. Filtra por rango de fechas si se especifica WHERE venta.fecha BETWEEN.
2. GROUP BY id_producto, nombre, talla, color. SELECT SUM(detalle_venta.cantidad) AS total_vendido, COUNT(DISTINCT venta.id_venta) AS num_ventas, SUM(detalle_venta.cantidad * detalle_venta.precio_unitario) AS ingresos_generados.
3. ORDER BY total_vendido DESC LIMIT 10 para obtener top 10.
4. JavaScript renderiza tabla con: posición (#), producto (nombre + talla + color), unidades vendidas, número de ventas, ingresos generados.
5. Opcionalmente muestra gráfica de barras con Chart.js usando nombres de productos en eje X y unidades vendidas en eje Y.
