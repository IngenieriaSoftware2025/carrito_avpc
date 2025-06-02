<?php

namespace Model;

class Clientes extends ActiveRecord {

    public static $tabla = 'clientes';
    public static $columnasDB = [
        'cliente_nombre',
        'cliente_apellido',
        'cliente_nit',
        'cliente_email',
        'cliente_telefono',
        'cliente_direccion',
        'cliente_situacion'
    ];

    public static $idTabla = 'cliente_id';
    public $cliente_id;
    public $cliente_nombre;
    public $cliente_apellido;
    public $cliente_nit;
    public $cliente_email;
    public $cliente_telefono;
    public $cliente_direccion;
    public $cliente_situacion;

    public function __construct($args = []){
        $this->cliente_id = $args['cliente_id'] ?? null;
        $this->cliente_nombre = $args['cliente_nombre'] ?? '';
        $this->cliente_apellido = $args['cliente_apellido'] ?? '';
        $this->cliente_nit = $args['cliente_nit'] ?? '';
        $this->cliente_email = $args['cliente_email'] ?? '';
        $this->cliente_telefono = $args['cliente_telefono'] ?? '';
        $this->cliente_direccion = $args['cliente_direccion'] ?? '';
        $this->cliente_situacion = $args['cliente_situacion'] ?? 1;
    }

    public function validar() {
        self::$alertas = [];

        if(!$this->cliente_nombre) {
            self::setAlerta('error', 'El nombre del cliente es obligatorio');
        }
        if(strlen($this->cliente_nombre) < 2) {
            self::setAlerta('error', 'El nombre debe tener al menos 2 caracteres');
        }

        if(!$this->cliente_apellido) {
            self::setAlerta('error', 'El apellido del cliente es obligatorio');
        }
        if(strlen($this->cliente_apellido) < 2) {
            self::setAlerta('error', 'El apellido debe tener al menos 2 caracteres');
        }

        if(!$this->cliente_nit) {
            self::setAlerta('error', 'El NIT es obligatorio');
        }

        return self::$alertas;
    }

    // Método para verificar si el NIT ya existe
    public static function existeNit($nit, $cliente_id = null) {
        $query = "SELECT COUNT(*) as total FROM " . self::$tabla . " WHERE cliente_nit = " . self::$db->quote($nit) . " AND cliente_situacion = 1";
        
        if($cliente_id) {
            $query .= " AND cliente_id != " . self::$db->quote($cliente_id);
        }
        
        $resultado = self::fetchFirst($query);
        return $resultado['total'] > 0;
    }

    // Obtener nombre completo del cliente
    public function getNombreCompleto() {
        return $this->cliente_nombre . ' ' . $this->cliente_apellido;
    }
}