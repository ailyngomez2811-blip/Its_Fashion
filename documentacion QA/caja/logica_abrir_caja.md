**Vista:** `views/admin/caja.php` y `views/empleado/caja.php`  
**Controlador:** `controllers/admin/CajaController.php` → acción `abrir`  
**Modelo:** `models/Caja.php`

Al cargar vista ejecuta `cajaActiva()` que hace SELECT WHERE estado='Abierta'. Renderiza banner: si existe caja → "ABIERTA" fondo azul, sino → "CERRADA" fondo gris. Botones condicionales: si NO hay caja → "Abrir caja", sino → "Movimiento manual" y "Cerrar caja". Click "Abrir caja" abre modal con input saldo_inicial. Submit valida monto, ejecuta fetch POST con action=abrir. Controlador verifica rol [1,2]. Modelo `abrir()` valida no hay caja abierta, hace INSERT con saldo_inicial, total_ingresos=0, total_egresos=0, fecha_apertura=NOW(), estado='Abierta'. JavaScript muestra toast y recarga página.
