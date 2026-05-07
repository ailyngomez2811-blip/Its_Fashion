<?php
// ClienteController.php — Gestión de clientes desde panel admin (views/admin)
session_start();

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Cliente.php';

// Protección: solo admin o empleado
if (!isset($_SESSION['user_id']) || !in_array((int)$_SESSION['user_rol'], [1, 2])) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'msg' => 'No autorizado']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

$database = new Database();
$db       = $database->conectar();
$cliente  = new Cliente($db);

switch ($action) {

    // ── Cambiar estado (Activo / Inactivo) ──────────────────────────────────
    case 'cambiarEstado':
        $id     = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        $estado = $_POST['estado'] ?? $_GET['estado'] ?? '';

        if (!$id || !in_array($estado, ['Activo', 'Inactivo'])) {
            echo json_encode(['ok' => false, 'msg' => 'Datos inválidos']);
            exit;
        }

        $ok = $cliente->cambiarEstado($id, $estado);
        echo json_encode(['ok' => $ok, 'msg' => $ok ? 'Estado actualizado' : 'Error al actualizar']);
        exit;

        // ── Historial de compras del cliente (AJAX) ─────────────────────────────
    case 'compras':
        $id = (int)($_GET['id'] ?? 0);
        echo json_encode($cliente->compras($id));
        exit;

        // ── Historial de devoluciones del cliente (AJAX) ────────────────────────
    case 'devoluciones':
        $id = (int)($_GET['id'] ?? 0);
        echo json_encode($cliente->devoluciones($id));
        exit;

    default:
        header("Location: ../../views/admin/clientes.php");
        exit;
}
