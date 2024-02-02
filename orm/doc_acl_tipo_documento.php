<?php
namespace gamboamartin\documento\models;
use base\orm\_defaults;
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
        $tabla = 'doc_acl_tipo_documento';
        $columnas = array($tabla=>false, 'doc_tipo_documento'=>$tabla, 'adm_grupo'=>$tabla);
        $campos_obligatorios = array('doc_tipo_documento_id', 'adm_grupo_id');
        parent::__construct(link: $link,tabla:  $tabla,campos_obligatorios: $campos_obligatorios, columnas:  $columnas);

        $this->NAMESPACE = __NAMESPACE__;

        $this->etiqueta = 'ACL Por Doc';

    }

    public function alta_bd(): array|stdClass
    {
        $codigo = $this->registro['doc_tipo_documento_id'].'.'.$this->registro['adm_grupo_id'];
        if(!isset($this->registro['codigo'])){
            $this->registro['codigo'] = $codigo;
        }

        $descripcion = $this->registro['doc_tipo_documento_id'].'.'.$this->registro['adm_grupo_id'];
        if(!isset($this->registro['descripcion'])){
            $this->registro['descripcion'] = $descripcion;
        }

        $r_alta_bd = parent::alta_bd();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar extension permitida',data:  $r_alta_bd);
        }
        return $r_alta_bd;

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
        $filtro['doc_acl_tipo_documento.status'] = 'activo';

        $existe = $this->existe(filtro: $filtro);
        if(errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener acl', data: $existe);
        }

        return $existe;
    }
}