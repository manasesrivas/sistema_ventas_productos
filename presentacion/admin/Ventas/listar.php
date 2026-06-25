<?php

require_once __DIR__ . '/../../../negocio/VentaNegocio.php';

$ventaNegocio = new VentaNegocio();

$mensaje = $_GET['mensaje'] ?? null;

// -- FILTROS --
$filtroEstado = $_GET['estado'] ?? '';
$filtroDia    = $_GET['dia']    ?? '';
$filtroMes    = $_GET['mes']    ?? '';
$filtroAnio   = $_GET['anio']  ?? '';

$ventas = $ventaNegocio->listarVentas($filtroEstado, $filtroDia, $filtroMes, $filtroAnio);

function mostrarValor($valor)
{
    return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de ventas</title>
    <link rel="stylesheet" href="../../../public/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../../public/bootstrap/styles/style.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <?php
        include __DIR__ . '/../_partials/menu.php';
    ?>
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Listado de ventas</h4>
            <a href="crear.php" class="btn btn-light btn-sm">+ Nueva venta</a>
        </div>
        <div class="card-body">

            <?php if ($mensaje === 'creado'): ?>
                <div class="alert alert-success">Venta registrada correctamente.</div>
            <?php endif; ?>

            <?php if ($mensaje === 'anulado'): ?>
                <div class="alert alert-warning">Venta anulada correctamente.</div>
            <?php endif; ?>

            <!--FILTROS-->
            <form method="GET" action="listar.php" class="row g-2 mb-3">
                <div class="col-md-2">
                    <select name="estado" class="form-select form-select-sm">
                        <option value="">Todos los estados</option>
                        <option value="activa"  <?php echo $filtroEstado === 'activa'  ? 'selected' : ''; ?>>Activas</option>
                        <option value="anulada" <?php echo $filtroEstado === 'anulada' ? 'selected' : ''; ?>>Anuladas</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" name="dia" class="form-control form-control-sm"
                           placeholder="Día (1-31)"
                           min="1" max="31"
                           value="<?php echo mostrarValor($filtroDia); ?>">
                </div>
                <div class="col-md-2">
                    <select name="mes" class="form-select form-select-sm">
                        <option value="">Todos los meses</option>
                        <?php
                        $meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
                                  'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
                        foreach ($meses as $i => $nombreMes):
                            $num = $i + 1;
                        ?>
                            <option value="<?php echo $num; ?>"
                                <?php echo (int)$filtroMes === $num ? 'selected' : ''; ?>>
                                <?php echo $nombreMes; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" name="anio" class="form-control form-control-sm"
                           placeholder="Año (ej: 2024)"
                           min="2000" max="2099"
                           value="<?php echo mostrarValor($filtroAnio); ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">Filtrar</button>
                </div>
                <div class="col-md-2">
                    <a href="listar.php" class="btn btn-secondary btn-sm w-100">Limpiar</a>
                </div>
            </form>

            <?php if (empty($ventas)): ?>
                <p class="text-muted">No hay ventas que coincidan con los filtros.</p>
            <?php else: ?>
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Usuario</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ventas as $venta): ?>
                        <tr>
                            <td><?php echo mostrarValor($venta['id_venta']); ?></td>
                            <td><?php echo mostrarValor($venta['fecha_venta']); ?></td>
                            <td><?php echo mostrarValor($venta['nombre_cliente']); ?></td>
                            <td><?php echo mostrarValor($venta['nombre_usuario']); ?></td>
                            <td>$<?php echo mostrarValor(number_format($venta['total'], 2)); ?></td>
                            <td>
                                <?php if ($venta['estado'] === 'activa'): ?>
                                    <span class="badge bg-success">Activa</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Anulada</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($venta['estado'] === 'activa'): ?>
                                    <a href="editar.php?id=<?php echo mostrarValor($venta['id_venta']); ?>"
                                       class="btn btn-warning btn-sm">Editar</a>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
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