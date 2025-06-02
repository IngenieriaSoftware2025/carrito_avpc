<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\Ventas;
use Model\Clientes;
use Model\ProductosVentas;
use Model\DetalleVentas;
use MVC\Router;

class VentaController extends ActiveRecord
{
    public function renderizarPagina(Router $router)
    {
        $router->render('ventas/index', []);
    }

    // Obtener clientes para el select
    public static function clientesAPI()
    {
        try {
            $sql = "SELECT * FROM clientes WHERE cliente_situacion = 1 ORDER BY cliente_nombre, cliente_apellido";
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Clientes obtenidos correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener los clientes',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    // Obtener productos disponibles
    public static function productosDisponiblesAPI()
    {
        try {
            $data = ProductosVentas::obtenerDisponibles();

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

    // Guardar venta completa
    public static function guardarAPI()
    {
        getHeadersApi();

        // Validar cliente
        $_POST['venta_cliente_id'] = filter_var($_POST['venta_cliente_id'], FILTER_VALIDATE_INT);
        if (!$_POST['venta_cliente_id'] || $_POST['venta_cliente_id'] <= 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe seleccionar un cliente válido'
            ]);
            return;
        }

        // Validar que exista el cliente
        $cliente = Clientes::find($_POST['venta_cliente_id']);
        if (!$cliente) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El cliente seleccionado no existe'
            ]);
            return;
        }

        // Validar productos
        if (!isset($_POST['productos']) || empty($_POST['productos'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe seleccionar al menos un producto'
            ]);
            return;
        }

        $productos = json_decode($_POST['productos'], true);
        if (!$productos || !is_array($productos)) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Formato de productos inválido'
            ]);
            return;
        }

        $total_venta = 0;
        $productos_validados = [];

        // Validar cada producto
        foreach ($productos as $producto) {
            // Validar producto_id
            $producto_id = filter_var($producto['producto_id'], FILTER_VALIDATE_INT);
            if (!$producto_id || $producto_id <= 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'ID de producto inválido'
                ]);
                return;
            }

            // Validar cantidad
            $cantidad = filter_var($producto['cantidad'], FILTER_VALIDATE_INT);
            if (!$cantidad || $cantidad <= 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'La cantidad debe ser mayor a 0'
                ]);
                return;
            }

            // Verificar que el producto existe y tiene stock
            $producto_obj = ProductosVentas::find($producto_id);
            if (!$producto_obj) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El producto seleccionado no existe'
                ]);
                return;
            }

            if (!$producto_obj->tieneStock($cantidad)) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => "Stock insuficiente para el producto: {$producto_obj->producto_nombre}. Stock disponible: {$producto_obj->producto_stock}"
                ]);
                return;
            }

            $subtotal = $cantidad * $producto_obj->producto_precio;
            $total_venta += $subtotal;

            $productos_validados[] = [
                'producto_id' => $producto_id,
                'cantidad' => $cantidad,
                'precio_unitario' => $producto_obj->producto_precio,
                'subtotal' => $subtotal,
                'producto_obj' => $producto_obj
            ];
        }

        try {
            // Crear la venta
            $venta = new Ventas([
                'venta_cliente_id' => $_POST['venta_cliente_id'],
                'venta_total' => $total_venta,
                'venta_fecha' => date('Y-m-d H:i:s')
            ]);

            $resultado_venta = $venta->crear();
            $venta_id = $resultado_venta['id'];

            // Crear los detalles y descontar stock
            foreach ($productos_validados as $producto) {
                // Crear detalle
                $detalle = new DetalleVentas([
                    'detalle_venta_id' => $venta_id,
                    'detalle_producto_id' => $producto['producto_id'],
                    'detalle_cantidad' => $producto['cantidad'],
                    'detalle_precio_unitario' => $producto['precio_unitario'],
                    'detalle_subtotal' => $producto['subtotal']
                ]);

                $detalle->crear();

                // Descontar stock
                $producto['producto_obj']->descontarStock($producto['cantidad']);
            }

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Venta guardada correctamente',
                'venta_id' => $venta_id
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar la venta',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    // Buscar todas las ventas
    public static function buscarAPI()
    {
        try {
            $data = Ventas::obtenerVentasConCliente();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Ventas obtenidas correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener las ventas',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    // Obtener detalle de una venta específica
    public static function detalleVentaAPI()
    {
        try {
            $venta_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
            
            if (!$venta_id) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'ID de venta inválido'
                ]);
                return;
            }

            $venta = Ventas::obtenerVentaConCliente($venta_id);
            if (!$venta) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Venta no encontrada'
                ]);
                return;
            }

            $detalles = DetalleVentas::obtenerDetallesPorVenta($venta_id);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Detalle obtenido correctamente',
                'venta' => $venta,
                'detalles' => $detalles
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener el detalle',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    // Eliminar venta
    public static function eliminarAPI()
    {
        try {
            $venta_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

            if (!$venta_id) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'ID de venta inválido'
                ]);
                return;
            }

            $ejecutar = Ventas::eliminarVenta($venta_id);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'La venta ha sido eliminada correctamente y el stock ha sido devuelto'
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al eliminar la venta',
                'detalle' => $e->getMessage(),
            ]);
        }
    }
}