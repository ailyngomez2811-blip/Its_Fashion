**Vista:** `views/admin/caja.php`  
**Controlador:** Similar a cerrar caja  
**Modelo:** `models/Caja.php`

Arqueo es verificación antes del cierre. Modal muestra resumen: saldo_inicial + total_ingresos - total_egresos = saldo_teorico. Usuario ingresa conteo físico. JavaScript ejecuta `calcDif()` en tiempo real con oninput calculando diferencia = conteo - saldo_teorico. Si diferencia === 0: mensaje verde. Si > 0: mensaje azul "Sobrante". Si < 0: mensaje rojo "Faltante". Si hay diferencia: textarea justificación obligatorio. Al cerrar guarda diferencia y justificacion en tabla caja.
