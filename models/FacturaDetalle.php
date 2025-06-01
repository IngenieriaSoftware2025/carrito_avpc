<?php

namespace Model;

class FacturaDetalle extends ActiveRecord {

    public static $tabla = 'factura_detalle';
    public static $columnasDB = [
        'detalle_factura_id',
        'detalle_producto_id',
        'detalle_cantidad',
        'detalle_precio_unitario',
        'detalle_subtotal'
    ];

    public static $idTabla = 'detalle_id';
    public $detalle_id;
    public $detalle_factura_id;
    public $detalle_producto_id;
    public $detalle_cantidad;
    public $detalle_precio_unitario;
    public $detalle_subtotal;

    public function __construct($args = []){
        $this->detalle_id = $args['detalle_id'] ?? null;
        $this->detalle_factura_id = $args['detalle_factura_id'] ?? null;
        $this->detalle_producto_id = $args['detalle_producto_id'] ?? null;
        $this->detalle_cantidad = $args['detalle_cantidad'] ?? 1;
        $this->detalle_precio_unitario = $args['detalle_precio_unitario'] ?? 0.00;
        $this->detalle_subtotal = $args['detalle_subtotal'] ?? 0.00;
    }





     public static function obtenerPorFactura($factura_id) {
        $sql = "SELECT fd.*, p.producto_nombre 
                FROM factura_detalle fd 
                INNER JOIN productos p ON fd.detalle_producto_id = p.producto_id 
                WHERE fd.detalle_factura_id = $factura_id 
                ORDER BY fd.detalle_id";
        return self::fetchArray($sql);
    }

    // Eliminar todos los detalles de una factura
    public static function eliminarPorFactura($factura_id) {
        $sql = "DELETE FROM factura_detalle WHERE detalle_factura_id = $factura_id";
        return self::SQL($sql);
    }
}