**Vista:** `views/admin/dashboard.php`  
**Controlador:** Múltiples controladores para KPIs  
**Modelos:** `Venta.php`, `Caja.php`, `Producto.php`, `Devolucion.php`

Al cargar vista, JavaScript envía múltiples fetch para KPIs. Ventas: `totales()` hace SELECT COUNT(*), SUM(total), ventas hoy con CURDATE(). Caja: `cajaActiva()` hace SELECT WHERE estado='Abierta', muestra saldo calculado. Productos: `totales()` hace SELECT COUNT, activos, agotados, críticos. Devoluciones: `pendientes()` hace SELECT COUNT WHERE estado='Pendiente'. JavaScript renderiza cards con datos, usa toLocaleString() para formatear. Gráfica ventas últimos 7 días. Tablas: últimas ventas LIMIT 5, productos más vendidos con JOIN y GROUP BY. setInterval() actualiza cada 5 minutos.
