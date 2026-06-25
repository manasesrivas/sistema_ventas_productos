<?php

require_once __DIR__ . '/../datos/UsuarioDatos.php';


class UsuarioNegocio{
    private $usuarioDatos;

    public function __construct()
    {
        $this->usuarioDatos = new UsuarioDatos();
    }

    public function validarLogin($usuario, $password){
        if(empty($usuario) || empty($password)){
            return null;
        }

        $usuarioEncontrado = $this->usuarioDatos->obtenerUsuarioPorNombre($usuario);

        if($usuarioEncontrado === null){
            return null;
        }

        if(!password_verify($password, $usuarioEncontrado['Password'])){
            return null;
        }

        return $usuarioEncontrado;
        
    }
}