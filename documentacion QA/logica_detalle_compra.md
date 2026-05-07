**Vista:** `views/admin/compras.php` o modal
**Controlador:** `controllers/admin/CompraController.php` → acción `detalle`
**Modelo:** `models/Compra.php`

1. Click en "Ver detalle" ejecuta fetch GET a action=detalle&id=X.
2. Controlador obtiene id (int). Llama `detalle($id)` que hace SELECT de detallecompra JOIN productos WHERE id_compra. Retorna array con: producto, talla, color, cantidad, precio_unitario, subtotal.
3. JavaScript renderiza modal con: encabezado con fecha y número de compra, proveedor, tabla de productos con columnas: producto, talla, color, cantidad, precio unitario, subtotal. Footer con total de la compra.
