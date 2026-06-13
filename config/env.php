<?php

$archivoEnv = __DIR__ . '/../.env';

if(!file_exists($archivoEnv)){
    die('No se encontro el archivo');
}

$lineas = file($archivoEnv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach($lineas as $linea){
    $linea = trim($linea);

    if($linea === '' || strpos($linea, '#') === 0){
        continue;
    }
    list($nombre, $valor) = explode('=', $linea, 2);
    $_ENV[trim($nombre)] = trim($valor);
}
