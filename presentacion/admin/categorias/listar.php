<?php

require_once __DIR__ . '/../../../negocio/CategoriaNegocio.php';

$categoriaNegocio = new CategoriaNegocio();
$categorias = $categoriaNegocio->listarCategorias();
$editando = '';

session_start();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorias registradas</title>
    <link rel="stylesheet" href="../../../public/bootstrap/styles/style.css">
    <link rel="stylesheet" href="../../../public/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../../public/bootstrap/styles/style.css">

</head>
    <body class="d-flex flex-column">

        <?php include '_partials/alert.php'; ?>

        <?php
            include __DIR__ . '/../_partials/menu.php';
        ?> 

        <div class="contenedor mx-auto">
            
            <h2>Categorías registrardas</h2>
            
            <form action="crear.php" method="POST" class="formulario">
                <?php
                    if(isset($_SESSION['ERRORES'])): ?>
                    <div class="alert alert-danger">    

                        <?php
                            foreach($_SESSION['ERRORES'] as $error){
                                echo '<p>'.$error.'</p>';
                            }
                            session_destroy();
                        ?>
                        
                    </div>
                <?php endif; ?>


                <input type="hidden" name="id_categoria">
                
                <span class="flag--state-form" style="color: #3498db; font-size: 13px; font-weight: bold; display:none;">Editando Categoría</span>

                <input type="text" name="categoria" placeholder="Nombre de la categoría (Ej: Memorias)"
                value="<?php echo @$_POST['categoria']?>"
                >
                <textarea name="descripcion" placeholder="Breve descripción de los productos..." ><?php echo @$_POST['descripcion']; ?></textarea>
                
                <div class="fila-botones">
                    <div onclick="cancelar()" style="display: none;" class="botton btn-cancelar cancelar">Cancelar</div>
                    <button class="botton btn-guardar boton-formulario">Guardar Categoría</button>
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
                        <?php foreach ($categorias as $categoria): ?>
                            <tr data-id="<?php echo $categoria['id_categoria']; ?>">
                                <td class="categoria"><strong><?php echo htmlspecialchars($categoria['categoria']); ?></strong></td>
                                <td class="descripcion" style="color: #777; font-size: 14px;"><?php echo htmlspecialchars($categoria['descripcion']); ?></td>
                                <td>
                                    <div class="acciones">
                                        <button onclick="onClick(this)" class="btn-accion btn-editar">Editar</button>
                                        <button data-id="<?php echo $categoria['id_categoria']; ?>" onclick="onDelete(this)" class="btn-accion btn-eliminar">Borrar</button>
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
        <script>

            const categoriaRecord = '';
            const formulario = document.querySelector('.formulario');
            const categoriaField = formulario.querySelector('[name="categoria"]');
            const descripcionField = formulario.querySelector('[name="descripcion"]');

            const inputIdCategoria = formulario.querySelector('[name="id_categoria"');

            const cancelarFormulario = formulario.querySelector('.cancelar');
            const botonFormulario = formulario.querySelector('.boton-formulario');

            const flagFormulario = formulario.querySelector('.flag--state-form');
            
            const onClick = target => {
                // console.log(target.closest('tr'));
                const record = target.closest('tr');

                const idCategoria = record.getAttribute('data-id');
                
                const categoria = record.querySelector('.categoria');
                const descripcion = record.querySelector('.descripcion');

                inputIdCategoria.value = idCategoria;
                
                categoriaField.value = categoria.textContent;
                descripcionField.value = descripcion.textContent;
                
                flagFormulario.style.display = 'block';
                
                botonFormulario.classList.remove('btn-guardar');
                botonFormulario.classList.add('btn-actualizar');
                botonFormulario.textContent = 'Actualizar';
                
                cancelarFormulario.style.display = 'inline';

                formulario.action = 'editar.php?id='+idCategoria;
            }
            
            const cancelar = target => {
                formulario.reset()
                botonFormulario.classList.add('btn-guardar');
                botonFormulario.classList.remove('btn-actualizar');
                botonFormulario.textContent = 'Guardar categoria';
                
                flagFormulario.style.display = 'none';
                cancelarFormulario.style.display = 'none';

                formulario.action = 'crear.php';
            }

            const onDelete = target => {
                id = target.getAttribute('data-id');
                if(window.confirm('Seguro que quieres eliminar esta categoria?')) window.location.href = 'eliminar.php?id='+id
            }

            function ocultarModal() {
                document.querySelector('.modal-homemade').style.display = 'none';
            }
            
        </script>

    </body>
</html> 
