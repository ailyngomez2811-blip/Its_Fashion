<?php
session_start();
if (!isset($_SESSION['user_id']) || !in_array((int)$_SESSION['user_rol'], [1, 2])) {
    echo json_encode(['ok' => false, 'msg' => 'No autorizado']);
    exit;
}
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Venta.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$db     = (new Database())->conectar();
$ventaM = new Venta($db);

header('Content-Type: application/json');

switch ($action) {
    case 'crear':
        $metodo     = $_POST['metodo_pago'] ?? '';
        $id_cliente = (int)($_POST['id_cliente'] ?? 0);
        $items_json = $_POST['items'] ?? '[]';
        $items      = json_decode($items_json, true);

        if (empty($items)) {
            echo json_encode(['ok' => false, 'msg' => 'Agrega al menos un producto']);
            exit;
        }
        if (!in_array($metodo, ['Efectivo', 'Transferencia bancaria'])) {
            echo json_encode(['ok' => false, 'msg' => 'Método de pago inválido']);
            exit;
        }

        // Verificar caja abierta para efectivo
        if ($metodo === 'Efectivo' && !$ventaM->cajaAbierta()) {
            echo json_encode(['ok' => false, 'msg' => 'Debes abrir la caja antes de registrar ventas en efectivo']);
            exit;
        }

        $total = array_sum(array_map(fn($i) => $i['cantidad'] * $i['precio_unitario'], $items));
        $datos = [
            'total'       => $total,
            'id_cliente'  => $id_cliente ?: null,
            'metodo_pago' => $metodo,
            'id_usuario'  => $_SESSION['user_id'],
        ];

        try {
            $id = $ventaM->crear($datos, $items);
            echo json_encode(['ok' => true, 'msg' => 'Venta registrada correctamente', 'id_venta' => $id]);
        } catch (Exception $e) {
            echo json_encode(['ok' => false, 'msg' => $e->getMessage()]);
        }
        exit;

    case 'detalle':
        $id = (int)($_GET['id'] ?? 0);
        $venta   = $ventaM->obtener($id);
        $detalle = $ventaM->detalle($id);
        echo json_encode(['venta' => $venta, 'detalle' => $detalle]);
        exit;

    case 'buscarCliente':
        $q = trim($_GET['q'] ?? '');
        echo json_encode($ventaM->buscarCliente($q));
        exit;

    default:
        header('Location: ../../views/admin/ventas.php');
        exit;
}
