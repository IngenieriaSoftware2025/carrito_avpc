<?php 
require_once __DIR__ . '/../includes/app.php';

use Controllers\ProductoController;
use Controllers\ClienteController;
use Controllers\FacturaController;  
use MVC\Router;
use Controllers\AppController;

$router = new Router();
$router->setBaseURL('/' . $_ENV['APP_NAME']);

$router->get('/', [AppController::class,'index']);

//RUTAS PARA PRODUCTOS
$router->get('/productos', [ProductoController::class, 'renderizarPagina']);
$router->post('/productos/guardarAPI', [ProductoController::class, 'guardarAPI']);
$router->get('/productos/buscarAPI', [ProductoController::class, 'buscarAPI']);
$router->post('/productos/modificarAPI', [ProductoController::class, 'modificarAPI']);
$router->get('/productos/eliminar', [ProductoController::class, 'EliminarAPI']);
$router->get('/productos/categoriasAPI', [ProductoController::class, 'categoriasAPI']);

//RUTAS PARA CLIENTES
$router->get('/clientes', [ClienteController::class, 'renderizarPagina']);
$router->post('/clientes/guardarAPI', [ClienteController::class, 'guardarAPI']);
$router->get('/clientes/buscarAPI', [ClienteController::class, 'buscarAPI']);
$router->post('/clientes/modificarAPI', [ClienteController::class, 'modificarAPI']);
$router->get('/clientes/eliminar', [ClienteController::class, 'eliminarAPI']);

//RUTAS PARA FACTURAS (CARRITO) 
$router->get('/facturas', [FacturaController::class, 'renderizarPagina']);
$router->get('/facturas/productos-disponibles', [FacturaController::class, 'obtenerProductosDisponiblesAPI']);
$router->get('/facturas/clientes', [FacturaController::class, 'obtenerClientesAPI']);
$router->post('/facturas/procesar-venta', [FacturaController::class, 'procesarVentaAPI']);
$router->get('/facturas/buscarAPI', [FacturaController::class, 'buscarFacturasAPI']);
$router->get('/facturas/detalle', [FacturaController::class, 'obtenerFacturaCompletaAPI']);
$router->get('/facturas/anular', [FacturaController::class, 'anularFacturaAPI']);

$router->comprobarRutas();