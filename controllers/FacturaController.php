<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\Productos;
use Model\Clientes;
use Model\Facturas;
use Model\FacturaDetalle;
use MVC\Router;

class FacturaController extends ActiveRecord
{
    public function renderizarPagina(Router $router)
    {
        $router->render('facturas/index', []);
    }

    public static function obtenerProductosDisponiblesAPI()
    {
        try {
            $sql = "SELECT p.*, c.categoria_nombre 
                    FROM productos p 
                    INNER JOIN categorias c ON p.producto_categoria_id = c.categoria_id 
                    WHERE p.producto_situacion = 1 AND p.producto_stock > 0
                    ORDER BY c.categoria_nombre, p.producto_nombre";
            
            $data = self::fetchArray($sql);

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
                'mensaje' => 'Error al obtener productos disponibles',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    public static function obtenerClientesAPI()
    {
        try {
            $sql = "SELECT * FROM clientes 
                    WHERE cliente_situacion = 1 
                    ORDER BY cliente_nombre";
            
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
                'mensaje' => 'Error al obtener clientes',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    public static function procesarVentaAPI()
    {
        getHeadersApi();

        // Validar cliente
        $cliente_id = filter_var($_POST['cliente_id'], FILTER_VALIDATE_INT);
        if (!$cliente_id) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe seleccionar un cliente'
            ]);
            return;
        }

        // Validar productos (viene como JSON)
        $productos_json = $_POST['productos'] ?? '';
        if (empty($productos_json)) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe seleccionar al menos un producto'
            ]);
            return;
        }

        $productos = json_decode($productos_json, true);
        if (!$productos || count($productos) == 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'No se han seleccionado productos válidos'
            ]);
            return;
        }

        try {
            // Verificar stock disponible para todos los productos
            foreach ($productos as $item) {
                $producto = Productos::find($item['producto_id']);
                if (!$producto) {
                    http_response_code(400);
                    echo json_encode([
                        'codigo' => 0,
                        'mensaje' => 'Producto no encontrado: ' . $item['producto_id']
                    ]);
                    return;
                }

                if ($producto->producto_stock < $item['cantidad']) {
                    http_response_code(400);
                    echo json_encode([
                        'codigo' => 0,
                        'mensaje' => 'Stock insuficiente para: ' . $producto->producto_nombre . ' (Disponible: ' . $producto->producto_stock . ')'
                    ]);
                    return;
                }
            }

            // Calcular totales
            $subtotal = 0;
            foreach ($productos as $item) {
                $subtotal += $item['cantidad'] * $item['precio'];
            }

            // Crear factura
            $numero_factura = Facturas::generarNumero();
            $factura = new Facturas([
                'factura_numero' => $numero_factura,
                'factura_cliente_id' => $cliente_id,
                'factura_subtotal' => $subtotal,
                'factura_total' => $subtotal,
                'factura_situacion' => 1
            ]);

            $resultado_factura = $factura->crear();
            $factura_id = $resultado_factura['id'];

            // Crear detalle de factura y reducir stock
            foreach ($productos as $item) {
                // Crear detalle
                $detalle = new FacturaDetalle([
                    'detalle_factura_id' => $factura_id,
                    'detalle_producto_id' => $item['producto_id'],
                    'detalle_cantidad' => $item['cantidad'],
                    'detalle_precio_unitario' => $item['precio'],
                    'detalle_subtotal' => $item['cantidad'] * $item['precio']
                ]);
                $detalle->crear();

                // Reducir stock
                Productos::reducirStock($item['producto_id'], $item['cantidad']);
            }

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Venta procesada correctamente',
                'factura_id' => $factura_id,
                'numero_factura' => $numero_factura,
                'total' => $subtotal
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al procesar la venta',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    public static function buscarFacturasAPI()
    {
        try {
            $sql = "SELECT f.*, c.cliente_nombre, c.cliente_nit 
                    FROM facturas f 
                    INNER JOIN clientes c ON f.factura_cliente_id = c.cliente_id 
                    WHERE f.factura_situacion = 1 
                    ORDER BY f.factura_fecha DESC";
            
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Facturas obtenidas correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener facturas',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    public static function obtenerFacturaCompletaAPI()
    {
        try {
            $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

            // Obtener cabecera de factura
            $sql_factura = "SELECT f.*, c.cliente_nombre, c.cliente_nit, c.cliente_direccion, c.cliente_telefono
                           FROM facturas f 
                           INNER JOIN clientes c ON f.factura_cliente_id = c.cliente_id 
                           WHERE f.factura_id = $id";
            $factura = self::fetchFirst($sql_factura);

            if (!$factura) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Factura no encontrada'
                ]);
                return;
            }

            // Obtener detalle de factura
            $sql_detalle = "SELECT fd.*, p.producto_nombre 
                           FROM factura_detalle fd 
                           INNER JOIN productos p ON fd.detalle_producto_id = p.producto_id 
                           WHERE fd.detalle_factura_id = $id 
                           ORDER BY fd.detalle_id";
            $detalle = self::fetchArray($sql_detalle);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Factura obtenida correctamente',
                'factura' => $factura,
                'detalle' => $detalle
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener la factura',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    public static function anularFacturaAPI()
    {
        try {
            $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

            // Obtener detalle para devolver stock
            $sql_detalle = "SELECT * FROM factura_detalle WHERE detalle_factura_id = $id";
            $detalles = self::fetchArray($sql_detalle);

            // Devolver stock a los productos
            foreach ($detalles as $detalle) {
                $sql_devolver = "UPDATE productos 
                               SET producto_stock = producto_stock + " . $detalle['detalle_cantidad'] . " 
                               WHERE producto_id = " . $detalle['detalle_producto_id'];
                self::SQL($sql_devolver);
            }

            // Anular factura
            $sql_anular = "UPDATE facturas SET factura_situacion = 0 WHERE factura_id = $id";
            self::SQL($sql_anular);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Factura anulada correctamente y stock devuelto'
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al anular la factura',
                'detalle' => $e->getMessage(),
            ]);
        }
    }
}