<?php
$routes = [
    [
        'route' => '/daw/proyecto_final/presentacion/admin/productos/listar.php',
        'name' => 'Productos'
    ],
    [
        'name' => 'Categorias',
        'route' => '/daw/proyecto_final/presentacion/admin/categorias/listar.php',
    ],
    [
        'name' => 'Marcas',
        'route' => '/daw/proyecto_final/presentacion/admin/marcas/listar.php',
    ],
    [
        'name' => 'Clientes',
        'route' => '/daw/proyecto_final/presentacion/admin/clientes/listar.php',
    ],
    [
        'name' => 'Ventas',
        'route' => '/daw/proyecto_final/presentacion/admin/ventas/listar.php',
    ]
];
?>

<div class="body--menu">

    <div>
        <h2 class="name--store">
            Tienda Ñia Aide
        </h2>
    </div>

    <nav>
        <ul>
            <?php foreach($routes as $route): ?>
                <li>
                    <a href="<?php echo $route['route']; ?>"
                    class="<?php echo ($_SERVER['REQUEST_URI'] == $route['route']) ? 'path-active': ''; ?>"><?php echo $route['name']; ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
</div>