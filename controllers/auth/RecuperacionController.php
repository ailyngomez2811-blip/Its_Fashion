<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Usuario.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$db = (new Database())->conectar();
$usuario = new Usuario($db);

switch ($action) {
    case 'solicitar':
        $email = trim($_POST['email'] ?? '');
        if (empty($email)) {
            $_SESSION['alert'] = ['icon' => 'warning', 'title' => 'Cuidado', 'text' => 'Debes escribir tu correo.'];
            header("Location: ../../views/auth/recuperar_password.php");
            exit;
        }

        $token = bin2hex(random_bytes(32));

        if ($usuario->guardarTokenRecuperacion($email, $token)) {
            // Generar enlace simulado
            $enlace = "http://" . $_SERVER['HTTP_HOST'] . "/its%20fashion%20-%20copia/views/auth/reset_password.php?token=" . $token;

            // Para el modo de pruebas local y preparativo API: exponemos el enlace para flujo funcional:
            $_SESSION['test_link'] = $enlace;
            // TODO: Integrar API de terceros aquí en un futuro
        }

        $_SESSION['alert'] = ['icon' => 'success', 'title' => 'Simulación exitosa', 'text' => 'Si tu correo existe en el sistema, habrás recibido un enlace (ver simulador).'];
        header("Location: ../../views/auth/recuperar_password.php");
        exit;

    case 'restablecer':
        $token = $_POST['token'] ?? '';
        $pw1 = $_POST['password'] ?? '';
        $pw2 = $_POST['confirmar_password'] ?? '';

        if (empty($token) || empty($pw1) || empty($pw2)) {
            $_SESSION['alert'] = ['icon' => 'error', 'title' => 'Error', 'text' => 'Faltan datos por llenar.'];
            header("Location: ../../views/auth/reset_password.php?token=" . $token);
            exit;
        }

        if ($pw1 !== $pw2) {
            $_SESSION['alert'] = ['icon' => 'error', 'title' => 'Error', 'text' => 'Las contraseñas no coinciden.'];
            header("Location: ../../views/auth/reset_password.php?token=" . $token);
            exit;
        }

        if (strlen($pw1) < 6) {
            $_SESSION['alert'] = ['icon' => 'error', 'title' => 'Error', 'text' => 'La contraseña debe tener al menos 6 caracteres.'];
            header("Location: ../../views/auth/reset_password.php?token=" . $token);
            exit;
        }

        if ($usuario->validarToken($token)) {
            if ($usuario->actualizarPasswordYLimpiarToken($token, $pw1)) {
                $_SESSION['alert'] = ['icon' => 'success', 'title' => '¡Éxito!', 'text' => 'Tu contraseña ha sido actualizada correctamente. Ya puedes iniciar sesión.'];
                header("Location: ../../views/auth/login.php");
                exit;
            }
        }

        $_SESSION['alert'] = ['icon' => 'error', 'title' => 'Enlace no válido', 'text' => 'El enlace ya caducó o es inválido. Por favor, solicita uno nuevo.'];
        header("Location: ../../views/auth/recuperar_password.php");
        exit;

    default:
        header("Location: ../../views/auth/login.php");
        exit;
}
