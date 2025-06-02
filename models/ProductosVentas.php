<?php

namespace Model;

class ProductosVentas extends ActiveRecord {

    public static $tabla = 'productos';
    public static $columnasDB = [
        'producto_nombre',
        'producto_precio',
        'producto_stock',
        'producto_descripcion',
        'producto_situacion'
    ];

    public static $idTabla = 'producto_id';
    public $producto_id;
    public $producto_nombre;
    public $producto_precio;
    public $producto_stock;
    public $producto_descripcion;
    public $producto_situacion;

    public function __construct($args = []){
        $this->producto_id = $args['producto_id'] ?? null;
        $this->producto_nombre = $args['producto_nombre'] ?? '';
        $this->producto_precio = $args['producto_precio'] ?? 0.00;
        $this->producto_stock = $args['producto_stock'] ?? 0;
        $this->producto_descripcion = $args['producto_descripcion'] ?? '';
        $this->producto_situacion = $args['producto_situacion'] ?? 1;
    }

    public function validar() {
        self::$alertas = [];

        if(!$this->producto_nombre) {
            self::setAlerta('error', 'El nombre del producto es obligatorio');
        }
        if(strlen($this->producto_nombre) < 2) {
            self::setAlerta('error', 'El nombre debe tener al menos 2 caracteres');
        }

        if(!$this->producto_precio || $this->producto_precio <= 0) {
            self::setAlerta('error', 'El precio debe ser mayor a 0');
        }

        if($this->producto_stock < 0) {
            self::setAlerta('error', 'El stock no puede ser negativo');
        }

        return self::$alertas;
    }

    // Verificar si hay stock disponible
    public function tieneStock($cantidad = 1) {
        return $this->producto_stock >= $cantidad;
    }

    // Descontar stock
    public function descontarStock($cantidad) {
        if($this->tieneStock($cantidad)) {
            $this->producto_stock -= $cantidad;
            return $this->actualizar();
        }
        return false;
    }

    // Agregar stock (para devoluciones)
    public function agregarStock($cantidad) {
        $this->producto_stock += $cantidad;
        return $this->actualizar();
    }

    // Obtener productos disponibles (con stock > 0)
    public static function obtenerDisponibles() {
        $query = "SELECT * FROM " . self::$tabla . " WHERE producto_situacion = 1 AND producto_stock > 0 ORDER BY producto_nombre";
        return self::fetchArray($query);
    }

    // Verificar si el producto ya existe
    public static function existeProducto($nombre, $producto_id = null) {
        $query = "SELECT COUNT(*) as total FROM " . self::$tabla . " WHERE producto_nombre = " . self::$db->quote($nombre) . " AND producto_situacion = 1";
        
        if($producto_id) {
            $query .= " AND producto_id != " . self::$db->quote($producto_id);
        }
        
        $resultado = self::fetchFirst($query);
        return $resultado['total'] > 0;
    }
}