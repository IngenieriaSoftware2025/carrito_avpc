<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\ProductosVentas;
use MVC\Router;

class ProductoVentasController extends ActiveRecord
{
    public function renderizarPagina(Router $router)
    {
        $router->render('productos_ventas/index', []);
    }

    public static function guardarAPI()
    {
        getHeadersApi();

        // Validar nombre
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

        // Verificar que el producto no exista
        if (ProductosVentas::existeProducto($_POST['producto_nombre'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Este producto ya existe'
            ]);
            return;
        }

        // Validar precio
        $_POST['producto_precio'] = filter_var($_POST['producto_precio'], FILTER_VALIDATE_FLOAT);
        if (!$_POST['producto_precio'] || $_POST['producto_precio'] <= 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El precio debe ser mayor a 0'
            ]);
            return;
        }

        // Validar stock
        $_POST['producto_stock'] = filter_var($_POST['producto_stock'], FILTER_VALIDATE_INT);
        if ($_POST['producto_stock'] < 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El stock no puede ser negativo'
            ]);
            return;
        }

        // Sanitizar descripción
        $_POST['producto_descripcion'] = htmlspecialchars($_POST['producto_descripcion'] ?? '');

        try {
            $data = new ProductosVentas([
                'producto_nombre' => $_POST['producto_nombre'],
                'producto_precio' => $_POST['producto_precio'],
                'producto_stock' => $_POST['producto_stock'],
                'producto_descripcion' => $_POST['producto_descripcion'],
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
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    public static function modificarAPI()
    {
        getHeadersApi();

        $id = $_POST['producto_id'];

        // Validar nombre
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

        // Verificar que el producto no exista (excluyendo el actual)
        if (ProductosVentas::existeProducto($_POST['producto_nombre'], $id)) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Este producto ya existe'
            ]);
            return;
        }

        // Validar precio
        $_POST['producto_precio'] = filter_var($_POST['producto_precio'], FILTER_VALIDATE_FLOAT);
        if (!$_POST['producto_precio'] || $_POST['producto_precio'] <= 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El precio debe ser mayor a 0'
            ]);
            return;
        }

        // Validar stock
        $_POST['producto_stock'] = filter_var($_POST['producto_stock'], FILTER_VALIDATE_INT);
        if ($_POST['producto_stock'] < 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El stock no puede ser negativo'
            ]);
            return;
        }

        // Sanitizar descripción
        $_POST['producto_descripcion'] = htmlspecialchars($_POST['producto_descripcion'] ?? '');

        try {
            $data = ProductosVentas::find($id);
            if (!$data) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Producto no encontrado'
                ]);
                return;
            }

            $data->sincronizar([
                'producto_nombre' => $_POST['producto_nombre'],
                'producto_precio' => $_POST['producto_precio'],
                'producto_stock' => $_POST['producto_stock'],
                'producto_descripcion' => $_POST['producto_descripcion'],
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

    public static function eliminarAPI()
    {
        try {
            $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

            if (!$id) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'ID de producto inválido'
                ]);
                return;
            }

            // Verificar si el producto tiene ventas
            $sql_verificar = "SELECT COUNT(*) as total FROM detalle_ventas WHERE detalle_producto_id = " . $id . " AND detalle_situacion = 1";
            $verificacion = self::fetchFirst($sql_verificar);

            if ($verificacion['total'] > 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se puede eliminar el producto porque tiene ventas registradas'
                ]);
                return;
            }

            // Eliminar producto (cambiar situación)
            $sql = "UPDATE productos SET producto_situacion = 0 WHERE producto_id = " . $id;
            $ejecutar = self::SQL($sql);

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

    // Agregar stock a un producto
    public static function agregarStockAPI()
    {
        getHeadersApi();

        $id = filter_var($_POST['producto_id'], FILTER_SANITIZE_NUMBER_INT);
        $cantidad = filter_var($_POST['cantidad'], FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de producto inválido'
            ]);
            return;
        }

        if (!$cantidad || $cantidad <= 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La cantidad debe ser mayor a 0'
            ]);
            return;
        }

        try {
            $producto = ProductosVentas::find($id);
            if (!$producto) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Producto no encontrado'
                ]);
                return;
            }

            $resultado = $producto->agregarStock($cantidad);

            if ($resultado) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => "Se agregaron {$cantidad} unidades al stock del producto"
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al agregar stock'
                ]);
            }

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al agregar stock',
                'detalle' => $e->getMessage(),
            ]);
        }
    }
}