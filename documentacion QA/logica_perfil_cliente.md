**Vista:** `views/clientes/mi_perfil.php`  
**Controlador:** `controllers/clientes/ClienteController.php` → acción `actualizarPerfil`  
**Modelo:** Actualización directa en tabla usuario

Vista carga datos desde sesión. Formulario con: nombre, apellido, telefono, email, password_actual (opcional), password_nueva (opcional). JavaScript valida campos. Si cambiar contraseña: valida ambos campos llenos. Ejecuta fetch POST. Controlador valida campos obligatorios, verifica email único con SELECT WHERE email AND id_usuario != :id. Si password_nueva: valida password_actual con `password_verify()`, valida longitud >= 6, hace `password_hash()`. Construye UPDATE dinámico, si hay password lo incluye. Actualiza sesión con nuevos datos. Retorna JSON. JavaScript muestra toast.
