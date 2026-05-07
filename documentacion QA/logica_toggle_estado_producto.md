**Vista:** `views/admin/productos.php`
**Controlador:** `controllers/admin/ProductoController.php` → acción `toggleEstado`
**Modelo:** `models/Producto.php`

1. Cada producto en la tabla tiene badge de estado con onclick que ejecuta función JavaScript `toggleEstado(id, estadoActual)`.
2. JavaScript calcula nuevoEstado con operador ternario: estadoActual === 'Activo' ? 'Inactivo' : 'Activo'. Ejecuta fetch POST con FormData conteniendo action='toggleEstado', id, estado=nuevoEstado.
3. Controlador obtiene id con cast (int) y ?? 0, estado con ?? ''. Valida if (!$id || !in_array($estado, ['Activo', 'Inactivo'])) retorna JSON error.
4. Llama `cambiarEstado($id, $estado)` del modelo. Este hace prepare() de UPDATE productos SET estado WHERE id_producto. Ejecuta y retorna boolean.
5. Fetch recibe JSON: if (d.ok) actualiza el badge en la tabla cambiando clases CSS y texto sin recargar página.
