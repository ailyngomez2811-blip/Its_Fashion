**Vista:** `views/admin/reportes.php`
**Controlador:** Endpoint específico para reportes
**Modelo:** `models/Venta.php`

1. Vista incluye filtros con select de período: Hoy, Esta semana, Este mes, Rango personalizado. If rango personalizado: muestra inputs type="date" para fecha_inicio y fecha_fin.
2. Al seleccionar período, JavaScript calcula fechas correspondientes. Hoy: fecha_inicio=fecha_fin=CURDATE(). Esta semana: fecha_inicio=inicio de semana, fecha_fin=hoy. Este mes: fecha_inicio=primer día del mes, fecha_fin=hoy.
3. Ejecuta fetch POST con fechas a endpoint de reportes. Modelo ejecuta SELECT con WHERE fecha BETWEEN :inicio AND :fin para obtener: SUM(total) AS total_vendido, COUNT(*) AS num_transacciones, AVG(total) AS ticket_promedio.
4. Para ventas por método: SELECT metodo_pago, SUM(total) FROM venta WHERE fecha BETWEEN GROUP BY metodo_pago. Para ventas por día: SELECT DATE(fecha) AS dia, SUM(total) FROM venta WHERE fecha BETWEEN GROUP BY DATE(fecha) ORDER BY dia.
5. JavaScript recibe JSON y renderiza: KPIs en cards (total vendido con toLocaleString(), número de ventas, ticket promedio). Gráfica de barras con Chart.js usando datos de ventas por día en eje X y montos en eje Y.
6. Tabla con detalle de ventas más grandes: SELECT con ORDER BY total DESC LIMIT 10. Muestra: fecha, cliente, total, método de pago.
7. Botón "Exportar" ejecuta función que genera CSV o PDF con los datos del reporte (implementación puede variar).
