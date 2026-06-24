<?php
require_once __DIR__ . '/../datos/CategoriaDatos.php';

class CategoriaNegocio {
    private $categoriaDatos;

    public function __construct() {
        $this->categoriaDatos = new CategoriaDatos();
    }

    public function listarCategorias() {
        return $this->categoriaDatos->listarCategorias();
    }

    private function limpiarDatos($datos){
        return [
            'categoria' => trim($datos['categoria']),
            'descripcion' => isset( $datos['descripcion']) ? trim($datos['descripcion']) : ''
        ];
    }

    
    public function validarCategoria($datos) {
        $errores = [];

        if(!isset($datos['categoria']) || empty( trim($datos['categoria']) ))
            $errores[] = 'El nombre de la categoria es obligatorio';

        if(strlen( trim($datos['categoria']) ) > 50)
            $errores[] = 'El nombre de la categoria no debe superar los 50 caracteres.';

        if( strlen(trim($datos['descripcion'])) > 255) 
            $errores[] = 'La descripcion no debe contener más de 255 caracteres.';
    
        return $errores;
    }

    public function crearCategoria($datos){

        $errores = $this->validarCategoria($datos);
        
        if(!empty($errores))
            return [
                'exito' => false,
                'errores' => $errores
            ];
        
        $categoria = $this->limpiarDatos($datos);
        $resultado = $this->categoriaDatos->insertarCategoria($categoria);

        return[
            'exito' => $resultado,
            'mensaje' => $resultado ? 'Categoria registrada correctamente.' : 'No se pudo registrar la categoria.'
        ];
    }

    public function obtenerCategoriaPorId($id_categoria){
        if(!is_numeric($id_categoria) || $id_categoria <= 0) return null;

        return $this->categoriaDatos->obtenerCategoriaPorId($id_categoria);
    }

    public function actualizarCategoria($datos){
        $errores = $this->validarCategoria($datos);

        if(!isset($datos['id_categoria']) || empty($datos['id_categoria'])){
            $errores[] = 'El identificador de la categoria es obligatoria.';
        }

        if(!empty($errores)){
            return [
                'exito' => false,
                'errores' => $errores
            ];
        }

        $categoria = $this->limpiarDatos($datos);
        $categoria['id_categoria'] = (int)$datos['id_categoria'];

        $resultado = $this->categoriaDatos->actualizarCategoria($categoria);

        return [
            'exito' => $resultado,
            'mensaje' => $resultado ? 'categoria actualizada correctamente' : 'No se pudo actualizar.'
        ];
    }
    
}
?> 
