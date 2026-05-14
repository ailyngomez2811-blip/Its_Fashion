**Vista:** `views/admin/devoluciones.php`  
**Controlador:** `controllers/admin/DevolucionController.php` → acción `aprobar` o `rechazar`  
**Modelo:** `models/Devolucion.php`

Controlador verifica rol admin. Tabla con foreach de devoluciones. Estado='Pendiente' muestra botones "Aprobar" y "Rechazar". Click abre modal con detalle: cliente, productos, cantidades, motivo. Si rechazar: textarea obligatorio para justificación. Ejecuta fetch POST con action=aprobar o action=rechazar, id.

Modelo `aceptar()` valida estado='Pendiente', inicia transaction, UPDATE devoluciones SET estado='Aceptada', fecha_resolucion=NOW(). Foreach items: UPDATE productos stock + cantidad, INSERT inventario tipo='Entrada'. Si hay caja abierta: INSERT movimientos_caja tipo='Egreso', UPDATE caja total_egresos. Ejecuta commit. Rechazar: UPDATE estado='Rechazada'. JavaScript actualiza tabla sin recargar.
