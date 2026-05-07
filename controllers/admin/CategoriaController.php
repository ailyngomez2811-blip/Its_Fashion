<?php
session_start();
if (!isset($_SESSION['user_id']) || (int)$_SESSION['user_rol'] !== 1) {
    echo json_encode(['ok' => false, 'msg' => 'No autorizado']);
    exit;
}
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Categoria.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$db     = (new Database())->conectar();
$cat    = new Categoria($db);

header('Content-Type: application/json');

switch ($action) {
    case 'crear':
        $nombre = trim($_POST['nombre'] ?? '');
        $desc   = trim($_POST['descripcion'] ?? '');
        if (!$nombre) {
            echo json_encode(['ok' => false, 'msg' => 'El nombre es obligatorio']);
            exit;
        }
        if ($cat->existeNombre($nombre)) {
            echo json_encode(['ok' => false, 'msg' => 'Ya existe una categoría con ese nombre']);
            exit;
        }
        $ok = $cat->crear($nombre, $desc);
        echo json_encode(['ok' => $ok, 'msg' => $ok ? 'Categoría creada correctamente' : 'Error al crear']);
        exit;

    case 'editar':
        $id     = (int)($_POST['id'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $desc   = trim($_POST['descripcion'] ?? '');
        if (!$id || !$nombre) {
            echo json_encode(['ok' => false, 'msg' => 'Datos inválidos']);
            exit;
        }
        if ($cat->existeNombre($nombre, $id)) {
            echo json_encode(['ok' => false, 'msg' => 'Ya existe una categoría con ese nombre']);
            exit;
        }
        $ok = $cat->actualizar($id, $nombre, $desc);
        echo json_encode(['ok' => $ok, 'msg' => $ok ? 'Categoría actualizada' : 'Error al actualizar']);
        exit;

    case 'eliminar':
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) {
            echo json_encode(['ok' => false, 'msg' => 'ID inválido']);
            exit;
        }
        $ok = $cat->eliminar($id);
        echo json_encode(['ok' => $ok, 'msg' => $ok ? 'Categoría eliminada' : 'No se puede eliminar: tiene productos asociados']);
        exit;

    default:
        header('Location: ../../views/admin/categorias.php');
        exit;
}
