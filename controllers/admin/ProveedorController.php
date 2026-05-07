<?php
session_start();
if (!isset($_SESSION['user_id']) || (int)$_SESSION['user_rol'] !== 1) {
    echo json_encode(['ok' => false, 'msg' => 'No autorizado']);
    exit;
}
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Proveedor.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$db     = (new Database())->conectar();
$prov   = new Proveedor($db);

header('Content-Type: application/json');

switch ($action) {
    case 'crear':
        $d = [
            'nombre'    => trim($_POST['nombre']    ?? ''),
            'contacto'  => trim($_POST['contacto']  ?? ''),
            'telefono'  => trim($_POST['telefono']  ?? ''),
            'email'     => trim($_POST['email']     ?? ''),
            'direccion' => trim($_POST['direccion'] ?? ''),
            'documento' => trim($_POST['documento'] ?? ''),
        ];
        if (!$d['nombre'] || !$d['documento']) {
            echo json_encode(['ok' => false, 'msg' => 'Nombre y documento son obligatorios']);
            exit;
        }
        if ($prov->existeDocumento($d['documento'])) {
            echo json_encode(['ok' => false, 'msg' => 'Ya existe un proveedor con ese documento']);
            exit;
        }
        $ok = $prov->crear($d);
        echo json_encode(['ok' => $ok, 'msg' => $ok ? 'Proveedor creado correctamente' : 'Error al crear']);
        exit;

    case 'editar':
        $id = (int)($_POST['id'] ?? 0);
        $d  = [
            'nombre'    => trim($_POST['nombre']    ?? ''),
            'contacto'  => trim($_POST['contacto']  ?? ''),
            'telefono'  => trim($_POST['telefono']  ?? ''),
            'email'     => trim($_POST['email']     ?? ''),
            'direccion' => trim($_POST['direccion'] ?? ''),
            'documento' => trim($_POST['documento'] ?? ''),
        ];
        if (!$id || !$d['nombre'] || !$d['documento']) {
            echo json_encode(['ok' => false, 'msg' => 'Datos inválidos']);
            exit;
        }
        if ($prov->existeDocumento($d['documento'], $id)) {
            echo json_encode(['ok' => false, 'msg' => 'Ya existe un proveedor con ese documento']);
            exit;
        }
        $ok = $prov->actualizar($id, $d);
        echo json_encode(['ok' => $ok, 'msg' => $ok ? 'Proveedor actualizado' : 'Error al actualizar']);
        exit;

    case 'listar':
        echo json_encode($prov->listar());
        exit;

    default:
        header('Location: ../../views/admin/proveedores.php');
        exit;
}
