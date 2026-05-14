**Vista:** `views/admin/reportes.php`  
**Controlador:** Endpoint específico para reportes  
**Modelo:** `models/Venta.php`

Vista con filtros: select período (Hoy, Esta semana, Este mes, Rango personalizado). Si rango personalizado: inputs type="date". JavaScript calcula fechas según período. Ejecuta fetch POST con fechas. Modelo hace SELECT WHERE fecha BETWEEN para: SUM(total), COUNT(*), AVG(total). Ventas por método: SELECT metodo_pago, SUM(total) GROUP BY metodo_pago. Ventas por día: SELECT DATE(fecha), SUM(total) GROUP BY DATE(fecha). JavaScript renderiza KPIs en cards con toLocaleString(), gráfica barras con Chart.js, tabla ventas más grandes ORDER BY total DESC LIMIT 10. Botón "Exportar" genera CSV o PDF.
