<?php
require_once __DIR__ . '/Conexion.php';

class VentaDatos
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = new Conexion();
    }

    //--listar--
    public function listarVentas()
    {
        $this->conexion->query = "SELECT id_venta, fecha_venta, total, 
                                  descuento_id, cliente_id, usuario_id
                                  FROM ventas
                                  ORDER BY id_venta DESC";
        return $this->conexion->get_records();
    }

    //--Inertar
    public function insertarVenta($venta)
    {
        $this->conexion->query = "INSERT INTO ventas (total, descuento_id, cliente_id, usuario_id)
                                  VALUES (?, ?, ?, ?)";
        return $this->conexion->execute_query([
            $venta['total'],
            !empty($venta['descuento_id']) ? (int) $venta['descuento_id'] : null,
            $venta['cliente_id'],
            $venta['usuario_id']
        ]);
    }
}