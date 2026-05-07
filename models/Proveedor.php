<?php
class Proveedor
{
    private $conn;
    private $table = 'proveedor';

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

    public function obtener($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id_proveedor = :id LIMIT 1");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($d)
    {
        $sql  = "INSERT INTO {$this->table} (nombre, contacto, telefono, email, direccion, documento)
                 VALUES (:nombre,:contacto,:telefono,:email,:direccion,:documento)";
        $stmt = $this->conn->prepare($sql);
        foreach (['nombre', 'contacto', 'telefono', 'email', 'direccion', 'documento'] as $k)
            $stmt->bindValue(':' . $k, $d[$k] ?? null);
        return $stmt->execute();
    }

    public function actualizar($id, $d)
    {
        $sql  = "UPDATE {$this->table} SET nombre=:nombre, contacto=:contacto, telefono=:telefono,
                 email=:email, direccion=:direccion, documento=:documento WHERE id_proveedor=:id";
        $stmt = $this->conn->prepare($sql);
        foreach (['nombre', 'contacto', 'telefono', 'email', 'direccion', 'documento'] as $k)
            $stmt->bindValue(':' . $k, $d[$k] ?? null);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function existeDocumento($doc, $excluirId = null)
    {
        $sql  = "SELECT id_proveedor FROM {$this->table} WHERE documento = :d";
        if ($excluirId) $sql .= " AND id_proveedor != :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':d', $doc);
        if ($excluirId) $stmt->bindParam(':id', $excluirId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
