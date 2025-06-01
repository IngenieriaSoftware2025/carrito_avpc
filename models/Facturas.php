<?php

namespace Model;

class Facturas extends ActiveRecord {

    public static $tabla = 'facturas';
    public static $columnasDB = [
        'factura_numero',
        'factura_cliente_id',
        'factura_fecha',
        'factura_subtotal',
        'factura_total',
        'factura_situacion'
    ];

    public static $idTabla = 'factura_id';
    public $factura_id;
    public $factura_numero;
    public $factura_cliente_id;
    public $factura_fecha;
    public $factura_subtotal;
    public $factura_total;
    public $factura_situacion;

    public function __construct($args = []){
        $this->factura_id = $args['factura_id'] ?? null;
        $this->factura_numero = $args['factura_numero'] ?? '';
        $this->factura_cliente_id = $args['factura_cliente_id'] ?? null;
        $this->factura_fecha = $args['factura_fecha'] ?? null;
        $this->factura_subtotal = $args['factura_subtotal'] ?? 0.00;
        $this->factura_total = $args['factura_total'] ?? 0.00;
        $this->factura_situacion = $args['factura_situacion'] ?? 1;
    }




     // Generar siguiente número de factura
    public static function generarNumero() {
        $sql = "SELECT MAX(factura_id) as ultimo FROM facturas";
        $resultado = self::fetchFirst($sql);
        $siguiente = ($resultado['ultimo'] ?? 0) + 1;
        return 'F-' . str_pad($siguiente, 6, '0', STR_PAD_LEFT);
    }

    // Obtener facturas con información del cliente
    public static function obtenerConCliente() {
        $sql = "SELECT f.*, c.cliente_nombre, c.cliente_nit 
                FROM facturas f 
                INNER JOIN clientes c ON f.factura_cliente_id = c.cliente_id 
                WHERE f.factura_situacion = 1 
                ORDER BY f.factura_fecha DESC";
        return self::fetchArray($sql);
    }

    // Obtener factura completa (con detalle)
    public static function obtenerCompleta($id) {
        // Obtener cabecera de factura
        $sql = "SELECT f.*, c.cliente_nombre, c.cliente_nit, c.cliente_direccion, c.cliente_telefono
                FROM facturas f 
                INNER JOIN clientes c ON f.factura_cliente_id = c.cliente_id 
                WHERE f.factura_id = $id";
        return self::fetchFirst($sql);
    }
}