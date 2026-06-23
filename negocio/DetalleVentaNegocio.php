<?php

require_once __DIR__ . '/../datos/DetalleVentaDatos.php';

class DetalleVentaNegocio
{
    private $detalleVentaDatos;

    public function __construct()
    {
        $this->detalleVentaDatos = new DetalleVentaDatos();
    }

    //--validar un producto del detalle--
    private function validarDetalle($producto)
    {
        $errores = [];

        if (empty($producto['producto_id']) || !is_numeric($producto['producto_id'])) {
            $errores[] = "El producto es obligatorio.";
        }

        if (!isset($producto['cantidad']) || !is_numeric($producto['cantidad']) || $producto['cantidad'] <= 0) {
            $errores[] = "La cantidad debe ser un número mayor a cero.";
        }

        if (!isset($producto['precio']) || !is_numeric($producto['precio']) || $producto['precio'] < 0) {
            $errores[] = "El precio debe ser un número válido.";
        }

        return $errores;
    }

    //--registrar el detalle de un producto dentro de una venta--
    public function registrarDetalle($venta_id, $producto)
    {
        $errores = $this->validarDetalle($producto);

        if (!empty($errores)) {
            return ['exito' => false, 'errores' => $errores];
        }

        $resultado = $this->detalleVentaDatos->insertarDetalle([
            'venta_id'    => $venta_id,
            'producto_id' => $producto['producto_id'],
            'subtotal'    => $producto['precio'] * $producto['cantidad'],
            'cantidad'    => $producto['cantidad']
        ]);

        if (!$resultado) {
            return ['exito' => false, 'mensaje' => 'No se pudo registrar el detalle de la venta.'];
        }

        return ['exito' => true];
    }

    //--listar detalle de una venta--
    public function listarDetallePorVenta($venta_id)
    {
        return $this->detalleVentaDatos->listarPorVenta($venta_id);
    }
}