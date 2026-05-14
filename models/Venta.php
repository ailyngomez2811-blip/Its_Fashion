<?php
class Venta
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function listar()
    {
        $sql = "SELECT v.*,
                       CONCAT(uc.nombre,' ',uc.apellido) AS cliente_nombre,
                       CONCAT(ue.nombre,' ',ue.apellido) AS empleado
                FROM venta v
                LEFT JOIN usuario uc ON uc.id_usuario = v.id_cliente
                LEFT JOIN usuario ue ON ue.id_usuario = v.id_usuario
                ORDER BY v.fecha DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function detalle($id_venta)
    {
        $sql = "SELECT dv.*, p.nombre AS producto, p.talla, p.color
                FROM detalle_venta dv
                JOIN productos p ON p.id_producto = dv.id_producto
                WHERE dv.id_venta = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id_venta, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtener($id)
    {
        $sql = "SELECT v.*,
                       CONCAT(uc.nombre,' ',uc.apellido) AS cliente_nombre,
                       CONCAT(ue.nombre,' ',ue.apellido) AS empleado
                FROM venta v
                LEFT JOIN usuario uc ON uc.id_usuario = v.id_cliente
                LEFT JOIN usuario ue ON ue.id_usuario = v.id_usuario
                WHERE v.id_venta = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($datos, $items)
    {
        $this->conn->beginTransaction();
        try {
            // Verificar stock
            foreach ($items as $item) {
                $stmt = $this->conn->prepare("SELECT stock FROM productos WHERE id_producto = :id AND estado = 'Activo'");
                $stmt->bindParam(':id', $item['id_producto'], PDO::PARAM_INT);
                $stmt->execute();
                $prod = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$prod || $prod['stock'] < $item['cantidad'])
                    throw new Exception("Stock insuficiente para uno o más productos.");
            }

            // Insertar venta
            $sql  = "INSERT INTO venta (fecha, total, id_cliente, metodo_pago, estado, id_usuario)
                     VALUES (NOW(), :total, :id_cliente, :metodo_pago, 'Completada', :id_usuario)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':total',       $datos['total']);
            $stmt->bindValue(':id_cliente',  $datos['id_cliente'] ?: null, PDO::PARAM_INT);
            $stmt->bindValue(':metodo_pago', $datos['metodo_pago']);
            $stmt->bindValue(':id_usuario',  $datos['id_usuario'], PDO::PARAM_INT);
            $stmt->execute();
            $id_venta = $this->conn->lastInsertId();

            foreach ($items as $item) {
                // Detalle
                $sql2 = "INSERT INTO detalle_venta (id_venta, id_producto, cantidad, precio_unitario)
                         VALUES (:iv, :ip, :qty, :pu)";
                $stmt2 = $this->conn->prepare($sql2);
                $stmt2->bindValue(':iv',  $id_venta, PDO::PARAM_INT);
                $stmt2->bindValue(':ip',  $item['id_producto'], PDO::PARAM_INT);
                $stmt2->bindValue(':qty', $item['cantidad'], PDO::PARAM_INT);
                $stmt2->bindValue(':pu',  $item['precio_unitario']);
                $stmt2->execute();

                // Descontar stock
                $stmt3 = $this->conn->prepare("UPDATE productos SET stock = stock - :qty WHERE id_producto = :id");
                $stmt3->bindValue(':qty', $item['cantidad'], PDO::PARAM_INT);
                $stmt3->bindValue(':id',  $item['id_producto'], PDO::PARAM_INT);
                $stmt3->execute();

                // Movimiento inventario: cantidad exacta vendida
                $stmt4 = $this->conn->prepare(
                    "INSERT INTO inventario (fecha_registro, stock_disponible, tipo_movimiento, id_producto, cantidad)
                     SELECT NOW(), stock, 'Salida', id_producto, :qty FROM productos WHERE id_producto = :id"
                );
                $stmt4->bindValue(':qty', $item['cantidad'], PDO::PARAM_INT);
                $stmt4->bindValue(':id', $item['id_producto'], PDO::PARAM_INT);
                $stmt4->execute();
            }
            // Registrar en caja activa (Efectivo y Transferencia)
            $stmt5 = $this->conn->prepare(
                "SELECT id_caja FROM caja WHERE estado = 'Abierta' LIMIT 1"
            );
            $stmt5->execute();
            $caja = $stmt5->fetch(PDO::FETCH_ASSOC);
            if ($caja) {
                $stmt6 = $this->conn->prepare(
                    "INSERT INTO movimientos_caja (id_caja, tipo, monto, concepto, fecha)
                     VALUES (:ic, 'Ingreso', :monto, :concepto, NOW())"
                );
                $concepto = "Venta #$id_venta (" . $datos['metodo_pago'] . ")";
                $stmt6->bindValue(':ic',      $caja['id_caja'], PDO::PARAM_INT);
                $stmt6->bindValue(':monto',   $datos['total']);
                $stmt6->bindValue(':concepto', $concepto);
                $stmt6->execute();
                // Actualizar total_ingresos en caja
                $this->conn->prepare(
                    "UPDATE caja SET total_ingresos = COALESCE(total_ingresos,0) + :m WHERE id_caja = :ic"
                )->execute([':m' => $datos['total'], ':ic' => $caja['id_caja']]);
            }

            $this->conn->commit();
            return $id_venta;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function cajaAbierta()
    {
        $stmt = $this->conn->prepare("SELECT id_caja FROM caja WHERE estado = 'Abierta' LIMIT 1");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    public function buscarCliente($q)
    {
        $like = "%{$q}%";
        $sql  = "SELECT id_usuario, nombre, apellido, email FROM usuario
                 WHERE id_rol = 3 AND estado = 'Activo'
                 AND (nombre LIKE :q OR apellido LIKE :q OR email LIKE :q)
                 LIMIT 10";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':q', $like);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function totales()
    {
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) AS total,
                    COALESCE(SUM(total), 0) AS monto,
                    SUM(CASE WHEN estado='Completada' THEN 1 ELSE 0 END) AS completadas,
                    SUM(CASE WHEN DATE(fecha)=CURDATE() THEN 1 ELSE 0 END) AS hoy
             FROM venta"
        );
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function porCliente($id_cliente)
    {
        $sql = "SELECT v.id_venta, v.fecha, v.total, v.metodo_pago, v.estado,
                       GROUP_CONCAT(p.nombre,' x',dv.cantidad ORDER BY p.nombre SEPARATOR ', ') AS productos
                FROM venta v
                JOIN detalle_venta dv ON dv.id_venta = v.id_venta
                JOIN productos p ON p.id_producto = dv.id_producto
                WHERE v.id_cliente = :id
                GROUP BY v.id_venta ORDER BY v.fecha DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id_cliente, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
