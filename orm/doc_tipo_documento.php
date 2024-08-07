<?php
namespace gamboamartin\documento\models;
use base\orm\_modelo_parent;
use base\orm\modelo;
use gamboamartin\errores\errores;
use PDO;


class doc_tipo_documento extends _modelo_parent{ //FINALIZADAS
    /**
     * DEBUG INI
     * accion constructor.
     * @param PDO $link
     */
    public function __construct(PDO $link){
        $tabla = 'doc_tipo_documento';
        $columnas = array($tabla=>false);
        $campos_obligatorios = array();

        $doc_documento_etapa = "(SELECT pr_etapa.descripcion FROM pr_etapa 
            LEFT JOIN pr_etapa_proceso ON pr_etapa_proceso.pr_etapa_id = pr_etapa.id 
            LEFT JOIN doc_documento_etapa ON doc_documento_etapa.pr_etapa_proceso_id = pr_etapa_proceso.id 
            LEFT JOIN doc_documento ON doc_documento_etapa.doc_documento_id = doc_documento.id 
			WHERE doc_documento.doc_tipo_documento_id = doc_tipo_documento.id ORDER BY doc_documento_etapa.id DESC LIMIT 1)";

        $columnas_extra['doc_etapa'] = "IFNULL($doc_documento_etapa,'SIN ETAPA')";

        $columnas_extra['doc_tipo_documento_n_permisos'] = /** @lang sql */
            "(SELECT COUNT(*) FROM doc_acl_tipo_documento 
            WHERE doc_acl_tipo_documento.doc_tipo_documento_id = doc_tipo_documento.id)";

        $columnas_extra['doc_tipo_documento_n_documentos'] = /** @lang sql */
            "(SELECT COUNT(*) FROM doc_documento 
            WHERE doc_documento.doc_tipo_documento_id = doc_tipo_documento.id)";

        $columnas_extra['doc_tipo_documento_n_extensiones'] = /** @lang sql */
            "(SELECT COUNT(*) FROM doc_extension_permitido 
            WHERE doc_extension_permitido.doc_tipo_documento_id = doc_tipo_documento.id)";

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra);
        $this->NAMESPACE = __NAMESPACE__;

        $this->etiqueta = 'Tipo Documento';

        $this->id_code = true;

    }

    /**
     * Funcion válida si las extensiones sean iguales
     * @param string $extension Descripcion de extension a insertar
     * @param array $extensiones_permitidas Arreglo de extensiones que se identifican como permitidas
     * @return bool|array
     * @version 6.4.0
     */
    private function es_extension_permitida(string $extension, array $extensiones_permitidas): bool|array
    {
        if($extension === '') {
            return $this->error->error(mensaje: 'Error extension no puede venir vacio', data: $extension);
        }

        $es_extension_permitida = false;
        foreach ($extensiones_permitidas as $extension_permitida){
            if($extension_permitida['doc_extension_descripcion'] === $extension){
                $es_extension_permitida = true;
                break;
            }
        }

        return  $es_extension_permitida;
    }

    /**
     *
     * Obtienes todas las extensiones permitidas por tipo de documento
     * @param int $tipo_documento_id Tipo de documento del registro a insertar
     * @return array
     * @version 3.6.0
     */
    private function extensiones_permitidas(int $tipo_documento_id): array
    {
        if($tipo_documento_id<=0){
            return $this->error->error(mensaje: 'Error tipo_documento_id debe ser mayor a 0', data: $tipo_documento_id);
        }
        $filtro['doc_tipo_documento.id'] = $tipo_documento_id;

        $extension_permitido = (new doc_extension_permitido($this->link))->filtro_and(filtro: $filtro);
        if(errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener extensiones', data: $extension_permitido);
        }

        return $extension_permitido->registros;
    }

    /**
     * Devuelve un valor booleano el cual confirma si la extension es valida o invalida
     * @param string $extension Extension del documento a insertar
     * @param int $tipo_documento_id Tipo de documento del registro a insertar
     * @return bool|array
     */
    final public function valida_extension_permitida(string $extension, int $tipo_documento_id): bool|array
    {
        $extensiones_permitidas = $this->extensiones_permitidas(tipo_documento_id: $tipo_documento_id);
        if(errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener extensiones', data: $extensiones_permitidas);
        }

        $es_extension_permitida = $this->es_extension_permitida(extension: $extension,
            extensiones_permitidas: $extensiones_permitidas);
        if(errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener extensiones', data: $extensiones_permitidas);
        }

        return $es_extension_permitida;
    }
}