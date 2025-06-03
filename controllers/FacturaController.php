<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\Ventas;
use Model\VentaDetalles;
use MVC\Router;

class FacturaController extends ActiveRecord
{
    public static function verFactura(Router $router)
    {
        try {
            $id_venta = filter_var($_GET['venta_id'], FILTER_SANITIZE_NUMBER_INT);
            
            if (!$id_venta) {
                throw new Exception('ID de venta no válido');
            }
            
            $venta = Ventas::ObtenerVentaPorId($id_venta);
            $detalles = VentaDetalles::ObtenerDetallesPorVenta($id_venta);
            
            if (!$venta) {
                throw new Exception('Venta no encontrada');
            }

            // Pasar datos a la vista
            $router->render('facturas/ver', [
                'venta' => $venta,
                'detalles' => $detalles,
                'numero_factura' => str_pad($id_venta, 6, '0', STR_PAD_LEFT),
                'fecha_formateada' => date('d/m/Y H:i', strtotime($venta['venta_fecha']))
            ]);

        } catch (Exception $e) {
            // En caso de error, mostrar mensaje
            $router->render('facturas/error', [
                'mensaje' => $e->getMessage()
            ]);
        }
    }
}