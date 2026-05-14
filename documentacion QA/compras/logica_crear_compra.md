**Vista:** `views/admin/compras.php`  
**Controlador:** `controllers/admin/CompraController.php` → acción `crear`  
**Modelo:** `models/Compra.php`

Controlador verifica rol admin. Click "Nueva compra" abre modal. Select proveedores activos, desplegable productos. JavaScript autocompleta precio_compra (60% precio_venta) editable. Acumula productos en carrito, calcula total con reduce(). Ejecuta fetch POST con id_proveedor, items JSON.

Controlador valida id_proveedor y items no vacíos. Modelo `crear()` inicia transaction, INSERT compras, foreach items: INSERT detallecompra, UPDATE productos stock + cantidad, INSERT inventario tipo='Entrada'. Ejecuta commit. Retorna JSON. JavaScript muestra toast y recarga página.
