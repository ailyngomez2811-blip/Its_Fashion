<?php
class Devolucion
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function listar()
    {
        $sql = "SELECT d.*, v.id_cliente,
                       CONCAT(uc.nombre,' ',uc.apellido) AS cliente_nombre,
                       CONCAT(ue.nombre,' ',ue.apellido) AS solicitante,
                       CONCAT(ua.nombre,' ',ua.apellido) AS admin_nombre
                FROM devoluciones d
                JOIN venta v ON v.id_venta = d.id_venta
                LEFT JOIN usuario uc ON uc.id_usuario = v.id_cliente
                LEFT JOIN usuario ue ON ue.id_usuario = d.id_usuario
                LEFT JOIN usuario ua ON ua.id_usuario = d.id_admin
                ORDER BY d.fecha DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function detalle($id_devolucion)
    {
        $sql = "SELECT dd.*, p.nombre AS producto, p.talla, p.color
                FROM detalledevolucion dd
                JOIN productos p ON p.id_producto = dd.id_producto
                WHERE dd.id_devolucion = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id_devolucion, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crea la solicitud en estado Pendiente, sin tocar stock ni caja.
     */
    public function crear($datos, $items)
    {
        $this->conn->beginTransaction();
        try {
            $sql  = "INSERT INTO devoluciones (id_venta, fecha, motivo, total_devolucion, id_usuario, estado)
                     VALUES (:iv, NOW(), :motivo, :total, :iu, 'Pendiente')";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':iv',    $datos['id_venta'],  PDO::PARAM_INT);
            $stmt->bindValue(':motivo', $datos['motivo']);
            $stmt->bindValue(':total', $datos['total']);
            $stmt->bindValue(':iu',    $datos['id_usuario'], PDO::PARAM_INT);
            $stmt->execute();
            $id_dev = $this->conn->lastInsertId();

            foreach ($items as $item) {
                $sql2 = "INSERT INTO detalledevolucion (id_devolucion, id_producto, cantidad, precio_unitario)
                         VALUES (:id, :ip, :qty, :pu)";
                $stmt2 = $this->conn->prepare($sql2);
                $stmt2->bindValue(':id',  $id_dev, PDO::PARAM_INT);
                $stmt2->bindValue(':ip',  $item['id_producto'], PDO::PARAM_INT);
                $stmt2->bindValue(':qty', $item['cantidad'], PDO::PARAM_INT);
                $stmt2->bindValue(':pu',  $item['precio_unitario']);
                $stmt2->execute();
            }

            $this->conn->commit();
            return $id_dev;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    /**
     * Acepta la solicitud: actualiza stock, registra en inventario y descuenta de caja.
     */
    public function aceptar($id_devolucion, $id_admin)
    {
        // Verificar que esté Pendiente
        $check = $this->conn->prepare("SELECT estado, id_venta, total_devolucion FROM devoluciones WHERE id_devolucion = :id");
        $check->bindParam(':id', $id_devolucion, PDO::PARAM_INT);
        $check->execute();
        $dev = $check->fetch(PDO::FETCH_ASSOC);
        if (!$dev || $dev['estado'] !== 'Pendiente') return false;

        $this->conn->beginTransaction();
        try {
            // Marcar como Aceptada
            $upd = $this->conn->prepare(
                "UPDATE devoluciones SET estado='Aceptada', fecha_resolucion=NOW(), id_admin=:ia WHERE id_devolucion=:id"
            );
            $upd->bindValue(':ia', $id_admin, PDO::PARAM_INT);
            $upd->bindValue(':id', $id_devolucion, PDO::PARAM_INT);
            $upd->execute();

            // Devolver stock y registrar movimiento
            $items = $this->detalle($id_devolucion);
            foreach ($items as $item) {
                $this->conn->prepare("UPDATE productos SET stock = stock + :qty WHERE id_producto = :id")
                    ->execute([':qty' => $item['cantidad'], ':id' => $item['id_producto']]);

                // Movimiento inventario: cantidad exacta devuelta
                $this->conn->prepare(
                    "INSERT INTO inventario (fecha_registro, stock_disponible, tipo_movimiento, id_producto, cantidad)
                     SELECT NOW(), stock, 'Entrada', id_producto, :qty FROM productos WHERE id_producto = :id"
                )->execute([':qty' => $item['cantidad'], ':id' => $item['id_producto']]);
            }

            // Egreso en caja independientemente del método de pago
            $stmt5 = $this->conn->prepare("SELECT metodo_pago FROM venta WHERE id_venta = :id");
            $stmt5->bindParam(':id', $dev['id_venta'], PDO::PARAM_INT);
            $stmt5->execute();
            $venta = $stmt5->fetch(PDO::FETCH_ASSOC);
            if ($venta) {
                $stmt6 = $this->conn->prepare("SELECT id_caja FROM caja WHERE estado = 'Abierta' LIMIT 1");
                $stmt6->execute();
                $caja = $stmt6->fetch(PDO::FETCH_ASSOC);
                if ($caja) {
                    $concepto = "Devolución #$id_devolucion (" . $venta['metodo_pago'] . ")";
                    $this->conn->prepare(
                        "INSERT INTO movimientos_caja (id_caja, tipo, monto, concepto, fecha)
                         VALUES (:ic, 'Egreso', :monto, :concepto, NOW())"
                    )->execute([':ic' => $caja['id_caja'], ':monto' => $dev['total_devolucion'], ':concepto' => $concepto]);

                    $this->conn->prepare(
                        "UPDATE caja SET total_egresos = COALESCE(total_egresos,0) + :m WHERE id_caja = :ic"
                    )->execute([':m' => $dev['total_devolucion'], ':ic' => $caja['id_caja']]);
                }
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    /**
     * Rechaza la solicitud sin ningún efecto en stock ni caja.
     */
    public function rechazar($id_devolucion, $id_admin)
    {
        $upd = $this->conn->prepare(
            "UPDATE devoluciones SET estado='Rechazada', fecha_resolucion=NOW(), id_admin=:ia
             WHERE id_devolucion=:id AND estado='Pendiente'"
        );
        $upd->bindValue(':ia', $id_admin, PDO::PARAM_INT);
        $upd->bindValue(':id', $id_devolucion, PDO::PARAM_INT);
        $upd->execute();
        return $upd->rowCount() > 0;
    }

    public function totales()
    {
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) AS total, SUM(total_devolucion) AS monto FROM devoluciones WHERE estado='Aceptada'"
        );
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function pendientes()
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM devoluciones WHERE estado='Pendiente'");
        $stmt->execute();
        return (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
