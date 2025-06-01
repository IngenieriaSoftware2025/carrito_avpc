<?php

namespace Model;

class Productos extends ActiveRecord {

    public static $tabla = 'productos';
    public static $columnasDB = [
        'producto_nombre',
        'producto_precio',
        'producto_stock',
        'producto_categoria_id',
        'producto_situacion'
    ];

    public static $idTabla = 'producto_id';
    public $producto_id;
    public $producto_nombre;
    public $producto_precio;
    public $producto_stock;
    public $producto_categoria_id;
    public $producto_situacion;

    public function __construct($args = []){
        $this->producto_id = $args['producto_id'] ?? null;
        $this->producto_nombre = $args['producto_nombre'] ?? '';
        $this->producto_precio = $args['producto_precio'] ?? 0.00;
        $this->producto_stock = $args['producto_stock'] ?? 0;
        $this->producto_categoria_id = $args['producto_categoria_id'] ?? 1;
        $this->producto_situacion = $args['producto_situacion'] ?? 1;
    }    

}