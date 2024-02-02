<?php
namespace tests\orm;

use gamboamartin\administrador\models\_instalacion;
use gamboamartin\documento\instalacion\instalacion;
use gamboamartin\documento\models\doc_acl_tipo_documento;
use gamboamartin\errores\errores;
use gamboamartin\system\table;
use gamboamartin\test\liberator;
use gamboamartin\test\test;


class instalacionTest extends test {
    public errores $errores;
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->errores = new errores();
    }

    public function test_doc_tipo_documento()
    {
        $_SESSION['usuario_id'] = 2;
        errores::$error = false;
        $ins = new instalacion();
        $ins = new liberator($ins);


        $resultado = $ins->doc_tipo_documento($this->link);
        //print_r($resultado);exit;

        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('Ya existe tabla doc_tipo_documento',$resultado->create->create);
        errores::$error = false;

    }
}

