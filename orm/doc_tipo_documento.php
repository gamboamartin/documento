<?php
namespace gamboamartin\documento\models;
use base\orm\_defaults;
use base\orm\modelo;
use gamboamartin\errores\errores;
use PDO;


class doc_tipo_documento extends modelo{ //FINALIZADAS
    /**
     * DEBUG INI
     * accion constructor.
     * @param PDO $link
     */
    public function __construct(PDO $link){
        $tabla = 'doc_tipo_documento';
        $columnas = array($tabla=>false);
        $campos_obligatorios = array();

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

        if(!isset($_SESSION['init'][$tabla])) {

            $catalogo = array();
            $catalogo[] = array('id'=>1,'codigo' => '01', 'descripcion' => 'xml_sin_timbrar');
            $catalogo[] = array('id'=>2,'codigo' => '02', 'descripcion' => 'xml_timbrado');
            $catalogo[] = array('id'=>3,'codigo' => '03', 'descripcion' => 'qr_cfdi');
            $catalogo[] = array('id'=>4,'codigo' => '04', 'descripcion' => 'cadena_orginal_cfdi');
            $catalogo[] = array('id'=>5,'codigo' => '05', 'descripcion' => 'CSDKEY');
            $catalogo[] = array('id'=>6,'codigo' => '06', 'descripcion' => 'CSDCER');


            $r_alta_bd = (new _defaults())->alta_defaults(catalogo: $catalogo, entidad: $this);
            if (errores::$error) {
                $error = $this->error->error(mensaje: 'Error al insertar', data: $r_alta_bd);
                print_r($error);
                exit;
            }
            $_SESSION['init'][$tabla] = true;
        }
    }

    /**
     * PRUEBA P ORDER P INT
     * Funcion válida si las extensiones sean iguales
     * @param string $extension Descripcion de extension a insertar
     * @param array $extensiones_permitidas Arreglo de extensiones que se identifican como permitidas
     * @return bool|array
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
     * PRUEBA P ORDER P INT
     * Devuelve un valor booleano el cual confimar si la extension es validad o invalida
     * @param string $extension Extension del documento a insertar
     * @param int $tipo_documento_id Tipo de documento del registro a insertar
     * @return bool|array
     */
    public function valida_extension_permitida(string $extension, int $tipo_documento_id): bool|array
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