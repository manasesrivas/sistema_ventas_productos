<?php

require_once __DIR__ . '/Conexion.php';

/**
 * clase Productos
 * 
 * esta clase pertenese a la capa de datos.
 * se encarga de ejecutar las consultas sql relacionadas
 * con la tabla tbl_productos.
 */

class ProductosDatos{
    public function listarProductos(){
        $conexion = new Conexion();
        $conexion->query = "SELECT  tbl_productos.IdProducto, tbl_productos.NombreProducto, tbl_productos.Modelo,
                                    tbl_productos.IdCategoria, tbl_categorias.NombreCategoria, tbl_productos.IdMarca,
                                    tbl_marcas.NombreMarca, tbl_productos.PrecioVenta, tbl_productos.Caracteristicas,
                                    tbl_productos.Existencias, tbl_productos.Imagen, tbl_productos.Eliminado
                            FROM tbl_productos
                            INNER JOIN tbl_categorias ON tbl_productos.IdCategoria = tbl_categorias.IdCategoria
                            INNER JOIN tbl_marcas ON tbl_productos.IdMarca = tbl_marcas.IdMarca
                            WHERE tbl_productos.Eliminado = 'N'
                            ORDER BY tbl_productos.IdProducto DESC";
        return $conexion->get_records();
    }

    /**
     * convertir valores vacios a NULL
     * 
     * valida si un campo viene vacio. si esta vacio, retorna null;
     * si contiene informacion, retorna el valor limpio.
     * 
     * @param string|null $valor valor reibido.
     * @return string|null valor limpio o null.
     */

    public function valorNulo($valor){
        return trim($valor ?? '') === '' ? null : trim($valor);
    }
    
    /**
     * insertar un nuevo producto.
     * recibe los datos del producto desde la capa de negocio
     * y ejecuta una consulta INSERT en la tabla tbl_productos
     * 
     * @param array $producto datos del producto
     * @return bool retorna true si el negocio se inserta correctamente
     */

    public function insertarProducto($producto){
        $conexion =new Conexion();

        $conexion->query = "INSERT INTO tbl_productos ( NombreProducto, Modelo, IdCaractegoria, 
                                        IdMarca, PrecioVenta, Caracteristicas, Existencias, Imagen, Eliminado)
                            VALUES (:nombreProducto, :modelo, :idCategoria, :idMarca,
                                    :precioVenta, :caracteristicas, :existencias, :imagen, 'N')";
        return $conexion->execute_query(
            [
                ':nombreProducto' => $producto['NombreProducto'],
                ':modelo' => $this->valorNulo($producto['Modelo']),
                ':idCategoria' => $producto['IdCategoria'],
                ':idMarca' => $producto['IdeMarca'],
                ':precioVenta' => $producto['PrecioVenta'],
                ':caracteristicas' => $this->valorNulo($producto['Caracteristicas']),
                ':existencias' => $producto['Existencias'],
                ':imagen' => $this->valorNulo($producto['Imagen'])
            ]
        );
    }

    /**
 * Obtener un producto por su ID.
 *
 * Busca un producto específico usando el campo IdProducto.
 * Este método se utiliza principalmente para cargar los datos
 * en los formularios de edición y eliminación.
 *
 * @param int $idProducto Identificador del producto.
 * @return array|false Datos del producto encontrado o false si no existe.
 */
public function obtenerProductoPorId($idProducto)
{
    $conexion = new Conexion();

    $conexion->query = "SELECT
                            tbl_productos.IdProducto,
                            tbl_productos.NombreProducto,
                            tbl_productos.Modelo,
                            tbl_productos.IdCategoria,
                            tbl_categorias.NombreCategoria,
                            tbl_productos.IdMarca,
                            tbl_marcas.NombreMarca,
                            tbl_productos.PrecioVenta,
                            tbl_productos.Caracteristicas,
                            tbl_productos.Existencias,
                            tbl_productos.Imagen,
                            tbl_productos.Eliminado
                        FROM tbl_productos
                        INNER JOIN tbl_categorias
                            ON tbl_productos.IdCategoria = tbl_categorias.IdCategoria
                        INNER JOIN tbl_marcas
                            ON tbl_productos.IdMarca = tbl_marcas.IdMarca
                        WHERE tbl_productos.IdProducto = :idProducto
                        AND tbl_productos.Eliminado = 'N'";

    return $conexion->get_record([
        ':idProducto' => $idProducto
    ]);
}
}