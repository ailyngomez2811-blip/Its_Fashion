**Vista:** `views/clientes/mis_compras.php`  
**Controlador:** `controllers/clientes/ClienteController.php` → acción `solicitarDevolucion`  
**Modelo:** `models/Devolucion.php`

Controlador verifica rol cliente. Vista muestra compras con botón "Solicitar devolución" si < 30 días y sin devolución pendiente/aceptada. Click abre modal con productos de la venta, checkboxes e inputs cantidad. Cliente selecciona productos, especifica cantidades, ingresa motivo. JavaScript valida: al menos un producto, cantidades válidas, motivo no vacío. Ejecuta fetch POST con id_venta, motivo, items JSON.

Controlador valida campos, verifica venta pertenece al cliente y estado='Completada'. Valida cantidades no superen lo comprado. Modelo `crear()` inicia transaction, INSERT devoluciones con estado='Pendiente', foreach items: INSERT detalledevolucion. Ejecuta commit. JavaScript muestra toast y actualiza vista con badge "Devolución pendiente".
