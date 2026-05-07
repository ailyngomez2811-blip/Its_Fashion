<?php
class Categoria
{
    private $conn;
    private $table = 'categoria';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function listar()
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} ORDER BY nombre ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear($nombre, $descripcion)
    {
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (nombre, descripcion) VALUES (:n, :d)");
        $stmt->bindParam(':n', $nombre);
        $stmt->bindParam(':d', $descripcion);
        return $stmt->execute();
    }

    public function actualizar($id, $nombre, $descripcion)
    {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET nombre=:n, descripcion=:d WHERE id_categoria=:id");
        $stmt->bindParam(':n',  $nombre);
        $stmt->bindParam(':d',  $descripcion);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function eliminar($id)
    {
        // Verificar si tiene productos asociados
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM productos WHERE id_categoria = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->fetchColumn() > 0) return false;
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id_categoria = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function existeNombre($nombre, $excluirId = null)
    {
        $sql  = "SELECT id_categoria FROM {$this->table} WHERE LOWER(TRIM(nombre)) = LOWER(TRIM(:n))";
        if ($excluirId) $sql .= " AND id_categoria != :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':n', $nombre);
        if ($excluirId) $stmt->bindParam(':id', $excluirId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
