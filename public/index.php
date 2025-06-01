<?php 
require_once __DIR__ . '/../includes/app.php';

use Controllers\ProductoController;
use Controllers\ClienteController;  
use MVC\Router;
use Controllers\AppController;

$router = new Router();
$router->setBaseURL('/' . $_ENV['APP_NAME']);

$router->get('/', [AppController::class,'index']);


//RUTAS PARA CLIENTES - AGREGAR ESTAS LÍNEAS SOLO PARA CLIENTES
$router->get('/clientes', [ClienteController::class, 'renderizarPagina']);
$router->post('/clientes/guardarAPI', [ClienteController::class, 'guardarAPI']);
$router->get('/clientes/buscarAPI', [ClienteController::class, 'buscarAPI']);
$router->post('/clientes/modificarAPI', [ClienteController::class, 'modificarAPI']);
$router->get('/clientes/eliminar', [ClienteController::class, 'eliminarAPI']);

//RUTAS PARA PRODUCTOS - AGREGAR ESTAS LÍNEAS SOLO PARA PRODUCTOS
$router->get('/productos', [ClienteController::class, 'renderizarPagina']);
$router->post('/productos/guardarAPI', [ClienteController::class, 'guardarAPI']);
$router->get('/productos/buscarAPI', [ClienteController::class, 'buscarAPI']);
$router->post('/productos/modificarAPI', [ClienteController::class, 'modificarAPI']);
$router->get('/productos/eliminar', [ClienteController::class, 'eliminarAPI']);
$router->get('/productos/categoriasAPI', [ProductoController::class, 'categoriasAPI']);

$router->comprobarRutas();