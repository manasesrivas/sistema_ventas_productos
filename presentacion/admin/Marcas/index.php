<?php

require_once __DIR__ . '/../../../negocio/MarcaNegocio.php';

$marcaNegocio = new MarcaNegocio();

$errores = [];
$mensaje = '';

$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';
$id     = $_POST['id_marca'] ?? $_GET['id'] ?? null;

if ($accion === 'crear') {
    $resultado = $marcaNegocio->crearMarca($_POST);
    if ($resultado['exito']) {
        $mensaje = $resultado['mensaje'];
    } else {
        $errores = $resultado['errores'];
    }
}

if ($accion === 'actualizar') {
    $resultado = $marcaNegocio->actualizarMarca($_POST);
    if ($resultado['exito']) {
        $mensaje = $resultado['mensaje'];
    } else {
        $errores = $resultado['errores'];
    }
}

if ($accion === 'activar' && $id) {
    $resultado = $marcaNegocio->reactivarMarca($id);
    $mensaje   = $resultado['mensaje'];
}

if ($accion === 'desactivar' && $id) {
    $resultado = $marcaNegocio->suspenderMarca($id);
    $mensaje   = $resultado['mensaje'];
}

$marcas = $marcaNegocio->listarTodasLasMarcas();

function mostrarValor($valor)
{
    return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Marcas</title>
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
            <h5 class="mb-0">Listado de marcas</h5>
            <button type="button" class="btn btn-light btn-sm"
                    data-bs-toggle="modal" data-bs-target="#modalMarca">
                ＋ Nueva marca
            </button>
        </div>
        <div class="card-body">
            <?php if (empty($marcas)): ?>
                <p class="text-muted">No hay marcas registradas.</p>
            <?php else: ?>
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Marca</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($marcas as $marca): ?>
                        <tr>
                            <td><?php echo mostrarValor($marca['id_marca']); ?></td>
                            <td><?php echo mostrarValor($marca['marca']); ?></td>
                            <td>
                                <?php if ((int)$marca['estado'] === 1): ?>
                                    <span class="badge bg-success">Activa</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactiva</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-warning btn-sm btnEditar"
                                        data-id="<?php echo mostrarValor($marca['id_marca']); ?>"
                                        data-nombre="<?php echo mostrarValor($marca['marca']); ?>">
                                    Editar
                                </button>

                                <?php if ((int)$marca['estado'] === 1): ?>
                                    <a href="index.php?accion=desactivar&id=<?php echo mostrarValor($marca['id_marca']); ?>"
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('¿Desactivar esta marca?');">
                                        Desactivar
                                    </a>
                                <?php else: ?>
                                    <a href="index.php?accion=activar&id=<?php echo mostrarValor($marca['id_marca']); ?>"
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

<!--MODAL CREAR/EDITAR-->
<div class="modal fade" id="modalMarca" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTitulo">Nueva marca</h5>
                <button type="button" class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php">
                <div class="modal-body">
                    <input type="hidden" name="accion" id="modalAccion" value="crear">
                    <input type="hidden" name="id_marca" id="modalId" value="">

                    <div class="mb-3" id="campoId" style="display:none;">
                        <label class="form-label">ID</label>
                        <input type="text" class="form-control" id="mostrarId" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="modalMarcaNombre" class="form-label">Nombre de la marca</label>
                        <input type="text" name="marca" id="modalMarcaNombre"
                               class="form-control" maxlength="30">
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
            const id     = this.dataset.id;
            const nombre = this.dataset.nombre;

            document.getElementById('modalTitulo').textContent  = 'Editar marca';
            document.getElementById('modalAccion').value        = 'actualizar';
            document.getElementById('modalId').value            = id;
            document.getElementById('mostrarId').value          = id;
            document.getElementById('modalMarcaNombre').value   = nombre;
            document.getElementById('campoId').style.display    = 'block';
            document.getElementById('modalBoton').textContent   = 'Actualizar';

            new bootstrap.Modal(document.getElementById('modalMarca')).show();
        });
    });

    document.getElementById('modalMarca').addEventListener('hidden.bs.modal', function () {
        document.getElementById('modalTitulo').textContent  = 'Nueva marca';
        document.getElementById('modalAccion').value        = 'crear';
        document.getElementById('modalId').value            = '';
        document.getElementById('mostrarId').value          = '';
        document.getElementById('modalMarcaNombre').value   = '';
        document.getElementById('campoId').style.display    = 'none';
        document.getElementById('modalBoton').textContent   = 'Guardar';
    });
</script>
</body>
</html>