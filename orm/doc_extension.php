<?php
namespace models;
use base\orm\modelo;
use gamboamartin\errores\errores;
use PDO;
use stdClass;


class doc_extension extends modelo{ //FINALIZADAS
    /**
     * DEBUG INI
     * accion constructor.
     * @param PDO $link
     */
    public function __construct(PDO $link){
        $tabla = __CLASS__;
        $columnas = array($tabla=>false);
        $campos_obligatorios = array();
        parent::__construct(link: $link,tabla:  $tabla,campos_obligatorios: $campos_obligatorios, columnas:  $columnas);
    }

    public function doc_extension_id(string $extension){
        if($extension === ''){
            return $this->error->error(mensaje: 'Error extension no puede venir vacia', data: $extension);
        }

        $filtro['doc_extension.descripcion'] = $extension;

        $r_extension = $this->filtro_and(filtro: $filtro);
        if(errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener extensiones', data: $r_extension);
        }
        if($r_extension->n_registros === 0){
            return $this->error->error(mensaje: 'Error no existe la extension', data: $extension);
        }

        return $r_extension->registros[0]['doc_extension_id'];
    }
}