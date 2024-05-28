<?php
namespace gamboamartin\documento\models;
use base\orm\modelo;
use config\generales;
use gamboamartin\errores\errores;
use gamboamartin\plugins\files;
use PDO;
use stdClass;


class doc_documento_etapa extends modelo{

    public function __construct(PDO $link){
        $tabla = 'doc_documento_etapa';
        $columnas = array($tabla=>false, 'doc_documento'=>$tabla, 'pr_etapa_proceso'=>$tabla);
        $campos_obligatorios = array('doc_documento_id', 'pr_etapa_proceso_id');
        parent::__construct(link: $link,tabla:  $tabla,campos_obligatorios: $campos_obligatorios, columnas:  $columnas);
        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Documento Etapa';
    }

}