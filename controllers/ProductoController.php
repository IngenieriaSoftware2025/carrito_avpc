<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\Productos;
use Model\Categorias;
use MVC\Router;

class ProductoController extends ActiveRecord
{
    public function renderizarPagina(Router $router)
    {
        $router->render('productos/index', []);
    }

    public static function guardarAPI()
    {
        getHeadersApi();

        // Validar nombre del producto
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

        // Validar precio del producto
        $_POST['producto_precio'] = filter_var($_POST['producto_precio'], FILTER_VALIDATE_FLOAT);

        if ($_POST['producto_precio'] <= 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El precio debe ser mayor a 0'
            ]);
            return;
        }

        // Validar stock del producto
        $_POST['producto_stock'] = filter_var($_POST['producto_stock'], FILTER_VALIDATE_INT);

        if ($_POST['producto_stock'] < 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El stock no puede ser negativo'
            ]);
            return;
        }

        // Validar categoría
        $_POST['producto_categoria_id'] = filter_var($_POST['producto_categoria_id'], FILTER_VALIDATE_INT);

        if (!$_POST['producto_categoria_id']) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe seleccionar una categoría'
            ]);
            return;
        }

        try {
            $data = new Productos([
                'producto_nombre' => $_POST['producto_nombre'],
                'producto_precio' => $_POST['producto_precio'],
                'producto_stock' => $_POST['producto_stock'],
                'producto_categoria_id' => $_POST['producto_categoria_id'],
                'producto_situacion' => 1
            ]);

            $crear = $data->crear();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'El producto ha sido agregado correctamente'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar',
                'detalle' => $e->getMessage(),
            ]);
        }
    }








    public static function buscarAPI()
    {
        try {
            $sql = "SELECT p.*, c.categoria_nombre 
                    FROM productos p 
                    INNER JOIN categorias c ON p.producto_categoria_id = c.categoria_id 
                    WHERE p.producto_situacion = 1 
                    ORDER BY c.categoria_nombre, p.producto_nombre";
            
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
                'detalle' => $e->getMessage(),
            ]);
        }
    }






 public static function modificarAPI()
    {
        getHeadersApi();

        $id = $_POST['producto_id'];

        // Validar nombre del producto
        
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

        // Validar precio del producto

        $_POST['producto_precio'] = filter_var($_POST['producto_precio'], FILTER_VALIDATE_FLOAT);

        if ($_POST['producto_precio'] <= 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El precio debe ser mayor a 0'
            ]);
            return;
        }

        // Validar stock del producto
        $_POST['producto_stock'] = filter_var($_POST['producto_stock'], FILTER_VALIDATE_INT);

        if ($_POST['producto_stock'] < 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El stock no puede ser negativo'
            ]);
            return;
        }

        // Validar categoría
        $_POST['producto_categoria_id'] = filter_var($_POST['producto_categoria_id'], FILTER_VALIDATE_INT);

        if (!$_POST['producto_categoria_id']) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe seleccionar una categoría'
            ]);
            return;
        }

        try {
            $data = Productos::find($id);
            $data->sincronizar([
                'producto_nombre' => $_POST['producto_nombre'],
                'producto_precio' => $_POST['producto_precio'],
                'producto_stock' => $_POST['producto_stock'],
                'producto_categoria_id' => $_POST['producto_categoria_id'],
                'producto_situacion' => 1
            ]);
            $data->actualizar();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'La información del producto ha sido modificada exitosamente'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar',
                'detalle' => $e->getMessage(),
            ]);
        }
    }










    public static function EliminarAPI()
    {
        try {
            $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

            // Verificar si el producto tiene stock
            $producto = Productos::find($id);
            // Si no se encuentra el producto, devolver error

            if ($producto && $producto->producto_stock > 0) {// Si el stock es mayor a 0, no se puede eliminar
                // Devolver error si el producto tiene stock
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se puede eliminar un producto que tiene stock en existencia'
                ]);
                return;
            }
            // Si el producto no tiene stock, se elimina
            $ejecutar = Productos::eliminarProducto($id);

            http_response_code(200);
            echo json_encode([    
                'codigo' => 1,
                'mensaje' => 'El producto ha sido eliminado correctamente'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al eliminar',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

}





