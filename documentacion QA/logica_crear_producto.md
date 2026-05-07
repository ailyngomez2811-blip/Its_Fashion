**Vista:** `views/admin/productos.php`
**Controlador:** `controllers/admin/ProductoController.php` → acción `crear`
**Modelo:** `models/Producto.php`

1. Controlador verifica sesión y if ((int)$_SESSION['user_rol'] !== 1) retorna error (solo admin puede crear).
2. Click en "Nuevo producto" abre modal con formulario. Obtiene datos con trim() y cast: nombre, descripcion, precio_venta (float), precio_compra (float), stock (int), stock_minimo (int), talla, color, estado, id_categoria (int).
3. Valida if (!$d['nombre'] || !$d['talla'] || !$d['color'] || !$d['id_categoria']) retorna error "Completa todos los campos obligatorios".
4. Valida if ($d['precio_venta'] <= $d['precio_compra']) retorna error "El precio de venta debe ser mayor al precio de compra".
5. Llama `crear($d)` del modelo. Retorna JSON con ok y mensaje según resultado.
6. Modelo `crear()` hace prepare() de INSERT en tabla productos con todos los campos. Usa foreach para hacer bindValue() de cada campo del array. Ejecuta con execute() y retorna boolean.
7. Fetch recibe JSON: if (d.ok) muestra toast success y recarga tabla automáticamente sin recargar página completa.
