**Vista:** `views/admin/compras.php`
**Controlador:** `controllers/admin/CompraController.php` → acción `crear`
**Modelo:** `models/Compra.php`

1. Controlador verifica sesión y if ((int)$_SESSION['user_rol'] !== 1) retorna error (solo admin).
2. Click en "Nueva compra" abre modal. Select de proveedores carga lista de proveedores activos. Desplegable de productos muestra todos los productos del sistema.
3. Al seleccionar producto, JavaScript autocompleta precio_compra sugerido (60% del precio_venta), pero es editable. Input de cantidad. Productos se acumulan en array carrito mostrando: producto, cantidad, precio_unitario, subtotal.
4. JavaScript calcula total automáticamente con reduce() sumando subtotales. Al confirmar, crea FormData con id_proveedor, items en JSON, ejecuta fetch POST a action=crear.
5. Controlador obtiene id_proveedor con cast (int) y ?? 0. Decodifica items con json_decode(). Valida if (!$id_proveedor) retorna error. Valida if (empty($items)) retorna error.
6. Calcula total con array_sum() y array_map(). Try-catch: llama `crear($datos, $items)`, retorna JSON ok:true con id_compra; catch retorna ok:false.
7. Modelo `crear()` inicia beginTransaction(). INSERT en tabla compras con fecha=NOW(), total, id_proveedor, id_usuario. Obtiene id_compra con lastInsertId().
8. Foreach items: calcula subtotal = cantidad * precio_unitario. INSERT en detallecompra con id_compra, id_producto, cantidad, precio_unitario, subtotal.
9. UPDATE productos SET stock = stock + cantidad WHERE id_producto. INSERT en inventario con fecha_registro=NOW(), stock_disponible=(SELECT stock), tipo_movimiento='Entrada', id_producto.
10. Ejecuta commit(). Fetch recibe JSON: if (d.ok) muestra toast success y recarga página.
