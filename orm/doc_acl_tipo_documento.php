<?php
namespace models;
use base\orm\modelo;
use gamboamartin\errores\errores;
use PDO;
use stdClass;


class doc_acl_tipo_documento extends modelo{ //FINALIZADAS
    /**
     * DEBUG INI
     * accion constructor.
     * @param PDO $link
     */
    public function __construct(PDO $link){
        $tabla = __CLASS__;
        $columnas = array($tabla=>false, 'doc_tipo_documento'=>$tabla, 'grupo'=>$tabla);
        $campos_obligatorios = array('doc_tipo_documento_id', 'grupo_id');
        parent::__construct(link: $link,tabla:  $tabla,campos_obligatorios: $campos_obligatorios, columnas:  $columnas);
    }

    /**
     * PRUEBA P ORDER P INT
     * @param int $grupo_id
     * @param int $tipo_documento_id
     * @return array|bool
     */
    public function tipo_documento_permiso(int $grupo_id, int $tipo_documento_id): bool|array
    {
        if($grupo_id <= 0){
            return $this->error->error(mensaje: 'Error grupo id no puede ser menor a 1',data:  $grupo_id,
                params: get_defined_vars());
        }
        if($tipo_documento_id <= 0){
            return $this->error->error(mensaje: 'Error tipo documento id no puede ser menor a 1',
                data: $tipo_documento_id);
        }

        $filtro['doc_tipo_documento.id'] = $tipo_documento_id;
        $filtro['grupo.id'] = $grupo_id;

        $existe = $this->existe(filtro: $filtro);
        if(errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener acl', data: $existe);
        }

        return $existe;
    }
}