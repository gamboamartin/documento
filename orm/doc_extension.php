<?php
namespace gamboamartin\documento\models;
use base\orm\modelo;
use gamboamartin\errores\errores;
use PDO;


class doc_extension extends modelo{ //FINALIZADAS
    /**
     * DEBUG INI
     * accion constructor.
     * @param PDO $link
     */
    public function __construct(PDO $link){
        $tabla = 'doc_extension';
        $columnas = array($tabla=>false);
        $campos_obligatorios = array();
        $atributos_criticos[] = 'es_imagen';
        parent::__construct(link: $link,tabla:  $tabla,campos_obligatorios: $campos_obligatorios,
            columnas:  $columnas, atributos_criticos: $atributos_criticos);
        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Extension';

    }




    /**
     *
     * Esta funcion obtienen de la id de la extension
     * @param string $extension Descripcion de extension de documento a insertar
     * @return array|int
     * @version 9.0.0
     */
    final public function doc_extension_id(string $extension): int|array
    {

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

        return (int)$r_extension->registros[0]['doc_extension_id'];
    }
}