<?php

require_once __DIR__ . '/../../../negocio/productoNegocio.php';

$productoNegocio = new ProductoNegocio();
$productos = $productoNegocio->listarProductos();

$mensaje = $_GET['mensaje'] ?? '';

/**
 * mostrar valores de forma segura en HTML.
 * 
 * @param string|null $valor valor que se desea mostrar.
 * @return string valor escapado.
 */

function mostrarValor($valor)
{
    return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../../public/bootstrap/css/bootstrap.min.css">
    <title>Listado de productos</title>
</head>
<body>
    <div class="container-fluid mt-5 mb-5 px-4">
        <div class="d-flex justify-content-between align-align-items-center mb-3">
            <h3>Administracion de productos</h3>
            <a href="crear.php" class="btn btn-primary">Nuevo producto</a>
        </div>
        <?php if($mensaje === 'creado'): ?>
            <div class="alert alert-success">Producto registrado correctamente.</div>
        <?php elseif($mensaje === 'actualizado'): ?>
            <div class="alert alert-success">Producto actualizado correctamente.</div>
        <?php elseif($mensaje === 'elimnado'): ?>
            <div class="alert alert-success">Producto eliminado correctamente.</div>
        <?php endif; ?>

        <div class="card shadow">
            <div class="card-header bg-dark text-white">
                Productos registrado
            </div>
        </div>
        <div class="card-body table-reponsive">

        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Imagen</th>
                    <th>Producto</th>
                    <th>Modelo</th>
                    <th>Categoria</th>
                    <th>Marca</th>
                    <th>Precio</th>
                    <th>Existencias</th>
                    <th width="180">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($productos)): ?>
                    <?php foreach($productos as $producto): ?>
                        <tr>
                            <td>
                                <?php echo mostrarValor($producto['IdProducto']); ?>
                            </td>
                            <td>
                                <img src="../../../public/img/productos/<?php echo mostrarValor($producto['Image'] ?? 'sin-imagen.png'); ?>" alt="Imagen del producto"
                                class="img-thumbnail"
                                style="width: 70px; height: 70px; object-fit: cover;">
                            </td>

                            <td><?php echo mostrarValor($producto['NombreProducto']); ?></td>
                            <td><?php echo mostrarValor($producto['Modelo']); ?></td>
                            <td><?php echo mostrarValor($producto['NombreCategoria']); ?></td>
                            <td><?php echo mostrarValor($producto['NombreMarca']); ?></td>
                            <td><?php echo number_format((float)$producto['PrecioVenta'], 2); ?></td>
                            <td><?php echo mostrarValor($producto['existencias']); ?></td>
                            <td>
                                <a href="editar.php?id=<?php echo mostrarValor($producto['IdProducto']); ?>"
                                class="btn btn-warning btn-sm">Editar</a>
                                <a href="eliminar.php?id=<?php echo mostrarValor($producto['IdProducto']); ?>"
                                class="btn btn-darnger btn-sm">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">No hay productos registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
    <script src="../../../public/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>