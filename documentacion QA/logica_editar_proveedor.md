**Vista:** `views/admin/proveedores.php`
**Controlador:** `controllers/admin/ProveedorController.php` → acción `editar`
**Modelo:** `models/Proveedor.php`

1. Click en "Editar" carga datos del proveedor en modal. Usuario modifica campos.
2. JavaScript crea FormData con action='editar', id, y todos los campos. Ejecuta fetch POST.
3. Controlador obtiene id (int) y datos con trim(). Valida if (!$id || empty($nombre) || empty($telefono)) retorna error.
4. Modelo hace UPDATE proveedor SET nombre, contacto, telefono, email, direccion WHERE id_proveedor. Retorna boolean.
5. Fetch actualiza tabla sin recargar.
