<?php

require_once __DIR__ . '/../../../negocio/ClienteNegocio.php';

$clienteNegocio = new ClienteNegocio();

$idCliente = $_GET['id'] ?? null;

if(!$idCliente){
    header("Location: listar.php");
    exit;
}

$cliente = $clienteNegocio->obtenerClientePorId($idCliente);

if(!$cliente){
    header("Location: listar.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $idCliente = $_POST['IdCliente'] ?? null;
    
    $resultado = $clienteNegocio->eliminarCliente($idCliente);

    if($resultado['exito']){
        header("Location: listar.php?mensaje=eliminado");
        exit;
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
    <title>Eliminar cliente</title>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow">

            <div class="card-header bg-danger text-white">
                <h4 class="mb-0">Eliminar cliente</h4>
            </div>
            <div class="card-body">
                
                <div class="alert alert-warning">¿Estas seguro de elminar el siguiente cliente?</div>
                <p>
                    <strong>
                        Nombre: 
                    </strong>
                    <?php echo mostrarValor($cliente['NombreCliente']); ?>
                </p>
                <p>
                    <strong>
                        DUI: 
                    </strong>
                    <?php echo mostrarValor($cliente['DUI']); ?>
                </p>
                <p>
                    <strong>
                        Telefono: 
                    </strong>
                    <?php echo mostrarValor($cliente['Telefono']); ?>
                </p>
                <p>
                    <strong>
                        Direccion:
                    </strong>
                    <?php echo mostrarValor($cliente['Direccion']); ?>
                </p>
                <p>
                    <strong>
                        Tipo:
                    </strong>
                    <?php echo mostrarValor($cliente['Tipo']); ?>
                </p>
                <p>
                    <strong>
                        NIT:
                    </strong>
                    <?php echo mostrarValor($cliente['NIT']); ?>
                </p>
                <p>
                    <strong>
                        NRC:
                    </strong>
                    <?php echo mostrarValor($cliente['NRC']); ?>
                </p>
                
                <form action="eliminar.php?id=<?php echo mostrarValor($cliente['IdCliente']); ?>" method="POST">
                    <input type="hidden" name="IdCliente" value="<?php echo mostrarValor($cliente['IdCliente']) ?>" >
                    <button type="submit" class="btn btn-danger">Si, eliminar</button>
                    <a href="listar.php" class="btn btn-secondary">Cancelar</a>
                </form>
                
            </div>
        </div>
    </div>
    <script src="../../../public/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>