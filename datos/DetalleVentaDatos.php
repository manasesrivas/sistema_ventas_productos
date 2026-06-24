<?php

require_once __DIR__ . '/Conexion.php';

class DetalleVentaDatos
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = new Conexion();
    }

    //--listar detalle de una venta--
    public function listarPorVenta($venta_id)
    {
        $this->conexion->query = "SELECT dv.id_detalle_venta, dv.venta_id, dv.producto_id,
                                dv.subtotal, dv.cantidad,
                                p.nombre_producto
                                FROM detalle_ventas dv
                                INNER JOIN productos p ON dv.producto_id = p.id_producto
                                WHERE dv.venta_id = ?";
        return $this->conexion->get_records([$venta_id]);
    }

    //--insertar un detalle--
    public function insertarDetalle($detalle)
    {
        $this->conexion->query = "INSERT INTO detalle_ventas (venta_id, producto_id, subtotal, cantidad)
                                  VALUES (?, ?, ?, ?)";
        return $this->conexion->execute_query([
            $detalle['venta_id'],
            $detalle['producto_id'],
            $detalle['subtotal'],
            $detalle['cantidad']
        ]);
    }

    //--eliminar todos los detalles de una venta--
    public function eliminarPorVenta($venta_id)
    {
        $this->conexion->query = "DELETE FROM detalle_ventas WHERE venta_id = ?";
        return $this->conexion->execute_query([$venta_id]);
    }
}   