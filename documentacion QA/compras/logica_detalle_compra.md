**Vista:** `views/admin/compras.php` o modal  
**Controlador:** `controllers/admin/CompraController.php` → acción `detalle`  
**Modelo:** `models/Compra.php`

Click "Ver detalle" ejecuta fetch GET a action=detalle&id=X. Controlador llama `detalle()` que hace SELECT detallecompra JOIN productos WHERE id_compra. Retorna JSON con array productos (nombre, talla, color, cantidad, precio_unitario, subtotal). JavaScript renderiza modal con tabla de productos y total de compra.
