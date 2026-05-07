**Vista:** `views/admin/caja.php` y `views/empleado/caja.php`
**Controlador:** `controllers/admin/CajaController.php` → acción `abrir`
**Modelo:** `models/Caja.php`

1. Al cargar la vista, ejecuta `cajaActiva()` que hace SELECT WHERE estado = 'Abierta'. Si retorna datos, `$caja` tiene el registro; si no, es false.
2. Renderiza banner con operador ternario: if `$caja` existe → fondo azul + "ABIERTA"; else → fondo gris + "CERRADA".
3. Botones condicionales: if NO hay caja → "Abrir caja"; else → "Movimiento manual" y "Cerrar caja".
4. Click en "Abrir caja" ejecuta `openModal('apertura')` que construye el formulario dinámicamente.
5. Submit ejecuta `submitModal('apertura')`: obtiene valor del input, valida if (!monto) muestra error y return. Crea FormData con action='abrir' y saldo_inicial, ejecuta fetch POST.
6. Controlador verifica sesión y rol con in_array([1,2]). Obtiene action con ??, instancia Database y modelo.
7. Switch case 'abrir': cast (float) del saldo, valida if ($saldo < 0). Try-catch: llama `abrir()`, retorna JSON ok:true o ok:false.
8. Modelo `abrir()`: SELECT verifica caja abierta, if fetch() hace throw Exception. INSERT con bindValue() de saldo_inicial, total_ingresos=0, total_egresos=0, fecha_apertura=NOW(), estado='Abierta', id_usuario. Retorna lastInsertId().
9. Fetch recibe JSON: if (d.ok) ejecuta showToast(), closeModal(), setTimeout() para location.reload() en 800ms.
10. Al recargar, `cajaActiva()` retorna la caja abierta y renderiza estado "ABIERTA".
