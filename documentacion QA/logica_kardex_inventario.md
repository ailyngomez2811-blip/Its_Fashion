**Vista:** `views/admin/inventario.php`
**Controlador:** No tiene controlador específico, se consulta directamente desde la vista
**Modelo:** `models/Inventario.php`

1. Vista de inventario muestra tabla de productos. Cada producto tiene botón "Ver kardex" con onclick que ejecuta función JavaScript.
2. JavaScript ejecuta fetch GET a un endpoint que llama `listarHistorial()` filtrando por id_producto (implementación puede variar).
3. Modelo `listarHistorial()` hace SELECT de tabla inventario JOIN productos con ORDER BY fecha_registro DESC LIMIT. Retorna array con: fecha_registro, stock_disponible, tipo_movimiento, nombre, talla, color del producto.
4. Los movimientos se registran automáticamente en otras operaciones: Compra ejecuta `registrarMovimiento($id_producto, 'Entrada', stock_final)` que hace INSERT con fecha_registro=NOW(), stock_disponible, tipo_movimiento='Entrada', id_producto.
5. Venta ejecuta INSERT con tipo_movimiento='Salida'. Devolución aceptada ejecuta INSERT con tipo_movimiento='Entrada'.
6. JavaScript recibe JSON y renderiza tabla en modal mostrando: fecha/hora, tipo (badge verde para Entrada, rojo para Salida), cantidad (calculada por diferencia de stocks), stock resultante.
7. Tabla incluye filtros por rango de fechas y tipo de movimiento usando JavaScript para filtrar el array de resultados.
