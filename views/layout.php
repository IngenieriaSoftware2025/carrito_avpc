<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="build/js/app.js"></script>
    <link rel="shortcut icon" href="<?= asset('images/carreta.jpg') ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?= asset('build/styles.css') ?>">
    <title>carrito_avpc</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark  bg-dark">

        <div class="container-fluid">

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="/<?= $_ENV['APP_NAME'] ?>/">
                <i class="bi bi-grid-3x3-gap"></i> Aplicaciones
            </a>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav ms-auto">

                    <li class="nav-item">
                        <a class="nav-link" href="/carrito_avpc">
                            <i class="bi bi-house-door me-2"></i>Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/carrito_avpc/clientes">
                            <i class="bi bi-cart3 me-2"></i>clientes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/carrito_avpc/productos">
                            <i class="bi bi-cart3 me-2"></i>productos
                        </a>
                    </li>
                     <li class="nav-item">
                        <a class="nav-link" href="/carrito_avpc/ventas">
                            <i class="bi bi-cart3 me-2"></i>Ventas
                        </a>
                    </li>
                     <li class="nav-item">
                        <a class="nav-link" href="/carrito_avpc/productos_ventas">
                            <i class="bi bi-cart3 me-2"></i>detalle venta
                        </a>
                    </li>
                </ul>
            </div>
        </div>

    </nav>
    
    <div class="progress fixed-bottom" style="height: 6px;">
        <div class="progress-bar progress-bar-animated bg-danger" id="bar" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
    <div class="container-fluid pt-5 mb-4" style="min-height: 85vh">

        <?php echo $contenido; ?>
    </div>
    <div class="container-fluid ">
        <div class="row justify-content-center text-center">
            <div class="col-12">
                <p style="font-size:xx-small; font-weight: bold;">
                    Compras organizadas <?= date('Y') ?> &copy;
                </p>
            </div>
        </div>
    </div>
</body>

</html>