<?php

require_once __DIR__ . '/../../../negocio/CategoriaNegocio.php';

session_start();
$categoriaNegocio = new CategoriaNegocio();

$id_categoria = $_GET['id'] ?? null;

if(!$id_categoria){
    header('Location: listar.php');
    exit;
}

$categoria = $categoriaNegocio->obtenerCategoriaPorId($id_categoria);

if(!$categoria){
    header('Location: listar.php');
    exit;
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $datos = [
        'id_categoria' => $_POST['id_categoria'],
        'categoria' => $_POST['categoria'],
        'descripcion' => $_POST['descripcion']
    ];


    $resultado = $categoriaNegocio->actualizarCategoria($datos);

    if(!$resultado['exito']) $_SESSION['ERRORES'] = $resultado['errores'];
    else $_SESSION['MENSAJE'] = 'actualizado';
    
    header('Location: listar.php');
    

}