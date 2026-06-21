<?php

require_once __DIR__ . '/Conexion.php';

/**
 * clase Productos
 * 
 * esta clase pertenese a la capa de datos.
 * se encarga de ejecutar las consultas sql relacionadas
 * con la tabla productos.
 */

class ProductosDatos{
    public function listarProductos(){
        $conexion = new Conexion();
        $conexion->query = "SELECT productos.id_producto, productos.nombre_producto, 
                                    productos.categoria_id, categorias.categoria, productos.marca_id,
                                    marcas.marca, productos.precio,
                                    productos.stock, productos.imagen, productos.eliminado
                            FROM productos
                            INNER JOIN categorias ON productos.categoria_id = categorias.id_categoria
                            INNER JOIN marcas ON productos.marca_id = marcas.id_marca
                            WHERE productos.eliminado = 'N'
                            ORDER BY productos.id_producto DESC";
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
     * y ejecuta una consulta INSERT en la tabla productos
     * 
     * @param array $producto datos del producto
     * @return bool retorna true si el negocio se inserta correctamente
     */

    public function insertarProducto($producto){
        $conexion =new Conexion();

        $conexion->query = "INSERT INTO productos ( nombre_producto, marca_id, precio, categoria_id,
                                                    stock, imagen, modelo, caracteristicas, eliminado)
                            VALUES (:nombre_producto, :marca_id, :precio, :categoria_id, :stock, :imagen, :modelo, :caracteristicas, 'N')";
        return $conexion->execute_query(
            [
                ':nombre_producto' => $producto['nombre_producto'],
                ':modelo' => $this->valorNulo($producto['modelo']),
                ':categoria_id' => $producto['categoria_id'],
                ':marca_id' => $producto['marca_id'],
                ':precio' => $producto['precio'],
                ':caracteristicas' => $this->valorNulo($producto['caracteristicas']),
                ':stock' => $producto['stock'],
                ':imagen' => $this->valorNulo($producto['imagen'])
            ]
        );
    }

    /**
 * Obtener un producto por su ID.
 *
 * Busca un producto específico usando el campo id_producto.
 * Este método se utiliza principalmente para cargar los datos
 * en los formularios de edición y eliminación.
 *
 * @param int $id_producto Identificador del producto.
 * @return array|false Datos del producto encontrado o false si no existe.
 */
public function obtenerProductoPorId($id_producto)
{
    $conexion = new Conexion();

    $conexion->query = "SELECT
                            productos.id_producto,
                            productos.nombre_producto,
                            productos.categoria_id,
                            productos.marca_id,
                            marcas.marca,
                            productos.precio,
                            productos.stock,
                            productos.imagen,
                            productos.eliminado
                        FROM productos
                        INNER JOIN categorias
                            ON productos.categoria_id = categorias.categoria_id
                        INNER JOIN tbl_marcas
                            ON productos.marca_id = marca.id_marca
                        WHERE productos.id_producto = :id_producto
                        AND productos.eliminado = 'N'";

    return $conexion->get_record([
        ':id_producto' => $id_producto
    ]);
}
}