<?php
namespace gamboamartin\documento\models;
use base\orm\_modelo_parent_sin_codigo;
use base\orm\modelo;
use config\generales;
use gamboamartin\errores\errores;
use gamboamartin\plugins\files;
use PDO;
use stdClass;


class doc_documento_etapa extends _modelo_parent_sin_codigo{

    public function __construct(PDO $link){
        $tabla = 'doc_documento_etapa';
        $columnas = array($tabla=>false, 'doc_documento'=>$tabla, 'pr_etapa_proceso'=>$tabla);
        $campos_obligatorios = array('doc_documento_id', 'pr_etapa_proceso_id');
        parent::__construct(link: $link,tabla:  $tabla,campos_obligatorios: $campos_obligatorios, columnas:  $columnas);
        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Documento Etapa';
    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        $this->registro = $this->inicializa_campos($this->registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al inicializar campo base', data: $this->registro);
        }

        $horaActual = date("H:i:s");
        $fechaConHora = $this->registro['fecha'] . " " . $horaActual;
        $this->registro['fecha'] = $fechaConHora;

        $r_alta_bd = parent::alta_bd($keys_integra_ds);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar solicitud', data: $r_alta_bd);
        }

        return $r_alta_bd;
    }

    public function inicializa_campos(array $registros): array
    {
        $registros['codigo'] = $this->get_codigo_aleatorio();
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error generar codigo', data: $registros);
        }
        $registros['descripcion'] = $registros['codigo'];

        return $registros;
    }

}