**Vista:** `views/clientes/mis_compras.php`
**Controlador:** `controllers/clientes/ClienteController.php` → acción `solicitarDevolucion`
**Modelo:** `models/Devolucion.php`

1. Controlador verifica sesión y if ((int)$_SESSION['user_rol'] !== 3) retorna error (solo clientes).
2. Vista muestra compras del cliente. Cada compra con botón "Solicitar devolución" si tiene menos de 30 días y no tiene devolución pendiente/aceptada (validación en vista con PHP).
3. Click abre modal que ejecuta AJAX para obtener detalle de la venta. Muestra productos con checkboxes e inputs de cantidad.
4. Cliente selecciona productos con checkboxes, especifica cantidad a devolver (input type="number" con max=cantidad_comprada), ingresa motivo en textarea.
5. JavaScript valida: al menos un producto seleccionado con filter() y length > 0, cantidades válidas con every(), motivo no vacío con trim(). Si falla, muestra toast error y return.
6. Crea FormData con action='solicitarDevolucion', id_venta, motivo, items en JSON. Ejecuta fetch POST.
7. Controlador obtiene id_venta (int), motivo con trim(), items con json_decode(). Valida if (!$id_venta || !$motivo || empty($items)) retorna error.
8. Ejecuta `obtener($id_venta)` para verificar venta. Valida if (!$venta || (int)$venta['id_cliente'] !== (int)$_SESSION['user_id']) retorna error 'Venta no autorizada'. Valida if ($venta['estado'] !== 'Completada') retorna error.
9. Ejecuta `detalle($id_venta)` y crea mapa con array_column($detalle, 'cantidad', 'id_producto'). Foreach items: valida if (!isset($mapa[$item['id_producto']]) || $item['cantidad'] > $mapa[$item['id_producto']]) retorna error 'Cantidad supera lo comprado'.
10. Calcula total con array_sum() y array_map(). Try-catch: llama `crear()` del modelo Devolucion con id_venta, motivo, total, id_usuario.
11. Modelo `crear()` inicia beginTransaction(). INSERT en devoluciones con id_venta, fecha=NOW(), motivo, total_devolucion, id_usuario, estado='Pendiente'. Obtiene id_devolucion con lastInsertId().
12. Foreach items: INSERT en detalledevolucion con id_devolucion, id_producto, cantidad, precio_unitario. Ejecuta commit() y retorna id_devolucion.
13. Fetch recibe JSON: if (d.ok) muestra toast success y actualiza vista mostrando badge "Devolución pendiente" en la compra.
