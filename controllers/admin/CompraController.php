<?php
session_start();
if (!isset($_SESSION['user_id']) || (int)$_SESSION['user_rol'] !== 1) {
    echo json_encode(['ok' => false, 'msg' => 'No autorizado']);
    exit;
}
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Compra.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$db     = (new Database())->conectar();
$compraM = new Compra($db);

header('Content-Type: application/json');

switch ($action) {
    case 'crear':
        $id_proveedor = (int)($_POST['id_proveedor'] ?? 0);
        $items_json   = $_POST['items'] ?? '[]';
        $items        = json_decode($items_json, true);

        if (!$id_proveedor) {
            echo json_encode(['ok' => false, 'msg' => 'Selecciona un proveedor']);
            exit;
        }
        if (empty($items)) {
            echo json_encode(['ok' => false, 'msg' => 'Agrega al menos un producto']);
            exit;
        }

        $total = array_sum(array_map(fn($i) => $i['cantidad'] * $i['precio_unitario'], $items));
        $datos = [
            'total'        => $total,
            'id_proveedor' => $id_proveedor,
            'id_usuario'   => $_SESSION['user_id'],
        ];

        try {
            $id = $compraM->crear($datos, $items);
            echo json_encode(['ok' => true, 'msg' => 'Compra registrada correctamente', 'id_compra' => $id]);
        } catch (Exception $e) {
            echo json_encode(['ok' => false, 'msg' => $e->getMessage()]);
        }
        exit;

    case 'detalle':
        $id = (int)($_GET['id'] ?? 0);
        echo json_encode($compraM->detalle($id));
        exit;

    default:
        header('Location: ../../views/admin/compras.php');
        exit;
}
