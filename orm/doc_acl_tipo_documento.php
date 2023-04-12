<?php
namespace gamboamartin\documento\models;
use base\orm\_defaults;
use base\orm\modelo;
use gamboamartin\errores\errores;
use PDO;


class doc_acl_tipo_documento extends modelo{ //FINALIZADAS
    /**
     * DEBUG INI
     * accion constructor.
     * @param PDO $link
     */
    public function __construct(PDO $link){
        $tabla = 'doc_acl_tipo_documento';
        $columnas = array($tabla=>false, 'doc_tipo_documento'=>$tabla, 'adm_grupo'=>$tabla);
        $campos_obligatorios = array('doc_tipo_documento_id', 'adm_grupo_id');
        parent::__construct(link: $link,tabla:  $tabla,campos_obligatorios: $campos_obligatorios, columnas:  $columnas);

        $this->NAMESPACE = __NAMESPACE__;

        $this->etiqueta = 'ACL Por Doc';


        if(!isset($_SESSION['init'][$tabla])) {

            unset($_SESSION['init']['doc_tipo_documento']);
            unset($_SESSION['init']['adm_grupo']);

            new doc_tipo_documento(link: $this->link);
            new adm_grupo(link: $this->link);

            $catalago = array();
            $catalago[] = array('id'=>1,'doc_tipo_documento_id'=>1,'adm_grupo_id'=>2);
            $catalago[] = array('id'=>2,'doc_tipo_documento_id'=>2,'adm_grupo_id'=>2);
            $catalago[] = array('id'=>3,'doc_tipo_documento_id'=>3,'adm_grupo_id'=>2);
            $catalago[] = array('id'=>4,'doc_tipo_documento_id'=>4,'adm_grupo_id'=>2);
            $catalago[] = array('id'=>5,'doc_tipo_documento_id'=>5,'adm_grupo_id'=>2);
            $catalago[] = array('id'=>6,'doc_tipo_documento_id'=>6,'adm_grupo_id'=>2);
            $catalago[] = array('id'=>7,'doc_tipo_documento_id'=>7,'adm_grupo_id'=>2);

            $r_alta_bd = (new _defaults())->alta_defaults(catalogo: $catalago, entidad: $this);
            if (errores::$error) {
                $error = $this->error->error(mensaje: 'Error al insertar', data: $r_alta_bd);
                print_r($error);
                exit;
            }
            $_SESSION['init'][$tabla] = true;
        }


    }

    /**
     *
     * Funcion que verifica si existe un acl_tipo_documento conforme al grupo_id y el tipo_documento_id
     * @param int $grupo_id Grupo de usuario
     * @param int $tipo_documento_id Tipo de documento en base de datos no relacionado a la extension,
     * mas bien al objeto del tipo del documento ej INE
     * @return array|bool
     * @version 0.9.1
     */
    final public function tipo_documento_permiso(int $grupo_id, int $tipo_documento_id): bool|array
    {

        if($grupo_id <= 0){
            return $this->error->error(mensaje: 'Error grupo id no puede ser menor a 1',data:  $grupo_id);
        }
        if($tipo_documento_id <= 0){
            return $this->error->error(mensaje: 'Error tipo documento id no puede ser menor a 1',
                data: $tipo_documento_id);
        }

        $filtro['doc_tipo_documento.id'] = $tipo_documento_id;
        $filtro['adm_grupo.id'] = $grupo_id;

        $existe = $this->existe(filtro: $filtro);
        if(errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener acl', data: $existe);
        }

        return $existe;
    }
}