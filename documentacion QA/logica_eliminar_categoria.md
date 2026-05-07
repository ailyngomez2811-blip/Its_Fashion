**Vista:** `views/admin/categorias.php`
**Controlador:** `controllers/admin/CategoriaController.php` → acción `eliminar`
**Modelo:** `models/Categoria.php`

1. Click en "Eliminar" muestra confirmación con SweetAlert. If usuario confirma, ejecuta fetch POST con action='eliminar', id.
2. Controlador obtiene id (int). Valida if (!$id) retorna error. Verifica si hay productos asociados con SELECT COUNT(*) FROM productos WHERE id_categoria. If count > 0 retorna error "No se puede eliminar, tiene productos asociados".
3. Modelo hace DELETE FROM categoria WHERE id_categoria=:id. Retorna boolean.
4. Fetch recibe JSON: if (d.ok) muestra toast y elimina fila de la tabla con JavaScript.
