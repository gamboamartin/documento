<?php
namespace gamboamartin\documento\controllers;



use gamboamartin\documento\models\adm_grupo;
use gamboamartin\errores\errores;
use gamboamartin\template_1\html;
use html\adm_grupo_html;
use html\doc_tipo_documento_html;
use PDO;
use stdClass;

class controlador_adm_grupo extends \gamboamartin\acl\controllers\controlador_adm_grupo {
    protected array $not_actions = array('usuarios','asigna_permiso');
    public string $link_doc_acl_tipo_documento_alta_bd = '';

    public function __construct(PDO $link, html $html = new html(), stdClass $paths_conf = new stdClass())
    {

        $datatables_custom_cols = array();
        $datatables_custom_cols['adm_grupo_n_permisos_doc']['titulo'] = 'N Permisos por doc';

        parent::__construct(link: $link, html: $html, datatables_custom_cols: $datatables_custom_cols,
            paths_conf: $paths_conf);


        $this->modelo = new adm_grupo($link);

        $link_doc_acl_tipo_documento_alta_bd = $this->obj_link->link_alta_bd(link: $link, seccion: 'doc_acl_tipo_documento');
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al obtener link',data:  $link_doc_acl_tipo_documento_alta_bd);
            print_r($error);
            exit;
        }
        $this->link_doc_acl_tipo_documento_alta_bd = $link_doc_acl_tipo_documento_alta_bd;

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

    protected function inputs_children(stdClass $registro): stdClass|array
    {
        $select_doc_tipo_documento_id = (new doc_tipo_documento_html(html: $this->html_base))->select_doc_tipo_documento_id(
            cols:6,con_registros: true,id_selected:  $registro->adm_grupo_id,link:  $this->link, disabled: false);

        if(errores::$error){
            return $this->errores->error(
                mensaje: 'Error al obtener select_doc_tipo_documento_id',data:  $select_doc_tipo_documento_id);
        }


        $select_adm_grupo_id = (new adm_grupo_html(html: $this->html_base))->select_adm_grupo_id(
            cols:6,con_registros: true,id_selected:  $this->registro_id,link:  $this->link, disabled: true);

        if(errores::$error){
            return $this->errores->error(
                mensaje: 'Error al obtener select_adm_grupo_id',data:  $select_adm_grupo_id);
        }


        $this->inputs = new stdClass();
        $this->inputs->select = new stdClass();
        $this->inputs->select->adm_grupo_id = $select_adm_grupo_id;
        $this->inputs->select->doc_tipo_documento_id = $select_doc_tipo_documento_id;

        return $this->inputs;
    }

}