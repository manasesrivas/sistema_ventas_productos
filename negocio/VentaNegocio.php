<?php

require_once __DIR__ . '/../datos/VentaDatos.php';

class VentasNegocio
{
    private $ventaDatos;
    private $IVA = 0.13;

    public function __construct()
    {
        $this->ventaDatos = new VentaDatos();
    }

    //--errores-
    private function validarVenta($datos)
    {
        $errores = [];

        if (empty($datos['cliente_id']) || !is_numeric($datos['cliente_id'])) {
            $errores[] = "El cliente es obligatorio.";
        }

        if (empty($datos['usuario_id']) || !is_numeric($datos['usuario_id'])) {
            $errores[] = "El usuario es obligatorio.";
        }

        if (empty($productos) || !is_array($productos)) {
            $errores[] = "Debe agregar al menos un producto.";
        }
        return $errores;
    }

    //--Total-
    public function calcularTotales($productos)
    {
        $subtotal = 0;

        foreach ($productos as $producto) {
            $subtotal += $producto['precio'] * $producto['cantidad'];
        }

        $iva   = $subtotal * $this->IVA;
        $total = $subtotal + $iva;

        return [
            'subtotal' => round($subtotal, 2),
            'iva'      => round($iva, 2),
            'total'    => round($total, 2)
        ];
    }

    //--crear-
    public function crearVenta($datos, $productos)
    {
        $errores = $this->validarVenta($datos);

        if (!empty($errores)) {
            return ['exito' => false, 'errores' => $errores];
        }

        $totales = $this->calcularTotales($productos);

        $resultado = $this->ventaDatos->insertarVenta([
            'total'        => $totales['total'],
            'descuento_id' => $datos['descuento_id'] ?? null,
            'cliente_id'   => $datos['cliente_id'],
            'usuario_id'   => $datos['usuario_id']
        ]);

        if (!$resultado) {
            return ['exito' => false, 'mensaje' => 'No se pudo registrar la venta.'];
        }

        return [
            'exito'    => true,
            'mensaje'  => 'Venta registrada correctamente.',
            'id_venta' => $this->ventaDatos->obtenerUltimoId(),
            'totales'  => $totales
        ];
    }

    //--listar-
    public function listarVentas()
    {
        return $this->ventaDatos->listarVentas();
    }

}
