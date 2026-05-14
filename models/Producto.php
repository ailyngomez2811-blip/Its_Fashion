<?php
class Producto
{
    private $conn;
    private $table = 'productos';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function listar()
    {
        $sql = "SELECT p.*, c.nombre AS categoria_nombre
                FROM {$this->table} p
                LEFT JOIN categoria c ON c.id_categoria = p.id_categoria
                ORDER BY p.nombre ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscar($q)
    {
        $like = "%{$q}%";
        $sql  = "SELECT p.*, c.nombre AS categoria_nombre
                 FROM {$this->table} p
                 LEFT JOIN categoria c ON c.id_categoria = p.id_categoria
                 WHERE p.nombre LIKE :q OR p.talla LIKE :q OR p.color LIKE :q
                 ORDER BY p.nombre ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':q', $like);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtener($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id_producto = :id LIMIT 1");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($d)
    {
        $sql = "INSERT INTO {$this->table}
                (nombre, descripcion, precio_venta, precio_compra, stock, stock_minimo, talla, color, estado, id_categoria)
                VALUES (:nombre,:descripcion,:precio_venta,:precio_compra,:stock,:stock_minimo,:talla,:color,:estado,:id_categoria)";
        $stmt = $this->conn->prepare($sql);
        foreach (['nombre', 'descripcion', 'precio_venta', 'precio_compra', 'stock', 'stock_minimo', 'talla', 'color', 'estado', 'id_categoria'] as $k)
            $stmt->bindValue(':' . $k, $d[$k]);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function actualizar($id, $d)
    {
        $sql = "UPDATE {$this->table} SET
                nombre=:nombre, descripcion=:descripcion, precio_venta=:precio_venta,
                precio_compra=:precio_compra, stock=:stock, stock_minimo=:stock_minimo,
                talla=:talla, color=:color, estado=:estado, id_categoria=:id_categoria
                WHERE id_producto=:id";
        $stmt = $this->conn->prepare($sql);
        foreach (['nombre', 'descripcion', 'precio_venta', 'precio_compra', 'stock', 'stock_minimo', 'talla', 'color', 'estado', 'id_categoria'] as $k)
            $stmt->bindValue(':' . $k, $d[$k]);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function cambiarEstado($id, $estado)
    {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET estado=:e WHERE id_producto=:id");
        $stmt->bindParam(':e', $estado);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function actualizarStock($id, $cantidad)
    {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET stock = stock + :qty WHERE id_producto = :id");
        $stmt->bindParam(':qty', $cantidad, PDO::PARAM_INT);
        $stmt->bindParam(':id',  $id,       PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function totales()
    {
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) AS total,
                    SUM(estado='Activo') AS activos,
                    SUM(stock=0) AS agotados,
                    SUM(stock>0 AND stock<=stock_minimo) AS criticos
             FROM {$this->table}"
        );
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function listarActivos()
    {
        $sql = "SELECT p.*, c.nombre AS categoria_nombre
                FROM {$this->table} p
                LEFT JOIN categoria c ON c.id_categoria = p.id_categoria
                WHERE p.estado = 'Activo' AND p.stock > 0
                ORDER BY p.nombre ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
