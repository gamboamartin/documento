<?php
namespace tests\base\orm;

use base\orm\elementos;
use gamboamartin\errores\errores;

use gamboamartin\test\liberator;
use gamboamartin\test\test;



class elementosTest extends test {
    public errores $errores;
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->errores = new errores();
    }
    public function test_data_campo_tabla_externa(){
        errores::$error = false;
        $elementos = new elementos();
        $elementos = new liberator($elementos);

        $campo = array();

        $resultado = $elementos->data_campo_tabla_externa($campo);

        $this->assertIsArray($resultado);
        $this->assertTrue(errores::$error);
        $this->assertStringContainsStringIgnoringCase('Error al validar campo', $resultado['mensaje']);

        errores::$error = false;
        $campo = array();
        $campo['elemento_lista_campo_tabla_externa'] = 'a';

        $resultado = $elementos->data_campo_tabla_externa($campo);
        $this->assertIsString($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('a', $resultado);


        errores::$error = false;

    }

}