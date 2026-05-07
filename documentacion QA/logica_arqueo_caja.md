**Vista:** `views/admin/caja.php`
**Controlador:** Similar a cerrar caja
**Modelo:** `models/Caja.php`

1. Arqueo es el proceso de verificación antes del cierre. Modal de cierre muestra resumen calculado: saldo_inicial + total_ingresos - total_egresos = saldo_teorico.
2. Usuario ingresa conteo físico. JavaScript ejecuta `calcDif()` en tiempo real con oninput que calcula diferencia = conteo - saldo_teorico.
3. If diferencia === 0: muestra mensaje verde "Cuadra perfectamente". If diferencia > 0: muestra mensaje azul "Sobrante: $X". If diferencia < 0: muestra mensaje rojo "Faltante: $X".
4. If hay diferencia, textarea de justificación se vuelve obligatorio. Al cerrar, se guarda diferencia y justificacion en tabla caja.
