**Vista:** `views/admin/caja.php` y `views/empleado/caja.php`
**Controlador:** `controllers/admin/CajaController.php` → acción `movimiento`
**Modelo:** `models/Caja.php`

1. Click en "Movimiento manual" ejecuta `openModal('movimiento')`. Modal muestra select con opciones 'Ingreso' o 'Egreso', input de concepto (text), input de monto (number).
2. Al confirmar, `submitModal('movimiento')` obtiene valores: tipo, concepto con trim(), monto. Valida if (!concepto || !monto) retorna error. Crea FormData con action='movimiento', id_caja, tipo, concepto, monto. Ejecuta fetch POST.
3. Controlador obtiene id_caja (int), tipo, monto (float), concepto con trim(). Valida if (!$id_caja || !in_array($tipo, ['Ingreso','Egreso']) || $monto <= 0 || !$concepto) retorna error.
4. Llama `registrarMovimiento($id_caja, $tipo, $monto, $concepto)` del modelo. Este hace INSERT en movimientos_caja con id_caja, tipo, monto, concepto, fecha=NOW().
5. Determina columna con operador ternario: $col = $tipo === 'Ingreso' ? 'total_ingresos' : 'total_egresos'. UPDATE caja SET {$col} = COALESCE({$col},0) + monto WHERE id_caja. Retorna true.
6. Fetch recibe JSON: if (d.ok) muestra toast y recarga página con location.reload() para actualizar tabla de movimientos y KPIs.
