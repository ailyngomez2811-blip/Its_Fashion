**Vista:** `views/admin/ventas.php`  
**Controlador:** Filtrado en cliente con JavaScript  
**Modelo:** `models/Venta.php`

Tabla renderizada con foreach de `$ventas` obtenidas con `listar()` que hace SELECT venta JOIN usuario ORDER BY fecha DESC. Filtros: select método pago, select estado, inputs rango fechas. JavaScript escucha onchange, ejecuta función que filtra array en memoria con filter() evaluando: método, estado, fecha_inicio, fecha_fin. Renderiza tabla solo con ventas filtradas. Muestra contador "Mostrando X de Y ventas". NO usa DataTables, filtrado manual JavaScript.
