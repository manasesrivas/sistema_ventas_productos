<?php

require_once __DIR__ . '/../datos/VentaDatos.php';
require_once __DIR__ . '/DetalleVentaNegocio.php';

class VentaNegocio
{
    private $ventaDatos;
    private $detalleVentaNegocio;
    private $IVA = 0.13;

    public function __construct()
    {
        $this->ventaDatos = new VentaDatos();
        $this->detalleVentaNegocio = new DetalleVentaNegocio();
    }
    
    //--errores--
    private function validarVenta($datos, $productos)
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

        foreach ($productos as $i => $producto) {
            if (empty($producto['cantidad']) || (int) $producto['cantidad'] <= 0) {
                $errores[] = "El producto #" . ($i + 1) . " no tiene cantidad válida.";
            }
        }

        return $errores;
    }

    //--Total--
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

    //--crear--
    public function crearVenta($datos, $productos)
    {
        $errores = $this->validarVenta($datos, $productos);

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

        $id_venta = $this->ventaDatos->obtenerUltimoId();

        $erroresDetalle = [];

        foreach ($productos as $producto) {
            $resultadoDetalle = $this->detalleVentaNegocio->registrarDetalle($id_venta, $producto);

            if (!$resultadoDetalle['exito']) {
                $erroresDetalle[] = $resultadoDetalle['errores'] ?? $resultadoDetalle['mensaje'];
            }
        }

        if (!empty($erroresDetalle)) {
            return [
                'exito'    => false,
                'mensaje'  => 'La venta se registró pero hubo errores en el detalle.',
                'id_venta' => $id_venta,
                'errores'  => $erroresDetalle
            ];
        }

        return [
            'exito'    => true,
            'mensaje'  => 'Venta registrada correctamente.',
            'id_venta' => $id_venta,
            'totales'  => $totales
        ];
    }

    //--anular--
    public function anularVenta($id_venta)
    {
        if (!is_numeric($id_venta) || $id_venta <= 0) {
            return ['exito' => false, 'mensaje' => 'El identificador de la venta no es válido.'];
        }

        $resultado = $this->ventaDatos->anularVenta($id_venta);

        return [    
            'exito'   => $resultado,
            'mensaje' => $resultado ? 'Venta anulada correctamente.' : 'No se pudo anular la venta.'
        ];
    }

    //--listar--
    public function listarVentas($estado = '', $dia = '', $mes = '', $anio = '')
    {
        return $this->ventaDatos->listarVentas($estado, $dia, $mes, $anio);
    }

    //--obtener venta
    public function obtenerVentaConDetalle($id_venta)
    {
        if (!is_numeric($id_venta) || $id_venta <= 0) {
            return null;
        }

        $venta = $this->ventaDatos->obtenerPorId($id_venta);

        if (!$venta) {
            return null;
        }

        $venta['productos'] = $this->detalleVentaNegocio->listarDetallePorVenta($id_venta);

        return $venta;
    }

    public function actualizarVenta($id_venta, $datos, $productos)
    {
        $errores = $this->validarVenta($datos, $productos);

        if (!empty($errores)) {
            return ['exito' => false, 'errores' => $errores];
        }

        $totales = $this->calcularTotales($productos);

        $resultado = $this->ventaDatos->actualizarVenta($id_venta, [
            'total'        => $totales['total'],
            'descuento_id' => $datos['descuento_id'] ?? null,
            'cliente_id'   => $datos['cliente_id'],
            'usuario_id'   => $datos['usuario_id']
        ]);

        if (!$resultado) {
            return ['exito' => false, 'mensaje' => 'No se pudo actualizar la venta.'];
        }

        // borrar detalle viejo y reinsertar
        $this->detalleVentaNegocio->eliminarDetallePorVenta($id_venta);

        $erroresDetalle = [];

        foreach ($productos as $producto) {
            $resultadoDetalle = $this->detalleVentaNegocio->registrarDetalle($id_venta, $producto);

            if (!$resultadoDetalle['exito']) {
                $erroresDetalle[] = $resultadoDetalle['errores'] ?? $resultadoDetalle['mensaje'];
            }
        }

        if (!empty($erroresDetalle)) {
            return [
                'exito'   => false,
                'mensaje' => 'La venta se actualizó pero hubo errores en el detalle.',
                'errores' => $erroresDetalle
            ];
        }

        return [
            'exito'   => true,
            'mensaje' => 'Venta actualizada correctamente.',
            'totales' => $totales
        ];
    }
}