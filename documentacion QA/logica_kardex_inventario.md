**Vista:** `views/admin/inventario.php`  
**Controlador:** Consulta directa desde vista  
**Modelo:** `models/Inventario.php`

Tabla productos con botón "Ver kardex". Click ejecuta fetch GET que llama `listarHistorial()` filtrando por id_producto. Modelo hace SELECT inventario JOIN productos ORDER BY fecha_registro DESC. Movimientos se registran automáticamente: Compra ejecuta INSERT tipo='Entrada', Venta INSERT tipo='Salida', Devolución aceptada INSERT tipo='Entrada'. JavaScript renderiza modal con tabla: fecha/hora, tipo (badge verde Entrada, rojo Salida), cantidad, stock resultante. Incluye filtros por rango fechas y tipo con JavaScript.
