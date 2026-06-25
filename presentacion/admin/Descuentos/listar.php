<?php

require_once __DIR__ . '/../../../negocio/DescuentoNegocio.php';

$descuentoNegocio = new DescuentoNegocio();

$errores = [];
$mensaje = '';

$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';
$id     = $_POST['id_descuento'] ?? $_GET['id'] ?? null;

if ($accion === 'crear') {
    $resultado = $descuentoNegocio->crearDescuento($_POST);
    if ($resultado['exito']) {
        $mensaje = $resultado['mensaje'];
    } else {
        $errores = $resultado['errores'];
    }
}

if ($accion === 'actualizar') {
    $resultado = $descuentoNegocio->actualizarDescuento($_POST);
    if ($resultado['exito']) {
        $mensaje = $resultado['mensaje'];
    } else {
        $errores = $resultado['errores'];
    }
}

if ($accion === 'activar' && $id) {
    $resultado = $descuentoNegocio->reactivarDescuento($id);
    $mensaje   = $resultado['mensaje'];
}

if ($accion === 'desactivar' && $id) {
    $resultado = $descuentoNegocio->suspenderDescuento($id);
    $mensaje   = $resultado['mensaje'];
}

$descuentos = $descuentoNegocio->listarTodosLosDescuentos();

function mostrarValor($valor)
{
    return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Descuentos</title>
    <link rel="stylesheet" href="../../../public/bootstrap/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">

    <?php if ($mensaje): ?>
        <div class="alert alert-success"><?php echo mostrarValor($mensaje); ?></div>
    <?php endif; ?>

    <?php if (!empty($errores)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errores as $error): ?>
                    <li><?php echo mostrarValor($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card shadow">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Listado de descuentos</h5>
            <button type="button" class="btn btn-light btn-sm"
                    data-bs-toggle="modal" data-bs-target="#modalDescuento">
                ＋ Nuevo descuento
            </button>
        </div>
        <div class="card-body">
            <?php if (empty($descuentos)): ?>
                <p class="text-muted">No hay descuentos registrados.</p>
            <?php else: ?>
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Descuento (%)</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($descuentos as $descuento): ?>
                        <tr>
                            <td><?php echo mostrarValor($descuento['id_descuento']); ?></td>
                            <td><?php echo mostrarValor($descuento['descuento']); ?>%</td>
                            <td><?php echo mostrarValor($descuento['descripcion']); ?></td>
                            <td>
                                <?php if ($descuento['estado'] === 'activa'): ?>
                                    <span class="badge bg-success">Activa</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactiva</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-warning btn-sm btnEditar"
                                        data-id="<?php echo mostrarValor($descuento['id_descuento']); ?>"
                                        data-descuento="<?php echo mostrarValor($descuento['descuento']); ?>"
                                        data-descripcion="<?php echo mostrarValor($descuento['descripcion']); ?>">
                                    Editar
                                </button>

                                <?php if ($descuento['estado'] === 'activa'): ?>
                                    <a href="listar.php?accion=desactivar&id=<?php echo mostrarValor($descuento['id_descuento']); ?>"
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('¿Desactivar este descuento?');">
                                        Desactivar
                                    </a>
                                <?php else: ?>
                                    <a href="listar.php?accion=activar&id=<?php echo mostrarValor($descuento['id_descuento']); ?>"
                                       class="btn btn-success btn-sm">
                                        Activar
                                    </a>
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

<!-- MODAL CREAR/EDITAR -->
<div class="modal fade" id="modalDescuento" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTitulo">Nuevo descuento</h5>
                <button type="button" class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="listar.php">
                <div class="modal-body">
                    <input type="hidden" name="accion"       id="modalAccion" value="crear">
                    <input type="hidden" name="id_descuento" id="modalId"     value="">

                    <div class="mb-3" id="campoId" style="display:none;">
                        <label class="form-label">ID</label>
                        <input type="text" class="form-control" id="mostrarId" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="modalDescuentoValor" class="form-label">
                            Porcentaje de descuento (1 – 100)
                        </label>
                        <div class="input-group">
                            <input type="number" name="descuento" id="modalDescuentoValor"
                                   class="form-control" min="1" max="100">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="modalDescripcion" class="form-label">Descripción</label>
                        <input type="text" name="descripcion" id="modalDescripcion"
                               class="form-control" maxlength="50">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success"
                            id="modalBoton">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="../../../public/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelectorAll('.btnEditar').forEach(btn => {
        btn.addEventListener('click', function () {
            const id          = this.dataset.id;
            const descuento   = this.dataset.descuento;
            const descripcion = this.dataset.descripcion;

            document.getElementById('modalTitulo').textContent      = 'Editar descuento';
            document.getElementById('modalAccion').value            = 'actualizar';
            document.getElementById('modalId').value                = id;
            document.getElementById('mostrarId').value              = id;
            document.getElementById('modalDescuentoValor').value    = descuento;
            document.getElementById('modalDescripcion').value       = descripcion;
            document.getElementById('campoId').style.display        = 'block';
            document.getElementById('modalBoton').textContent       = 'Actualizar';

            new bootstrap.Modal(document.getElementById('modalDescuento')).show();
        });
    });

    document.getElementById('modalDescuento').addEventListener('hidden.bs.modal', function () {
        document.getElementById('modalTitulo').textContent      = 'Nuevo descuento';
        document.getElementById('modalAccion').value            = 'crear';
        document.getElementById('modalId').value                = '';
        document.getElementById('mostrarId').value              = '';
        document.getElementById('modalDescuentoValor').value    = '';
        document.getElementById('modalDescripcion').value       = '';
        document.getElementById('campoId').style.display        = 'none';
        document.getElementById('modalBoton').textContent       = 'Guardar';
    });
</script>
</body>
</html>