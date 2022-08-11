<?php
namespace controllers;

use base\controller\controlador_base;
use models\doc_documento;

class controlador_doc_documento extends controlador_base{


    public function __construct($link){
        $modelo = new doc_documento($link);
        parent::__construct($link, $modelo);
    }
}