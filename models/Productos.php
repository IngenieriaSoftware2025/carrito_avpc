<?php

namespace Model;


class Productos extends ActiveRecord
{
    public static $tabla = 'productos';
    public static $columnasDB = [
        'producto_nombre',
        'producto_precio',
        'producto_stock_disponible',
        'producto_fecha_creacion',
        'producto_situacion'
    ];

    public static $idTabla = 'id_producto';
    public $id_producto;
    public $producto_nombre;
    public $producto_precio;
    public $producto_stock_disponible;
    public $producto_fecha_creacion;
    public $producto_situacion;

    public function __construct($args = [])
    {
        $this->id_producto = $args['id_producto'] ?? null;
        $this->producto_nombre = $args['producto_nombre'] ?? '';
        $this->producto_precio = $args['producto_precio'] ?? 0.00;
        $this->producto_stock_disponible = $args['producto_stock_disponible'] ?? 0;
        $this->producto_fecha_creacion = $args['producto_fecha_creacion'] ?? date('Y-m-d H:i');
        $this->producto_situacion = $args['producto_situacion'] ?? 1;
    }

    public static function EliminarProducto($id_eliminado)
    {
        $sql = "UPDATE productos SET producto_situacion = 0 WHERE id_producto = $id_eliminado";
        return self::SQL($sql);
    }
}