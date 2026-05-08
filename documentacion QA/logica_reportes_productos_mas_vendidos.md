**Vista:** `views/admin/reportes.php` o dashboard  
**Modelo:** `models/Venta.php` con JOIN a detalle_venta

SELECT productos JOIN detalle_venta JOIN venta. Filtra WHERE venta.fecha BETWEEN si se especifica. GROUP BY id_producto, nombre, talla, color. SELECT SUM(cantidad) AS total_vendido, COUNT(DISTINCT id_venta) AS num_ventas, SUM(cantidad * precio_unitario) AS ingresos. ORDER BY total_vendido DESC LIMIT 10. JavaScript renderiza tabla con: posición, producto, unidades vendidas, número ventas, ingresos. Opcionalmente gráfica barras con Chart.js.
