
<?php
// datos/DatosCategoria.php

require_once $_SERVER['DOCUMENT_ROOT'] . '/sistema_ventas_productos/config/Conexion.php';

class DatosCategoria {
    private $db;

    public function __construct() {
        $this->db = new Conexion();
    }

    // LISTAR CATEGORÍAS
    public function obtenerCategorias() {
        $con = $this->db->conectar();
        $sql = "SELECT Id_Categoria, Categoria, Descripcion FROM Categorias";
        $stmt = $con->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // GUARDAR NUEVA CATEGORÍA
    public function insertarCategoria($nombre, $descripcion) {
        $con = $this->db->conectar();
        $sql = "INSERT INTO Categorias (Categoria, Descripcion) VALUES (:nombre, :descripcion)";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        return $stmt->execute();
    }

    // MODIFICAR CATEGORÍA EXISENTE
    public function actualizarCategoria($id, $nombre, $descripcion) {
        $con = $this->db->conectar();
        $sql = "UPDATE Categorias SET Categoria = :nombre, Descripcion = :descripcion WHERE Id_Categoria = :id";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        return $stmt->execute();
    }

    // ELIMINAR CATEGORÍA
    public function borrarCategoria($id) {
        $con = $this->db->conectar();
        $sql = "DELETE FROM Categorias WHERE Id_Categoria = :id";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
 
?> 