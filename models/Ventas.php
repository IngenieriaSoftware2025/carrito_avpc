<?php

namespace Model;

class Ventas extends ActiveRecord {

    public static $tabla = 'ventas';
    public static $columnasDB = [
        'venta_cliente_id',
        'venta_fecha',
        'venta_subtotal',
        'venta_total',
        'venta_situacion'
    ];

    public static $idTabla = 'venta_id';
    public $venta_id;
    public $venta_cliente_id;
    public $venta_fecha;
    public $venta_subtotal;
    public $venta_total;
    public $venta_situacion;

    public function __construct($args = []){
        $this->venta_id = $args['venta_id'] ?? null;
        $this->venta_cliente_id = $args['venta_cliente_id'] ?? 0;
        $this->venta_fecha = $args['venta_fecha'] ?? '';
        $this->venta_subtotal = $args['venta_subtotal'] ?? 0;
        $this->venta_total = $args['venta_total'] ?? 0;
        $this->venta_situacion = $args['venta_situacion'] ?? 1;
    }

    public static function EliminarVenta($id){
        $sql = "UPDATE ventas SET venta_situacion = 0 WHERE venta_id = $id";
        return self::SQL($sql);
    }

    public static function ObtenerVentasConClientes(){
        $sql = "SELECT v.venta_id, v.venta_fecha, v.venta_subtotal, v.venta_total, 
                       c.cliente_nombres, c.cliente_apellidos, c.cliente_correo
                FROM ventas v 
                INNER JOIN clientes c ON v.venta_cliente_id = c.cliente_id 
                WHERE v.venta_situacion = 1 ORDER BY v.venta_fecha DESC";
        return self::fetchArray($sql);
    }

    public static function ObtenerVentaPorId($id){
        $sql = "SELECT v.*, c.cliente_nombres, c.cliente_apellidos, c.cliente_correo, c.cliente_telefono
                FROM ventas v 
                INNER JOIN clientes c ON v.venta_cliente_id = c.cliente_id 
                WHERE v.venta_id = $id AND v.venta_situacion = 1";
        return self::fetchFirst($sql);
    }

    public static function ObtenerVentasPorCliente($cliente_id){
        $sql = "SELECT * FROM ventas WHERE venta_cliente_id = $cliente_id AND venta_situacion = 1 ORDER BY venta_fecha DESC";
        return self::fetchArray($sql);
    }

    public static function ObtenerVentasPorFecha($fecha_inicio, $fecha_fin){
        $sql = "SELECT v.*, c.cliente_nombres, c.cliente_apellidos 
                FROM ventas v 
                INNER JOIN clientes c ON v.venta_cliente_id = c.cliente_id 
                WHERE v.venta_fecha BETWEEN " . self::$db->quote($fecha_inicio) . " AND " . self::$db->quote($fecha_fin) . "
                AND v.venta_situacion = 1 ORDER BY v.venta_fecha DESC";
        return self::fetchArray($sql);
    }

    public static function ObtenerTotalVentasDelDia($fecha = null){
        if($fecha === null){
            $fecha = date('Y-m-d');
        }
        $sql = "SELECT SUM(venta_total) as total FROM ventas 
                WHERE DATE(venta_fecha) = " . self::$db->quote($fecha) . " AND venta_situacion = 1";
        $resultado = self::fetchFirst($sql);
        return $resultado['total'] ?? 0;
    }
}