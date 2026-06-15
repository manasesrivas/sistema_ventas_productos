<?php

require_once __DIR__ . '/../../../negocio/VentaNegocio.php';

require_once __DIR__ . '/../../../negocio/ClienteNegocio.php';
require_once __DIR__ . '/../../../negocio/UsuarioNegocio.php';

$ventasNegocio  = new VentasNegocio();
$clienteNegocio = new ClienteNegocio();
$usuarioNegocio = new UsuarioNegocio();

$errores = [];

$datos = [
    'cliente_id'   => '',
    'usuario_id'   => '',
    'descuento_id' => ''
];

$productos = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'cliente_id'   => $_POST['cliente_id']   ?? '',
        'usuario_id'   => $_POST['usuario_id']   ?? '',
        'descuento_id' => $_POST['descuento_id'] ?? null
    ];

    $productos = [];
    if (!empty($_POST['producto_id'])) {
        foreach ($_POST['producto_id'] as $i => $pid) {
            $productos[] = [
                'producto_id' => $pid,
                'precio'      => $_POST['precio'][$i]   ?? 0,
                'cantidad'    => $_POST['cantidad'][$i] ?? 0
            ];
        }
    }

    $resultado = $ventasNegocio->crearVenta($datos, $productos);

    if ($resultado['exito']) {
        header("Location: listar.php?mensaje=creado");
        exit;
    } else {
        $errores = $resultado['errores'];
    }
}

$clientes = $clienteNegocio->listarClientes();
$usuarios = $usuarioNegocio->listarUsuarios();

function mostrarValor($valor)
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar venta</title>
    <link rel="stylesheet" href="../../../public/bootstrap/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Registrar venta</h4>
        </div>
        <div class="card-body">

            <?php if (!empty($errores)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errores as $error): ?>
                        <li><?php echo mostrarValor($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <form method="POST" action="crear.php">

                <!--CLIENTE-->
                <div class="mb-3">
                    <label for="cliente_id" class="form-label">Cliente</label>
                    <select name="cliente_id" id="cliente_id" class="form-select">
                        <option value="">Seleccione un cliente</option>
                        <?php foreach ($clientes as $cliente): ?>
                            <option value="<?php echo mostrarValor($cliente['id_cliente']); ?>"
                                <?php echo $datos['cliente_id'] == $cliente['id_cliente'] ? 'selected' : ''; ?>>
                                <?php echo mostrarValor($cliente['nombre_cliente']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!--USUARIO-->
                <div class="mb-3">
                    <label for="usuario_id" class="form-label">Usuario</label>
                    <select name="usuario_id" id="usuario_id" class="form-select">
                        <option value="">Seleccione un usuario</option>
                        <?php foreach ($usuarios as $usuario): ?>
                            <option value="<?php echo mostrarValor($usuario['id_usuario']); ?>"
                                <?php echo $datos['usuario_id'] == $usuario['id_usuario'] ? 'selected' : ''; ?>>
                                <?php echo mostrarValor($usuario['nombre_usuario']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!--DESCUENTO-->
                <div class="mb-3">
                    <label for="descuento_id" class="form-label">Descuento (opcional)</label>
                    <input type="number" name="descuento_id" id="descuento_id" class="form-control"
                           value="<?php echo mostrarValor($datos['descuento_id'] ?? ''); ?>">
                </div>

                <!--PRODUCTOS-->
                <hr>
                <h5>Productos</h5>
                <table class="table table-bordered" id="tablaProductos">
                    <thead class="table-light">
                        <tr>
                            <th>Producto ID</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="cuerpoProductos">
                        <?php if (!empty($productos)): ?>
                            <?php foreach ($productos as $prod): ?>
                            <tr>
                                <td><input type="number" name="producto_id[]" class="form-control"
                                           value="<?php echo mostrarValor($prod['producto_id']); ?>"></td>
                                <td><input type="number" step="0.01" name="precio[]" class="form-control precio"
                                           value="<?php echo mostrarValor($prod['precio']); ?>"></td>
                                <td><input type="number" name="cantidad[]" class="form-control cantidad"
                                           value="<?php echo mostrarValor($prod['cantidad']); ?>"></td>
                                <td><button type="button" class="btn btn-danger btn-sm btnEliminar">X</button></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td><input type="number" name="producto_id[]" class="form-control"></td>
                                <td><input type="number" step="0.01" name="precio[]" class="form-control precio"></td>
                                <td><input type="number" name="cantidad[]" class="form-control cantidad"></td>
                                <td><button type="button" class="btn btn-danger btn-sm btnEliminar">X</button></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <button type="button" class="btn btn-secondary mb-3" id="btnAgregarFila">+ Agregar producto</button>

                <!--TABLITA-->
                <hr>
                <div class="row justify-content-end">
                    <div class="col-md-4">
                        <table class="table">
                            <tr><th>Subtotal</th><td id="mostrarSubtotal">$0.00</td></tr>
                            <tr><th>IVA (13%)</th><td id="mostrarIva">$0.00</td></tr>
                            <tr><th>Total</th><td id="mostrarTotal"><strong>$0.00</strong></td></tr>
                        </table>
                    </div>
                </div>

                <button type="submit" class="btn btn-success">Guardar venta</button>
                <a href="listar.php" class="btn btn-secondary">Cancelar</a>

            </form>
        </div>
    </div>
</div>

<script src="../../../public/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
    const IVA = 0.13;
    
    //calcula el total en tiempo real
    function calcularTotales() {
        let subtotal = 0;

        document.querySelectorAll('#cuerpoProductos tr').forEach(fila => {
            const precio   = parseFloat(fila.querySelector('.precio')?.value)   || 0;
            const cantidad = parseFloat(fila.querySelector('.cantidad')?.value) || 0;
            subtotal += precio * cantidad;
        });

        const iva   = subtotal * IVA;
        const total = subtotal + iva;

        document.getElementById('mostrarSubtotal').textContent = '$' + subtotal.toFixed(2);
        document.getElementById('mostrarIva').textContent      = '$' + iva.toFixed(2);
        document.getElementById('mostrarTotal').innerHTML      = '<strong>$' + total.toFixed(2) + '</strong>';
    }

    //comportamientos de las filas
    function agregarEventosFila(fila) {
        fila.querySelector('.btnEliminar').addEventListener('click', () => {
            fila.remove();
            calcularTotales();
        });

        fila.querySelectorAll('.precio, .cantidad').forEach(input => {
            input.addEventListener('input', calcularTotales);
        });
    }

    //crea filas
    function nuevaFila() {
        const fila = document.createElement('tr');
        fila.innerHTML = `
            <td><input type="number" name="producto_id[]" class="form-control"></td>
            <td><input type="number" step="0.01" name="precio[]" class="form-control precio"></td>
            <td><input type="number" name="cantidad[]" class="form-control cantidad"></td>
            <td><button type="button" class="btn btn-danger btn-sm btnEliminar">X</button></td>
        `;
        document.getElementById('cuerpoProductos').appendChild(fila);
        agregarEventosFila(fila);
    }

    document.querySelectorAll('#cuerpoProductos tr').forEach(agregarEventosFila);

    document.getElementById('btnAgregarFila').addEventListener('click', nuevaFila);
</script>
</body>
</html>