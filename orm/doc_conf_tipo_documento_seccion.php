<?php
namespace gamboamartin\documento\models;
use base\orm\_modelo_parent;
use base\orm\_modelo_parent_sin_codigo;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class doc_conf_tipo_documento_seccion extends _modelo_parent_sin_codigo {
    public function __construct(PDO $link, array $childrens = array()){
        $tabla = 'doc_conf_tipo_documento_seccion';
        $columnas = array($tabla=>false, 'doc_tipo_documento'=>$tabla, 'adm_seccion'=>$tabla);
        $campos_obligatorios = array('doc_tipo_documento_id','adm_seccion_id');

        $columnas_extra = array();

        $atributos_criticos =  array('doc_tipo_documento_id','adm_seccion_id');

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, childrens: $childrens,
            atributos_criticos: $atributos_criticos);

        $this->NAMESPACE = __NAMESPACE__;

        $this->etiqueta = 'Configuracion de tipo de documento por seccion';


    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        $this->registro = $this->inicializa_campos($this->registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al inicializar campo base', data: $this->registro);
        }

        $r_alta_bd = parent::alta_bd(keys_integra_ds: $keys_integra_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar configuracion de tipo de documento por seccion',
                data: $r_alta_bd);
        }

        return $r_alta_bd;
    }

    protected function inicializa_campos(array $registros): array
    {
        $registros['codigo'] = $this->get_codigo_aleatorio();
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error generar codigo', data: $registros);
        }

        if(!isset($registros['descripcion'])){
            $descripcion = trim($registros['doc_tipo_documento_id']);
            $descripcion .= '-'.trim($registros['adm_seccion_id']);
            $registros['descripcion'] = $descripcion;
        }

        return $registros;
    }

}