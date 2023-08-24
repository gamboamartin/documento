<?php
namespace gamboamartin\documento\controllers;

use base\controller\init;
use gamboamartin\documento\models\doc_extension;
use gamboamartin\errores\errores;
use gamboamartin\system\links_menu;
use gamboamartin\template_1\html;
use html\doc_extension_html;
use html\doc_tipo_documento_html;
use PDO;
use stdClass;

class controlador_doc_extension extends _parents_doc {
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

        $this->childrens_data['doc_documento']['title'] = 'Documento';


    }

    /**
     * Cambia el estado de una extension a si es imagen
     * @param bool $header
     * @param bool $ws
     * @return array|stdClass
     */
    public function es_imagen(bool $header = true, bool $ws = false): array|stdClass
    {
        $ejecuta = $this->row_upd(key: __FUNCTION__);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener ejecutar',data:  $ejecuta, header: $header,ws:  $ws);
        }
        $this->header_out(result: $ejecuta, header: $header,ws:  $ws);
        return $ejecuta;
    }

    protected function inputs_children(stdClass $registro): stdClass|array
    {
        $select_doc_tipo_documento_id = (new doc_tipo_documento_html(html: $this->html_base))->select_doc_tipo_documento_id(
            cols:6,con_registros: true,id_selected:  -1,link:  $this->link);

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

    protected function key_selects_txt(array $keys_selects): array
    {
        $keys_selects =  parent::key_selects_txt($keys_selects); // TODO: Change the autogenerated stub
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'descripcion', keys_selects:$keys_selects, place_holder: 'Extension');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        return $keys_selects;

    }

    public function versiones(bool $header = true, bool $ws = false, array $not_actions = array()): array|string
    {
        $contenido_table = (new _docs())->versiones(controler: $this,function: __FUNCTION__, not_actions: $not_actions);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener tbody',data:  $contenido_table, header: $header,ws:  $ws);
        }
        return $contenido_table;
    }
}