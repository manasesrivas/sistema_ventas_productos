<?php

require_once __DIR__ . '/../../../negocio/ProductoNegocio.php';

$productoNegocio = new ProductoNegocio();

$id_producto = $_GET['id'] ?? null;

if(!$id_producto){
    header('Location: listar.php');
    exit;
}

$producto = $productoNegocio->obtenerProductoPorId($id_producto);

if(!$producto){
    header('Location: listar.php');
    exit;
}

$mensajeError = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    echo $_POST['id_producto'];
    $id_producto = $_POST['id_producto'] ?? null;

    $resultado = $productoNegocio->eliminarProducto($id_producto);
    
    if($resultado['exito']){
        header('Location: listar.php?mensaje=eliminado');
        exit;
    }else{
        $mensajeError = $resultado['mensaje'];
    }
}

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
    <title>Eliminar producto</title>
</head>
<body class="bg-light">
    <div class="container mt-5">

        <div class="card shadow">
            <div class="card-header bg-danger text-white">
                <h4 class="mb-0">Eliminar producto</h4>
            </div>

            <div class="card-body">

                <?php if(!empty($mensajeError)): ?>
                    <div class="alert alert-danger">
                        <?php echo mostrarValor($mensajeError); ?>
                    </div>
                <?php endif; ?>

                <div class="alert alert-warning">
                    ¿Está seguro de eliminar el siguiente producto?
                </div>  
                
                <div class="mb-3">
                    <img src="../../../public/img/productos/<?php echo mostrarValor($producto['imagen'] ?: 'sin-imagen.php'); ?>"
                    alt="Imagen del producto"
                    class="img-thumbnail"
                    style="width: 120px; aspect-ratio: 1/1; object-fit: cover;">
                </div>
                <p><strong>Produto:</strong> <?php echo mostrarValor($producto['nombre_producto']); ?></p>
                <p><strong>Modelo:</strong> <?php echo mostrarValor($producto['modelo']); ?></p>
                <p><strong>Categoria:</strong> <?php echo mostrarValor($producto['categoria']); ?></p>
                <p><strong>Marca:</strong> <?php echo mostrarValor($producto['marca']); ?></p>
                <p><strong>Precio:</strong> <?php echo mostrarValor($producto['precio']); ?></p>
                <p><strong>Existencias:</strong> <?php echo mostrarValor($producto['stock']); ?></p>

                <form method="POST" action="eliminar.php?id=<?php echo mostrarValor($producto['id_producto']); ?>">
                    <input type="hidden" name="id_producto" value="<?php echo mostrarValor($producto['id_producto']); ?>">

                    <button type="submit" class="btn btn-danger">Sí, eliminar</button>
                    <a href="listar.php" class="btn btn-secondary">Cancelar</a>
                </form>
                
            </div>
        </div>
    </div>
    <script src="../../../public/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>
</html>