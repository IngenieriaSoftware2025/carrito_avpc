<?php

namespace Model;

class Productos extends ActiveRecord {

    public static $tabla = 'productos';
    public static $columnasDB = [
        'producto_nombre',
        'producto_descripcion',
        'producto_precio',
        'producto_cantidad',
        'producto_situacion'
    ];

    public static $idTabla = 'producto_id';
    public $producto_id;
    public $producto_nombre;
    public $producto_descripcion;
    public $producto_precio;
    public $producto_cantidad;
    public $producto_situacion;

    public function __construct($args = []){
        $this->producto_id = $args['producto_id'] ?? null;
        $this->producto_nombre = $args['producto_nombre'] ?? '';
        $this->producto_descripcion = $args['producto_descripcion'] ?? '';
        $this->producto_precio = $args['producto_precio'] ?? 0;
        $this->producto_cantidad = $args['producto_cantidad'] ?? 0;
        $this->producto_situacion = $args['producto_situacion'] ?? 1;
    }

    public static function EliminarProducto($id){
        $sql = "UPDATE productos SET producto_situacion = 0 WHERE producto_id = $id";
        return self::SQL($sql);
    }

    public static function ValidarStockProducto($id){
        $sql = "SELECT producto_cantidad FROM productos WHERE producto_id = $id AND producto_situacion = 1";
        $resultado = self::fetchFirst($sql);
        return $resultado['producto_cantidad'] ?? 0;
    }

    public static function ActualizarStockProducto($id, $cantidad_vendida){
        $sql = "UPDATE productos SET producto_cantidad = producto_cantidad - $cantidad_vendida WHERE producto_id = $id";
        return self::SQL($sql);
    }

    public static function ObtenerProductosDisponibles(){
        $sql = "SELECT * FROM productos WHERE producto_cantidad > 0 AND producto_situacion = 1 ORDER BY producto_nombre";
        return self::fetchArray($sql);
    }

    public static function ObtenerProductosActivos(){
        $sql = "SELECT * FROM productos WHERE producto_situacion = 1 ORDER BY producto_nombre";
        return self::fetchArray($sql);
    }

    public static function BuscarPorNombre($nombre){
        $sql = "SELECT * FROM productos WHERE LOWER(producto_nombre) LIKE " . self::$db->quote('%' . strtolower($nombre) . '%') . " AND producto_situacion = 1";
        return self::fetchArray($sql);
    }

    public static function RestaurarStockProducto($id, $cantidad){
        $sql = "UPDATE productos SET producto_cantidad = producto_cantidad + $cantidad WHERE producto_id = $id";
        return self::SQL($sql);
    }
}