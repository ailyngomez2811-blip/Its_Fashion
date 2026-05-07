**Vista:** `views/admin/devoluciones.php`
**Controlador:** `controllers/admin/DevolucionController.php` → acción `aprobar` o `rechazar`
**Modelo:** `models/Devolucion.php`

1. Controlador verifica sesión y if ((int)$_SESSION['user_rol'] !== 1) retorna error (solo admin).
2. Vista muestra tabla con todas las devoluciones. Devoluciones con estado='Pendiente' tienen botones "Aprobar" y "Rechazar".
3. Click en botón abre modal mostrando detalle: cliente, fecha compra, productos a devolver con cantidades, motivo. Si es rechazar, muestra textarea obligatorio para justificación.
4. Al confirmar aprobar: crea FormData con action='aprobar', id. Fetch POST. Al confirmar rechazar: FormData con action='rechazar', id.
5. Controlador case 'aprobar': obtiene id con cast (int). Valida if (!$id) retorna error. Try-catch: llama `aceptar($id, $_SESSION['user_id'])`, retorna JSON ok:true o ok:false según resultado.
6. Modelo `aceptar()` hace SELECT estado, id_venta, total_devolucion WHERE id_devolucion. If (!$dev || $dev['estado'] !== 'Pendiente') retorna false.
7. Inicia beginTransaction(). UPDATE devoluciones SET estado='Aceptada', fecha_resolucion=NOW(), id_admin WHERE id_devolucion.
8. Ejecuta `detalle($id_devolucion)` que hace SELECT de detalledevolucion JOIN productos. Foreach items: UPDATE productos SET stock = stock + cantidad. INSERT en inventario con tipo_movimiento='Entrada', stock_disponible=(SELECT stock).
9. SELECT metodo_pago FROM venta WHERE id_venta. SELECT id_caja FROM caja WHERE estado='Abierta'. If existe caja: INSERT en movimientos_caja con tipo='Egreso', monto=total_devolucion, concepto="Devolución #X (método)". UPDATE caja SET total_egresos = total_egresos + monto.
10. Ejecuta commit(). Case 'rechazar': UPDATE devoluciones SET estado='Rechazada', fecha_resolucion=NOW(), id_admin WHERE id_devolucion AND estado='Pendiente'. Retorna rowCount() > 0.
11. Fetch recibe JSON: if (d.ok) muestra toast y actualiza tabla sin recargar.
