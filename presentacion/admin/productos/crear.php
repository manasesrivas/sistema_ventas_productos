<?php

require_once __DIR__ . '/../../../negocio/productoNegocio.php';
require_once __DIR__ . '/../../../negocio/CategoriaNegocio.php';
require_once __DIR__ . '/../../../negocio/MarcaNegocio.php';

$productoNegocio = new ProductoNegocio();
$categoriaNegocio = new CategoriaNegocio();
$marcaNegocio = new MarcaNegocio();

$categorias = $categoriaNegocio->listarCategorias();
$marcas = $marcaNegocio->listarMarcas();

$errores = [];

$datos = [
    'nombre_producto' => '',
    'modelo' => '',
    'categoria_id' => '',
    'marca_id' => '',
    'precio' => '',
    'caracteristicas' => '',
    'stock' => '',
    'imagen' => 'sin-imagen.png'
];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $datos = [
        'nombre_producto' => $_POST['NombreProducto'] ?? '',
        'modelo'         => $_POST['Modelo'] ?? '',
        'categoria_id'    => $_POST['IdCategoria'] ?? '',
        'marca_id'        => $_POST['IdMarca'] ?? '',
        'precio'    => $_POST['PrecioVenta'] ?? '',
        'caracteristicas'=> $_POST['Caracteristicas'] ?? '',
        'stock'    => $_POST['Existencias'] ?? '',
        'imagen'         => procesarImagen($_FILES['Imagen'] ?? null, $errores)
    ];

    if (empty($errores)) {

        $resultado = $productoNegocio->crearProducto($datos);

        if ($resultado['exito']) {

            header("Location: listar.php?mensaje=creado");
            exit;

        } else {

            $errores = $resultado['errores'];
        }
    }
}

function mostrarValor($valor){
    return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Procesar imagen del producto.
 *
 * Valida y guarda la imagen cargada desde el formulario.
 * Si no se selecciona ninguna imagen, retorna una imagen por defecto.
 *
 * @param array|null $archivo Archivo recibido desde $_FILES.
 * @param array $errores Lista de errores por referencia.
 * @return string Nombre del archivo de imagen.
 */
function procesarImagen($archivo, &$errores)
{
    if (!$archivo || $archivo['error'] === UPLOAD_ERR_NO_FILE) {
        return 'sin-imagen.png';
    }

    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        $errores[] = "Ocurrió un error al subir la imagen.";
        return 'sin-imagen.png';
    }

    $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];
    $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

    if (!in_array($extension, $extensionesPermitidas)) {
        $errores[] = "La imagen debe tener formato JPG, JPEG, PNG o WEBP.";
        return 'sin-imagen.png';
    }

    if ($archivo['size'] > 2 * 1024 * 1024) {
        $errores[] = "La imagen no debe superar los 2 MB.";
        return 'sin-imagen.png';
    }

    $directorioDestino = __DIR__ . '/../../../public/img/productos/';

    if (!is_dir($directorioDestino)) {
        mkdir($directorioDestino, 0777, true);
    }

    $nombreArchivo = 'producto_' . time() . '_' . rand(1000, 9999) . '.' . $extension;
    $rutaDestino = $directorioDestino . $nombreArchivo;

    if (!move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
        $errores[] = "No se pudo guardar la imagen del producto.";
        return 'sin-imagen.png';
    }

    return $nombreArchivo;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../../public/bootstrap/css/bootstrap.min.css">
    <title>Registrar producto</title>
</head>
<body class="bg-light">
    <div class="container mt-5 mb-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Registrar producto</h4>
            </div>

            <div class="card-body">
                <?php if(!empty($errores)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach($errores as $error): ?>
                                <li><?php echo mostrarValor($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                <form method="POST" action="crear.php" enctype="multipart/form-data">

                    <div class="mb-3">
                        <label class="form-label">Nombre del producto</label>
                        <input type="text" name="NombreProducto" class="form-control"
                        value="<?php echo @$_POST['NombreProducto']; ?>"
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Modelo</label>
                        <input type="text" name="Modelo" class="form-control"
                        value="<?php echo @$_POST['Modelo']; ?>"
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Categoría</label>
                        <select name="IdCategoria" class="form-select">
                            <option value="">Seleccione una categoría</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option 
                                    <?php echo ($categoria['id_categoria'] === @$_POST['IdCategoria']) ? 'selected': '';?> 
                                    value="<?php echo mostrarValor($categoria['id_categoria']); ?>">
                                    <?php echo mostrarValor($categoria['categoria']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Marca</label>
                        <select name="IdMarca" class="form-select">
                            <option value="">Seleccione una marca</option>
                            <?php foreach ($marcas as $marca): ?>
                                <option 
                                <?php echo ($marca['id_marca'] === @$_POST['IdMarca']) ? 'selected': '';?>
                                value="<?php echo mostrarValor($marca['id_marca']); ?>">
                                    <?php echo mostrarValor($marca['marca']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Precio de venta</label>
                        <input type="number" step="0.01" min="0" name="PrecioVenta" class="form-control"
                        value="<?php echo @$_POST['PrecioVenta']; ?>"
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Existencias</label>
                        <input type="number" min="0" name="Existencias" class="form-control"
                        value="<?php echo @$_POST['Existencias']; ?>"
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Características</label>
                        <textarea name="Caracteristicas" class="form-control" rows="4"><?php echo @$_POST['Caracteristicas']; ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Imagen del producto</label>
                        <input type="file" name="Imagen" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                        <div class="form-text">
                            Formatos permitidos: JPG, JPEG, PNG o WEBP. Tamaño máximo: 2 MB.
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success">Guardar producto</button>
                    <a href="listar.php" class="btn btn-secondary">Cancelar</a>


                </form>
            </div>
        </div>
    </div>
    <script src="../../../../public/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>