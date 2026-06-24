<?php

require_once __DIR__ . '/../datos/ProductosDatos.php';
/**
 * clase ProductoNegocio
 * 
 * esta clase pertenece a la capa de negocio.
 * se encarga de validar ey prepara los datos de los productos
 * antes de enviarlos a al capa de datos.
 */

class ProductoNegocio{
    private $productoDatos;

    /**
     * Constructor de la clase.
     * 
     * crea una instancia de ProductoDatos para acceder
     * a las operaciones relacionadas con la tabla tbl_productos.
     */

    public function __construct()
    {
        $this->productoDatos = new ProductosDatos();
    }

    /**
     * Listar productos activos.
     * 
     * solicita a la capa de datos todos los productos que no han sido eliminado logicamente.
     * 
     * @return array lista de productos activos.
     */

    public function listarProductos()
    {
        return $this->productoDatos->listarProductos();
    }

    /**
     * limpiar los datos del producto
     * 
     * elimina espacio innecesarios y convierte los valores numericos al tipo correspondiente antes de enviarlo a la capa de datos.
     * 
     * @param array $datos datos recibidos desde el formulario
     * @return array datos limpios del producto
     */
    
    private function limpiarDatos($datos){
        return [
            'nombre_producto' => trim($datos['nombre_producto']),
            'modelo' => isset( $datos['modelo']) ? trim($datos['modelo']) : '',
            'categoria_id' => (int) $datos['categoria_id'],
            'marca_id' => (int) $datos['marca_id'],
            'precio' => number_format((float) $datos['precio'], 2, '.', ''),
            'caracteristicas' => isset($datos['ccaracteristicas']) ? trim($datos['caracteristicas']) : '',
            'stock' => (int) $datos['stock'],
            'imagen' => isset($datos['imagen']) ? trim($datos['imagen']): 'sin-imagen.png'
        ];
    }

    private function validarProducto($datos){
        $errores = [];


        if(!isset($datos['nombre_producto']) || empty( trim($datos['nombre_producto'])) ){
            $errores[] = "El nombre del producto es obligatorio.";
        }

        if(isset($datos['nombre_producto']) && strlen( trim($datos['nombre_producto']) ) > 255 ){
            $errores[] = "El nombre del producto no debe superar los 255 caracteres";
        }

        if(!empty($datos['modelo']) && strlen( trim($datos['modelo'])) > 255 ){
            $errores[] = "El modelo no debe superar los 255 caracteres.";
        }

        if(!isset($datos['categoria_id']) || empty($datos['categoria_id']) || !is_numeric($datos['categoria_id'])){
            $errores[] = "Debe seleccionar una categoria valida.";
        }

        if(!isset($datos['marca_id']) || empty($datos['marca_id']) || !is_numeric($datos['marca_id'])){
            $errores[] = 'Debe seleccionar una marca valida.';
        }
        
        if(!isset($datos['precio']) || $datos['precio'] === '' || !is_numeric($datos['precio'])){
            $errores[] = "El Precio de venta es obligatorio y debe ser numerico";
        }elseif((float) $datos['precio'] <= 0){
            $errores[] = "El precio de venta debe ser mayor que cero.";
        }

        if(strlen(trim($datos['caracteristicas'])) > 255) {
            $errores[] = 'caracteristicas no debe de contener más de 255 caracteres';
        }

        if(!isset($datos['stock']) || $datos['stock'] === '' || !is_numeric($datos['stock'])){
            $errores[] = "las existencias son obligatorias y debe ser numericas.";
        }elseif((int) $datos['stock'] < 0){
            $errores[] = 'Las existencias no pueden ser negativas.';
        }

        if(!empty($datos['imagen']) && strlen(trim($datos['imagen'])) > 255 ){
            $errores[] = "El nombre de la imagen no debe superar los 255 caracteres.";
        }

        return $errores;
    }

    /**
     * crear un nuevo producto
     * 
     * valida los daots recibidos desde el formulario y, si son correctos,
     * los envia a la capa de datos para registrar el producto
     * 
     * @param array $datos datos recibidos ddesde el formulario.
     * @return array resultado de la operacion
     */

    public function crearProducto($datos){
        $errores = $this->validarProducto($datos);
        if(!empty($errores)){
            return [
                'exito' => false,
                'errores' => $errores
            ];
        }
        
        $producto = $this->limpiarDatos($datos);
        $resultado = $this->productoDatos->insertarProducto($producto);

        return [
            'exito' => $resultado,
            'mensaje' => $resultado ? 'Producto registrado correctamente.' : 'No se puedo registrar el producto'
        ];
    }


    public function obtenerProductoPorId($idProducto){
        if(!is_numeric($idProducto) || $idProducto <= 0) return null;

        return $this->productoDatos->obtenerProductoPorId($idProducto);
    }
        
    public function actualizarProducto($datos){
        $errores = $this->validarProducto($datos);

        if(!isset($datos['id_producto']) || empty($datos['id_producto'])){
            $errores[] = "El identificacdor del producto es obligatorio.";
        }

        if(!empty($errores)){
            return [
                'exito' => false,
                'errores' => $errores
            ];
        }

        $producto = $this->limpiarDatos($datos);
        $producto['id_producto'] = (int) $datos['id_producto'];

        $resultado = $this->productoDatos->actualizarProducto($producto);

        return [
            'exito' => $resultado,
            'mensaje' => $resultado ? 'Producto actualizado correcctamente.' : 'No se puedo actualizar el producto'
        ];
        
        
    }

    public function eliminarProducto($id_producto){
        if(!is_numeric($id_producto) || $id_producto <= 0){
            return [
                'exito' => false,
                'mensaje' => 'El identificador del producto no es válido.'.$id_producto
            ];
        }

        $resultado = $this->productoDatos->eliminarProducto($id_producto);

        return [
            'exito' => $resultado,
            'mensaje' => $resultado ? 'Producto eliminado correctamente.' : 'No se pudo eliminar el producto.'
        ];
    }

}