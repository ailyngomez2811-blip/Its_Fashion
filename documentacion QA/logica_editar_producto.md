**Vista:** `views/admin/productos.php`
**Controlador:** `controllers/admin/ProductoController.php` → acción `editar`
**Modelo:** `models/Producto.php`

1. Controlador verifica sesión y if ((int)$_SESSION['user_rol'] !== 1) retorna error (solo admin).
2. Click en botón "Editar" ejecuta JavaScript que hace fetch GET a action=obtener&id=X. Obtiene datos del producto y pre-llena el formulario del modal.
3. Usuario modifica campos. Al confirmar, JavaScript crea FormData con action='editar', id, y todos los campos con cast: nombre, descripcion, precio_venta (float), precio_compra (float), stock (int), stock_minimo (int), talla, color, estado, id_categoria (int).
4. Valida if (!$id || !$d['nombre'] || !$d['talla'] || !$d['color'] || !$d['id_categoria']) retorna error 'Datos inválidos'.
5. Valida if ($d['precio_venta'] <= $d['precio_compra']) retorna error "El precio de venta debe ser mayor al precio de compra".
6. Llama `actualizar($id, $d)` del modelo. Este hace prepare() de UPDATE productos SET con todos los campos WHERE id_producto. Usa foreach para bindValue() de cada campo. Ejecuta y retorna boolean.
7. Fetch recibe JSON: if (d.ok) muestra toast success y actualiza tabla sin recargar página.
