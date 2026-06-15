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
            'NombreProducto' => trim($datos['NombreProducto']),
            'Modelo' => isset( $datos['Modelo']) ? trim($datos['Modelo']) : '',
            'IdCategoria' => (int) $datos['IdCategoria'],
            'IdMarca' => (int) $datos['IdMarca'],
            'PrecioVenta' => number_format((float) $datos['PrecioVenta'], 2, '.', ''),
            'Caracteristicas' => isset($datos['Caracteristicas']) ? trim($datos['Caracteristicas']) : '',
            'Existencias' => (int) $datos['Existencias'],
            'Imagen' => isset($datos['Imagen']) ? trim($datos['Imgen']): 'sin-imagen.png'
        ];
    }

    private function validarProducto($datos){
        $errores = [];

        if(!isset($datos['NombreProducto']) || empty( trim($datos['NombreProducto'])) ){
            $errores[] = "El nombre del prodcuto es obligatorio.";
        }

        if(isset($datos['NombreProducto']) && strlen( trim($datos['NombreProducto']) ) > 255 ){
            $errores[] = "El nombre del producto no debe superar los 255 caracteres";
        }

        if(!empty($datos['Modelo']) && strlen( trim($datos['Modelo'])) > 255 ){
            $errores[] = "El modelo no debe superar los 255 caracteres.";
        }

        if(!isset($datos['IdCategoria']) || empty($datos['IdCategoria']) || !is_numeric($datos['IdCategoria'])){
            $errores[] = "Debe seleccionar una categoria valida.";
        }

        if(!isset($datos['IdMarca']) || empty($datos['IdMarca']) || !is_numeric($datos['PrecioVenta'])){
            $errores[] = 'Debe seleccionar una marca valida.';
        }
        
        if(!isset($datos['PrecioVenta']) || $datos['PrecioVenta'] === '' || !is_numeric($datos['PrecioVenta'])){
            $errores[] = "El Precio de venta es obligatorio y debe ser numerico";
        }elseif((float) $datos['PrecioVenta'] <= 0){
            $errores[] = "El precio de venta debe ser mayor que cero.";
        }

        if(!isset($datos['Existencias']) || $datos['Existencias'] === '' || !is_numeric($datos['Existencias'])){
            $errores[] = "las existencias son obligatorias y debe ser numericas.";
        }elseif((int) $datos['Existencias'] < 0){
            $errores[] = 'Las existencias no pueden ser negativas.';
        }

        if(!empty($datos['Imagen']) && strlen(trim($datos['Imagen'])) > 255 ){
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
            'mensaje' => $resultado ? 'Producto registrado correctamente. ' : 'No se puedo registrar el producto'
        ];
    }
        

}