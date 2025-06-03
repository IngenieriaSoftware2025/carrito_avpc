<?php 
require_once __DIR__ . '/../includes/app.php';

use Controllers\ClienteController;
use MVC\Router;
use Controllers\AppController;
use Controllers\FacturaController;
use Controllers\ProductoController;
use Controllers\VentaController;

$router = new Router();
$router->setBaseURL('/' . $_ENV['APP_NAME']);

// Ruta principal
$router->get('/', [AppController::class,'index']);


$router->get('/clientes', [ClienteController::class, 'renderizarPagina']);
$router->post('/clientes/guardarAPI', [ClienteController::class, 'guardarAPI']);
$router->get('/clientes/buscarAPI', [ClienteController::class, 'buscarAPI']);
$router->post('/clientes/modificarAPI', [ClienteController::class, 'modificarAPI']);
$router->get('/clientes/eliminar', [ClienteController::class, 'EliminarAPI']);
$router->get('/clientes/activos', [ClienteController::class, 'clientesActivosAPI']);


$router->get('/productos', [ProductoController::class, 'renderizarPagina']);
$router->post('/productos/guardarAPI', [ProductoController::class, 'guardarAPI']);
$router->get('/productos/buscarAPI', [ProductoController::class, 'buscarAPI']);
$router->post('/productos/modificarAPI', [ProductoController::class, 'modificarAPI']);
$router->get('/productos/eliminar', [ProductoController::class, 'EliminarAPI']);
$router->get('/productos/disponibles', [ProductoController::class, 'productosDisponiblesAPI']);


$router->get('/ventas', [VentaController::class, 'renderizarPagina']);
$router->post('/ventas/guardarAPI', [VentaController::class, 'guardarAPI']);
$router->get('/ventas/buscarAPI', [VentaController::class, 'buscarAPI']);
$router->get('/ventas/detalle', [VentaController::class, 'obtenerDetalleAPI']);
$router->post('/ventas/modificarAPI', [VentaController::class, 'modificarAPI']);
$router->get('/ventas/eliminar', [VentaController::class, 'eliminarAPI']);
$router->get('/ventas/clientes', [VentaController::class, 'obtenerClientesAPI']);


$router->get('/facturas/ver', [FacturaController::class, 'verFactura']);

$router->comprobarRutas();