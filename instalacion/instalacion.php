<?php
namespace gamboamartin\documento\instalacion;

use config\generales;
use gamboamartin\administrador\models\_instalacion;
use gamboamartin\documento\models\adm_grupo;
use gamboamartin\documento\models\doc_acl_tipo_documento;
use gamboamartin\documento\models\doc_documento;
use gamboamartin\documento\models\doc_extension;
use gamboamartin\documento\models\doc_extension_permitido;
use gamboamartin\documento\models\doc_tipo_documento;
use gamboamartin\errores\errores;
use gamboamartin\plugins\Importador;
use PDO;
use stdClass;

class instalacion
{

    private function _add_doc_acl_tipo_documento(PDO $link): array|stdClass
    {
        $result = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'doc_acl_tipo_documento');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al create', data:  $create);
        }
        $result->create = $create;

        $foraneas = array();
        $foraneas['doc_tipo_documento_id'] = new stdClass();
        $foraneas['adm_grupo_id'] = new stdClass();


        $foraneas_r = $init->foraneas(foraneas: $foraneas,table:  'doc_acl_tipo_documento');

        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $foraneas_r);
        }
        $result->foraneas_r = $foraneas_r;

        $campos = new stdClass();

        $campos->codigo = new stdClass();
        $campos->descripcion = new stdClass();
        $campos->descripcion_select = new stdClass();
        $campos->alias = new stdClass();
        $campos->codigo_bis = new stdClass();
        $campos->predeterminado = new stdClass();
        $campos->predeterminado->default = 'inactivo';

        $campos_r = $init->add_columns(campos: $campos,table:  'doc_acl_tipo_documento');

        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $campos_r);
        }

        return $result;
    }

    private function _add_doc_documento(PDO $link): array|stdClass
    {
        $result = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'doc_documento');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al create', data:  $create);
        }
        $result->create = $create;

        $foraneas = array();
        $foraneas['doc_tipo_documento_id'] = new stdClass();
        $foraneas['doc_extension_id'] = new stdClass();


        $foraneas_r = $init->foraneas(foraneas: $foraneas,table:  'doc_documento');

        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $foraneas_r);
        }

        $campos = new stdClass();

        $campos->ruta_absoluta = new stdClass();
        $campos->ruta_relativa = new stdClass();
        $campos->nombre = new stdClass();
        $campos->codigo = new stdClass();
        $campos->alias = new stdClass();
        $campos->codigo_bis = new stdClass();
        $campos->name_out = new stdClass();
        $campos->name_out->default = 'SN';

        $campos_r = $init->add_columns(campos: $campos,table:  'doc_documento');

        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $campos_r);
        }



        $result->campos_r = $campos_r;
        return $result;
    }

    private function _add_doc_extension(PDO $link): array|stdClass
    {
        $result = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'doc_extension');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al create', data:  $create);
        }
        $result->create = $create;

        $campos = new stdClass();

        $campos->es_imagen = new stdClass();
        $campos->es_imagen->tipo_dato = 'VARCHAR';
        $campos->es_imagen->default = 'inactivo';


        $campos_r = $init->add_columns(campos: $campos,table:  'doc_extension');

        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $campos_r);
        }

        $result->campos_r = $campos_r;

        return $result;
    }

    private function _add_doc_extension_permitido(PDO $link): array|stdClass
    {
        $result = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'doc_extension_permitido');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al create', data:  $create);
        }
        $result->create = $create;

        $foraneas = array();
        $foraneas['doc_tipo_documento_id'] = new stdClass();
        $foraneas['doc_extension_id'] = new stdClass();


        $foraneas_r = $init->foraneas(foraneas: $foraneas,table:  'doc_extension_permitido');

        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $foraneas_r);
        }


        $result->foraneas_r = $foraneas_r;


        $campos = new stdClass();

        $campos->codigo = new stdClass();
        $campos->descripcion = new stdClass();
        $campos->descripcion_select = new stdClass();
        $campos->alias = new stdClass();
        $campos->codigo_bis = new stdClass();
        $campos->predeterminado = new stdClass();
        $campos->predeterminado->default = 'inactivo';

        $campos_r = $init->add_columns(campos: $campos,table:  'doc_extension_permitido');

        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $campos_r);
        }

        return $result;
    }

    private function _add_doc_tipo_documento(PDO $link): array|stdClass
    {
        $result = new stdClass();
        $init = (new _instalacion(link: $link));


        $create = $init->create_table_new(table: 'doc_tipo_documento');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al create', data:  $create);
        }
        $result->create = $create;


        $campos = new stdClass();

        $campos->descripcion_select = new stdClass();
        $campos->alias = new stdClass();
        $campos->codigo_bis = new stdClass();
        $campos->predeterminado = new stdClass();
        $campos->predeterminado->default = 'inactivo';

        $campos_r = $init->add_columns(campos: $campos,table:  'doc_tipo_documento');

        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $campos_r);
        }

        return  $result;
    }
    private function doc_acl_tipo_documento(PDO $link): array|stdClass
    {
        $result = new stdClass();

        $create = $this->_add_doc_acl_tipo_documento(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al create', data:  $create);
        }

        return $result;

    }
    private function doc_documento(PDO $link): array|stdClass
    {

        $create = $this->_add_doc_documento(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al create', data:  $create);
        }

        $registros = (new doc_documento(link: $link))->registros(columnas_en_bruto: true);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener registros', data:  $registros);
        }

        foreach ($registros as $registro){
            if($registro['name_out']==='SN'){
                $upd['name_out'] = $registro['nombre'];
                $r_doc = (new doc_documento(link: $link))->modifica_bd_base(registro: $upd,id: $registro['id']);
                if(errores::$error){
                    return (new errores())->error(mensaje: 'Error al actualizar doc_documento', data:  $r_doc);
                }
            }
        }

        return $create;

    }

    private function doc_extension(PDO $link): array|stdClass
    {
        $result = new stdClass();

        $create = $this->_add_doc_extension(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al create', data:  $create);
        }


        $doc_extension_modelo = new doc_extension(link: $link);

        $importador = new Importador();
        $columnas = array();
        $columnas[] = 'id';
        $columnas[] = 'codigo';
        $columnas[] = 'descripcion';
        $columnas[] = 'status';
        $columnas[] = 'predeterminado';

        $ruta = (new generales())->path_base."instalacion/".__FUNCTION__.'.ods';

        if((new generales())->sistema !== 'documento'){
            $ruta = (new generales())->path_base;
            $ruta .= "vendor/gamboa.martin/documento/instalacion/doc_extension.ods";
        }

        $n_extensiones = $doc_extension_modelo->cuenta();
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al contar n_extensiones', data: $n_extensiones);
        }
        $altas = array();
        if($n_extensiones !== 10) {

            $data = $importador->leer_registros(ruta_absoluta: $ruta, columnas: $columnas);
            if (errores::$error) {
                return (new errores())->error(mensaje: 'Error al leer cat_sat_cve_prod', data: $data);
            }

            foreach ($data as $row) {
                $row = (array)$row;
                $doc_extension_ins['id'] = trim($row['id']);
                $doc_extension_ins['codigo'] = trim($row['codigo']);
                $doc_extension_ins['descripcion'] = trim($row['descripcion']);
                $doc_extension_ins['descripcion_select'] = trim($row['id']) . ' ' . trim($row['descripcion']);
                $doc_extension_ins['predeterminado'] = 'inactivo';
                $doc_extension_ins['alias'] =  trim($row['codigo']);
                $doc_extension_ins['codigo_bis'] =  trim($row['codigo']);
                $alta = $doc_extension_modelo->inserta_registro_si_no_existe(registro: $doc_extension_ins);
                if (errores::$error) {
                    return (new errores())->error(mensaje: 'Error al insertar doc_extension_ins', data: $alta);
                }
                $altas[] = $alta;
            }
        }
        $result->altas = $altas;



        return $result;

    }

    private function doc_extension_permitido(PDO $link): array|stdClass
    {
        $create = $this->_add_doc_extension_permitido(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al create', data:  $create);
        }

        return $create;

    }

    /**
     * Genera la estructura de un tipo documento con una estructura basica de datos
     *
     * @param PDO $link Conexion a la base de datos.
     * @return array|stdClass Si hay error retorna un array.
     */
    private function doc_tipo_documento(PDO $link): array|stdClass
    {
        $result = new stdClass();

        $create = $this->_add_doc_tipo_documento(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al create', data:  $create);
        }
        $result->create = $create;

        $create = $this->_add_doc_acl_tipo_documento(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al create', data:  $create);
        }
        $result->doc_acl_tipo_documento = $create;

        $create = $this->_add_doc_extension(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al create', data:  $create);
        }
        $result->doc_documento = $create;

        $create = $this->_add_doc_documento(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al create', data:  $create);
        }
        $result->doc_documento = $create;

        $create = $this->_add_doc_extension_permitido(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al create', data:  $create);
        }
        $result->doc_extension_permitido = $create;

        $importador = new Importador();
        $columnas = array();
        $columnas[] = 'id';
        $columnas[] = 'descripcion';
        $columnas[] = 'codigo';
        $columnas[] = 'status';

        $ruta = (new generales())->path_base."instalacion/".__FUNCTION__.'.ods';

        if((new generales())->sistema !== 'documento'){
            $ruta = (new generales())->path_base;
            $ruta .= "vendor/gamboa.martin/documento/instalacion/doc_tipo_documento.ods";
        }

        $doc_tipo_documento_modelo = new doc_tipo_documento(link: $link);

        $n_tipos_documento = $doc_tipo_documento_modelo->cuenta();
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al contar n_tipos_documento', data: $n_tipos_documento);
        }
        $altas = array();
        if($n_tipos_documento !== 9) {

            $data = $importador->leer_registros(ruta_absoluta: $ruta, columnas: $columnas);
            if (errores::$error) {
                return (new errores())->error(mensaje: 'Error al leer cat_sat_cve_prod', data: $data);
            }

            foreach ($data as $row) {
                $row = (array)$row;
                $row_ins['id'] = trim($row['id']);
                $row_ins['codigo'] = trim($row['codigo']);
                $row_ins['descripcion'] = trim($row['descripcion']);
                $row_ins['descripcion_select'] = trim($row['codigo']) . ' ' . trim($row['descripcion']);
                $row_ins['alias'] =  trim($row['descripcion']);
                $row_ins['codigo_bis'] =  trim($row['codigo']);
                $alta = $doc_tipo_documento_modelo->inserta_registro_si_no_existe(registro: $row_ins);
                if (errores::$error) {
                    return (new errores())->error(mensaje: 'Error al insertar doc_extension_ins', data: $alta);
                }
                $altas[] = $alta;
            }
        }
        $result->altas = $altas;


        $doc_extensiones = (new doc_extension(link: $link))->registros();
        if (errores::$error) {
            return (new errores())->error(mensaje: 'Error obtener extensiones', data: $doc_extensiones);
        }
        foreach ($doc_extensiones as $doc_extension){
            $filtro = array();
            $filtro['doc_extension.id'] = $doc_extension['doc_extension_id'];
            $filtro['doc_tipo_documento.id'] = 9;
            $existe = (new doc_extension_permitido(link: $link))->existe(filtro: $filtro);
            if (errores::$error) {
                return (new errores())->error(mensaje: 'Error valida si existe', data: $existe);
            }

            if(!$existe){
                $doc_extension_permitido_ins['doc_extension_id'] = $doc_extension['doc_extension_id'];
                $doc_extension_permitido_ins['doc_tipo_documento_id'] = 9;
                $ins = (new doc_extension_permitido(link: $link))->alta_registro(registro: $doc_extension_permitido_ins);
                if (errores::$error) {
                    return (new errores())->error(mensaje: 'Error insertar', data: $ins);
                }
            }
        }

        $adm_grupos = (new adm_grupo(link: $link))->registros();
        if (errores::$error) {
            return (new errores())->error(mensaje: 'Error obtener adm_grupos', data: $adm_grupos);
        }
        foreach ($adm_grupos as $adm_grupo){
            $filtro = array();
            $filtro['adm_grupo.id'] = $adm_grupo['adm_grupo_id'];
            $filtro['doc_tipo_documento.id'] = 9;
            $existe = (new doc_acl_tipo_documento(link: $link))->existe(filtro: $filtro);
            if (errores::$error) {
                return (new errores())->error(mensaje: 'Error valida si existe', data: $existe);
            }

            if(!$existe){
                $doc_acl_tipo_documento_ins['adm_grupo_id'] = $adm_grupo['adm_grupo_id'];
                $doc_acl_tipo_documento_ins['doc_tipo_documento_id'] = 9;
                $ins = (new doc_acl_tipo_documento(link: $link))->alta_registro(registro: $doc_acl_tipo_documento_ins);
                if (errores::$error) {
                    return (new errores())->error(mensaje: 'Error insertar', data: $ins);
                }
            }
        }

        return $result;

    }

    private function doc_version(PDO $link): array|stdClass
    {
        $result = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: __FUNCTION__);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al create', data:  $create);
        }
        $result->create = $create;

        $foraneas = array();
        $foraneas['doc_documento_id'] = new stdClass();
        $foraneas['doc_extension_id'] = new stdClass();


        $foraneas_r = $init->foraneas(foraneas: $foraneas,table:  __FUNCTION__);

        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $foraneas_r);
        }

        $result->foraneas_r = $foraneas_r;

        $campos = new stdClass();

        $campos->ruta_absoluta = new stdClass();
        $campos->ruta_relativa = new stdClass();
        $campos->nombre = new stdClass();
        $campos->codigo = new stdClass();
        $campos->alias = new stdClass();
        $campos->codigo_bis = new stdClass();
        $campos->descripcion = new stdClass();
        $campos->descripcion_select = new stdClass();


        $campos_r = $init->add_columns(campos: $campos,table:  'doc_version');

        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $campos_r);
        }


        $campos_r = $init->add_columns(campos: $campos,table:  __FUNCTION__);

        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $campos_r);
        }



        $result->campos_r = $campos_r;

        return $result;

    }

    final public function instala(PDO $link): array|stdClass
    {

        $result = new stdClass();

        $doc_tipo_documento = $this->doc_tipo_documento(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar doc_tipo_documento', data:  $doc_tipo_documento);
        }

        $doc_extension = $this->doc_extension(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar doc_extension', data:  $doc_extension);
        }

        $doc_acl_tipo_documento = $this->doc_acl_tipo_documento(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar doc_acl_tipo_documento', data:  $doc_acl_tipo_documento);
        }

        $doc_extension_permitido = $this->doc_extension_permitido(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar doc_extension_permitido', data:  $doc_extension_permitido);
        }

        $doc_documento = $this->doc_documento(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar doc_documento', data:  $doc_documento);
        }
        $doc_version = $this->doc_version(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar doc_version', data:  $doc_version);
        }

        return $result;

    }

}
