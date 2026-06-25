<?php

require_once __DIR__ . '/../../../negocio/VentaNegocio.php';
require_once __DIR__ . '/../../../negocio/ClienteNegocio.php';
require_once __DIR__ . '/../../../negocio/productoNegocio.php';
require_once __DIR__ . '/../../../negocio/DescuentoNegocio.php';

session_start();

$productoNegocio = new ProductoNegocio();
$ventaNegocio  = new VentaNegocio();
$clienteNegocio = new ClienteNegocio();
$descuentoNegocio = new DescuentoNegocio();
$productoNegocio = new ProductoNegocio();

$descuentos = $descuentoNegocio->listarDescuentos();

$errores = [];
$clientePreseleccionado = ''; 

$datos = [
    'cliente_id'   => '',
    'usuario_id'   => '',
    'descuento_id' => ''
];

$productosNew = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    
    $datos = [
        'cliente_id'   => $_POST['cliente_id']   ?? '',
        'usuario_id'   => $_SESSION['idUsuario']   ?? '',
        'descuento_id' => $_POST['descuento_id'] ?? null
        ];
        
        echo '<pre>';
        echo var_dump($datos);
        echo '</pre>';

    if (!empty($_POST['producto_id'])) {
        foreach ($_POST['producto_id'] as $i => $id) {
            if (empty($id)) continue;
            $productosNew[] = [
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

    $resultado = $ventaNegocio->crearVenta($datos, $productosNew);

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
$productos = $productoNegocio->listarProductos();


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
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="clienteBusqueda" class="form-label">DUI</label>
                        <input type="text" id="clienteBusqueda" class="form-control"
                        placeholder="dui"
                        name="dui"
                        list="clientes-list"
                        onchange="onChangeDui(this)"
                        value="<?php echo @$_POST['dui']; ?>"
                        >
                        <input type="hidden" name="cliente_id" id="cliente">
                    </div>
                        
                    <div class="col-md-6">
                        <label for="" class="form-label">Nombre</label>
                        <input type="text"
                        placeholder="Nombres"
                        class="form-control"
                        id="nombres"
                        value="<?php echo @$_POST['nombres']; ?>">
                    </div>
                    <datalist id="clientes-list">
                        <?php foreach($clientes as $cliente):?>
                            <option 
                            value="<?php echo $cliente['dui']; ?>"
                            data-nombres="<?php echo $cliente['nombres']; ?>"
                            data-cliente-id="<?php echo $cliente['id_cliente']; ?>"
                            >
                        <?php endforeach; ?>
                    </datalist>

                </div>

                <!--DESCUENTO-->
                <div class="mb-3">
                    <div class="col-md-6">
                        <label for="descuento_id" class="form-label">Descuento </label>
                        <select name="descuento_id" id="descuento_select" class="form-control">
                            <option value="">elije un descuento</option>
                            <?php foreach($descuentos as $descuento): ?>
                                <option 
                                    data-descuento="<?php echo $descuento['descuento']; ?>"
                                    value="<?php echo $descuento['id_descuento']; ?>">
                                    <?php echo "{$descuento['descripcion']} - {$descuento['descuento']}%"; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>


               <!--PRODUCTOS-->
<hr>
<h5>Productos</h5>
<table class="table table-bordered" id="tablaProductos">
    <thead class="table-light">
        <tr>
            <th>Producto</th>
            <th>Precio</th>
            <th>Marca</th>
            <th>Cantidad</th>
            <th></th>
        </tr>
    </thead>
    <tbody id="cuerpoProductos">
        <tr>
            <td class="position-relative">
                <input
                type="text" 
                list="productos-list"
                id="id_producto"
                class="form-control id_producto"
                placeholder="Buscar producto..."
                name="producto_id[]" 
                onchange="onChangeProductos(this)"
                >
                <datalist id="productos-list">
                    <?php foreach($productos as $producto): ?>
                        <option 
                        value="<?php echo $producto['id_producto']; ?>"
                        data-precio="<?php echo $producto['precio']; ?>"
                        data-nombre-producto="<?php echo $producto['nombre_producto']; ?>"
                        data-marca="<?php echo $producto['marca']; ?>"
                        >
                    <?php endforeach; ?>
                </datalist>
            </td>
            <td>
                <input style="pointer-events: none;" type="number" name="precio[]"
                    class="form-control precio" readonly>
            </td>
            <td>
                <input style="pointer-events: none;" type="text"
                    class="form-control marca" >
            </td>
            <td>
                <input onchange="onChangeCantidad(this)" type="number" name="cantidad[]" value="1" class="form-control cantidad" min="1">
            </td>
            <td>
                <button onclick="removeMySelf(this)" type="button" class="btn btn-danger btn-sm btnEliminar">X</button>
            </td>
        </tr>
    </tbody>
</table>
<button onclick="onAgregarProducto()" type="button" class="btn btn-secondary" id="btnAgregarFila">+</button>
                <hr>
                <div class="row justify-content-end">
                    <div class="col-md-4">
                        <table class="table">
                            <tr>
                                <th>Subtotal</th>
                                <td id="mostrarSubtotal">$0.00</td>
                            </tr>
                            <tr>
                                <th>IVA (13%)</th>
                                <td id="mostrarIva">$0.00</td>
                            </tr>
                            <tr>
                                <th>DESCUENTO</th>
                                <td id="mostrardDescuento">$0.00</td>
                            </tr>
                            <tr>
                                <th>Total</th>
                                <td id="mostrarTotal">
                                    <strong>$0.00</strong>
                                </td>
                            </tr>
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
    let canAdd = false;
    const agregarProducto = tr => {
        tr.insertAdjacentHTML(
            'afterend',
            `<tr>
                <td>
                    <input
                    type="text" 
                    list="productos-list"
                    id="id_producto"
                    class="form-control id_producto"
                    placeholder="Buscar producto..."
                    name="producto_id[]" 
                    onchange="onChangeProductos(this)"
                    >
                </td>
                <td>
                    <input style="pointer-events: none;" type="number" name="precio[]"
                    class="form-control precio" readonly>
                </td>
                <td>
                    <input style="pointer-events: none;" type="text"
                        class="form-control marca" >
                </td>
                <td>
                    <input onchange="onChangeCantidad(this)" type="number" name="cantidad[]" value="1" class="form-control cantidad" min="1">
                </td>
                <td>
                    <button onclick="removeMySelf(this)" type="button" class="btn btn-danger btn-sm btnEliminar">X</button>
                </td>
                </tr>`
            );
            canAdd = false;
    }

    const onAgregarProducto = () => {
        if(!canAdd) return;
        const a = document.querySelector('#cuerpoProductos').querySelectorAll('tr');
        agregarProducto(a[a.length-1]);
    }

    const clienteBusqueda = document.querySelector('#clienteBusqueda');
    const clienteNombre = document.querySelector('#nombres');
    const clienteId = document.querySelector('#cliente');
    const dataList = document.querySelector('#clientes-list');

    const onChangeDui = target => {
        console.log(target.value)
        const dui = target.value;
        const cliente = dataList.querySelector(`[value="${dui}"]`)
        console.log(cliente)
        clienteNombre.value = cliente.getAttribute('data-nombres');
        clienteId.value = cliente.getAttribute('data-cliente-id');
        
    }
    

    const dataListProductos = document.querySelector('#productos-list');

    const onChangeProductos = target => {
        const idProducto = target.value;
        const producto = dataListProductos.querySelector(`[value="${idProducto}"]`)
        const precio = producto.getAttribute('data-precio');
        const marca = producto.getAttribute('data-marca');
        const tr = target.closest('tr');

        const fieldPrecio = tr.querySelector('.precio');
        fieldPrecio.value = precio/100;
        fieldPrecio.setAttribute('data-precio', precio);
        tr.querySelector('.marca').value = marca;

        canAdd = true;

        subtotales(target.closest('tbody'));
    }

    const removeMySelf = target => {
        const tbody = target.closest('tbody');
        const tr = target.closest('tr');
        if(tbody.children.length > 1) {
            tr.remove();
            canAdd = true;
        }
        subtotales(tbody)
    }

    const onChangeCantidad = target => {
        const tr = target.closest('tr');
        const idProducto = tr.querySelector('.id_producto').value;
        const producto = dataListProductos.querySelector(`[value="${idProducto}"]`)

        const precio = producto.getAttribute('data-precio');
        const fieldPrecio = tr.querySelector('.precio');
        fieldPrecio.value = (precio * target.value)/100;
        subtotales(target.closest('tbody'))
    }

    const subtotalProyeccion = document.querySelector('#mostrarSubtotal');
    const ivaProyeccion = document.querySelector('#mostrarIva');
    const descuento = document.querySelector('#mostraDescuento');
    const mostrarTotal = document.querySelector('#mostrarTotal');

    const descuentoSelect = documento.querySelector('#descuento_select');

    const subtotales = tbody => {
        const subtotalesField = [...tbody.querySelectorAll('.precio')];
        let subtotal = subtotalesField.reduce(
            (acumulador, numero) => acumulador + parseInt(numero.getAttribute('data-precio')),
            0
        )
        console.log(subtotal)
        subtotalProyeccion.textContent = (subtotal / 100).toFixed(2);
        const iva = (0.13 * subtotal);
        const total = ((subtotal + iva)/100).toFixed(2);

        ivaProyeccion.textContent = (iva/100).toFixed(2);
        descuento.textContext = descuentoSelect.value;
        mostrarTotal.textContent = total;
    }

</script>
</body>
</html>