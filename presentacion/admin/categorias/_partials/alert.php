
<?php 
$mensaje = $_SESSION['MENSAJE'] ?? '';

if(isset($_SESSION['MENSAJE'])):

?>

<div  class="modal-homemade">
    <div class="modal-contenido">
        <?php if($mensaje === 'creado'): session_destroy();?>
            <div class="alert alert-success">Categoria registrado correctamente.</div>
        <?php elseif($mensaje === 'actualizado'): session_destroy();?>
            <div class="alert alert-success">Categoria actualizado correctamente.</div>
        <?php elseif($mensaje === 'elimnado'): session_destroy();?>
            <div class="alert alert-success">Categoria eliminado correctamente.</div>
        <?php endif; ?>
        <button class="btn-accion btn-editar" onclick="ocultarModal()">Cerrar</button>
    </div>
</div>


<?php endif; ?>