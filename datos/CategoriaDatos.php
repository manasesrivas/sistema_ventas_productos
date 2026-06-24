<?php

require_once __DIR__ . '/Conexion.php';

class CategoriaDatos {
    public function listarCategorias() {
        $conexion = new Conexion();
        $conexion->query = "SELECT id_categoria, categoria, descripcion FROM categorias";
        return $conexion->get_records();
    }

    public function valorNulo($valor){
        return trim($valor ?? '') === '' ? null : trim($valor);
    }

    public function insertarCategoria($categoria) {
        $conexion = new Conexion();

        $conexion->query = "INSERT INTO categorias (categoria, descripcion) 
                            VALUES(:categoria, :descripcion)";
        return $conexion->execute_query(
            [
                ':categoria' => $categoria['categoria'],
                ':descripcion' => $this->valorNulo($categoria['descripcion'])
            ]
        );
    }

    public function obtenerCategoriaPorId($id_categoria){
        $conexion = new Conexion();

        $conexion->query = "SELECT 
                                id_categoria,
                                categoria,
                                descripcion
                            FROM categorias WHERE id_categoria = :id_categoria";
        
        return $conexion->get_record(
            [
                ':id_categoria' => $id_categoria
            ]
        );
        
    }
    
    public function actualizarCategoria($categoria) {
        $conexion = new Conexion();
        
        $conexion->query = "UPDATE categorias
                            SET categoria = :categoria,
                                descripcion = :descripcion
                            WHERE id_categoria = :id_categoria";
        return $conexion->execute_query(
            [
                ':categoria' => $categoria['categoria'],
                ':descripcion' => $this->valorNulo($categoria['descripcion']),
                ':id_categoria' => $categoria['id_categoria']
            ]
        );
    }

    // ELIMINAR CATEGORÍA
    public function eliminarCategoria($id_categoria) {
        $conexion = new Conexion();

        $conexion->query = "DELETE FROM categorias 
                            WHERE id_categoria = :id_categoria";
        return $conexion->execute_query(
            [
                ':id_categoria' => $id_categoria
            ]
        );
    }
}
 
?> 