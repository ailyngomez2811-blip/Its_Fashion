<?php
session_start();
if (!isset($_SESSION['user_id']) || !in_array((int)$_SESSION['user_rol'], [1, 2])) {
    echo json_encode(['ok' => false, 'msg' => 'No autorizado']);
    exit;
}
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Caja.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$db     = (new Database())->conectar();
$cajaM  = new Caja($db);

header('Content-Type: application/json');

switch ($action) {
    case 'abrir':
        $saldo = (float)($_POST['saldo_inicial'] ?? 0);
        if ($saldo < 0) {
            echo json_encode(['ok' => false, 'msg' => 'El saldo inicial no puede ser negativo']);
            exit;
        }
        try {
            $id = $cajaM->abrir($saldo, $_SESSION['user_id']);
            echo json_encode(['ok' => true, 'msg' => 'Caja abierta correctamente', 'id_caja' => $id]);
        } catch (Exception $e) {
            echo json_encode(['ok' => false, 'msg' => $e->getMessage()]);
        }
        exit;

    case 'cerrar':
        $id_caja    = (int)($_POST['id_caja'] ?? 0);
        $saldo_final = (float)($_POST['saldo_final'] ?? 0);
        $justif     = trim($_POST['justificacion'] ?? '');
        if (!$id_caja) {
            echo json_encode(['ok' => false, 'msg' => 'Caja inválida']);
            exit;
        }
        try {
            $cajaM->cerrar($id_caja, $saldo_final, $justif);
            echo json_encode(['ok' => true, 'msg' => 'Caja cerrada correctamente']);
        } catch (Exception $e) {
            echo json_encode(['ok' => false, 'msg' => $e->getMessage()]);
        }
        exit;

    case 'movimiento':
        $id_caja  = (int)($_POST['id_caja'] ?? 0);
        $tipo     = $_POST['tipo'] ?? '';
        $monto    = (float)($_POST['monto'] ?? 0);
        $concepto = trim($_POST['concepto'] ?? '');
        if (!$id_caja || !in_array($tipo, ['Ingreso', 'Egreso']) || $monto <= 0 || !$concepto) {
            echo json_encode(['ok' => false, 'msg' => 'Completa todos los campos']);
            exit;
        }
        $cajaM->registrarMovimiento($id_caja, $tipo, $monto, $concepto);
        echo json_encode(['ok' => true, 'msg' => 'Movimiento registrado']);
        exit;

    case 'saldo':
        $id_caja = (int)($_GET['id_caja'] ?? 0);
        echo json_encode(['saldo' => $cajaM->saldoTeorico($id_caja)]);
        exit;

    default:
        header('Location: ../../views/admin/caja.php');
        exit;
}
