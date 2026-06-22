<?php
// presentacion/admin/listar.php
session_start();    

// Simulador en memoria activa por defecto para que lo pruebes YA sin que se trabe
if (!isset($_SESSION['lista_categorias'])) {
    $_SESSION['lista_categorias'] = [
        ['id_categoria' => 1, 'Categoria' => 'Laptops', 'Descripcion' => 'Equipos portátiles Dell, HP, etc.'],
        ['id_categoria' => 2, 'Categoria' => 'Componentes', 'Descripcion' => 'Memorias RAM, Discos SSD y más']
    ];
}

$editando = false;
$idEditar = "";
$catEditar = "";
$descEditar = "";

// --- DETECTOR: GUARDAR / ACTUALIZAR ---
if (isset($_POST['btn_guardar'])) {
    $nombre = trim($_POST['txt_categoria']);
    $desc = trim($_POST['txt_descripcion']);
    $accion = $_POST['accion'];

    if (!empty($nombre)) {
        if ($accion === 'nuevo') {
            $nuevoId = count($_SESSION['lista_categorias']) + 1;
            $_SESSION['lista_categorias'][] = [
                'id_categoria' => $nuevoId, 
                'Categoria' => $nombre, 
                'Descripcion' => $desc
            ];
        } else if ($accion === 'editar') {
            $id = $_POST['id_categoria'];
            foreach ($_SESSION['lista_categorias'] as &$item) {
                if ($item['id_categoria'] == $id) {
                    $item['Categoria'] = $nombre;
                    $item['Descripcion'] = $desc;
                }
            }
        }
    }
    header("Location: listar.php");
    exit();
}

// --- DETECTOR: ACCIONAR EDICIÓN ---
if (isset($_GET['editar'])) {
    $editando = true;
    $idEditar = $_GET['editar'];
    foreach ($_SESSION['lista_categorias'] as $item) {
        if ($item['id_categoria'] == $idEditar) {
            $catEditar = $item['Categoria'];
            $descEditar = $item['Descripcion'];
        }
    }
}

// --- DETECTOR: ELIMINAR ---
if (isset($_GET['eliminar'])) {
    $idEliminar = $_GET['eliminar'];
    foreach ($_SESSION['lista_categorias'] as $index => $item) {
        if ($item['id_categoria'] == $idEliminar) {
            array_splice($_SESSION['lista_categorias'], $index, 1);
        }
    }
    header("Location: listar.php");
    exit();
}

$categorias = $_SESSION['lista_categorias'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Categorías</title>
    <link rel="stylesheet" href="../../../public/bootstrap/styles/style.css">
</head>
<body>

<div class="contenedor">
    <h2>Categorías registrardas</h2>
    
    <form action="" method="POST" class="formulario">
        <input type="hidden" name="accion" value="<?php echo $editando ? 'editar' : 'nuevo'; ?>">
        <input type="hidden" name="id_categoria" value="<?php echo $idEditar; ?>">
        
        <?php if ($editando): ?>
            <span style="color: #3498db; font-size: 13px; font-weight: bold;">Editando Categoría ID: <?php echo $idEditar; ?></span>
        <?php endif; ?>

        <input type="text" name="txt_categoria" placeholder="Nombre de la categoría (Ej: Memorias)" value="<?php echo htmlspecialchars($catEditar); ?>" required>
        <textarea name="txt_descripcion" placeholder="Breve descripción de los productos..." required><?php echo htmlspecialchars($descEditar); ?></textarea>
        
        <div class="fila-botones">
            <?php if ($editando): ?>
                <button type="submit" name="btn_guardar" class="btn btn-actualizar">Actualizar</button>
                <a href="listar.php" class="btn btn-cancelar">Cancelar</a>
            <?php else: ?>
                <button type="submit" name="btn_guardar" class="btn btn-guardar">Guardar Categoría</button>
            <?php endif; ?>
        </div>
    </form>

    <h3>Categorías Registradas:</h3>
    <table class="tabla">
        <thead>
            <tr>
                <th>Categoría</th>
                <th>Descripción</th>
                <th style="width: 140px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($categorias)): ?>
                <?php foreach ($categorias as $item): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($item['Categoria']); ?></strong></td>
                        <td style="color: #777; font-size: 14px;"><?php echo htmlspecialchars($item['Descripcion']); ?></td>
                        <td>
                            <div class="acciones">
                                <a href="listar.php?editar=<?php echo $item['id_categoria']; ?>" class="btn-accion btn-editar">Editar</a>
                                <a href="listar.php?eliminar=<?php echo $item['id_categoria']; ?>" class="btn-accion btn-eliminar" onclick="return confirm('¿Eliminar esta categoría?')">Borrar</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" style="text-align: center; color: #999;">No hay categorías añadidas.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html> 
