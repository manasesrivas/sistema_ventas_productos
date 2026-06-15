<?php

require_once __DIR__ . '/../config/env.php';

class Conexion{
    private $server;
    private $user;
    private $password;
    private $database;
    private $charset;
    private $connection;

    public $query;
    public $record_count;

    public function __construct()
    {
        $this->server   = $_ENV['DB_HOST'];
        $this->user     = $_ENV['DB_USER'];
        $this->password = $_ENV['DB_PASS'];
        $this->database = $_ENV['DB_NAME'];
        $this->charset  = $_ENV['DB_CHARSET'];
    }

    private function create_connection(){
        try{
            $dns = "mysql:host={$this->server};dbname={$this->database};charset={$this->charset}";
            $this->connection = new PDO(
                $dns,
                $this->user,
                $this->password
            );

            // configuracion de errores 
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            

            // retorno asociativo por defecto
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        }catch(PDOException $e){
            die('<h3 style="color: tomato; font-family: Arial;">
            No se puede establecer conexion con la base de datos:' . $e->getMessage() . '</h3>');
            }
        return $this->connection;
    }

    private function close_connection(){
        $this->connection = null;
    }

    // ejeccutar consultas INSERT, UPDATE o DELETE

    public function execute_query($params = []){
        try{
            $stmt = $this->create_connection()->prepare($this->query);
            $result = $stmt->execute($params);
            $this->close_connection();
            return $result;
        } catch(PDOException $e){
            die('Error en la consulta: ' . $e->getMessage());
        }
    }

    public function get_records($params = []){
        try{
            $stmt = $this->create_connection()->prepare($this->query);
            $stmt->execute($params);
            
            $records = $stmt->fetchAll();
            
            $this->record_count = count($records);
            
            $this->close_connection();

            return $records;
        }catch(PDOException $e){
            die("Error en la consulta: ". $e->getMessage());
        }
    }

    public function get_record($params = []){
        try{
            $stmt = $this->create_connection()->prepare($this->query);
            $stmt->execute($params);
            
            $record = $stmt->fetch();
            
            $this->close_connection();

            return $record;
        }catch(PDOException $e){
            die("Error en la consulta: ". $e->getMessage());
        }
        
    }
    
}