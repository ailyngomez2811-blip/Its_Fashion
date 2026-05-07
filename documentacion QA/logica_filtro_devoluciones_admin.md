**Vista:** `views/admin/devoluciones.php`
**Controlador:** Filtrado en cliente
**Modelo:** `models/Devolucion.php`

1. Vista carga todas las devoluciones con `listar()`. Select de filtro por estado: Todos, Pendiente, Aceptada, Rechazada.
2. JavaScript escucha onchange del select. Ejecuta filter() sobre array de devoluciones. If estado !== 'Todos' && devolucion.estado !== estado, retorna false.
3. Renderiza tabla con devoluciones filtradas. Devoluciones pendientes se destacan con fondo amarillo claro y muestran botones de acción.
