<?php
session_start();
if (!isset($_SESSION['user_id']) || (int)$_SESSION['user_rol'] !== 1) {
    echo json_encode(['ok' => false, 'msg' => 'No autorizado']);
    exit;
}
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Devolucion.php';
require_once __DIR__ . '/../../models/Venta.php';

$action  = $_POST['action'] ?? $_GET['action'] ?? '';
$db      = (new Database())->conectar();
$devM    = new Devolucion($db);
$ventaM  = new Venta($db);

header('Content-Type: application/json');

switch ($action) {

    case 'aprobar':
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) { echo json_encode(['ok' => false, 'msg' => 'ID inválido']); exit; }
        try {
            $ok = $devM->aceptar($id, $_SESSION['user_id']);
            echo json_encode($ok
                ? ['ok' => true,  'msg' => 'Devolución aceptada correctamente']
                : ['ok' => false, 'msg' => 'No se pudo aceptar (ya fue procesada o no existe)']
            );
        } catch (Exception $e) {
            echo json_encode(['ok' => false, 'msg' => $e->getMessage()]);
        }
        exit;

    case 'rechazar':
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) { echo json_encode(['ok' => false, 'msg' => 'ID inválido']); exit; }
        $ok = $devM->rechazar($id, $_SESSION['user_id']);
        echo json_encode($ok
            ? ['ok' => true,  'msg' => 'Devolución rechazada']
            : ['ok' => false, 'msg' => 'No se pudo rechazar (ya fue procesada o no existe)']
        );
        exit;

    case 'detalle':
        $id = (int)($_GET['id'] ?? 0);
        echo json_encode($devM->detalle($id));
        exit;

    case 'ventaDetalle':
        $id = (int)($_GET['id'] ?? 0);
        $venta   = $ventaM->obtener($id);
        $detalle = $ventaM->detalle($id);
        echo json_encode(['venta' => $venta, 'detalle' => $detalle]);
        exit;

    default:
        header('Location: ../../views/admin/devoluciones.php');
        exit;
}
