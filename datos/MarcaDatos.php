<?php

require_once __DIR__ . '/Conexion.php';

class MarcaDatos
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = new Conexion();
    }

    //--listar--
    public function listarMarcas()
    {
        $this->conexion->query = "SELECT id_marca, marca, estado
                                  FROM marcas
                                  WHERE estado = 1
                                  ORDER BY marca ASC";
        return $this->conexion->get_records();
    }

    //--obtener por id--
    public function obtenerPorId($id_marca)
    {
        $this->conexion->query = "SELECT id_marca, marca, estado
                                  FROM marcas
                                  WHERE id_marca = ?";
        return $this->conexion->get_record([$id_marca]);
    }

    //--insertar--
    public function insertarMarca($marca)
    {
        $this->conexion->query = "INSERT INTO marcas (marca, estado)
                                  VALUES (?, 0)";
        return $this->conexion->execute_query([$marca['marca']]);
    }

    //--actualizar--
    public function actualizarMarca($marca)
    {
        $this->conexion->query = "UPDATE marcas SET marca = ?
                                  WHERE id_marca = ?";
        return $this->conexion->execute_query([
            $marca['marca'],
            $marca['id_marca']
        ]);
    }

    //--suspender--
    public function suspenderMarca($id_marca)
    {
        $this->conexion->query = "UPDATE marcas SET estado = 0
                                  WHERE id_marca = ?";
        return $this->conexion->execute_query([$id_marca]);
    }

    //--reactivar--
    public function reactivarMarca($id_marca)
    {
        $this->conexion->query = "UPDATE marcas SET estado = 1
                                  WHERE id_marca = ?";
        return $this->conexion->execute_query([$id_marca]);
    }

    //--obtener último id--
    public function obtenerUltimoId()
    {
        return $this->conexion->ultimo_id;
    }

    public function listarTodasLasMarcas()
{
    $this->conexion->query = "SELECT id_marca, marca, estado
                              FROM marcas
                              ORDER BY marca ASC";
    return $this->conexion->get_records();
}
}