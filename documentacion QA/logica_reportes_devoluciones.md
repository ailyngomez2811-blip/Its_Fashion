**Vista:** `views/admin/reportes.php` sección devoluciones
**Modelo:** `models/Devolucion.php`

1. KPIs de devoluciones: `totales()` hace SELECT COUNT(*) AS total, SUM(total_devolucion) AS monto FROM devoluciones WHERE estado='Aceptada'. Muestra total de devoluciones aceptadas y monto total reembolsado.
2. Tasa de devolución: calcula (devoluciones aceptadas / total ventas) * 100. Muestra porcentaje.
3. Tabla de devoluciones por período: SELECT con filtro de fechas WHERE fecha BETWEEN. Muestra: fecha, cliente, venta asociada, motivo, monto, estado.
4. Gráfica de devoluciones por estado: SELECT estado, COUNT(*) FROM devoluciones GROUP BY estado. Muestra distribución en gráfica de dona: Pendiente, Aceptada, Rechazada.
5. Productos más devueltos: SELECT de detalledevolucion JOIN productos JOIN devoluciones WHERE estado='Aceptada' GROUP BY id_producto ORDER BY SUM(cantidad) DESC LIMIT 10.
