<?php

namespace Model;

class VentaDetalles extends ActiveRecord {

    public static $tabla = 'venta_detalles';
    public static $columnasDB = [
        'detalle_venta_id',
        'detalle_producto_id',
        'detalle_cantidad',
        'detalle_precio_unitario',
        'detalle_subtotal'
    ];

    public static $idTabla = 'detalle_id';
    public $detalle_id;
    public $detalle_venta_id;
    public $detalle_producto_id;
    public $detalle_cantidad;
    public $detalle_precio_unitario;
    public $detalle_subtotal;

    public function __construct($args = []){
        $this->detalle_id = $args['detalle_id'] ?? null;
        $this->detalle_venta_id = $args['detalle_venta_id'] ?? 0;
        $this->detalle_producto_id = $args['detalle_producto_id'] ?? 0;
        $this->detalle_cantidad = $args['detalle_cantidad'] ?? 0;
        $this->detalle_precio_unitario = $args['detalle_precio_unitario'] ?? 0;
        $this->detalle_subtotal = $args['detalle_subtotal'] ?? 0;
    }

    public static function ObtenerDetallesPorVenta($venta_id){
        $sql = "SELECT vd.*, p.producto_nombre, p.producto_descripcion 
                FROM venta_detalles vd 
                INNER JOIN productos p ON vd.detalle_producto_id = p.producto_id 
                WHERE vd.detalle_venta_id = $venta_id 
                ORDER BY p.producto_nombre";
        return self::fetchArray($sql);
    }

    public static function EliminarDetallesPorVenta($venta_id){
        $sql = "DELETE FROM venta_detalles WHERE detalle_venta_id = $venta_id";
        return self::SQL($sql);
    }

    public static function ObtenerProductosMasVendidos($limite = 10){
        $sql = "SELECT p.producto_nombre, SUM(vd.detalle_cantidad) as total_vendido, 
                       SUM(vd.detalle_subtotal) as total_ingresos
                FROM venta_detalles vd 
                INNER JOIN productos p ON vd.detalle_producto_id = p.producto_id 
                INNER JOIN ventas v ON vd.detalle_venta_id = v.venta_id 
                WHERE v.venta_situacion = 1
                GROUP BY p.producto_id, p.producto_nombre 
                ORDER BY total_vendido DESC 
                LIMIT $limite";
        return self::fetchArray($sql);
    }

    public static function ObtenerVentasPorProducto($producto_id){
        $sql = "SELECT vd.*, v.venta_fecha, c.cliente_nombres, c.cliente_apellidos
                FROM venta_detalles vd 
                INNER JOIN ventas v ON vd.detalle_venta_id = v.venta_id 
                INNER JOIN clientes c ON v.venta_cliente_id = c.cliente_id 
                WHERE vd.detalle_producto_id = $producto_id AND v.venta_situacion = 1
                ORDER BY v.venta_fecha DESC";
        return self::fetchArray($sql);
    }

    public static function CalcularTotalDetalle($venta_id){
        $sql = "SELECT SUM(detalle_subtotal) as total FROM venta_detalles WHERE detalle_venta_id = $venta_id";
        $resultado = self::fetchFirst($sql);
        return $resultado['total'] ?? 0;
    }

    public static function RestaurarStockDeVenta($venta_id){
        $detalles = self::fetchArray("SELECT detalle_producto_id, detalle_cantidad 
                                     FROM venta_detalles WHERE detalle_venta_id = $venta_id");
        
        foreach($detalles as $detalle){
            self::SQL("UPDATE productos SET producto_cantidad = producto_cantidad + " . 
                     $detalle['detalle_cantidad'] . " WHERE producto_id = " . $detalle['detalle_producto_id']);
        }
        return true;
    }
}