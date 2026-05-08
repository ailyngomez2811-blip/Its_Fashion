**Vista:** `views/admin/caja.php` y `views/empleado/caja.php`  
**Controlador:** `controllers/admin/CajaController.php` → acción `cerrar`  
**Modelo:** `models/Caja.php`

Click "Cerrar caja" abre modal mostrando saldo_teorico calculado. Input conteo físico con oninput ejecuta `calcDif()` que calcula diferencia = conteo - saldo_teorico. Si diferencia === 0: mensaje verde "Cuadra perfectamente", oculta textarea. Si diferencia !== 0: mensaje azul (sobrante) o rojo (faltante), textarea obligatorio para justificación. Submit valida conteo y justificación si hay diferencia. Ejecuta fetch POST con action=cerrar, id_caja, saldo_final, justificacion. Modelo `cerrar()` calcula saldo_teorico, diferencia, hace UPDATE caja SET saldo_final, diferencia, justificacion, fecha_cierre=NOW(), estado='Cerrada'. JavaScript muestra toast y recarga página.
