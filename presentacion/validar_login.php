<?php

session_start();

require_once __DIR__ . '/../negocio/UsuarioNegocio.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header('Location: login.php'); 
    exit;
}

if(!isset($_POST['usuario']) || !isset($_POST['password'])){
    header('Location: login.php?mensaje=Debe completar todos los campos');
    exit;
}

$usuario = trim($_POST['usuario']);
$password = trim($_POST['password']);

if(empty($usuario) || empty($password)){
    header('Location: login.php?mensaje=Debe ingresar usuario y contraseña'); exit;
}

$usuarioNegocio = new UsuarioNegocio();

$datosUsuario = $usuarioNegocio->validarLogin($usuario, $password);

if($datosUsuario === null){
    header("Location: login.php?mensaje=Usuario o contraseña incorrectos"); exit;
}

session_regenerate_id(true);

$_SESSION['idUsuario'] = $datosUsuario['IdUsuario'];
$_SESSION['nombreCompleto'] = $datosUsuario['NombreCompleto'];
$_SESSION['usuario'] = $datosUsuario['Usuario'];
$_SESSION['tipoCuenta'] = $datosUsuario['TipoCuenta'];

if($datosUsuario['TipoCuenta'] === 'ADMINISTRADOR'){
    header('Location: admin/ventas/listar.php');
    exit;
}

if($datosUsuario['TipoCuenta'] === 'VENDEDOR'){
    header('Location: vendedor/index.php');
    exit;
}

header('Location: admin/index.php');
exit;