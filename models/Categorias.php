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

}