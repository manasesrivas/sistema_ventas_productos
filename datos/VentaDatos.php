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
    public function listarVentas($estado = '', $dia = '', $mes = '', $anio = '')
{
    $condiciones = [];
    $params      = [];

    if (!empty($estado)) {
        $condiciones[] = "v.estado = ?";
        $params[]      = $estado;
    }

    if (!empty($dia) && is_numeric($dia)) {
        $condiciones[] = "DAY(v.fecha_venta) = ?";
        $params[]      = (int) $dia;
    }

    if (!empty($mes) && is_numeric($mes)) {
        $condiciones[] = "MONTH(v.fecha_venta) = ?";
        $params[]      = (int) $mes;
    }

    if (!empty($anio) && is_numeric($anio)) {
        $condiciones[] = "YEAR(v.fecha_venta) = ?";
        $params[]      = (int) $anio;
    }

    $where = !empty($condiciones) ? "WHERE " . implode(" AND ", $condiciones) : "";

    $this->conexion->query = "SELECT v.id_venta, v.fecha_venta, v.total,
                              v.descuento_id, v.cliente_id, v.usuario_id, v.estado,
                              c.nombres AS nombre_cliente,
                              u.nombre_usuario
                              FROM ventas v
                              INNER JOIN clientes c ON v.cliente_id = c.id_cliente
                              INNER JOIN usuarios u ON v.usuario_id = u.id_usuario
                              {$where}
                              ORDER BY v.id_venta DESC";

    return $this->conexion->get_records($params);
}

    //--obtener una venta por id--
    public function obtenerPorId($id_venta)
    {
        $this->conexion->query = "SELECT v.id_venta, v.fecha_venta, v.total,
                                v.descuento_id, v.cliente_id, v.usuario_id, v.estado,
                                c.nombres AS nombre_cliente,
                                u.nombre_usuario
                                FROM ventas v
                                INNER JOIN clientes c ON v.cliente_id = c.id_cliente
                                INNER JOIN usuarios u ON v.usuario_id = u.id_usuario
                                WHERE v.id_venta = ?";
        return $this->conexion->get_record([$id_venta]);
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

     //--obtener último id insertado--
    public function obtenerUltimoId()
    {
        return $this->conexion->ultimo_id;
    }

    //--anular--
    public function anularVenta($id_venta)
    {
        $this->conexion->query = "UPDATE ventas SET estado = 'anulada'
                                WHERE id_venta = ?";
        return $this->conexion->execute_query([$id_venta]);
    }

    //--editar
    public function actualizarVenta($id_venta, $venta)
    {
        $this->conexion->query = "UPDATE ventas
                                SET total = ?, descuento_id = ?, cliente_id = ?, usuario_id = ?
                                WHERE id_venta = ?";
        return $this->conexion->execute_query([
            $venta['total'],
            !empty($venta['descuento_id']) ? (int) $venta['descuento_id'] : null,
            $venta['cliente_id'],
            $venta['usuario_id'],
            $id_venta
        ]);
    }

}