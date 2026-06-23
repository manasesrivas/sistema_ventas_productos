<?php

require_once __DIR__ . '/Conexion.php';

class ClienteDatos{
    public function listarClientes(){
        $conexion = new Conexion();

        $conexion->query = "SELECT id_cliente, nombres, dui, nit, telefono, direccion, tipo, nrc, eliminado
        FROM clientes WHERE eliminado = 'N' ORDER BY id_cliente DESC";

        return $conexion->get_records();
    }

    private function valorNulo($valor){
        return trim($valor) === '' ? null: trim($valor);
    }

    public function insertarCliente($cliente){
        $conexion = new Conexion();

        $conexion->query = "INSERT INTO clientes 
        (nombres, dui, nit, telefono, direccion, tipo, nrc, eliminado)
        VALUES (:nombre, :dui, :nit, :telefono, :direccion, :tipo, :nrc, 'N')";

        return $conexion->execute_query(
            [
                ':nombre' => $cliente['nombres'],
                ':dui' => $this->valorNulo($cliente['dui']),
                ':nit' => $this->valorNulo($cliente['nit']),
                ':telefono' => $this->valorNulo($cliente['telefono']),
                ':direccion' => $this->valorNulo($cliente['direccion']),
                ':tipo' => $this->valorNulo($cliente['tipo']),
                ':nrc' => $this->valorNulo($cliente['nrc']),
            ]
        );
        
        }
        
    public function actualizarCliente($cliente){
        $conexion = new Conexion();
        $conexion->query = "UPDATE clientes SET 
        nombres = :nombre, dui = :dui, nit = :nit, telefono = :telefono, direccion = :direccion,
        tipo = :tipo, nrc = :nrc WHERE id_cliente = :idCliente";
                    
        return $conexion->execute_query([
            ':nombre' => $cliente['nombres'],
            ':dui' => $this->valorNulo($cliente['dui']),
            ':nit' => $this->valorNulo($cliente['nit']),
            ':telefono' => $this->valorNulo($cliente['telefono']),
            ':direccion' => $this->valorNulo($cliente['direccion']),
            ':tipo' => $this->valorNulo($cliente['tipo']),
            ':nrc' => $this->valorNulo($cliente['nrc']),
            ':idCliente' => $cliente['id_cliente'],
        ]);

    }

    public function obtenerClientePorId($idCliente){
        $conexion = new Conexion();
        $conexion->query = "SELECT id_cliente, nombres, dui, nit, telefono, direccion, tipo, nrc, eliminado
        FROM clientes WHERE id_cliente = :idCliente AND eliminado = 'N'";

        return $conexion->get_record(
            [
                ':idCliente' => $idCliente
            ]
        );
        
    }

    public function eliminarCliente($idCliente){
        $conexion = new Conexion();

        $conexion->query = "UPDATE FROM clientes SET eliminado = 'S' WHERE id_cliente = :idCliente";

        return $conexion->execute_query(
            [
                ':idCliente' => $idCliente
            ]
        );
    }

}