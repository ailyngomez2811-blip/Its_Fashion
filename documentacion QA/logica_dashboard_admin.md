**Vista:** `views/admin/dashboard.php`
**Controlador:** Múltiples controladores para diferentes KPIs
**Modelos:** `Venta.php`, `Caja.php`, `Producto.php`, `Devolucion.php`

1. Al cargar vista, JavaScript envía múltiples peticiones AJAX con fetch() para obtener KPIs del sistema.
2. Para ventas: ejecuta `totales()` del modelo Venta que hace SELECT COUNT(*) AS total, SUM(total) AS monto, SUM(CASE WHEN DATE(fecha)=CURDATE() THEN 1 ELSE 0 END) AS hoy FROM venta. Retorna array con totales.
3. Para caja: ejecuta `cajaActiva()` que hace SELECT WHERE estado='Abierta' LIMIT 1. If existe, muestra quién la abrió y saldo actual calculado con saldo_inicial + total_ingresos - total_egresos.
4. Para productos: ejecuta `totales()` que hace SELECT COUNT(*) AS total, SUM(estado='Activo') AS activos, SUM(stock=0) AS agotados, SUM(stock>0 AND stock<=stock_minimo) AS criticos FROM productos.
5. Para devoluciones: ejecuta `pendientes()` que hace SELECT COUNT(*) WHERE estado='Pendiente'. Muestra badge con número.
6. JavaScript renderiza KPIs en cards con los datos recibidos. Usa toLocaleString() para formatear números. Muestra gráfica de ventas últimos 7 días con datos agrupados por fecha.
7. Tabla de últimas ventas hace SELECT con ORDER BY fecha DESC LIMIT 5. Tabla de productos más vendidos hace SELECT con JOIN a detalle_venta, GROUP BY producto, ORDER BY SUM(cantidad) DESC LIMIT 5.
8. setInterval() ejecuta actualización automática cada 5 minutos (300000ms) recargando los KPIs con fetch.
