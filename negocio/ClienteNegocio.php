<?php

require_once __DIR__ . '/../datos/ClienteDatos.php';

class ClienteNegocio{
    private $clienteDatos;
    
    public function __construct()
    {
        $this->clienteDatos = new ClienteDatos();
    }

    public function listarClientes(){
        return $this->clienteDatos->listarClientes();
    }
    
    public function limpiarDatos($datos)
    {
        return [
            'nombres' =>  trim($datos['nombres']),
            'dui' =>            isset( $datos['dui']) ? trim($datos['dui']): '',
            'nit' =>            isset( $datos['nit']) ? trim($datos['nit']): '',
            'telefono' =>       isset( $datos['telefono']) ? trim($datos['telefono']): '',
            'direccion' =>      isset( $datos['direccion']) ? trim($datos['direccion']): '',
            'tipo' =>           isset( $datos['tipo']) ? trim($datos['tipo']): '',
            'nrc' =>            isset( $datos['nrc']) ? trim($datos['nrc']): '',

        ];
    }

    public function validarCliente($datos){
        $errores = [];
        if(!isset($datos['nombres']) || empty(trim($datos['nombres']))){
            $errores[] = 'El nombre del cliente es obligatorio.';
        }
        if(isset($datos['nombres']) && strlen(trim($datos['nombres'])) > 255){
            $errores[] = 'El nombre del cliente no debe superar los 255 caracteres.';
        }
        if(!empty($datos['dui']) && !preg_match('/^[0-9]{8}-?[0-9]{1}$/', trim($datos['dui']))){
            $errores[] = 'El DUI debe tener un formato valido. Ejemplo: 12345678-9.';
        }
        if(!empty($datos['nit']) && strlen(trim($datos['nit'])) > 20){
            $errores[] = 'El NIT no debe superar los 20 caracteres.';
        }
        if(!empty($datos['telefono']) && !preg_match('/[0-9]{4}-?[0-9]{4}$/', trim($datos['telefono']))){
            $errores[] = 'El Telefono debe tener un formato valido. Ejemplo: 7777-8888.';
        }
        if(!empty($datos['direccion']) && strlen(trim($datos['direccion'])) > 255){
            $errores[] = 'La direccion no debe superar los 255 caracteres.';
        }
        if(!empty($datos['tipo']) && strlen(trim($datos['tipo'])) > 2){
            $errores[] = 'El tipo de cliente no debe superar los 2 caracteres.';
        }
        if(!empty($datos['nrc']) && strlen(trim($datos['tipo'])) > 15){
            $errores[] = 'El NRC no debe superar los 15 caracteres.';
        }

        return $errores;
    }

    public function crearCliente($datos){
        $errores = $this->validarCliente($datos);

        if(!empty($errores)){
            return [
                'exito' => false,
                'errores' => $errores
            ];
        }
        $cliente = $this->limpiarDatos($datos);
        
        $resultado = $this->clienteDatos->insertarCliente($cliente);

        return [
            'exito' => $resultado,
            'mensaje' => $resultado ? 'Cliente registrado correctamente.' : 'No se pudo registrar el cliente.'
        ];
    }
    
    public function obtenerClientePorId($idCliente){
        if(!is_numeric($idCliente) || $idCliente <= 0){
            return null;
        }
        return $this->clienteDatos->obtenerClientePorId($idCliente);
    }

    public function actualizarCliente($datos){
        $errores = $this->validarCliente($datos);
        if(!isset($datos['id_cliente']) || empty($datos['id_cliente'])){
            $errores[] = "El identificador del cliente es obligatorio.";
        }

        if(!empty($errores)){
            return [
                'exito' => false,
                'errores' => $errores
            ];
        }
        $cliente = $this->limpiarDatos($datos);
        $cliente['id_cliente'] = (int)$datos['id_cliente'];

        $resultado = $this->clienteDatos->actualizarCliente($cliente);

        return [
            'exito' => $resultado,
            'mensaje' => $resultado ? 'Cliente actualizado correcatmente.' : 'No se pudo actualizar el cliente.'
        ];

    }

    public function eliminarCliente($idCliente){
        if(!is_numeric($idCliente) || $idCliente <= 0){
            return [
                'exito' => false,
                'mensaje' => 'El identificdor del cliente no es valido'
            ];
        }
        $resultado = $this->clienteDatos->eliminarCliente($idCliente);
        return [
            'exito' => $resultado,
            'mensaje' => $resultado ? 'Cliente eliminado correctamente.' : 'No se pudo eliminar el cliente.'
        ];
    }
}


class venta{

    public function __construct($producto, $cantidad, $precio)
    {
        $subtotal = $precio * $cantidad;

        $descuento = 0.10;

        $total = $subtotal - ($descuento * $subtotal);
    
    }
}