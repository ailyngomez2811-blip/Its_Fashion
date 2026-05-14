<?php
date_default_timezone_set('America/Bogota'); // Zona horaria para PHP (fechas en vistas y controladores)

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

            // Se agregan los comandos iniciales para configurar la zona horaria en la base de datos
            $options = [
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '-05:00'"
            ];

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);

            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {

            die("Error de conexión: " . $e->getMessage());
        }

        return $this->conn;
    }
}
