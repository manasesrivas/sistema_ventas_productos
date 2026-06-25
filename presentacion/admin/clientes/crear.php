<?php


require_once __DIR__ . '/../../../negocio/ClienteNegocio.php';

$clienteNegocio = new ClienteNegocio();

$errores = [];
$mensaje = '';

$datos = [
    'nombres' => '',
    'dui' => '',
    'nit' => '',
    'telefono' => '',
    'direccion' => '',
    'tipo' => '',
    'nrc' => ''
];

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $datos = [
        'nombres' => $_POST['Nombres'],
        'dui' => $_POST['DUI'],
        'nit' => $_POST['NIT'],
        'telefono' => $_POST['Telefono'],
        'direccion' => $_POST['Direccion'],
        'tipo' => $_POST['Tipo'],
        'nrc' => $_POST['NRC']
    ];

    $resultado = $clienteNegocio->crearCliente($datos);

    if($resultado['exito']){
        header('Location: listar.php?mensaje=creado');
        exit;
    }else{
        $errores = $resultado['errores'];
    }
    
    function mostrarValor($valor){
        return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
    }
    
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../../public/bootstrap/css/bootstrap.min.css">
    <title>Registrar cliente</title>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Registrar cliente</h4>
            </div>

            <div class="card-body">
                <?php if(!empty($errores)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach($errores as $error): ?>
                                <li><?php echo mostrarValor($error)?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            
                <form action="crear.php" method="post">
                    <div class="mb-3">
                        <label for="" class="form-label">Nombre del cliente</label>
                        <input type="text" name="Nombres" id="" class="form-control"
                        value="<?php echo @$_POST['Nombres'];?>">
                    </div>
                    <div class="mb-3">
                        <label for="" class="form-label">DUI</label>
                        <input type="text" name="DUI" id="" class="form-control" placeholder="12345678-9"
                        value="<?php echo @$_POST['DUI']; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="" class="form-label">NIT</label>
                        <input type="text" name="NIT" id="" class="form-control"
                        value="<?php echo @$_POST['NIT']; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="" class="form-label">Telefono</label>
                        <input type="text" name="Telefono" id="" class="form-control"
                        value="<?php echo @$_POST['Telefono']; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="" class="form-label">Direccion</label>
                        <textarea name="Direccion" rows="3" id="" class="form-control"><?php echo @$_POST['Direccion']; ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="" class="form-label">Tipo de cliente</label>
                        <select name="Tipo" id="" class="form-select">
                            <option value="">Seleccione</option>
                            <option value="PN">Persona natural</option>
                            <option value="PJ">persona juridica</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="" class="form-label">NRC</label>
                        <input type="text" name="NRC" id="" class="form-control" 
                        value="<?php echo @$_POST['NRC']; ?>">
                    </div>

                    <button type="submit" class="btn btn-success">Guardar</button>
                    <a href="listar.php" class="btn btn-secondary">Cancelar</a>
                    
                </form>
            </div>
        </div>
    </div>
    <script src="../../../public/bootstrap/js/bootstrap.bundle.min.js"></script>t
</body>
</html>


