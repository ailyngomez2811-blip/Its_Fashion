**Vista:** `views/admin/caja.php` y `views/empleado/caja.php`
**Controlador:** `controllers/admin/CajaController.php` → acción `cerrar`
**Modelo:** `models/Caja.php`

1. Click en "Cerrar caja" ejecuta `openModal('cierre')`. JavaScript calcula saldo_teorico desde variable PHP. Modal muestra saldo teórico en card destacado.
2. Input para conteo físico con oninput que ejecuta `calcDif()`. Esta función calcula diferencia = conteo - saldo_teorico.
3. If diferencia === 0: muestra div verde "Sin diferencia. El efectivo cuadra perfectamente", oculta textarea de justificación. Else: muestra div azul (sobrante) o rojo (faltante) con el monto absoluto, muestra textarea obligatorio para justificación.
4. Al confirmar, `submitModal('cierre')` obtiene valores de conteo y justificacion. Valida if (!conteo) retorna error. Calcula diferencia con parseFloat(). If (dif !== 0 && !justif) retorna error "La justificación es obligatoria cuando hay diferencia".
5. Crea FormData con action='cerrar', id_caja, saldo_final=conteo, justificacion. Ejecuta fetch POST.
6. Controlador obtiene id_caja, saldo_final y justificacion con cast y trim(). Valida if (!$id_caja) retorna error. Try-catch: llama `cerrar($id_caja, $saldo_final, $justif)`.
7. Modelo `cerrar()` hace SELECT * FROM caja WHERE id_caja LIMIT 1. If (!$caja) hace throw Exception 'Caja no encontrada'.
8. Calcula saldo_teorico = saldo_inicial + total_ingresos - total_egresos. Calcula diferencia = saldo_final - saldo_teorico.
9. UPDATE caja SET saldo_final, diferencia, justificacion, fecha_cierre=NOW(), estado='Cerrada' WHERE id_caja. Retorna execute().
10. Fetch recibe JSON: if (d.ok) muestra toast, closeModal(), setTimeout() para location.reload() en 800ms. Al recargar, `cajaActiva()` retorna false y renderiza estado "CERRADA".
