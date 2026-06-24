<?php

require_once __DIR__ . '/../datos/MarcaDatos.php';

class MarcaNegocio
{
    private $marcaDatos;

    public function __construct()
    {
        $this->marcaDatos = new MarcaDatos();
    }

    //--validar--
    private function validarMarca($datos)
    {
        $errores = [];

        if (empty(trim($datos['marca']))) {
            $errores[] = "El nombre de la marca es obligatorio.";
        }

        if (strlen(trim($datos['marca'])) > 30) {
            $errores[] = "La marca no debe superar los 30 caracteres.";
        }

        return $errores;
    }

    //--listar--
    public function listarMarcas()
    {
        return $this->marcaDatos->listarMarcas();
    }

    //--crear--
    public function crearMarca($datos)
    {
        $errores = $this->validarMarca($datos);

        if (!empty($errores)) {
            return ['exito' => false, 'errores' => $errores];
        }

        $resultado = $this->marcaDatos->insertarMarca([
            'marca' => trim($datos['marca'])
        ]);

        if (!$resultado) {
            return ['exito' => false, 'mensaje' => 'No se pudo registrar la marca.'];
        }

        return [
            'exito'    => true,
            'mensaje'  => 'Marca registrada correctamente.',
            'id_marca' => $this->marcaDatos->obtenerUltimoId()
        ];
    }

    //--actualizar--
    public function actualizarMarca($datos)
    {
        $errores = $this->validarMarca($datos);

        if (empty($datos['id_marca']) || !is_numeric($datos['id_marca'])) {
            $errores[] = "El identificador de la marca es obligatorio.";
        }

        if (!empty($errores)) {
            return ['exito' => false, 'errores' => $errores];
        }

        $resultado = $this->marcaDatos->actualizarMarca([
            'marca'    => trim($datos['marca']),
            'id_marca' => (int) $datos['id_marca']
        ]);

        return [
            'exito'   => $resultado,
            'mensaje' => $resultado ? 'Marca actualizada correctamente.' : 'No se pudo actualizar la marca.'
        ];
    }

    //--suspender--
    public function suspenderMarca($id_marca)
    {
        if (!is_numeric($id_marca) || $id_marca <= 0) {
            return ['exito' => false, 'mensaje' => 'El identificador de la marca no es válido.'];
        }

        $resultado = $this->marcaDatos->suspenderMarca($id_marca);

        return [
            'exito'   => $resultado,
            'mensaje' => $resultado ? 'Marca suspendida correctamente.' : 'No se pudo suspender la marca.'
        ];
    }

    //--reactivar--
    public function reactivarMarca($id_marca)
    {
        if (!is_numeric($id_marca) || $id_marca <= 0) {
            return ['exito' => false, 'mensaje' => 'El identificador de la marca no es válido.'];
        }

        $resultado = $this->marcaDatos->reactivarMarca($id_marca);

        return [
            'exito'   => $resultado,
            'mensaje' => $resultado ? 'Marca reactivada correctamente.' : 'No se pudo reactivar la marca.'
        ];
    }

    public function obtenerPorId($id_marca)
    {
        if (!is_numeric($id_marca) || $id_marca <= 0) {
            return null;
        }
        return $this->marcaDatos->obtenerPorId($id_marca);
    }

    // lista TODAS (activas e inactivas) para el panel de admin
    public function listarTodasLasMarcas()
    {
        return $this->marcaDatos->listarTodasLasMarcas();
    }
}