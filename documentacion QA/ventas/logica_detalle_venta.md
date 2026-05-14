**Vista:** `views/admin/ventas.php` o modal  
**Controlador:** `controllers/admin/VentaController.php` → acción `detalle`  
**Modelo:** `models/Venta.php`

Click "Ver detalle" ejecuta fetch GET a action=detalle&id=X. Controlador llama `obtener()` que hace SELECT venta JOIN usuario, y `detalle()` que hace SELECT detalle_venta JOIN productos. Retorna JSON con venta (fecha, total, cliente, empleado, metodo_pago, estado) y detalle (array productos con nombre, talla, color, cantidad, precio_unitario, subtotal). JavaScript renderiza modal con tabla de productos y total.
