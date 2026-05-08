**Vista:** `views/admin/proveedores.php`  
**Controlador:** `controllers/admin/ProveedorController.php` → acción `editar`  
**Modelo:** `models/Proveedor.php`

Click "Editar" carga datos en modal. Usuario modifica campos. JavaScript ejecuta fetch POST con action=editar, id y todos los campos. Controlador valida id, nombre y telefono no vacíos. Modelo hace UPDATE proveedor SET nombre, contacto, telefono, email, direccion WHERE id_proveedor. Retorna JSON. JavaScript actualiza tabla sin recargar.
