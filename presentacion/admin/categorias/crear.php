<?php

require_once __DIR__ .'/../../../negocio/CategoriaNegocio.php';

$categoriaNegocio = new CategoriaNegocio();


session_start();


$datos = [
    'categoria' => '',
    'descripcion' => ''
];

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $datos = [
        'categoria' => $_POST['categoria'],
        'descripcion' => $_POST['descripcion']
    ];

    $resultado = $categoriaNegocio->crearCategoria($datos);

    if(!$resultado['exito']) $_SESSION['ERRORES'] = $resultado['errores'];
    else {
        $_SESSION['MENSAJE'] = 'creado';
    }

    header('Location: listar.php');

}