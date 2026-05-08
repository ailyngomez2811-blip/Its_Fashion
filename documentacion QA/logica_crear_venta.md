**Vista:** `views/admin/ventas.php` y `views/empleado/ventas.php`  
**Controlador:** `controllers/admin/VentaController.php` → acción `crear`  
**Modelo:** `models/Venta.php`

Click "Nueva venta" abre modal. Búsqueda de cliente ejecuta AJAX a action=buscarCliente que hace SELECT con LIKE WHERE id_rol=3 AND estado='Activo' LIMIT 10. Si no selecciona cliente: id_cliente=0 (mostrador). Select método pago: 'Efectivo' o 'Transferencia'. Desplegable productos activos con stock > 0. JavaScript valida cantidad <= stock, acumula productos en array carrito. Al confirmar calcula total, ejecuta fetch POST con metodo_pago, id_cliente, items JSON.

Controlador verifica sesión rol [1,2], decodifica items, valida no vacíos y método válido. Si Efectivo: verifica `cajaAbierta()`. Modelo `crear()` inicia transaction, foreach items valida stock, hace INSERT venta, INSERT detalle_venta, UPDATE productos stock, INSERT inventario tipo='Salida'. Si hay caja abierta: INSERT movimientos_caja tipo='Ingreso', UPDATE caja total_ingresos. Ejecuta commit. JavaScript muestra toast y recarga página.
