<?php

namespace Model;

class Ventas extends ActiveRecord {

    public static $tabla = 'ventas';
    public static $columnasDB = [
        'venta_cliente_id',
        'venta_fecha',
        'venta_total',
        'venta_situacion'
    ];

    public static $idTabla = 'venta_id';
    public $venta_id;
    public $venta_cliente_id;
    public $venta_fecha;
    public $venta_total;
    public $venta_situacion;

    public function __construct($args = []){
        $this->venta_id = $args['venta_id'] ?? null;
        $this->venta_cliente_id = $args['venta_cliente_id'] ?? 0;
        $this->venta_fecha = $args['venta_fecha'] ?? date('Y-m-d H:i:s');
        $this->venta_total = $args['venta_total'] ?? 0.00;
        $this->venta_situacion = $args['venta_situacion'] ?? 1;
    }

    public function validar() {
        self::$alertas = [];

        if(!$this->venta_cliente_id || $this->venta_cliente_id <= 0) {
            self::setAlerta('error', 'Debe seleccionar un cliente');
        }

        if(!$this->venta_total || $this->venta_total <= 0) {
            self::setAlerta('error', 'El total de la venta debe ser mayor a 0');
        }

        return self::$alertas;
    }

    // Calcular total basado en los detalles
    public function calcularTotal() {
        $sql = "SELECT SUM(detalle_subtotal) as total FROM detalle_ventas WHERE detalle_venta_id = " . $this->venta_id . " AND detalle_situacion = 1";
        $resultado = self::fetchFirst($sql);
        $this->venta_total = $resultado['total'] ?? 0.00;
        return $this->venta_total;
    }

    // Obtener ventas con información del cliente
    public static function obtenerVentasConCliente() {
        $sql = "SELECT v.*, 
                       c.cliente_nombre, 
                       c.cliente_apellido, 
                       c.cliente_nit
                FROM ventas v 
                INNER JOIN clientes c ON v.venta_cliente_id = c.cliente_id 
                WHERE v.venta_situacion = 1 
                ORDER BY v.venta_fecha DESC";
        
        return self::fetchArray($sql);
    }

    // Obtener una venta específica con cliente
    public static function obtenerVentaConCliente($venta_id) {
        $sql = "SELECT v.*, 
                       c.cliente_nombre, 
                       c.cliente_apellido, 
                       c.cliente_nit,
                       c.cliente_email,
                       c.cliente_telefono,
                       c.cliente_direccion
                FROM ventas v 
                INNER JOIN clientes c ON v.venta_cliente_id = c.cliente_id 
                WHERE v.venta_id = " . self::$db->quote($venta_id) . " 
                AND v.venta_situacion = 1";
        
        return self::fetchFirst($sql);
    }

    // Eliminar venta (cambiar situación a 0)
    public static function eliminarVenta($venta_id) {
        try {
            // Primero devolver el stock de todos los productos
            $sql_productos = "SELECT dv.detalle_producto_id, dv.detalle_cantidad 
                             FROM detalle_ventas dv 
                             WHERE dv.detalle_venta_id = " . self::$db->quote($venta_id) . " 
                             AND dv.detalle_situacion = 1";
            
            $productos = self::fetchArray($sql_productos);
            
            foreach($productos as $producto) {
                $sql_stock = "UPDATE productos 
                             SET producto_stock = producto_stock + " . $producto['detalle_cantidad'] . " 
                             WHERE producto_id = " . $producto['detalle_producto_id'];
                self::SQL($sql_stock);
            }
            
            // Luego eliminar los detalles (cambiar situación)
            $sql_detalle = "UPDATE detalle_ventas SET detalle_situacion = 0 WHERE detalle_venta_id = " . self::$db->quote($venta_id);
            self::SQL($sql_detalle);
            
            // Finalmente eliminar la venta (cambiar situación)
            $sql_venta = "UPDATE ventas SET venta_situacion = 0 WHERE venta_id = " . self::$db->quote($venta_id);
            return self::SQL($sql_venta);
            
        } catch (Exception $e) {
            throw $e;
        }
    }
}