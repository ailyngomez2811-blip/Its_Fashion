**Vista:** `views/admin/devoluciones.php`  
**Controlador:** Filtrado en cliente  
**Modelo:** `models/Devolucion.php`

Tabla renderizada con foreach de `$devoluciones` obtenidas con `listar()`. Select filtro por estado: Todos, Pendiente, Aceptada, Rechazada. JavaScript escucha onchange, ejecuta filter() sobre array evaluando estado. Renderiza tabla con devoluciones filtradas. Pendientes destacadas con fondo amarillo y botones de acción. NO usa DataTables, filtrado manual JavaScript.
