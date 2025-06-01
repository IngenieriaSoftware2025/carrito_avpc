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
    
    

// Obtener productos con información de categoría
    public static function obtenerConCategoria() {
        $sql = "SELECT p.*, c.categoria_nombre 
                FROM productos p 
                INNER JOIN categorias c ON p.producto_categoria_id = c.categoria_id 
                WHERE p.producto_situacion = 1 
                ORDER BY c.categoria_nombre, p.producto_nombre";
        return self::fetchArray($sql);
    }

    // Obtener productos disponibles (con stock)
    public static function obtenerDisponibles() {
        $sql = "SELECT p.*, c.categoria_nombre 
                FROM productos p 
                INNER JOIN categorias c ON p.producto_categoria_id = c.categoria_id 
                WHERE p.producto_situacion = 1 AND p.producto_stock > 0
                ORDER BY c.categoria_nombre, p.producto_nombre";
        return self::fetchArray($sql);
    }

    // Reducir stock después de una venta
    public static function reducirStock($id, $cantidad) {
        $sql = "UPDATE productos 
                SET producto_stock = producto_stock - $cantidad 
                WHERE producto_id = $id AND producto_stock >= $cantidad";
        return self::SQL($sql);
    }

    // Eliminar producto (cambiar situación)
    public static function eliminarProducto($id) {
        $sql = "UPDATE productos SET producto_situacion = 0 WHERE producto_id = $id";
        return self::SQL($sql);
    }




}