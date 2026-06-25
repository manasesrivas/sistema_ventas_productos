<?php

require_once __DIR__ . '/../../../negocio/ProductoNegocio.php';
require_once __DIR__ . '/../../../datos/ProductosDatos.php';

$productoNegocio = new ProductoNegocio();

$errores = [];

$id_pruducto = $_GET['id'] ?? null;

if(!$id_pruducto){
    header("Location: listar.php");
    exit;
}

$producto = $productoNegocio->obtenerProductoPorId($id_pruducto);

if(!$producto){
    header('Location: listar.php');
    exit;
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    
    $imagenActual = $producto['imagen'] ?? 'sin-imagen.png';
    $imagen = procesarImagen($_FILES['Imagen'] ?? null, $errores, $imagenActual);

    $datos = [
        'id_producto' => $_POST['id_producto'] ?? '',
        'nombre_producto' => $_POST['NombreProducto'] ?? '',
        'modelo'         => $_POST['Modelo'] ?? '',
        'categoria_id'    => $_POST['IdCategoria'] ?? '',
        'marca_id'        => $_POST['IdMarca'] ?? '',
        'precio'    => $_POST['PrecioVenta'] ?? '',
        'caracteristicas'=> $_POST['Caracteristicas'] ?? '',
        'stock'    => $_POST['Existencias'] ?? '',
        'imagen'         => $imagen
    ];

    if(empty($errores)){
        $resultado = $productoNegocio->actualizarProducto($datos);

        if($resultado['exito']){
            header('Location: listar.php?mensaje=actualizado');
            exit;
        }else{
            $errores = $resultado['errores'];
            $producto = $datos;
        }
    }else{
        $producto = $datos;
    }
}


function procesarImagen($archivo, &$errores, $imagenActual){
    if(!$archivo || $archivo['error'] === UPLOAD_ERR_NO_FILE){
        return $imagenActual;
    }
    if($archivo['error'] !== UPLOAD_ERR_OK){
        $errores[] = 'Ocurrio un erro al ssubir la imagen.';
    }
    $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];
    $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

    if(!in_array($extension, $extensionesPermitidas)){
        $errores[] = 'La imagen debe etenr un fomrato JPG, JPEG, PNG 0 WEBP.';
        return $imagenActual;
    }

    if($archivo['size'] > 2 * 1024 * 1024){
        $errores[] = 'La imagen no debe superar los 2 MB.';
        return $imagenActual;
    }

    $directorioDestino = __DIR__ . '/../../../public/img/productos/';

    if(!is_dir($directorioDestino)){
        mkdir($directorioDestino, 0777, true);
    }

    $nombreArchivo = 'producto_'.time().'_'.rand(1000, 9999).'.'.$extension;
    $rutaDestino = $directorioDestino . $nombreArchivo;

    if(!move_uploaded_file($archivo['tmp_name'], $rutaDestino)){
        $errores[] = 'No se pudo guardar la imagen del producto.';
        return $imagenActual;
    }

    return $nombreArchivo;
    
}

/**
 * mostrar valores de forma segura en HTML.
 * 
 * @param string|null $valor valor que se desea mostrar.
 * @return string valor escapado.
 */
function mostrarValor($valor){
    return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../../public/bootstrap/css/bootstrap.min.css">
    <title>Editar producto</title>
</head>
<body class="bt-ligth">
    <div class="container mt-5 mb-5">
        <div class="card shadow">
            <div class="card-header bb-warning">
                <h4 class="mb-0">Editar producto</h4>
            </div>

            <div class="card-body">

                <?php if(!empty($errores)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errores as $error): ?>
                                <li><?php echo mostrarValor($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                <form action="editar.php?id=<?php echo mostrarValor($producto['id_producto']); ?>" enctype="multipart/form-data" method="POST">
                    
                    <input type="hidden" name="id_producto" value="<?php echo mostrarValor($producto['id_producto']); ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Nombre del producto</label>
                        <input type="text" name="NombreProducto" class="form-control"
                        value="<?php echo mostrarValor($producto['nombre_producto']); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Modelo</label>
                        <input type="text" name="Modelo" class="form-control"
                        value="<?php echo mostrarValor($producto['modelo']); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Categoría</label>
                        <select name="IdCategoria" class="form-select">
                            <option value="">Seleccione una categoría</option>
                            <option value="1" selected>Teclados</option>
                            <?php // foreach ($categorias as $categoria): ?>
                                <option value="<?php // echo mostrarValor($categoria['IdCategoria']); ?>">
                                    <?php // echo mostrarValor($categoria['NombreCategoria']); ?>
                                </option>
                                <?php // endforeach; ?>
                        </select>
                    </div>
                        
                    <div class="mb-3">
                        <label class="form-label">Marca</label>
                        <select name="IdMarca" class="form-select">
                            <option value="">Seleccione una marca</option>
                            <option value="1" selected>HP</option>
                            <?php //foreach ($marcas as $marca): ?>
                                <option value="<?php //echo mostrarValor($marca['IdMarca']); ?>">
                                    <?php //echo mostrarValor($marca['NombreMarca']); ?>
                                </option>
                                <?php //endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Precio de venta</label>
                        <input type="number" step="0.01" min="0" name="PrecioVenta" class="form-control"
                        value="<?php echo mostrarValor($producto['precio']); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Existencias</label>
                        <input type="number" min="0" name="Existencias" class="form-control"
                        value="<?php echo mostrarValor($producto['stock']); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Características</label>
                        <textarea name="Caracteristicas" class="form-control" rows="4">
                            <?php echo mostrarValor($producto['caracteristicas']) ?>
                        </textarea>
                    </div>
                    
                    <div class="mb-3">  
                        <label for="form-label">Imagen actual</label>
                        <img src="/daw/proyecto_final/public/img/productos/<?php echo mostrarValor($producto['imagen']); ?>" 
                        alt="Imagen del producto"
                        class="img-thumbnail"
                        style="width: 120px; aspect-ratio: 1/1; object-fit: cover;">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Imagen del producto</label>
                        <input type="file" name="Imagen" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                        <div class="form-text">
                            Formatos permitidos: JPG, JPEG, PNG o WEBP. Tamaño máximo: 2 MB.
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-success">Actualizar producto</button>
                    <a href="listar.php" class="btn btn-secondary">Cancelar</a>
                    
                    
                </form>
                        
            </div>
        </div>
    </div>
    <script src="../../../public/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>
</html>