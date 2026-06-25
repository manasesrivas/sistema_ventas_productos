<?php

require_once __DIR__ . '/Conexion.php';

class DescuentoDatos
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = new Conexion();
    }

    //--listar activas--
    public function listarDescuentos()
    {
        $this->conexion->query = "SELECT id_descuento, descuento, descripcion, estado
                                  FROM descuentos
                                  WHERE estado = 'activa'
                                  ORDER BY descuento ASC";
        return $this->conexion->get_records();
    }

    //--listar todas (admin)--
    public function listarTodosLosDescuentos()
    {
        $this->conexion->query = "SELECT id_descuento, descuento, descripcion, estado
                                  FROM descuentos
                                  ORDER BY descuento ASC";
        return $this->conexion->get_records();
    }

    //--obtener por id--
    public function obtenerPorId($id_descuento)
    {
        $this->conexion->query = "SELECT id_descuento, descuento, descripcion, estado
                                  FROM descuentos
                                  WHERE id_descuento = ?";
        return $this->conexion->get_record([$id_descuento]);
    }

    //--insertar--
    public function insertarDescuento($datos)
    {
        $this->conexion->query = "INSERT INTO descuentos (descuento, descripcion, estado)
                                  VALUES (?, ?, 'activa')";
        return $this->conexion->execute_query([
            $datos['descuento'],
            $datos['descripcion']
        ]);
    }

    //--actualizar--
    public function actualizarDescuento($datos)
    {
        $this->conexion->query = "UPDATE descuentos
                                  SET descuento = ?, descripcion = ?
                                  WHERE id_descuento = ?";
        return $this->conexion->execute_query([
            $datos['descuento'],
            $datos['descripcion'],
            $datos['id_descuento']
        ]);
    }

    //--suspender--
    public function suspenderDescuento($id_descuento)
    {
        $this->conexion->query = "UPDATE descuentos SET estado = 'inactiva'
                                  WHERE id_descuento = ?";
        return $this->conexion->execute_query([$id_descuento]);
    }

    //--reactivar--
    public function reactivarDescuento($id_descuento)
    {
        $this->conexion->query = "UPDATE descuentos SET estado = 'activa'
                                  WHERE id_descuento = ?";
        return $this->conexion->execute_query([$id_descuento]);
    }

    //--obtener último id--
    public function obtenerUltimoId()
    {
        return $this->conexion->ultimo_id;
    }
}