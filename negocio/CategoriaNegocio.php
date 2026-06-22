<?php
// negocio/NegocioCategoria.php

require_once $_SERVER['DOCUMENT_ROOT'] . '/sistema_ventas_productos/datos/DatosCategoria.php';

class NegocioCategoria {
    private $datos;

    public function __construct() {
        $this->datos = new DatosCategoria();
    }

    public function listar() {
        return $this->datos->obtenerCategorias();
    }

    public function guardar($nombre, $descripcion) {
        // Aquí podrías poner reglas, ej: verificar que el nombre no vaya vacío
        return $this->datos->insertarCategoria($nombre, $descripcion);
    }

    public function modificar($id, $nombre, $descripcion) {
        return $this->datos->actualizarCategoria($id, $nombre, $descripcion);
    }

    public function eliminar($id) {
        return $this->datos->borrarCategoria($id);
    }
}
?> 
