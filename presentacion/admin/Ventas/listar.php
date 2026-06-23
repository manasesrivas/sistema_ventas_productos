<?php

require_once __DIR__ . '/../../../negocio/VentaNegocio.php';

$ventaNegocio = new VentaNegocio();

$ventas = $ventaNegocio->listarVentas();

$mensaje = $_GET['mensaje'] ?? null;

function mostrarValor($valor)
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de ventas</title>
    <link rel="stylesheet" href="../../../public/bootstrap/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Listado de ventas</h4>
            <a href="crear.php" class="btn btn-light btn-sm">+ Nueva venta</a>
        </div>
        <div class="card-body">

            <?php if ($mensaje === 'creado'): ?>
                <div class="alert alert-success">
                    Venta registrada correctamente.
                </div>
            <?php endif; ?>

            <?php if (empty($ventas)): ?>
                <p class="text-muted">No hay ventas registradas todavía.</p>
            <?php else: ?>
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Cliente ID</th>
                            <th>Usuario ID</th>
                            <th>Descuento ID</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ventas as $venta): ?>
                        <tr>
                            <td><?php echo mostrarValor($venta['id_venta']); ?></td>
                            <td><?php echo mostrarValor($venta['fecha_venta']); ?></td>
                            <td><?php echo mostrarValor($venta['cliente_id']); ?></td>
                            <td><?php echo mostrarValor($venta['usuario_id']); ?></td>
                            <td><?php echo mostrarValor($venta['descuento_id'] ?? '—'); ?></td>
                            <td>$<?php echo mostrarValor(number_format($venta['total'], 2)); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

        </div>
    </div>
</div>

<script src="../../../public/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>