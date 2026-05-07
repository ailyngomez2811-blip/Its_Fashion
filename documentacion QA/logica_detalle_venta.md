**Vista:** `views/admin/ventas.php` o modal
**Controlador:** `controllers/admin/VentaController.php` → acción `detalle`
**Modelo:** `models/Venta.php`

1. Click en botón "Ver detalle" ejecuta JavaScript que hace fetch GET a action=detalle&id=X.
2. Controlador obtiene id con cast (int) y ?? 0. Llama `obtener($id)` que hace SELECT de venta JOIN usuario para obtener datos de cliente y empleado. Llama `detalle($id)` que hace SELECT de detalle_venta JOIN productos.
3. Retorna JSON con dos objetos: venta (fecha, total, cliente_nombre, empleado, metodo_pago, estado) y detalle (array de productos con nombre, talla, color, cantidad, precio_unitario, subtotal).
4. JavaScript recibe JSON y renderiza modal mostrando: encabezado con fecha y número de venta, datos del cliente (o "Mostrador"), método de pago, tabla de productos con columnas: producto, talla, color, cantidad, precio unitario, subtotal. Footer con total general.
