<?php
namespace gamboamartin\documento\controllers;

use gamboamartin\documento\models\doc_documento;
use gamboamartin\system\links_menu;
use gamboamartin\system\system;
use gamboamartin\template\html;
use html\doc_documento_html;
use PDO;
use stdClass;

class controlador_doc_documento extends system {

    public function __construct(PDO $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass()){
        $modelo = new doc_documento(link: $link);
        $html_ = new doc_documento_html(html: $html);
        $obj_link = new links_menu(link: $link, registro_id: $this->registro_id);
        parent::__construct(html:$html_, link: $link,modelo:  $modelo, obj_link: $obj_link, paths_conf: $paths_conf);

        $this->titulo_lista = 'Documentos';
    }
}