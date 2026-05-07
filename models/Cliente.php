<?php

class Cliente
{
    private $conn;
    private $table = "usuario";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Listar todos los clientes (id_rol = 3) con conteo de compras y devoluciones
    public function listar()
    {
        $sql = "SELECT u.id_usuario, u.nombre, u.apellido, u.username, u.telefono, u.email,
                       u.estado, u.fecha_registro,
                       COUNT(DISTINCT v.id_venta) AS compras,
                       COUNT(DISTINCT d.id_devolucion) AS devoluciones
                FROM {$this->table} u
                LEFT JOIN venta v ON v.id_cliente = u.id_usuario
                LEFT JOIN devoluciones d ON d.id_venta = v.id_venta
                WHERE u.id_rol = 3
                GROUP BY u.id_usuario
                ORDER BY u.fecha_registro DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Historial de compras de un cliente
    public function compras($id_cliente)
    {
        $sql = "SELECT v.id_venta, v.fecha, v.total, v.metodo_pago, v.estado,
                       GROUP_CONCAT(p.nombre, ' × ', dv.cantidad ORDER BY p.nombre SEPARATOR ', ') AS productos
                FROM venta v
                JOIN detalle_venta dv ON dv.id_venta = v.id_venta
                JOIN productos p ON p.id_producto = dv.id_producto
                WHERE v.id_cliente = :id
                GROUP BY v.id_venta
                ORDER BY v.fecha DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id_cliente, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Devoluciones de un cliente
    public function devoluciones($id_cliente)
    {
        $sql = "SELECT d.id_devolucion, d.fecha, d.motivo, d.total_devolucion,
                       v.id_venta
                FROM devoluciones d
                JOIN venta v ON v.id_venta = d.id_venta
                WHERE v.id_cliente = :id
                ORDER BY d.fecha DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id_cliente, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Activar / Desactivar
    public function cambiarEstado($id, $estado)
    {
        $stmt = $this->conn->prepare(
            "UPDATE {$this->table} SET estado = :estado WHERE id_usuario = :id AND id_rol = 3"
        );
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Totales para KPIs
    public function totales()
    {
        $stmt = $this->conn->prepare(
            "SELECT
               COUNT(*) AS total,
               SUM(estado = 'Activo') AS activos,
               (SELECT COUNT(*) FROM venta v JOIN usuario u ON u.id_usuario = v.id_cliente WHERE u.id_rol = 3) AS compras,
               (SELECT COUNT(*) FROM devoluciones d JOIN venta v ON v.id_venta = d.id_venta JOIN usuario u ON u.id_usuario = v.id_cliente WHERE u.id_rol = 3) AS devoluciones
             FROM {$this->table} WHERE id_rol = 3"
        );
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function registrar($datos)
    {
        $sql = "INSERT INTO {$this->table}
                (nombre, apellido, username, telefono, email, password, id_rol, estado)
                VALUES (:nombre, :apellido, :username, :telefono, :email, :password, 3, 'Activo')";
        $stmt = $this->conn->prepare($sql);
        $hash     = password_hash($datos['password'], PASSWORD_BCRYPT);
        $username = $datos['username'] ?? $datos['documento'] ?? explode('@', $datos['email'])[0];
        $stmt->bindParam(':nombre',   $datos['nombre']);
        $stmt->bindParam(':apellido', $datos['apellido']);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':telefono', $datos['telefono']);
        $stmt->bindParam(':email',    $datos['email']);
        $stmt->bindParam(':password', $hash);
        return $stmt->execute();
    }

    public function existeEmail($email)
    {
        $stmt = $this->conn->prepare(
            "SELECT id_usuario FROM {$this->table} WHERE LOWER(TRIM(email)) = LOWER(TRIM(:email)) LIMIT 1"
        );
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
