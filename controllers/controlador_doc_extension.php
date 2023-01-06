<?php
namespace gamboamartin\documento\controllers;

use gamboamartin\documento\models\doc_extension;
use gamboamartin\errores\errores;
use gamboamartin\system\_ctl_base;
use gamboamartin\system\links_menu;
use gamboamartin\template_1\html;
use html\doc_extension_html;
use html\doc_tipo_documento_html;
use PDO;
use stdClass;

class controlador_doc_extension extends _ctl_base{
    public string $link_doc_extension_permitido_alta_bd = '';
    public function __construct(PDO $link,  html $html = new html(), stdClass $paths_conf = new stdClass()){
        $modelo = new doc_extension($link);

        $html_ = new doc_extension_html(html: $html);
        $obj_link = new links_menu(link: $link, registro_id: $this->registro_id);

        $datatables = new stdClass();
        $datatables->columns = array();
        $datatables->columns['doc_extension_id']['titulo'] = 'Id';
        $datatables->columns['doc_extension_descripcion']['titulo'] = 'Extension';


        parent::__construct(html: $html_, link: $link, modelo: $modelo, obj_link: $obj_link, datatables: $datatables,
            paths_conf: $paths_conf);

        $this->titulo_lista = 'Extensiones';


        $this->lista_get_data = true;

        $link_doc_extension_permitido_alta_bd = $this->obj_link->link_alta_bd(link: $link, seccion: 'doc_extension_permitido');
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al obtener link',data:  $link_doc_extension_permitido_alta_bd);
            print_r($error);
            exit;
        }
        $this->link_doc_extension_permitido_alta_bd = $link_doc_extension_permitido_alta_bd;

    }


    public function alta(bool $header, bool $ws = false): array|string
    {

        $r_alta = (new _docs())->alta_base(controlador: $this);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al inicializar alta',data:  $r_alta, header: $header,ws:  $ws);
        }

        return $r_alta;
    }

    protected function campos_view(): array
    {
        $campos_view = (new _docs())->campos_view_base(controlador: $this);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al inicializar campo view',data:  $campos_view);
        }

        return $campos_view;
    }

    public function documentos(bool $header = true, bool $ws = false): array|string
    {


        $contenido_table = (new _docs())->documentos(controler: $this,function: __FUNCTION__);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener tbody',data:  $contenido_table, header: $header,ws:  $ws);
        }


        return $contenido_table;


    }

    public function ext_permitida(bool $header = true, bool $ws = false): array|string
    {

        $contenido_table = (new _docs())->ext_permitida(controler: $this, function: __FUNCTION__);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener tbody',data:  $contenido_table, header: $header,ws:  $ws);
        }


        return $contenido_table;


    }

    protected function inputs_children(stdClass $registro): stdClass|array
    {
        $select_doc_tipo_documento_id = (new doc_tipo_documento_html(html: $this->html_base))->select_doc_tipo_documento_id(
            cols:6,con_registros: true,id_selected:  -1,link:  $this->link, disabled: false);

        if(errores::$error){
            return $this->errores->error(
                mensaje: 'Error al obtener select_doc_tipo_documento_id',data:  $select_doc_tipo_documento_id);
        }


        $select_doc_extension_id = (new doc_extension_html(html: $this->html_base))->select_doc_extension_id(
            cols:6,con_registros: true,id_selected:  $this->registro_id,link:  $this->link, disabled: true);

        if(errores::$error){
            return $this->errores->error(
                mensaje: 'Error al obtener select_doc_extension_id',data:  $select_doc_extension_id);
        }


        $this->inputs = new stdClass();
        $this->inputs->select = new stdClass();
        $this->inputs->select->doc_extension_id = $select_doc_extension_id;
        $this->inputs->select->doc_tipo_documento_id = $select_doc_tipo_documento_id;

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

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 12,key: 'descripcion', keys_selects:$keys_selects, place_holder: 'Extension');
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

    public function versiones(bool $header = true, bool $ws = false): array|string
    {


        $data_view = new stdClass();
        $data_view->names = array('Id','Tipo Doc', 'Doc', 'Ext',' Fecha','Acciones');
        $data_view->keys_data = array('doc_documento_id','doc_tipo_documento_descripcion','doc_documento_descripcion', 'doc_extension_descripcion', 'doc_version_fecha_alta');
        $data_view->key_actions = 'acciones';
        $data_view->namespace_model = 'gamboamartin\\documento\\models';
        $data_view->name_model_children = 'doc_version';


        $contenido_table = $this->contenido_children(data_view: $data_view, next_accion: __FUNCTION__);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener tbody',data:  $contenido_table, header: $header,ws:  $ws);
        }


        return $contenido_table;


    }
}