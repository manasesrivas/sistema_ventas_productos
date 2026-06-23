<?php

require_once __DIR__ . '/../../../negocio/ClienteNegocio.php';

$clienteNegocio = new ClienteNegocio();
$clientes = $clienteNegocio->listarClientes();

$errores = [];
$mensaje = '';



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
    <title>Listado de cliente</title>
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Administracion de clientes</h3>
        
        <?php if($mensaje === 'creado'): ?>
            <div class="alert alert-succes">Cliente registradfo corectamente.</div>
        <?php elseif($mensaje === 'actualizadod'): ?>
            <div class="alert alert-succes">Cliente actualizado correctamente.</div>
        <?php elseif($mensaje === 'eliminado'):?>
            <div class="alert alert-success">Cliente eliminado correctamente</div>
        <?php endif; ?>
        
        <a href="crear.php" class="btn btn-primary">Nuevo cliente</a>
    </div>

    <div class="card shadow">
        <div class="card-header bg-dark text-white">
            Clientes registrados
        </div>
        <div class="card-body table responsive">
            <table class="table table-bordered table-hover align-middle">

                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>DUI</th>
                        <th>NIT</th>
                        <th>Telefono</th>
                        <th>Direccion</th>
                        <th>Tipo</th>
                        <th>NRC</th>
                        <th width="180">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if(!empty($clientes)): ?>

                        <?php foreach($clientes as $cliente) : ?>
                            <tr>
                                <td><?php echo mostrarValor($cliente['id_cliente'])?></td>
                                <td><?php echo mostrarValor($cliente['nombres'])?></td>
                                <td><?php echo mostrarValor($cliente['dui'])?></td>
                                <td><?php echo mostrarValor($cliente['nit'])?></td>
                                <td><?php echo mostrarValor($cliente['telefono'])?></td>
                                <td><?php echo mostrarValor($cliente['direccion'])?></td>
                                <td><?php echo mostrarValor($cliente['tipo'])?></td>
                                <td><?php echo mostrarValor($cliente['nrc'])?></td>

                                <td>
                                    <a href="editar.php?id=<?php echo $cliente['id_cliente']; ?>" class="btn btn-warning btn-sm">Editar</a>
                                    <a href="eliminar.php?id=<?php echo $cliente['id_cliente']; ?>" class="btn btn-danger btn-sm">Eliminar</a>
                                </td>
                            </tr>

                        <?php endforeach ;?>
                    <?php else: ?>

                        <tr>
                            <td colspan="7" class="text-center">
                                No hay cliente registrados.
                            </td>
                        </tr>
                        
                    <?php endif; ?>
                    <script src="../../../public/bootstrap/js/bootstrap.bundle.min.js"></script>
                </tbody>
                
            </table>
        </div>
    </div>
    
</div>

    
</body>
</html>