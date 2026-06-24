<?php

require_once __DIR__ . '/../../../negocio/VentaNegocio.php';
require_once __DIR__ . '/../../../negocio/ClienteNegocio.php';
require_once __DIR__ . '/../../../negocio/ProductoNegocio.php';
// require_once __DIR__ . '/../../../negocio/UsuarioNegocio.php';

$ventaNegocio  = new VentaNegocio();
$clienteNegocio = new ClienteNegocio();
// $usuarioNegocio = new UsuarioNegocio();
$productoNegocio = new ProductoNegocio();

$errores = [];
$clientePreseleccionado = ''; 

$datos = [
    'cliente_id'   => '',
    'usuario_id'   => '',
    'descuento_id' => ''
];
$productosnew = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $datos = [
        'cliente_id'   => $_POST['cliente_id']   ?? '',
        'usuario_id'   => $_POST['usuario_id']   ?? '',
        'descuento_id' => $_POST['descuento_id'] ?? null
    ];

    if (!empty($_POST['producto_id'])) {
        foreach ($_POST['producto_id'] as $i => $id) {
            if (empty($id)) continue;
            $productosnew[] = [
                'producto_id' => $id,
                'precio'      => $_POST['precio'][$i]   ?? 0,
                'cantidad'    => $_POST['cantidad'][$i] ?? 0
            ];
        }
    }

    if (!empty($datos['cliente_id'])) {
        $clienteActual          = $clienteNegocio->obtenerClientePorId($datos['cliente_id']);
        $clientePreseleccionado = $clienteActual ? $clienteActual['nombres'] : '';
    }

    $resultado = $ventaNegocio->crearVenta($datos, $productosnew);

    if ($resultado['exito']) {
        header("Location: listar.php?mensaje=creado");
        exit;
    } else {
        if (!empty($resultado['errores'])) {
            $errores = aplanarErrores($resultado['errores']);
        } elseif (!empty($resultado['mensaje'])) {
            $errores = [$resultado['mensaje']];
        } else {
            $errores = ['Ocurrió un error inesperado al registrar la venta.'];
        }
    }
}  

$clientes = $clienteNegocio->listarClientes();
$todosLosProductos = $productoNegocio->listarProductos();
// $usuarios = $usuarioNegocio->listarUsuarios();

// TEMPORAL: usuarios de prueba hasta que exista UsuarioNegocio
$usuarios = [
    ['id_usuario' => 1, 'nombre_usuario' => 'Usuario de prueba']
];
// $todosLosProductos = [
//     ['id_producto' => 1, 'nombre_producto' => 'Laptop HP', 'precio' => 500.00],
//     ['id_producto' => 2, 'nombre_producto' => 'Mouse Logitech', 'precio' => 25.00],
//     ['id_producto' => 3, 'nombre_producto' => 'Teclado Genius', 'precio' => 15.00],
// ];

function mostrarValor($valor)
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}

function aplanarErrores($errores)
{
    $planos = [];

    foreach ($errores as $error) {
        if (is_array($error)) {
            $planos = array_merge($planos, $error);
        } else {
            $planos[] = $error;
        }
    }

    return $planos;
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
                <div class="mb-3 position-relative">
                    <label for="clienteBusqueda" class="form-label">Cliente</label>
                    <input type="text" id="clienteBusqueda" class="form-control"
                           placeholder="Buscar por nombre o DUI..."
                           autocomplete="off"
                           value="<?php echo mostrarValor($clientePreseleccionado); ?>">

                    <!--input oculto: es el que realmente se envía con el formulario-->
                    <input type="hidden" name="cliente_id" id="cliente_id"
                           value="<?php echo mostrarValor($datos['cliente_id']); ?>">

                    <!--lista de sugerencias que aparece mientras se escribe-->
                    <ul id="listaClientes" class="list-group position-absolute z-3"
                        style="display:none; max-height:200px; overflow-y:auto; width:100%;"></ul>

                    <!--confirmación visual de que se seleccionó un cliente-->
                    <small id="clienteSeleccionado" class="text-success mt-1 d-block"></small>
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
            <th>Producto</th>
            <th>Precio</th>
            <th>Cantidad</th>
            <th></th>
        </tr>
    </thead>
    <tbody id="cuerpoProductos">
        <tr>
            <td class="position-relative">
                <input type="text" class="form-control buscarProducto"
                       placeholder="Buscar producto..." autocomplete="off">
                <input type="hidden" name="producto_id[]" class="producto_id">
                <ul class="list-group position-absolute z-3 listaProductos"
                    style="display:none; max-height:200px; overflow-y:auto; width:100%;"></ul>
            </td>
            <td>
                <input type="number" step="0.01" name="precio[]"
                       class="form-control precio" readonly>
            </td>
            <td>
                <input type="number" name="cantidad[]" class="form-control cantidad" min="1">
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm btnEliminar">X</button>
            </td>
        </tr>
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

    // ── DATOS CARGADOS DESDE PHP ──────────────────────────
    const todosLosClientes  = <?php echo json_encode($clientes); ?>;
    const todosLosProductos = <?php echo json_encode($todosLosProductos); ?>;

    // ── AUTOCOMPLETE CLIENTE ──────────────────────────────
    const inputBusqueda       = document.getElementById('clienteBusqueda');
    const inputOculto         = document.getElementById('cliente_id');
    const lista               = document.getElementById('listaClientes');
    const clienteSeleccionado = document.getElementById('clienteSeleccionado');

    if (inputOculto.value && inputBusqueda.value) {
        clienteSeleccionado.textContent = '✓ ' + inputBusqueda.value;
    }

    let timeoutBusqueda = null;

    inputBusqueda.addEventListener('input', function () {
        const texto = this.value.trim();

        inputOculto.value               = '';
        clienteSeleccionado.textContent = '';

        if (texto.length < 2) {
            lista.style.display = 'none';
            lista.innerHTML     = '';
            clearTimeout(timeoutBusqueda);
            return;
        }

        clearTimeout(timeoutBusqueda);
        timeoutBusqueda = setTimeout(() => {
            const filtrados = todosLosClientes.filter(c =>
                c.nombres.toLowerCase().includes(texto.toLowerCase()) ||
                (c.dui && c.dui.toLowerCase().includes(texto.toLowerCase()))
            );

            if (filtrados.length === 0) {
                lista.innerHTML     = '<li class="list-group-item text-muted">Sin resultados</li>';
                lista.style.display = 'block';
                return;
            }

            lista.innerHTML = filtrados.map(c => `
                <li class="list-group-item list-group-item-action"
                    style="cursor:pointer"
                    data-id="${c.id_cliente}"
                    data-nombre="${c.nombres}">
                    <strong>${c.nombres}</strong>
                    ${c.dui ? '<span class="text-muted ms-2">— ' + c.dui + '</span>' : ''}
                </li>
            `).join('');

            lista.style.display = 'block';
        }, 300);
    });

    lista.addEventListener('click', function (e) {
        const item = e.target.closest('li[data-id]');
        if (!item) return;

        inputOculto.value               = item.dataset.id;
        inputBusqueda.value             = item.dataset.nombre;
        clienteSeleccionado.textContent = '✓ ' + item.dataset.nombre;
        lista.style.display             = 'none';
        lista.innerHTML                 = '';
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.position-relative')) {
            lista.style.display = 'none';
        }
    });

    // ── AUTOCOMPLETE PRODUCTOS ────────────────────────────
    function iniciarAutoCompleteProducto(fila) {
        const inputNombre = fila.querySelector('.buscarProducto');
        const inputId     = fila.querySelector('.producto_id');
        const inputPrecio = fila.querySelector('.precio');
        const listaP      = fila.querySelector('.listaProductos');

        inputNombre.addEventListener('input', function () {
            const texto = this.value.trim().toLowerCase();

            inputId.value     = '';
            inputPrecio.value = '';

            if (texto.length < 2) {
                listaP.style.display = 'none';
                listaP.innerHTML     = '';
                return;
            }

            const filtrados = todosLosProductos.filter(p =>
                p.nombre_producto.toLowerCase().includes(texto)
            );

            if (filtrados.length === 0) {
                listaP.innerHTML     = '<li class="list-group-item text-muted">Sin resultados</li>';
                listaP.style.display = 'block';
                return;
            }

            listaP.innerHTML = filtrados.map(p => `
                <li class="list-group-item list-group-item-action"
                    style="cursor:pointer"
                    data-id="${p.id_producto}"
                    data-nombre="${p.nombre_producto}"
                    data-precio="${p.precio}">
                    ${p.nombre_producto}
                    <span class="text-muted ms-2">$${parseFloat(p.precio).toFixed(2)}</span>
                </li>
            `).join('');

            listaP.style.display = 'block';
        });

        listaP.addEventListener('click', function (e) {
            const item = e.target.closest('li[data-id]');
            if (!item) return;

            inputNombre.value = item.dataset.nombre;
            inputId.value     = item.dataset.id;
            inputPrecio.value = item.dataset.precio;
            listaP.style.display = 'none';
            listaP.innerHTML     = '';
            calcularTotales();
        });

        document.addEventListener('click', function (e) {
            if (!fila.contains(e.target)) {
                listaP.style.display = 'none';
            }
        });
    }

    // ── TABLA DE PRODUCTOS ────────────────────────────────
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

    function agregarEventosFila(fila) {
        iniciarAutoCompleteProducto(fila);

        fila.querySelector('.btnEliminar').addEventListener('click', () => {
            fila.remove();
            calcularTotales();
        });

        fila.querySelectorAll('.cantidad').forEach(input => {
            input.addEventListener('input', calcularTotales);
        });
    }

    function nuevaFila() {
        const fila = document.createElement('tr');
        fila.innerHTML = `
            <td class="position-relative">
                <input type="text" class="form-control buscarProducto"
                       placeholder="Buscar producto..." autocomplete="off">
                <input type="hidden" name="producto_id[]" class="producto_id">
                <ul class="list-group position-absolute z-3 listaProductos"
                    style="display:none; max-height:200px; overflow-y:auto; width:100%;"></ul>
            </td>
            <td>
                <input type="number" step="0.01" name="precio[]"
                       class="form-control precio" readonly>
            </td>
            <td>
                <input type="number" name="cantidad[]" class="form-control cantidad" min="1">
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm btnEliminar">X</button>
            </td>
        `;
        document.getElementById('cuerpoProductos').appendChild(fila);
        agregarEventosFila(fila);
    }

    document.querySelectorAll('#cuerpoProductos tr').forEach(agregarEventosFila);
    document.getElementById('btnAgregarFila').addEventListener('click', nuevaFila);
    
    // ── VALIDACIÓN AL ENVIAR ──────────────────────────────
    document.querySelector('form').addEventListener('submit', function(e) {
    let valido = true;

    document.querySelectorAll('#cuerpoProductos tr').forEach(fila => {
        const id       = fila.querySelector('.producto_id').value;
        const cantidad = fila.querySelector('.cantidad').value;

        // si eligió producto pero no puso cantidad
        if (id && (!cantidad || parseInt(cantidad) <= 0)) {
            valido = false;
            fila.querySelector('.cantidad').classList.add('is-invalid');
        } else {
            fila.querySelector('.cantidad').classList.remove('is-invalid');
        }
    });

    if (!valido) {
        e.preventDefault(); // detiene el envío del formulario
        alert('Por favor ingresá la cantidad de todos los productos agregados.');
    }
});
</script>
</body>
</html>