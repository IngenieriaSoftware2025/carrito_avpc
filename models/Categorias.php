<?php

namespace Model;

class Categorias extends ActiveRecord {

    public static $tabla = 'categorias';
    public static $columnasDB = [
        'categoria_nombre',
        'categoria_situacion'
    ];

    public static $idTabla = 'categoria_id';
    public $categoria_id;
    public $categoria_nombre;
    public $categoria_situacion;

    public function __construct($args = []){
        $this->categoria_id = $args['categoria_id'] ?? null;
        $this->categoria_nombre = $args['categoria_nombre'] ?? '';
        $this->categoria_situacion = $args['categoria_situacion'] ?? 1;
    }

    // Obtener categorías activas
    public static function obtenerActivas() {
        $sql = "SELECT * FROM categorias WHERE categoria_situacion = 1 ORDER BY categoria_nombre";
        return self::fetchArray($sql);
    }


}