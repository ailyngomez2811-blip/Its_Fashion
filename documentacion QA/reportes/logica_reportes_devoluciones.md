**Vista:** `views/admin/reportes.php` sección devoluciones  
**Modelo:** `models/Devolucion.php`

KPIs: `totales()` hace SELECT COUNT(*), SUM(total_devolucion) FROM devoluciones WHERE estado='Aceptada'. Tasa devolución: (devoluciones / total ventas) * 100. Tabla devoluciones por período: SELECT WHERE fecha BETWEEN. Gráfica por estado: SELECT estado, COUNT(*) GROUP BY estado, muestra dona con Pendiente, Aceptada, Rechazada. Productos más devueltos: SELECT detalledevolucion JOIN productos JOIN devoluciones WHERE estado='Aceptada' GROUP BY id_producto ORDER BY SUM(cantidad) DESC LIMIT 10.
