<?php
namespace gamboamartin\documento\controllers;

use gamboamartin\documento\models\doc_tipo_documento;
use gamboamartin\errores\errores;
use gamboamartin\system\_ctl_base;
use gamboamartin\system\links_menu;
use gamboamartin\template_1\html;
use html\adm_grupo_html;
use html\doc_documento_html;
use html\doc_extension_html;
use html\doc_tipo_documento_html;
use PDO;
use stdClass;

class controlador_doc_tipo_documento extends _ctl_base{
    public string $link_doc_acl_tipo_documento_alta_bd = '';
    public string $link_doc_documento_alta_bd = '';

    public string $link_doc_extension_permitido_alta_bd = '';
    public function __construct(PDO $link,  html $html = new html(), stdClass $paths_conf = new stdClass()){
        $modelo = new doc_tipo_documento($link);

        $html_ = new doc_tipo_documento_html(html: $html);
        $obj_link = new links_menu(link: $link, registro_id: $this->registro_id);

        $datatables = new stdClass();
        $datatables->columns = array();
        $datatables->columns['doc_tipo_documento_id']['titulo'] = 'Id';
        $datatables->columns['doc_tipo_documento_descripcion']['titulo'] = 'Tipos de documento';
        $datatables->columns['doc_tipo_documento_n_permisos']['titulo'] = 'N Permisos';


        parent::__construct(html: $html_, link: $link, modelo: $modelo, obj_link: $obj_link, datatables: $datatables,
            paths_conf: $paths_conf);

        $this->titulo_lista = 'Tipos de documentos';

        $link_doc_acl_tipo_documento_alta_bd = $this->obj_link->link_alta_bd(link: $link, seccion: 'doc_acl_tipo_documento');
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al obtener link',data:  $link_doc_acl_tipo_documento_alta_bd);
            print_r($error);
            exit;
        }
        $this->link_doc_acl_tipo_documento_alta_bd = $link_doc_acl_tipo_documento_alta_bd;

        $link_doc_documento_alta_bd = $this->obj_link->link_alta_bd(link: $link, seccion: 'doc_documento');
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al obtener link',data:  $link_doc_documento_alta_bd);
            print_r($error);
            exit;
        }
        $this->link_doc_documento_alta_bd = $link_doc_documento_alta_bd;

        $link_doc_extension_permitido_alta_bd = $this->obj_link->link_alta_bd(link: $link, seccion: 'doc_extension_permitido');
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al obtener link',data:  $link_doc_extension_permitido_alta_bd);
            print_r($error);
            exit;
        }
        $this->link_doc_extension_permitido_alta_bd = $link_doc_extension_permitido_alta_bd;

        $this->lista_get_data = true;

    }

    public function acl_tipo_documento(bool $header = true, bool $ws = false): array|string
    {


        $data_view = new stdClass();
        $data_view->names = array('Id','Tipo Doc', 'Grupo','Acciones');
        $data_view->keys_data = array('doc_acl_tipo_documento_id','doc_tipo_documento_descripcion','adm_grupo_descripcion');
        $data_view->key_actions = 'acciones';
        $data_view->namespace_model = 'gamboamartin\\documento\\models';
        $data_view->name_model_children = 'doc_acl_tipo_documento';


        $contenido_table = $this->contenido_children(data_view: $data_view, next_accion: __FUNCTION__);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener tbody',data:  $contenido_table, header: $header,ws:  $ws);
        }


        return $contenido_table;


    }

    public function alta(bool $header, bool $ws = false): array|string
    {

        $r_alta = $this->init_alta();
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al inicializar alta',data:  $r_alta, header: $header,ws:  $ws);
        }


        $keys_selects['descripcion'] = new stdClass();
        $keys_selects['descripcion']->cols = 12;

        $inputs = $this->inputs(keys_selects: $keys_selects);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs',data:  $inputs, header: $header,ws:  $ws);
        }


        return $r_alta;
    }

    protected function campos_view(): array
    {
        $keys = new stdClass();
        $keys->inputs = array('codigo','descripcion');
        $keys->selects = array();

        $init_data = array();

        $campos_view = $this->campos_view_base(init_data: $init_data,keys:  $keys);

        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al inicializar campo view',data:  $campos_view);
        }


        return $campos_view;
    }

    public function documentos(bool $header = true, bool $ws = false): array|string
    {
        $data_view = new stdClass();
        $data_view->names = array('Id','Tipo Doc', 'Doc','Acciones');
        $data_view->keys_data = array('doc_documento_id','doc_tipo_documento_descripcion','doc_documento_descripcion');
        $data_view->key_actions = 'acciones';
        $data_view->namespace_model = 'gamboamartin\\documento\\models';
        $data_view->name_model_children = 'doc_documento';


        $contenido_table = $this->contenido_children(data_view: $data_view, next_accion: __FUNCTION__);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener tbody',data:  $contenido_table, header: $header,ws:  $ws);
        }

        $documento = (new doc_documento_html(html: $this->html_base))->input_file(cols: 6, name: 'documento',row_upd: new stdClass(),value_vacio: false);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener documento',data:  $documento, header: $header,ws:  $ws);
        }

        $this->inputs->documento = $documento;



        return $contenido_table;


    }

    public function ext_permitida(bool $header = true, bool $ws = false): array|string
    {

        $data_view = new stdClass();
        $data_view->names = array('Id','Tipo Doc', 'Extension','Acciones');
        $data_view->keys_data = array('doc_extension_permitido_id','doc_tipo_documento_descripcion','doc_extension_descripcion');
        $data_view->key_actions = 'acciones';
        $data_view->namespace_model = 'gamboamartin\\documento\\models';
        $data_view->name_model_children = 'doc_extension_permitido';


        $contenido_table = $this->contenido_children(data_view: $data_view, next_accion: __FUNCTION__);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener tbody',data:  $contenido_table, header: $header,ws:  $ws);
        }


        return $contenido_table;


    }

    protected function inputs_children(stdClass $registro): stdClass|array
    {
        $select_doc_tipo_documento_id = (new doc_tipo_documento_html(html: $this->html_base))->select_doc_tipo_documento_id(
            cols:6,con_registros: true,id_selected:  $this->registro_id,link:  $this->link, disabled: true);

        if(errores::$error){
            return $this->errores->error(
                mensaje: 'Error al obtener select_doc_tipo_documento_id',data:  $select_doc_tipo_documento_id);
        }

        $select_doc_extension_id = (new doc_extension_html(html: $this->html_base))->select_doc_extension_id(
            cols:6,con_registros: true,id_selected:  -1,link:  $this->link);

        if(errores::$error){
            return $this->errores->error(
                mensaje: 'Error al obtener select_doc_extension_id',data:  $select_doc_extension_id);
        }


        $select_adm_grupo_id = (new adm_grupo_html(html: $this->html_base))->select_adm_grupo_id(
            cols:6,con_registros: true,id_selected:  -1,link:  $this->link, disabled: false);

        if(errores::$error){
            return $this->errores->error(
                mensaje: 'Error al obtener select_adm_grupo_id',data:  $select_adm_grupo_id);
        }




        $this->inputs = new stdClass();
        $this->inputs->select = new stdClass();
        $this->inputs->select->adm_grupo_id = $select_adm_grupo_id;
        $this->inputs->select->doc_tipo_documento_id = $select_doc_tipo_documento_id;
        $this->inputs->select->doc_extension_id = $select_doc_extension_id;

        return $this->inputs;
    }

    /**
     * Genera los keys para inputs de frontend
     * @param array $keys_selects Keys predefinidos
     * @return array
     */
    protected function key_selects_txt(array $keys_selects): array
    {

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 12, key: 'codigo', keys_selects: $keys_selects, place_holder: 'Cod');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 12,key: 'descripcion', keys_selects:$keys_selects, place_holder: 'Tipo Doc');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        return $keys_selects;
    }

    public function modifica(
        bool $header, bool $ws = false): array|stdClass
    {
        $r_modifica = $this->init_modifica(); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al generar salida de template',data:  $r_modifica,header: $header,ws: $ws);
        }



        $keys_selects['descripcion'] = new stdClass();
        $keys_selects['descripcion']->cols = 12;

        $keys_selects['codigo'] = new stdClass();
        $keys_selects['codigo']->disabled = true;

        $base = $this->base_upd(keys_selects: $keys_selects, params: array(),params_ajustados: array());
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al integrar base',data:  $base, header: $header,ws:  $ws);
        }




        return $r_modifica;
    }
}