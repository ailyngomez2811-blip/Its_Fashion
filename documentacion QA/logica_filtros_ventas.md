**Vista:** `views/admin/ventas.php`
**Controlador:** Filtrado en cliente con JavaScript
**Modelo:** `models/Venta.php`

1. Vista carga todas las ventas con `listar()` que hace SELECT de venta JOIN usuario ORDER BY fecha DESC.
2. Filtros en vista: select de método de pago (Todos, Efectivo, Transferencia), select de estado (Todos, Completada, Cancelada), inputs de rango de fechas.
3. JavaScript escucha eventos onchange de los filtros. Al cambiar cualquier filtro, ejecuta función que filtra el array de ventas en memoria usando filter().
4. Filter valida: if método seleccionado !== 'Todos' && venta.metodo_pago !== método, retorna false. If estado seleccionado !== 'Todos' && venta.estado !== estado, retorna false. If fecha_inicio && venta.fecha < fecha_inicio, retorna false. If fecha_fin && venta.fecha > fecha_fin, retorna false.
5. Renderiza tabla solo con ventas que pasaron todos los filtros. Muestra contador "Mostrando X de Y ventas".
