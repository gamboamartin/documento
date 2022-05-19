<?php
namespace tests\orm;

use config\generales;
use gamboamartin\errores\errores;
use gamboamartin\test\test;
use models\doc_documento;


class doc_documentoTest extends test {
    public errores $errores;
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->errores = new errores();
    }

    public function test_alta_bd()
    {
        errores::$error = false;
        $doc_documento = new doc_documento($this->link);
        //$inicializacion = new liberator($inicializacion);


        $resultado = $doc_documento->alta_bd();

    }
}

