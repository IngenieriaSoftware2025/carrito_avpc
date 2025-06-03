<?php

namespace Model;

class Clientes extends ActiveRecord {

    public static $tabla = 'clientes';
    public static $columnasDB = [
        'cliente_nombres',
        'cliente_apellidos',
        'cliente_nit',
        'cliente_telefono',
        'cliente_correo',
        'cliente_estado',
        'cliente_fecha',
        'cliente_situacion'
    ];

    public static $idTabla = 'cliente_id';
    public $cliente_id;
    public $cliente_nombres;
    public $cliente_apellidos;
    public $cliente_nit;
    public $cliente_telefono;
    public $cliente_correo;
    public $cliente_estado;
    public $cliente_situacion;
    public $cliente_fecha;

    public function __construct($args = []){
        $this->cliente_id = $args['cliente_id'] ?? null;
        $this->cliente_nombres = $args['cliente_nombres'] ?? '';
        $this->cliente_apellidos = $args['cliente_apellidos'] ?? '';
        $this->cliente_nit = $args['cliente_nit'] ?? 0;
        $this->cliente_telefono = $args['cliente_telefono'] ?? 0;
        $this->cliente_correo = $args['cliente_correo'] ?? '';
        $this->cliente_estado = $args['cliente_estado'] ?? 'A';
        $this->cliente_fecha = $args['cliente_fecha'] ?? '';
        $this->cliente_situacion = $args['cliente_situacion'] ?? 1;
    }

    public static function EliminarCliente($id){
        $sql = "UPDATE clientes SET cliente_situacion = 0 WHERE cliente_id = $id";
        return self::SQL($sql);
    }

    public static function ObtenerClientesActivos(){
        $sql = "SELECT * FROM clientes WHERE cliente_situacion = 1 ORDER BY cliente_nombres";
        return self::fetchArray($sql);
    }

    public static function BuscarPorCorreo($correo){
        $sql = "SELECT * FROM clientes WHERE cliente_correo = " . self::$db->quote($correo) . " AND cliente_situacion = 1";
        return self::fetchFirst($sql);
    }

    public static function BuscarPorNit($nit){
        $sql = "SELECT * FROM clientes WHERE cliente_nit = $nit AND cliente_situacion = 1";
        return self::fetchFirst($sql);
    }
}