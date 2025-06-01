<?php

namespace Model;

class Clientes extends ActiveRecord {

    public static $tabla = 'clientes';
    public static $columnasDB = [
        'cliente_nombre',
        'cliente_nit',
        'cliente_direccion',
        'cliente_telefono',
        'cliente_situacion'
    ];

    public static $idTabla = 'cliente_id';
    public $cliente_id;
    public $cliente_nombre;
    public $cliente_nit;
    public $cliente_direccion;
    public $cliente_telefono;
    public $cliente_situacion;

    public function __construct($args = []){
        $this->cliente_id = $args['cliente_id'] ?? null;
        $this->cliente_nombre = $args['cliente_nombre'] ?? '';
        $this->cliente_nit = $args['cliente_nit'] ?? '';
        $this->cliente_direccion = $args['cliente_direccion'] ?? '';
        $this->cliente_telefono = $args['cliente_telefono'] ?? '';
        $this->cliente_situacion = $args['cliente_situacion'] ?? 1;
    }


    // Obtener clientes activos
    public static function obtenerActivos() {
        $sql = "SELECT * FROM clientes WHERE cliente_situacion = 1 ORDER BY cliente_nombre";
        return self::fetchArray($sql);
    }

    // Buscar cliente por NIT
    public static function buscarPorNit($nit) {
        $sql = "SELECT * FROM clientes WHERE cliente_nit = '$nit' AND cliente_situacion = 1";
        return self::fetchFirst($sql);
    }
}