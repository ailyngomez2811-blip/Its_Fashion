<?php
session_start();

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Usuario.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

$database = new Database();
$db = $database->conectar();
$usuario = new Usuario($db);

switch ($action) {
    case 'login':
        $identifier = trim($_POST['username'] ?? '');
        $password   = $_POST['password'] ?? '';

        if (empty($identifier) || empty($password)) {
            $_SESSION['alert'] = ['text' => 'Por favor completa todos los campos.'];
            header("Location: ../../views/auth/login.php");
            exit;
        }

        $user = $usuario->verificarCredenciales($identifier, $password);

        if ($user) {
            $_SESSION['user_id']       = $user['id_usuario'];
            $_SESSION['user_nombre']   = $user['nombre'];
            $_SESSION['user_apellido'] = $user['apellido'];
            $_SESSION['user_rol']      = $user['id_rol'];
            $_SESSION['user_email']    = $user['email'];

            switch ((int)$user['id_rol']) {
                case 1:
                    header("Location: ../../views/admin/dashboard.php");
                    break;
                case 2:
                    header("Location: ../../views/empleado/dashboard_empleado.php");
                    break;
                case 3:
                    header("Location: ../../views/clientes/dashboard_cliente.php");
                    break;
                default:
                    header("Location: ../../views/auth/login.php");
            }
            exit;
        }

        $_SESSION['alert'] = ['text' => 'Usuario o contraseña incorrectos.'];
        header("Location: ../../views/auth/login.php");
        exit;

    case 'create':
        if (!isset($_SESSION['user_id']) || (int)$_SESSION['user_rol'] !== 1) {
            header("Location: ../../views/auth/login.php");
            exit;
        }
        $datos = [
            'nombre'     => trim($_POST['nombre']   ?? ''),
            'apellido'   => trim($_POST['apellido'] ?? ''),
            'username'   => trim($_POST['username'] ?? ''),
            'email'      => trim($_POST['email']    ?? ''),
            'telefono'   => trim($_POST['telefono'] ?? ''),
            'password'   => $_POST['password']      ?? '',
            'id_rol'     => (int)($_POST['id_rol']  ?? 2),
            'estado'     => $_POST['estado']        ?? 'Activo',
            'creado_por' => $_SESSION['user_id'],
        ];
        require_once __DIR__ . '/../../models/Usuario.php';
        $m = new Usuario($db);
        
        if ($m->existeDuplicado($datos['username'], $datos['email'])) {
            $_SESSION['toast'] = ['type' => 'error', 'text' => 'El username o email ya existen.'];
            header("Location: ../../views/admin/usuarios.php");
            exit;
        }

        if ($m->registrar($datos)) {
            $_SESSION['toast'] = ['type' => 'success', 'text' => 'Empleado creado correctamente.'];
        } else {
            $_SESSION['toast'] = ['type' => 'error',   'text' => 'Error al crear el empleado.'];
        }
        header("Location: ../../views/admin/usuarios.php");
        exit;

    case 'update':
        if (!isset($_SESSION['user_id']) || (int)$_SESSION['user_rol'] !== 1) {
            header("Location: ../../views/auth/login.php");
            exit;
        }
        $id     = (int)($_POST['id'] ?? 0);
        $nombre = trim($_POST['nombre']   ?? '');
        $apell  = trim($_POST['apellido'] ?? '');
        $user   = trim($_POST['username'] ?? '');
        $email  = trim($_POST['email']    ?? '');
        $tel    = trim($_POST['telefono'] ?? '');
        $estado = $_POST['estado']        ?? 'Activo';
        $caja   = $_POST['puede_operar_caja'] ?? 0;
        $pw     = $_POST['password']      ?? '';

        require_once __DIR__ . '/../../models/Usuario.php';
        $m = new Usuario($db);
        if ($m->existeDuplicado($user, $email, $id)) {
            $_SESSION['toast'] = ['type' => 'error', 'text' => 'El username o email ya pertenecen a otra cuenta.'];
            header("Location: ../../views/admin/usuarios.php");
            exit;
        }

        $sql = "UPDATE usuario SET nombre=:n, apellido=:a, username=:u, email=:e, telefono=:t, estado=:s";
        if (!empty($pw)) $sql .= ", password=:p";
        $sql .= " WHERE id_usuario=:id";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':n',  $nombre);
        $stmt->bindParam(':a',  $apell);
        $stmt->bindParam(':u',  $user);
        $stmt->bindParam(':e',  $email);
        $stmt->bindParam(':t',  $tel);
        $stmt->bindParam(':s',  $estado);
        $stmt->bindParam(':id', $id);
        if (!empty($pw)) {
            $hash = password_hash($pw, PASSWORD_BCRYPT);
            $stmt->bindParam(':p', $hash);
        }

        if ($stmt->execute()) {
            $_SESSION['toast'] = ['type' => 'success', 'text' => 'Empleado actualizado correctamente.'];
        } else {
            $_SESSION['toast'] = ['type' => 'error',   'text' => 'Error al actualizar.'];
        }
        header("Location: ../../views/admin/usuarios.php");
        exit;

    case 'toggleEstado':
        $id     = (int)($_GET['id'] ?? 0);
        $estado = $_GET['estado'] ?? 'Activo';
        $stmt   = $db->prepare("UPDATE usuario SET estado=:s WHERE id_usuario=:id");
        $stmt->bindParam(':s',  $estado);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        exit;

    case 'logout':
        session_destroy();
        header("Location: ../../public/index.php");
        exit;

    default:
        header("Location: ../../views/auth/login.php");
        exit;
}
