**Vista:** `views/admin/ventas.php` y `views/empleado/ventas.php`
**Controlador:** `controllers/admin/VentaController.php` → acción `crear`
**Modelo:** `models/Venta.php`

1. Click en "Nueva venta" abre modal con formulario. Búsqueda de cliente envía AJAX a action=buscarCliente, ejecuta `buscarCliente($q)` que hace SELECT con LIKE en nombre, apellido y email WHERE id_rol=3 AND estado='Activo' LIMIT 10. Si no se selecciona cliente, id_cliente queda en 0 (venta mostrador).
2. Select de método de pago con opciones 'Efectivo' o 'Transferencia bancaria'. Desplegable de productos activos con stock > 0.
3. JavaScript valida cantidad <= stock antes de agregar al carrito. Productos se acumulan en array temporal visible en el modal.
4. Al confirmar, JavaScript calcula total con array_sum() y map(), crea FormData con metodo_pago, id_cliente, items en JSON, ejecuta fetch POST a action=crear.
5. Controlador verifica sesión y rol con in_array([1,2]). Decodifica items con json_decode(). Valida if (empty($items)) retorna error. Valida if (!in_array($metodo, ['Efectivo','Transferencia bancaria'])) retorna error.
6. If método es 'Efectivo', ejecuta `cajaAbierta()` que hace SELECT WHERE estado='Abierta', if retorna false hace echo error y exit.
7. Calcula total con array_sum() y array_map(). Try-catch: llama `crear($datos, $items)`, retorna JSON ok:true con id_venta; catch retorna ok:false con mensaje.
8. Modelo `crear()` inicia beginTransaction(). Foreach items: hace SELECT stock WHERE id_producto AND estado='Activo', if (!$prod || $prod['stock'] < $item['cantidad']) hace throw Exception.
9. INSERT en tabla venta con fecha=NOW(), total, id_cliente (null si es 0), metodo_pago, estado='Completada', id_usuario. Obtiene id_venta con lastInsertId().
10. Foreach items: INSERT en detalle_venta con id_venta, id_producto, cantidad, precio_unitario. UPDATE productos SET stock = stock - cantidad. INSERT en inventario con fecha_registro=NOW(), stock_disponible=(SELECT stock), tipo_movimiento='Salida', id_producto.
11. SELECT caja WHERE estado='Abierta'. If existe caja: INSERT en movimientos_caja con id_caja, tipo='Ingreso', monto=total, concepto="Venta #X (método)", fecha=NOW(). UPDATE caja SET total_ingresos = total_ingresos + monto.
12. Ejecuta commit(). Fetch recibe JSON: if (d.ok) muestra toast success y recarga página con location.reload().
