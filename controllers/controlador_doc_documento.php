<?php
namespace gamboamartin\documento\controllers;

use gamboamartin\documento\models\doc_documento;
use gamboamartin\errores\errores;
use gamboamartin\system\_ctl_base;
use gamboamartin\system\actions;
use gamboamartin\system\links_menu;
use gamboamartin\template_1\html;
use html\doc_documento_html;
use PDO;
use stdClass;
use Throwable;

class controlador_doc_documento extends _ctl_base{
    public function __construct(PDO $link,  html $html = new html(), stdClass $paths_conf = new stdClass()){
        $modelo = new doc_documento($link);

        $html_ = new doc_documento_html(html: $html);
        $obj_link = new links_menu(link: $link, registro_id: $this->registro_id);

        $datatables = new stdClass();
        $datatables->columns = array();
        $datatables->columns['doc_documento_id']['titulo'] = 'Id';
        $datatables->columns['doc_documento_descripcion']['titulo'] = 'Documento';


        parent::__construct(html: $html_, link: $link, modelo: $modelo, obj_link: $obj_link, datatables: $datatables,
            paths_conf: $paths_conf);

        $this->titulo_lista = 'Documentos';


        $this->lista_get_data = true;

        $this->modelo = $modelo;

    }


    public function alta(bool $header, bool $ws = false): array|string
    {

        $r_alta = $this->init_alta();
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al inicializar alta',data:  $r_alta, header: $header,ws:  $ws);
        }

        $this->modelo->campos_view['documento']['type'] = 'files';

        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'doc_tipo_documento_id',
            keys_selects: array(), id_selected: -1, label: 'Tipo Doc');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
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

    public function alta_bd(bool $header, bool $ws = false): array|stdClass
    {
        $this->modelo->file = $_FILES['documento'];
        $r_alta_bd =  parent::alta_bd(header: false); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al generar insertar registro',data:  $r_alta_bd,header: $header,ws: $ws);
        }

        $siguiente_view = (new actions())->init_alta_bd();
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener siguiente view', data: $siguiente_view,
                header:  $header, ws: $ws);
        }

        $seccion_retorno = $this->tabla;
        if(isset($_POST['seccion_retorno'])){
            $seccion_retorno = $_POST['seccion_retorno'];
            unset($_POST['seccion_retorno']);
        }

        $id_retorno = -1;
        if(isset($_POST['id_retorno'])){
            $id_retorno = $_POST['id_retorno'];
            unset($_POST['id_retorno']);
        }

        if($header){
            if($id_retorno === -1) {
                $id_retorno = $r_alta_bd->registro_id;
            }
            $this->retorno_base(registro_id:$id_retorno, result: $r_alta_bd, siguiente_view: $siguiente_view,
                ws:  $ws,seccion_retorno: $seccion_retorno);
        }
        if($ws){
            header('Content-Type: application/json');
            try {
                echo json_encode($r_alta_bd, JSON_THROW_ON_ERROR);
            }
            catch (Throwable $e){
                $error = (new errores())->error(mensaje: 'Error al maquetar JSON' , data: $e);
                print_r($error);
            }
            exit;
        }

        return $r_alta_bd;
    }

    protected function campos_view(): array
    {
        $keys = new stdClass();
        $keys->inputs = array('codigo','descripcion');
        $keys->selects = array();


        $init_data = array();
        $init_data['doc_tipo_documento'] = "gamboamartin\\documento";

        $campos_view = $this->campos_view_base(init_data: $init_data,keys:  $keys);


        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al inicializar campo view',data:  $campos_view);
        }


        return $campos_view;
    }

    public function descarga(bool $header, bool $ws = false){
        ob_clean();
        $doc_documento = $this->modelo->registro(registro_id: $this->registro_id, retorno_obj: true);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al generar obtener documento',data:  $doc_documento,header: $header,ws: $ws);
        }
        $ruta_absoluta = $doc_documento->doc_documento_ruta_absoluta;
        if(file_exists($ruta_absoluta)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($ruta_absoluta).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($ruta_absoluta));
            flush(); // Flush system output buffer
            readfile($ruta_absoluta);
        }
        exit;

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

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6,key: 'documento', keys_selects:$keys_selects, place_holder: 'Documento');
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

        $this->modelo->campos_view['documento']['type'] = 'files';

        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'doc_tipo_documento_id',
            keys_selects: array(), id_selected: $this->registro['doc_tipo_documento_id'], label: 'Tipo Doc');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
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