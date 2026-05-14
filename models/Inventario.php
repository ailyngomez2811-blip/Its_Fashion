<?php
class Inventario {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Registra un nuevo movimiento en el historial del kardex
     */
    public function registrarMovimiento($id_producto, $tipo_movimiento, $stock_abastecido_final) {
        $sql = "INSERT INTO inventario (fecha_registro, stock_disponible, tipo_movimiento, id_producto) 
                VALUES (NOW(), :stock, :tipo, :id_producto)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':stock', $stock_abastecido_final, PDO::PARAM_INT);
        $stmt->bindValue(':tipo', $tipo_movimiento, PDO::PARAM_STR);
        $stmt->bindValue(':id_producto', $id_producto, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Obtiene todo el historial de kardex ordenado por los más recientes
     */
    public function listarHistorial($limite = 200) {
        $sql = "SELECT i.*, p.nombre, p.talla, p.color, p.estado 
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
