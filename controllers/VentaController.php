<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\Ventas;
use Model\VentaDetalles;
use Model\Productos;
use Model\Clientes;
use MVC\Router;

class VentaController extends ActiveRecord{
    
    public static function renderizarPagina(Router $router){
        $router->render('ventas/index', []);
    }

    //Guardar Venta
    public static function guardarAPI(){
        getHeadersApi();

        if (empty($_POST['venta_cliente_id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe seleccionar un cliente'
            ]);
            return;
        }

        if (empty($_POST['productos'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe seleccionar al menos un producto'
            ]);
            return;
        }

        $productos = is_string($_POST['productos']) ? json_decode($_POST['productos'], true) : $_POST['productos'];
        
        if (!is_array($productos)) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Formato de productos inválido'
            ]);
            return;
        }

        $total_venta = 0;

        // Validar stock y calcular total
        foreach ($productos as $p) {
            $producto_id = $p['producto_id'];
            $cantidad_solicitada = $p['cantidad'];

            $stock_disponible = Productos::ValidarStockProducto($producto_id);

            if ($stock_disponible < $cantidad_solicitada) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => "Stock insuficiente para producto ID: {$producto_id}"
                ]);
                return;
            }

            $precio_unitario = $p['precio'];
            $subtotal = $cantidad_solicitada * $precio_unitario;
            $total_venta += $subtotal;
        }

        try {
            $venta = new Ventas([
                'venta_cliente_id' => $_POST['venta_cliente_id'],
                'venta_fecha' => date('Y-m-d H:i'),
                'venta_subtotal' => $total_venta,
                'venta_total' => $total_venta,
                'venta_situacion' => 1
            ]);

            $resultado_venta = $venta->crear();
            $venta_id = $resultado_venta['id'];

            // Guardar detalles
            foreach ($productos as $p) {
                $producto_id = $p['producto_id'];
                $cantidad = $p['cantidad'];
                $precio_unitario = $p['precio'];
                $subtotal = $cantidad * $precio_unitario;

                $detalle = new VentaDetalles([
                    'detalle_venta_id' => $venta_id,
                    'detalle_producto_id' => $producto_id,
                    'detalle_cantidad' => $cantidad,
                    'detalle_precio_unitario' => $precio_unitario,
                    'detalle_subtotal' => $subtotal
                ]);

                $detalle->crear();

                // Actualizar stock
                Productos::ActualizarStockProducto($producto_id, $cantidad);
            }

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Venta registrada correctamente',
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

    //Buscar Ventas
    public static function buscarAPI(){
        try {
            $data = Ventas::ObtenerVentasConClientes();

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

    //Obtener Detalle de Venta
    public static function obtenerDetalleAPI(){
        try {
            $venta_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
            
            $venta = Ventas::ObtenerVentaPorId($venta_id);
            $detalles = VentaDetalles::ObtenerDetallesPorVenta($venta_id);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Detalle de venta obtenido correctamente',
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

   
    public static function obtenerClientesAPI(){
        try {
            $sql = "SELECT cliente_id, cliente_nombres, cliente_apellidos, cliente_correo 
                    FROM clientes WHERE cliente_situacion = 1 
                    ORDER BY cliente_nombres";
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
}