<?php

class Usuario
{
    private $conn;
    private $table_name = "usuario";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function existeDuplicado($username, $email, $id_excluir = null)
    {
        // Se valida que el username coincida o que el email coincida (si no está vacío)
        $sql = "SELECT id_usuario FROM " . $this->table_name . " 
                WHERE (username = :u OR (email = :e AND email != ''))";
        if ($id_excluir) {
            $sql .= " AND id_usuario != :id";
        }
        $sql .= " LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':u', $username);
        $stmt->bindParam(':e', $email);
        if ($id_excluir) {
            $stmt->bindParam(':id', $id_excluir, PDO::PARAM_INT);
        }
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    public function registrar($datos)
    {
        $query = "INSERT INTO " . $this->table_name . "
                  (nombre, apellido, username, email, telefono, password, id_rol, estado)
                  VALUES (:nombre, :apellido, :username, :email, :telefono, :password, :id_rol, :estado)";

        $stmt = $this->conn->prepare($query);
        $hash = password_hash($datos['password'], PASSWORD_BCRYPT);

        $stmt->bindParam(':nombre',   $datos['nombre']);
        $stmt->bindParam(':apellido', $datos['apellido']);
        $stmt->bindParam(':username', $datos['username']);
        $stmt->bindParam(':email',    $datos['email']);
        $stmt->bindParam(':telefono', $datos['telefono']);
        $stmt->bindParam(':password', $hash);
        $stmt->bindParam(':id_rol',   $datos['id_rol']);
        $stmt->bindParam(':estado',   $datos['estado']);

        return $stmt->execute();
    }

    public function verificarCredenciales($username_or_email, $password)
    {
        $query = "SELECT id_usuario, nombre, apellido, username, email, password, id_rol, estado 
                  FROM " . $this->table_name . " 
                  WHERE (username = :u OR email = :e) AND estado = 'Activo'
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':u', $username_or_email);
        $stmt->bindParam(':e', $username_or_email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $row['password'])) {
                return $row;
            }
        }

        return false;
    }

    public function guardarTokenRecuperacion($email, $token)
    {
        // 1 hora de expiración
        $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $query = "UPDATE " . $this->table_name . " 
                  SET reset_token = :t, token_expiracion = :exp 
                  WHERE email = :e AND estado = 'Activo'";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':t', $token);
        $stmt->bindParam(':exp', $expira);
        $stmt->bindParam(':e', $email);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function validarToken($token)
    {
        $ahora = date('Y-m-d H:i:s');
        $query = "SELECT id_usuario FROM " . $this->table_name . " 
                  WHERE reset_token = :t AND token_expiracion > :ahora 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':t', $token);
        $stmt->bindParam(':ahora', $ahora);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function actualizarPasswordYLimpiarToken($token, $nueva_password)
    {
        $hash = password_hash($nueva_password, PASSWORD_BCRYPT);

        $query = "UPDATE " . $this->table_name . " 
                  SET password = :p, reset_token = NULL, token_expiracion = NULL 
                  WHERE reset_token = :t";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':p', $hash);
        $stmt->bindParam(':t', $token);
        
        return $stmt->execute() && $stmt->rowCount() > 0;
    }
}
