<?php
class Database
{
    private $host = "127.0.0.1"; //permiso host
    private $port = "3306"; //  ← PUERTO POR DEFECTO DE MYSQL EN LARAGON
    private $db_name = "its-fashion"; //nombre de la base de datos      
    private $username = "root"; //permiso usuario
    private $password = ""; //permiso contraseña


    public $conn; //variable conexion a la base de datos

    public function conectar()
    {

        $this->conn = null; //conexion a la base de datos

        try {

            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db_name};charset=utf8mb4";

            $this->conn = new PDO($dsn, $this->username, $this->password);

            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {

            die("Error de conexión: " . $e->getMessage());
        }

        return $this->conn;
    }
}
