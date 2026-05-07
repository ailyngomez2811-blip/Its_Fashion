<?php
class Compra
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function listar()
    {
        $sql = "SELECT c.*, p.nombre AS proveedor_nombre,
                       CONCAT(u.nombre,' ',u.apellido) AS empleado
                FROM compras c
                LEFT JOIN proveedor p ON p.id_proveedor = c.id_proveedor
                LEFT JOIN usuario u ON u.id_usuario = c.id_usuario
                ORDER BY c.fecha DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function detalle($id_compra)
    {
        $sql = "SELECT dc.*, p.nombre AS producto, p.talla, p.color
                FROM detallecompra dc
                JOIN productos p ON p.id_producto = dc.id_producto
                WHERE dc.id_compra = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id_compra, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear($datos, $items)
    {
        $this->conn->beginTransaction();
        try {
            $sql  = "INSERT INTO compras (fecha, total, id_proveedor, id_usuario)
                     VALUES (NOW(), :total, :id_proveedor, :id_usuario)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':total',       $datos['total']);
            $stmt->bindValue(':id_proveedor', $datos['id_proveedor'], PDO::PARAM_INT);
            $stmt->bindValue(':id_usuario',  $datos['id_usuario'],   PDO::PARAM_INT);
            $stmt->execute();
            $id_compra = $this->conn->lastInsertId();

            foreach ($items as $item) {
                $subtotal = $item['cantidad'] * $item['precio_unitario'];
                $sql2 = "INSERT INTO detallecompra (id_compra, id_producto, cantidad, precio_unitario, subtotal)
                         VALUES (:ic, :ip, :qty, :pu, :sub)";
                $stmt2 = $this->conn->prepare($sql2);
                $stmt2->bindValue(':ic',  $id_compra, PDO::PARAM_INT);
                $stmt2->bindValue(':ip',  $item['id_producto'], PDO::PARAM_INT);
                $stmt2->bindValue(':qty', $item['cantidad'], PDO::PARAM_INT);
                $stmt2->bindValue(':pu',  $item['precio_unitario']);
                $stmt2->bindValue(':sub', $subtotal);
                $stmt2->execute();

                // Aumentar stock
                $stmt3 = $this->conn->prepare("UPDATE productos SET stock = stock + :qty WHERE id_producto = :id");
                $stmt3->bindValue(':qty', $item['cantidad'], PDO::PARAM_INT);
                $stmt3->bindValue(':id',  $item['id_producto'], PDO::PARAM_INT);
                $stmt3->execute();

                // Movimiento inventario
                $stmt4 = $this->conn->prepare(
                    "INSERT INTO inventario (fecha_registro, stock_disponible, tipo_movimiento, id_producto)
                     SELECT NOW(), stock, 'Entrada', id_producto FROM productos WHERE id_producto = :id"
                );
                $stmt4->bindValue(':id', $item['id_producto'], PDO::PARAM_INT);
                $stmt4->execute();
            }

            $this->conn->commit();
            return $id_compra;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function totales()
    {
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) AS total,
                    COALESCE(SUM(total), 0) AS monto,
                    SUM(CASE WHEN DATE(fecha)=CURDATE() THEN 1 ELSE 0 END) AS hoy
             FROM compras"
        );
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
