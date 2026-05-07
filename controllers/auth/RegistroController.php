<?php
// RegistroController.php — Registro público de clientes (views/auth)
session_start();

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Cliente.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

$database = new Database();
$db       = $database->conectar();
$cliente  = new Cliente($db);

switch ($action) {

    case 'registrar':
        $nombre    = trim($_POST['nombre']    ?? '');
        $apellido  = trim($_POST['apellido']  ?? '');
        $documento = trim($_POST['documento'] ?? '');
        $telefono  = trim($_POST['telefono']  ?? '');
        $email     = trim($_POST['email']     ?? '');
        $password  = $_POST['password']       ?? '';
        $confirmar = $_POST['confirmar_password'] ?? '';

        if (empty($nombre) || empty($apellido) || empty($documento) || empty($telefono) || empty($email) || empty($password)) {
            $_SESSION['alert'] = ['icon' => 'warning', 'title' => 'Campos incompletos', 'text' => 'Completa todos los campos.'];
            header("Location: ../../views/auth/registro_cliente.php");
            exit;
        }

        if ($password !== $confirmar) {
            $_SESSION['alert'] = ['icon' => 'error', 'title' => 'Error', 'text' => 'Las contraseñas no coinciden.'];
            header("Location: ../../views/auth/registro_cliente.php");
            exit;
        }

        if ($cliente->existeEmail($email)) {
            $_SESSION['alert'] = ['icon' => 'error', 'title' => 'Correo ya registrado', 'text' => 'Ya existe una cuenta con ese correo.'];
            header("Location: ../../views/auth/registro_cliente.php");
            exit;
        }

        $datos = compact('nombre', 'apellido', 'documento', 'telefono', 'email', 'password');

        if ($cliente->registrar($datos)) {
            $_SESSION['alert'] = ['icon' => 'success', 'title' => '¡Registro exitoso!', 'text' => 'Cuenta creada correctamente.', 'redirect' => 'login.php'];
        } else {
            $_SESSION['alert'] = ['icon' => 'error', 'title' => 'Error', 'text' => 'No se pudo completar el registro.'];
        }

        header("Location: ../../views/auth/registro_cliente.php");
        exit;

    default:
        header("Location: ../../views/auth/registro_cliente.php");
        exit;
}
