<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\Productos;
use MVC\Router;

class ProductoController extends ActiveRecord{
    
    public static function renderizarPagina(Router $router){
        $router->render('productos/index', []);
    }

   
    public static function guardarAPI(){
        getHeadersApi();

        $_POST['producto_nombre'] = htmlspecialchars($_POST['producto_nombre']);
        $cantidad_nombre = strlen($_POST['producto_nombre']);

        if ($cantidad_nombre < 2){
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre del producto debe tener al menos 2 caracteres'
            ]);
            return;
        }

   
        $nombre_repetido = trim(strtolower($_POST['producto_nombre']));
        $sql_verificar = "SELECT producto_id FROM productos 
                         WHERE LOWER(TRIM(producto_nombre)) = " . self::$db->quote($nombre_repetido) . "
                         AND producto_situacion = 1";
        $nombre_existe = self::fetchFirst($sql_verificar);
        
        if ($nombre_existe) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Ya existe un producto con este nombre'
            ]);
            return;
        }

        $_POST['producto_descripcion'] = htmlspecialchars($_POST['producto_descripcion']);

        $precio_validado = filter_var($_POST['producto_precio'], FILTER_VALIDATE_FLOAT);
        if ($precio_validado === false || $precio_validado <= 0){
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El precio debe ser mayor a cero y ser un número válido'
            ]);
            return;
        }
        $_POST['producto_precio'] = $precio_validado;

        $cantidad_validada = filter_var($_POST['producto_cantidad'], FILTER_VALIDATE_INT);
        if ($cantidad_validada === false || $cantidad_validada < 0){
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La cantidad debe ser un número válido y no puede ser negativa'
            ]);
            return;
        }
        $_POST['producto_cantidad'] = $cantidad_validada;

        try {
            $data = new Productos([
                'producto_nombre' => $_POST['producto_nombre'],
                'producto_descripcion' => $_POST['producto_descripcion'],
                'producto_precio' => $_POST['producto_precio'],
                'producto_cantidad' => $_POST['producto_cantidad'],
                'producto_situacion' => 1
            ]);

            $crear = $data->crear();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'El producto ha sido registrado con éxito'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar el producto',
                'detalle' => $e->getMessage()
            ]);
        }
    }


    public static function buscarAPI(){
        try {
            $sql = "SELECT * FROM productos WHERE producto_situacion = 1 ORDER BY producto_nombre";
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Productos obtenidos correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener los productos',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    
    public static function modificarAPI(){
        getHeadersApi();

        $id = $_POST['producto_id'];

        $_POST['producto_nombre'] = htmlspecialchars($_POST['producto_nombre']);
        $cantidad_nombre = strlen($_POST['producto_nombre']);

        if ($cantidad_nombre < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre del producto debe tener al menos 2 caracteres'
            ]);
            return;
        }

   
        $nombre_repetido = trim(strtolower($_POST['producto_nombre']));
        $sql_verificar = "SELECT producto_id FROM productos 
                         WHERE LOWER(TRIM(producto_nombre)) = " . self::$db->quote($nombre_repetido) . "
                         AND producto_situacion = 1 
                         AND producto_id != " . (int)$id;
        $nombre_existe = self::fetchFirst($sql_verificar);
        
        if ($nombre_existe) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Ya existe otro producto con este nombre'
            ]);
            return;
        }

        $_POST['producto_descripcion'] = htmlspecialchars($_POST['producto_descripcion']);

        $precio_validado = filter_var($_POST['producto_precio'], FILTER_VALIDATE_FLOAT);
        if ($precio_validado === false || $precio_validado <= 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El precio debe ser mayor a cero y ser un número válido'
            ]);
            return;
        }
        $_POST['producto_precio'] = $precio_validado;

        $cantidad_validada = filter_var($_POST['producto_cantidad'], FILTER_VALIDATE_INT);
        if ($cantidad_validada === false || $cantidad_validada < 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La cantidad debe ser un número válido y no puede ser negativa'
            ]);
            return;
        }
        $_POST['producto_cantidad'] = $cantidad_validada;

        try {
            $data = Productos::find($id);
            $data->sincronizar([
                'producto_nombre' => $_POST['producto_nombre'],
                'producto_descripcion' => $_POST['producto_descripcion'],
                'producto_precio' => $_POST['producto_precio'],
                'producto_cantidad' => $_POST['producto_cantidad'],
                'producto_situacion' => 1
            ]);

            $data->actualizar();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'La información del producto ha sido modificada con éxito'
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al modificar el producto',
                'detalle' => $e->getMessage()
            ]);
        }
    }

  
    public static function EliminarAPI(){
        try {
            $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

            $stock_actual = Productos::ValidarStockProducto($id);
            
            if ($stock_actual > 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se puede eliminar el producto porque tiene existencias en stock',
                    'detalle' => "Stock actual: $stock_actual unidades. Debe agotar el stock antes de eliminar el producto."
                ]);
                return;
            }

         
            $ventas_producto = self::fetchFirst("SELECT COUNT(*) as total FROM venta_detalles vd 
                                               INNER JOIN ventas v ON vd.detalle_venta_id = v.venta_id 
                                               WHERE vd.detalle_producto_id = $id AND v.venta_situacion = 1");
            
            if ($ventas_producto['total'] > 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se puede eliminar el producto porque tiene ventas registradas',
                    'detalle' => "El producto tiene {$ventas_producto['total']} ventas asociadas"
                ]);
                return;
            }

            $sql_verificar = "SELECT producto_id, producto_nombre FROM productos WHERE producto_id = $id AND producto_situacion = 1";
            $producto = self::fetchFirst($sql_verificar);
            
            if (!$producto) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El producto no existe o ya está inactivo'
                ]);
                return;
            }

            Productos::EliminarProducto($id);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'El producto ha sido desactivado correctamente',
                'detalle' => "Producto '{$producto['producto_nombre']}' desactivado exitosamente"
            ]);
        
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al eliminar el producto',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function productosDisponiblesAPI(){
        try {
            $data = Productos::ObtenerProductosDisponibles();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Productos disponibles obtenidos correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener los productos disponibles',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}