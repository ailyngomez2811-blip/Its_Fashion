<?php
session_start();

if (!isset($_SESSION['user_id']) || (int)$_SESSION['user_rol'] !== 3) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'msg' => 'No autorizado']);
    exit;
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Venta.php';
require_once __DIR__ . '/../../models/Devolucion.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$db     = (new Database())->conectar();
$ventaM = new Venta($db);
$devM   = new Devolucion($db);

header('Content-Type: application/json');

switch ($action) {

    // Historial de compras del cliente autenticado
    case 'misCompras':
        echo json_encode($ventaM->porCliente($_SESSION['user_id']));
        exit;

        // Detalle de una compra (valida que pertenezca al cliente)
    case 'detalleCompra':
        $id = (int)($_GET['id'] ?? 0);
        $venta = $ventaM->obtener($id);
        if (!$venta || (int)$venta['id_cliente'] !== (int)$_SESSION['user_id']) {
            echo json_encode(['ok' => false, 'msg' => 'Venta no encontrada']);
            exit;
        }
        echo json_encode(['venta' => $venta, 'detalle' => $ventaM->detalle($id)]);
        exit;

        // Solicitar devolución sobre una compra propia
    case 'solicitarDevolucion':
        $id_venta = (int)($_POST['id_venta'] ?? 0);
        $motivo   = trim($_POST['motivo'] ?? '');
        $items    = json_decode($_POST['items'] ?? '[]', true);

        if (!$id_venta || !$motivo || empty($items)) {
            echo json_encode(['ok' => false, 'msg' => 'Datos incompletos']);
            exit;
        }

        $venta = $ventaM->obtener($id_venta);
        if (!$venta || (int)$venta['id_cliente'] !== (int)$_SESSION['user_id']) {
            echo json_encode(['ok' => false, 'msg' => 'Venta no autorizada']);
            exit;
        }
        if ($venta['estado'] !== 'Completada') {
            echo json_encode(['ok' => false, 'msg' => 'Solo se pueden devolver ventas completadas']);
            exit;
        }

        // Validar cantidades contra la venta original
        $detalle = $ventaM->detalle($id_venta);
        $mapa    = array_column($detalle, 'cantidad', 'id_producto');
        foreach ($items as $item) {
            if (!isset($mapa[$item['id_producto']]) || $item['cantidad'] > $mapa[$item['id_producto']]) {
                echo json_encode(['ok' => false, 'msg' => 'Cantidad supera lo comprado']);
                exit;
            }
        }

        $total = array_sum(array_map(fn($i) => $i['cantidad'] * $i['precio_unitario'], $items));
        try {
            $id = $devM->crear([
                'id_venta'   => $id_venta,
                'motivo'     => $motivo,
                'total'      => $total,
                'id_usuario' => $_SESSION['user_id'],
            ], $items);
            echo json_encode(['ok' => true, 'msg' => 'Devolución registrada correctamente', 'id_devolucion' => $id]);
        } catch (Exception $e) {
            echo json_encode(['ok' => false, 'msg' => $e->getMessage()]);
        }
        exit;

        // Actualizar perfil del cliente
    case 'actualizarPerfil':
        $nombre   = trim($_POST['nombre']   ?? '');
        $apellido = trim($_POST['apellido'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $email    = trim($_POST['email']    ?? '');
        $pw_actual= $_POST['password_actual'] ?? '';
        $pw_nueva = $_POST['password_nueva']  ?? '';

        if (!$nombre || !$apellido || !$telefono || !$email) {
            echo json_encode(['ok'=>false,'msg'=>'Completa todos los campos obligatorios']); exit;
        }

        // Verificar que el email no lo use otro usuario
        $stmt = $db->prepare(
            "SELECT id_usuario FROM usuario WHERE LOWER(TRIM(email))=LOWER(TRIM(:e)) AND id_usuario != :id LIMIT 1"
        );
        $stmt->execute([':e' => $email, ':id' => $_SESSION['user_id']]);
        if ($stmt->rowCount() > 0) {
            echo json_encode(['ok'=>false,'msg'=>'Ese correo ya está registrado por otro usuario']); exit;
        }

        // Si quiere cambiar contraseña, verificar la actual
        $hash_nuevo = null;
        if (!empty($pw_nueva)) {
            if (empty($pw_actual)) {
                echo json_encode(['ok'=>false,'msg'=>'Ingresa tu contraseña actual para cambiarla']); exit;
            }
            $stmt2 = $db->prepare("SELECT password FROM usuario WHERE id_usuario=:id");
            $stmt2->execute([':id' => $_SESSION['user_id']]);
            $row = $stmt2->fetch(PDO::FETCH_ASSOC);
            if (!$row || !password_verify($pw_actual, $row['password'])) {
                echo json_encode(['ok'=>false,'msg'=>'La contraseña actual es incorrecta']); exit;
            }
            if (strlen($pw_nueva) < 6) {
                echo json_encode(['ok'=>false,'msg'=>'La nueva contraseña debe tener al menos 6 caracteres']); exit;
            }
            $hash_nuevo = password_hash($pw_nueva, PASSWORD_BCRYPT);
        }

        $sql = "UPDATE usuario SET nombre=:n, apellido=:a, telefono=:t, email=:e";
        if ($hash_nuevo) $sql .= ", password=:p";
        $sql .= " WHERE id_usuario=:id";

        $params = [':n'=>$nombre,':a'=>$apellido,':t'=>$telefono,':e'=>$email,':id'=>$_SESSION['user_id']];
        if ($hash_nuevo) $params[':p'] = $hash_nuevo;

        $stmt3 = $db->prepare($sql);
        if ($stmt3->execute($params)) {
            // Actualizar sesión
            $_SESSION['user_nombre']   = $nombre;
            $_SESSION['user_apellido'] = $apellido;
            $_SESSION['user_email']    = $email;
            echo json_encode(['ok'=>true,'msg'=>'Perfil actualizado correctamente']);
        } else {
            echo json_encode(['ok'=>false,'msg'=>'Error al actualizar el perfil']);
        }
        exit;

    // Mis devoluciones
    case 'misDevoluciones':
        $stmt = $db->prepare(
            "SELECT d.id_devolucion, d.fecha, d.motivo, d.total_devolucion, v.id_venta
             FROM devoluciones d
             JOIN venta v ON v.id_venta = d.id_venta
             WHERE v.id_cliente = :id ORDER BY d.fecha DESC"
        );
        $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;

    default:
        header('Location: ../../views/clientes/dashboard_cliente.php');
        exit;
}
