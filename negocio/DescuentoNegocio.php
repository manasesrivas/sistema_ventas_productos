<?php

require_once __DIR__ . '/../datos/DescuentoDatos.php';

class DescuentoNegocio
{
    private $descuentoDatos;

    public function __construct()
    {
        $this->descuentoDatos = new DescuentoDatos();
    }

    //--validar--
    private function validarDescuento($datos)
    {
        $errores = [];

        if (!isset($datos['descuento']) || $datos['descuento'] === '') {
            $errores[] = "El porcentaje de descuento es obligatorio.";
        } else {
            $valor = (int) $datos['descuento'];
            if ($valor < 1 || $valor > 100) {
                $errores[] = "El descuento debe ser un porcentaje entre 1 y 100.";
            }
        }

        if (empty(trim($datos['descripcion'] ?? ''))) {
            $errores[] = "La descripción es obligatoria.";
        }

        if (strlen(trim($datos['descripcion'] ?? '')) > 50) {
            $errores[] = "La descripción no debe superar los 50 caracteres.";
        }

        return $errores;
    }

    //--listar activos--
    public function listarDescuentos()
    {
        return $this->descuentoDatos->listarDescuentos();
    }

    //--listar todos (admin)--
    public function listarTodosLosDescuentos()
    {
        return $this->descuentoDatos->listarTodosLosDescuentos();
    }

    //--crear--
    public function crearDescuento($datos)
    {
        $errores = $this->validarDescuento($datos);

        if (!empty($errores)) {
            return ['exito' => false, 'errores' => $errores];
        }

        $resultado = $this->descuentoDatos->insertarDescuento([
            'descuento'   => (int) $datos['descuento'],
            'descripcion' => trim($datos['descripcion'])
        ]);

        if (!$resultado) {
            return ['exito' => false, 'errores' => ['No se pudo registrar el descuento.']];
        }

        return [
            'exito'        => true,
            'mensaje'      => 'Descuento registrado correctamente.',
            'id_descuento' => $this->descuentoDatos->obtenerUltimoId()
        ];
    }

    //--actualizar--
    public function actualizarDescuento($datos)
    {
        $errores = $this->validarDescuento($datos);

        if (empty($datos['id_descuento']) || !is_numeric($datos['id_descuento'])) {
            $errores[] = "El identificador del descuento es obligatorio.";
        }

        if (!empty($errores)) {
            return ['exito' => false, 'errores' => $errores];
        }

        $resultado = $this->descuentoDatos->actualizarDescuento([
            'descuento'    => (int) $datos['descuento'],
            'descripcion'  => trim($datos['descripcion']),
            'id_descuento' => (int) $datos['id_descuento']
        ]);

        return [
            'exito'   => $resultado,
            'mensaje' => $resultado
                ? 'Descuento actualizado correctamente.'
                : 'No se pudo actualizar el descuento.'
        ];
    }

    //--suspender--
    public function suspenderDescuento($id_descuento)
    {
        if (!is_numeric($id_descuento) || $id_descuento <= 0) {
            return ['exito' => false, 'mensaje' => 'El identificador del descuento no es válido.'];
        }

        $resultado = $this->descuentoDatos->suspenderDescuento($id_descuento);

        return [
            'exito'   => $resultado,
            'mensaje' => $resultado
                ? 'Descuento suspendido correctamente.'
                : 'No se pudo suspender el descuento.'
        ];
    }

    //--reactivar--
    public function reactivarDescuento($id_descuento)
    {
        if (!is_numeric($id_descuento) || $id_descuento <= 0) {
            return ['exito' => false, 'mensaje' => 'El identificador del descuento no es válido.'];
        }

        $resultado = $this->descuentoDatos->reactivarDescuento($id_descuento);

        return [
            'exito'   => $resultado,
            'mensaje' => $resultado
                ? 'Descuento reactivado correctamente.'
                : 'No se pudo reactivar el descuento.'
        ];
    }

    //--obtener por id--
    public function obtenerPorId($id_descuento)
    {
        if (!is_numeric($id_descuento) || $id_descuento <= 0) {
            return null;
        }
        return $this->descuentoDatos->obtenerPorId($id_descuento);
    }
}