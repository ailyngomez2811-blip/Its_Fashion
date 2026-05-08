**Vista:** `views/admin/categorias.php`  
**Controlador:** `controllers/admin/CategoriaController.php` → acción `eliminar`  
**Modelo:** `models/Categoria.php`

Click "Eliminar" muestra confirmación con SweetAlert. Si confirma: ejecuta fetch POST con action=eliminar, id. Controlador verifica si hay productos asociados con SELECT COUNT(*) FROM productos WHERE id_categoria. Si count > 0: retorna error "No se puede eliminar, tiene productos asociados". Modelo hace DELETE FROM categoria WHERE id_categoria. Retorna JSON. JavaScript elimina fila de tabla con JavaScript.
