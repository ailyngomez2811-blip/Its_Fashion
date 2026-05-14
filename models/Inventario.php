<?php
class Inventario {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Registra un nuevo movimiento en el historial del kardex.
     * $cantidad = unidades que se movieron (positivo siempre, el tipo indica la dirección).
     */
    public function registrarMovimiento($id_producto, $tipo_movimiento, $stock_resultante, $cantidad = null) {
        // Si no se pasa cantidad, intentamos calcularla desde el stock anterior
        if ($cantidad === null) {
            $prev = $this->conn->prepare("SELECT stock_disponible FROM inventario WHERE id_producto = :id ORDER BY id_inventario DESC LIMIT 1");
            $prev->bindValue(':id', $id_producto, PDO::PARAM_INT);
            $prev->execute();
            $row = $prev->fetch(PDO::FETCH_ASSOC);
            $cantidad = $row ? abs($stock_resultante - (int)$row['stock_disponible']) : $stock_resultante;
        }

        $sql = "INSERT INTO inventario (fecha_registro, stock_disponible, tipo_movimiento, id_producto, cantidad)
                VALUES (NOW(), :stock, :tipo, :id_producto, :cantidad)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':stock',      $stock_resultante,  PDO::PARAM_INT);
        $stmt->bindValue(':tipo',       $tipo_movimiento,   PDO::PARAM_STR);
        $stmt->bindValue(':id_producto',$id_producto,       PDO::PARAM_INT);
        $stmt->bindValue(':cantidad',   (int)$cantidad,     PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Obtiene todo el historial de kardex ordenado por los más recientes.
     */
    public function listarHistorial($limite = 200) {
        $sql = "SELECT i.id_inventario, i.fecha_registro, i.stock_disponible,
                       i.tipo_movimiento, i.id_producto,
                       COALESCE(i.cantidad, 0) AS cantidad_movimiento,
                       p.nombre, p.talla, p.color, p.estado
                FROM inventario i
                INNER JOIN productos p ON i.id_producto = p.id_producto
                ORDER BY i.fecha_registro DESC
                LIMIT :lim";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':lim', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
