<?php

class Database {

    private $host = "localhost";
    private $db_name = "citas_medicas";
    private $username = "root";
    private $password = "admin";
    private $charset = "utf8mb4";

    public function conectar() {

        try {

            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";

            $pdo = new PDO($dsn, $this->username, $this->password);

            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            return $pdo;

        } catch (PDOException $e) {

            die("Error de conexión: " . $e->getMessage());

        }
    }
}