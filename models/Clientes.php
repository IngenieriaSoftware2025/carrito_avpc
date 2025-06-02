<?php

namespace Model;

class Clientes extends ActiveRecord
{
    public static $tabla = 'clientes';
    public static $columnasDB = [
        'cliente_nombre',
        'cliente_email',
        'cliente_telefono',
        'cliente_direccion',
        'cliente_fecha_registro',
        'cliente_situacion'
    ];

    public static $idTabla = 'id_cliente';
    public $id_cliente;
    public $cliente_nombre;
    public $cliente_email;
    public $cliente_telefono;
    public $cliente_direccion;
    public $cliente_fecha_registro;
    public $cliente_situacion;

    public function __construct($args = [])
    {
        $this->id_cliente = $args['id_cliente'] ?? null;
        $this->cliente_nombre = $args['cliente_nombre'] ?? '';
        $this->cliente_email = $args['cliente_email'] ?? '';
        $this->cliente_telefono = $args['cliente_telefono'] ?? '';
        $this->cliente_direccion = $args['cliente_direccion'] ?? '';
        $this->cliente_fecha_registro = $args['cliente_fecha_registro'] ?? date('Y-m-d H:i');
        $this->cliente_situacion = $args['cliente_situacion'] ?? 1;
    }

    public static function EliminarCliente($id_eliminado)
    {
        $sql = "UPDATE clientes SET cliente_situacion = 0 WHERE id_cliente = $id_eliminado";
        return self::SQL($sql);
    }
}
