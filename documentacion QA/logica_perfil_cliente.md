**Vista:** `views/clientes/mi_perfil.php`
**Controlador:** `controllers/clientes/ClienteController.php` → acción `actualizarPerfil`
**Modelo:** Actualización directa en tabla usuario

1. Vista carga datos del cliente desde sesión. Formulario con inputs: nombre, apellido, telefono, email, password_actual (opcional), password_nueva (opcional).
2. JavaScript valida campos obligatorios. If quiere cambiar contraseña, valida que ambos campos de password estén llenos. Crea FormData y ejecuta fetch POST.
3. Controlador obtiene datos con trim(). Valida if (!$nombre || !$apellido || !$telefono || !$email) retorna error.
4. Verifica email único con SELECT WHERE email AND id_usuario != :id. If existe retorna error "Ese correo ya está registrado".
5. If password_nueva no está vacío: valida if (empty($pw_actual)) retorna error. SELECT password WHERE id_usuario. Usa password_verify($pw_actual, $row['password']). If no verifica retorna error "La contraseña actual es incorrecta". Valida if (strlen($pw_nueva) < 6) retorna error. Hace password_hash($pw_nueva, PASSWORD_BCRYPT).
6. Construye UPDATE dinámicamente: "UPDATE usuario SET nombre, apellido, telefono, email". If hay password nuevo concatena ", password". WHERE id_usuario. Ejecuta con bindParam().
7. If exitoso: actualiza sesión con $_SESSION['user_nombre'], $_SESSION['user_apellido'], $_SESSION['user_email']. Retorna JSON ok:true.
8. Fetch muestra toast success.
