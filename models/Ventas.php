<?php

namespace Model;

class Ventas extends ActiveRecord
{
    public static $tabla = 'ventas';
    public static $columnasDB = [
        'id_cliente',
        'fecha_venta',
        'total_venta'
    ];

    public static $idTabla = 'id_venta';
    public $id_venta;
    public $id_cliente;
    public $fecha_venta;
    public $total_venta;

    public function __construct($args = [])
    {
        $this->id_venta = $args['id_venta'] ?? null;
        $this->id_cliente = $args['id_cliente'] ?? null;
        $this->fecha_venta = $args['fecha_venta'] ?? date('Y-m-d H:i');
        $this->total_venta = $args['total_venta'] ?? 0.00;
    }

    public static function EliminarVenta($id_eliminado)
    {
        $sql = "DELETE FROM ventas WHERE id_venta = $id_eliminado";
        return self::SQL($sql);
    }
}

