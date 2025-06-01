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
}