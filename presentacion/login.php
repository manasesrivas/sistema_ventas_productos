<?php 
session_start();

if(isset($_SESSION['usuario'])){
    if($_SESSION['tipoCuenta'] === 'ADMINISTRADOR'){
        header('Location: admin/ventas/listar.php');
        exit;
    }
    if($_SESSION['tipoCuenta'] === 'VENDEDOR'){
        header('Location: vendedor/index.php');
        exit;
    }
}
$mensaje = $_GET['mensaje'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="../public/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../public/css/estilos.css">
</head>

<body class="bg-light">

    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-12 col-md-6 col-lg-5">

                <div class="card shadow border-0 rounded-4">
                    <div class="card-header bg-primary text-white text-center rounded-top-4 py-3">
                        <h4 class="mb-0">Sistema de Ventas</h4>
                        <small>Inicio de sesión</small>
                    </div>

                    <div class="card-body p-4">

                        <?php if(!empty($mensaje)): ?>

                            <div class="alert alert-danger text-center">
                                <?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?>
                            </div>
                            
                        <?php endif; ?> 
                        
                        <form action="validar_login.php" method="POST">

                            <div class="mb-3">
                                <label for="usuario" class="form-label">Usuario</label>
                                <input type="text" name="usuario" id="usuario" class="form-control" placeholder="Ingrese su usuario" autocomplete="username" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" name="password" id="password" class="form-control"  placeholder="Ingrese su contraseña" autocomplete="current-password" required>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    Ingresar
                                </button>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="../public/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>