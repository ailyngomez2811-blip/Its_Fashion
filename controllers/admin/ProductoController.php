<?php
session_start();
if (!isset($_SESSION['user_id']) || !in_array((int)$_SESSION['user_rol'], [1, 2])) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'msg' => 'No autorizado']);
    exit;
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Producto.php';
require_once __DIR__ . '/../../models/Categoria.php';
require_once __DIR__ . '/../../models/Inventario.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$db     = (new Database())->conectar();
$prod   = new Producto($db);
$invM   = new Inventario($db);

switch ($action) {

    case 'listar':
        header('Content-Type: application/json');
        echo json_encode($prod->listar());
        exit;

    case 'buscar':
        header('Content-Type: application/json');
        $q = trim($_GET['q'] ?? '');
        echo json_encode($q ? $prod->buscar($q) : $prod->listar());
        exit;

    case 'crear':
        if ((int)$_SESSION['user_rol'] !== 1) {
            echo json_encode(['ok' => false, 'msg' => 'Solo el administrador puede crear productos']);
            exit;
        }
        $d = [
            'nombre'       => trim($_POST['nombre']       ?? ''),
            'descripcion'  => trim($_POST['descripcion']  ?? ''),
            'precio_venta' => (float)($_POST['precio_venta']  ?? 0),
            'precio_compra' => (float)($_POST['precio_compra'] ?? 0),
            'stock'        => (int)($_POST['stock']        ?? 0),
            'stock_minimo' => (int)($_POST['stock_minimo'] ?? 0),
            'talla'        => trim($_POST['talla']         ?? ''),
            'color'        => trim($_POST['color']         ?? ''),
            'estado'       => $_POST['estado']             ?? 'Activo',
            'id_categoria' => (int)($_POST['id_categoria'] ?? 0),
        ];
        if (!$d['nombre'] || !$d['talla'] || !$d['color'] || !$d['id_categoria']) {
            echo json_encode(['ok' => false, 'msg' => 'Completa todos los campos obligatorios']);
            exit;
        }
        if ($d['precio_venta'] <= $d['precio_compra']) {
            echo json_encode(['ok' => false, 'msg' => 'El precio de venta debe ser mayor al precio de compra']);
            exit;
        }
        $id_insertado = $prod->crear($d);
        if ($id_insertado) {
            $invM->registrarMovimiento($id_insertado, 'Entrada', $d['stock']);
            echo json_encode(['ok' => true, 'msg' => 'Producto creado correctamente']);
        } else {
            echo json_encode(['ok' => false, 'msg' => 'Error al crear el producto']);
        }
        exit;

    case 'editar':
        if ((int)$_SESSION['user_rol'] !== 1) {
            echo json_encode(['ok' => false, 'msg' => 'Solo el administrador puede editar productos']);
            exit;
        }
        $id = (int)($_POST['id'] ?? 0);
        $d  = [
            'nombre'       => trim($_POST['nombre']       ?? ''),
            'descripcion'  => trim($_POST['descripcion']  ?? ''),
            'precio_venta' => (float)($_POST['precio_venta']  ?? 0),
            'precio_compra' => (float)($_POST['precio_compra'] ?? 0),
            'stock'        => (int)($_POST['stock']        ?? 0),
            'stock_minimo' => (int)($_POST['stock_minimo'] ?? 0),
            'talla'        => trim($_POST['talla']         ?? ''),
            'color'        => trim($_POST['color']         ?? ''),
            'estado'       => $_POST['estado']             ?? 'Activo',
            'id_categoria' => (int)($_POST['id_categoria'] ?? 0),
        ];
        if (!$id || !$d['nombre'] || !$d['talla'] || !$d['color'] || !$d['id_categoria']) {
            echo json_encode(['ok' => false, 'msg' => 'Datos inválidos']);
            exit;
        }
        if ($d['precio_venta'] <= $d['precio_compra']) {
            echo json_encode(['ok' => false, 'msg' => 'El precio de venta debe ser mayor al precio de compra']);
            exit;
        }
        $old = $prod->obtener($id);
        $diff = $d['stock'] - $old['stock'];

        $ok = $prod->actualizar($id, $d);
        if ($ok && $diff !== 0) {
            $tipo = $diff > 0 ? 'Entrada' : 'Salida';
            try {
                $invM->registrarMovimiento($id, $tipo, $d['stock']);
            } catch (Exception $e) {
                // Ignore or log error
            }
        }

        echo json_encode(['ok' => $ok, 'msg' => $ok ? 'Producto actualizado' : 'Error al actualizar']);
        exit;

    case 'toggleEstado':
        $id     = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        $estado = $_POST['estado'] ?? $_GET['estado'] ?? '';
        if (!$id || !in_array($estado, ['Activo', 'Inactivo'])) {
            echo json_encode(['ok' => false, 'msg' => 'Datos inválidos']);
            exit;
        }
        $ok = $prod->cambiarEstado($id, $estado);
        echo json_encode(['ok' => $ok]);
        exit;

    case 'obtener':
        header('Content-Type: application/json');
        $id = (int)($_GET['id'] ?? 0);
        echo json_encode($prod->obtener($id));
        exit;

    default:
        header('Location: ../../views/admin/productos.php');
        exit;
}
