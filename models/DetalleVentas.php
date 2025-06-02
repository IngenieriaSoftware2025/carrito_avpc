<?php

namespace Model;

class DetalleVentas extends ActiveRecord {

    public static $tabla = 'detalle_ventas';
    public static $columnasDB = [
        'detalle_venta_id',
        'detalle_producto_id',
        'detalle_cantidad',
        'detalle_precio_unitario',
        'detalle_subtotal',
        'detalle_situacion'
    ];

    public static $idTabla = 'detalle_id';
    public $detalle_id;
    public $detalle_venta_id;
    public $detalle_producto_id;
    public $detalle_cantidad;
    public $detalle_precio_unitario;
    public $detalle_subtotal;
    public $detalle_situacion;

    public function __construct($args = []){
        $this->detalle_id = $args['detalle_id'] ?? null;
        $this->detalle_venta_id = $args['detalle_venta_id'] ?? 0;
        $this->detalle_producto_id = $args['detalle_producto_id'] ?? 0;
        $this->detalle_cantidad = $args['detalle_cantidad'] ?? 1;
        $this->detalle_precio_unitario = $args['detalle_precio_unitario'] ?? 0.00;
        $this->detalle_subtotal = $args['detalle_subtotal'] ?? 0.00;
        $this->detalle_situacion = $args['detalle_situacion'] ?? 1;
    }

    public function validar() {
        self::$alertas = [];

        if(!$this->detalle_venta_id || $this->detalle_venta_id <= 0) {
            self::setAlerta('error', 'ID de venta inválido');
        }

        if(!$this->detalle_producto_id || $this->detalle_producto_id <= 0) {
            self::setAlerta('error', 'Debe seleccionar un producto');
        }

        if(!$this->detalle_cantidad || $this->detalle_cantidad <= 0) {
            self::setAlerta('error', 'La cantidad debe ser mayor a 0');
        }

        if(!$this->detalle_precio_unitario || $this->detalle_precio_unitario <= 0) {
            self::setAlerta('error', 'El precio unitario debe ser mayor a 0');
        }

        return self::$alertas;
    }

    // Calcular subtotal automáticamente
    public function calcularSubtotal() {
        $this->detalle_subtotal = $this->detalle_cantidad * $this->detalle_precio_unitario;
        return $this->detalle_subtotal;
    }

    // Obtener detalles de una venta con información del producto
    public static function obtenerDetallesPorVenta($venta_id) {
        $sql = "SELECT dv.*, 
                       p.producto_nombre,
                       p.producto_descripcion
                FROM detalle_ventas dv 
                INNER JOIN productos p ON dv.detalle_producto_id = p.producto_id 
                WHERE dv.detalle_venta_id = " . self::$db->quote($venta_id) . " 
                AND dv.detalle_situacion = 1 
                ORDER BY p.producto_nombre";
        
        return self::fetchArray($sql);
    }

    // Verificar si un producto ya está en la venta
    public static function productoEnVenta($venta_id, $producto_id) {
        $sql = "SELECT COUNT(*) as total FROM " . self::$tabla . " 
                WHERE detalle_venta_id = " . self::$db->quote($venta_id) . " 
                AND detalle_producto_id = " . self::$db->quote($producto_id) . " 
                AND detalle_situacion = 1";
        
        $resultado = self::fetchFirst($sql);
        return $resultado['total'] > 0;
    }

    // Obtener un detalle específico
    public static function obtenerDetalleProducto($venta_id, $producto_id) {
        $sql = "SELECT * FROM " . self::$tabla . " 
                WHERE detalle_venta_id = " . self::$db->quote($venta_id) . " 
                AND detalle_producto_id = " . self::$db->quote($producto_id) . " 
                AND detalle_situacion = 1";
        
        return self::fetchFirst($sql);
    }

    // Eliminar detalle específico y devolver stock
    public static function eliminarDetalle($detalle_id) {
        try {
            // Obtener información del detalle para devolver stock
            $detalle = self::find($detalle_id);
            if($detalle) {
                // Devolver stock al producto
                $sql_stock = "UPDATE productos 
                             SET producto_stock = producto_stock + " . $detalle->detalle_cantidad . " 
                             WHERE producto_id = " . $detalle->detalle_producto_id;
                self::SQL($sql_stock);
                
                // Eliminar detalle (cambiar situación)
                $sql_detalle = "UPDATE detalle_ventas SET detalle_situacion = 0 WHERE detalle_id = " . self::$db->quote($detalle_id);
                return self::SQL($sql_detalle);
            }
            return false;
        } catch (Exception $e) {
            throw $e;
        }
    }

    // Actualizar cantidad en detalle existente
    public function actualizarCantidad($nueva_cantidad) {
        $diferencia = $nueva_cantidad - $this->detalle_cantidad;
        
        // Verificar si hay suficiente stock
        $producto = ProductosVentas::find($this->detalle_producto_id);
        if($diferencia > 0 && !$producto->tieneStock($diferencia)) {
            return false;
        }
        
        // Actualizar stock del producto
        if($diferencia > 0) {
            $producto->descontarStock($diferencia);
        } else if($diferencia < 0) {
            $producto->agregarStock(abs($diferencia));
        }
        
        // Actualizar detalle
        $this->detalle_cantidad = $nueva_cantidad;
        $this->calcularSubtotal();
        return $this->actualizar();
    }
}