**Vista:** `views/admin/usuarios.php`
**Controlador:** `controllers/auth/UsuarioController.php` → acción `toggleEstado`

1. Badge de estado en tabla tiene onclick que ejecuta función JavaScript con id y estado actual.
2. JavaScript calcula nuevo estado con ternario. Ejecuta fetch GET a UsuarioController.php?action=toggleEstado&id=X&estado=Y.
3. Controlador obtiene id (int) y estado con ?? 'Activo'. Hace prepare() de UPDATE usuario SET estado WHERE id_usuario. Ejecuta con execute().
4. JavaScript actualiza badge en la tabla cambiando clases y texto sin recargar.
