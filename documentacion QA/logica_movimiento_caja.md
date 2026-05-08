**Vista:** `views/admin/caja.php` y `views/empleado/caja.php`  
**Controlador:** `controllers/admin/CajaController.php` → acción `movimiento`  
**Modelo:** `models/Caja.php`

Click "Movimiento manual" abre modal con select tipo ('Ingreso' o 'Egreso'), input concepto, input monto. Submit valida campos, ejecuta fetch POST con action=movimiento, id_caja, tipo, concepto, monto. Controlador valida tipo válido y monto > 0. Modelo `registrarMovimiento()` hace INSERT movimientos_caja con fecha=NOW(). Determina columna con ternario: tipo === 'Ingreso' ? 'total_ingresos' : 'total_egresos'. UPDATE caja SET columna = COALESCE(columna,0) + monto. JavaScript muestra toast y recarga página.
