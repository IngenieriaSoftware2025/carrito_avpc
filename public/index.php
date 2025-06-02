<?php 
require_once __DIR__ . '/../includes/app.php';

use Controllers\VentaController;
use Controllers\ClienteController;
use Controllers\ProductoVentasController;
use MVC\Router;
use Controllers\AppController;

$router = new Router();
$router->setBaseURL('/' . $_ENV['APP_NAME']);

// RUTA PRINCIPAL
$router->get('/', [AppController::class,'index']);

// RUTAS DE VENTAS
$router->get('/ventas', [VentaController::class, 'renderizarPagina']);
$router->get('/ventas/clientesAPI', [VentaController::class, 'clientesAPI']);
$router->get('/ventas/productosDisponiblesAPI', [VentaController::class, 'productosDisponiblesAPI']);
$router->post('/ventas/guardarAPI', [VentaController::class, 'guardarAPI']);
$router->get('/ventas/buscarAPI', [VentaController::class, 'buscarAPI']);
$router->get('/ventas/detalleVentaAPI', [VentaController::class, 'detalleVentaAPI']);
$router->get('/ventas/eliminar', [VentaController::class, 'eliminarAPI']);

// RUTAS DE CLIENTES
$router->get('/clientes', [ClienteController::class, 'renderizarPagina']);
$router->post('/clientes/guardarAPI', [ClienteController::class, 'guardarAPI']);
$router->get('/clientes/buscarAPI', [ClienteController::class, 'buscarAPI']);
$router->post('/clientes/modificarAPI', [ClienteController::class, 'modificarAPI']);
$router->get('/clientes/eliminar', [ClienteController::class, 'eliminarAPI']);

// RUTAS DE PRODUCTOS
$router->get('/productos', [ProductoVentasController::class, 'renderizarPagina']);
$router->post('/productos/guardarAPI', [ProductoVentasController::class, 'guardarAPI']);
$router->get('/productos/buscarAPI', [ProductoVentasController::class, 'buscarAPI']);
$router->post('/productos/modificarAPI', [ProductoVentasController::class, 'modificarAPI']);
$router->get('/productos/eliminar', [ProductoVentasController::class, 'eliminarAPI']);
$router->post('/productos/agregarStockAPI', [ProductoVentasController::class, 'agregarStockAPI']);

$router->comprobarRutas();