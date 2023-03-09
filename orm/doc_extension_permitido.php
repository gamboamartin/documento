<?php
namespace gamboamartin\documento\models;
use base\orm\_defaults;
use base\orm\modelo;
use gamboamartin\errores\errores;
use PDO;



class doc_extension_permitido extends modelo{ //FINALIZADAS
    /**
     * DEBUG INI
     * accion constructor.
     * @param PDO $link
     */
    public function __construct(PDO $link){
        $tabla = 'doc_extension_permitido';
        $columnas = array($tabla=>false, 'doc_tipo_documento'=>$tabla, 'doc_extension'=>$tabla);
        $campos_obligatorios = array('doc_tipo_documento_id', 'doc_extension_id');
        parent::__construct(link: $link,tabla:  $tabla,campos_obligatorios: $campos_obligatorios, columnas:  $columnas);
        $this->NAMESPACE = __NAMESPACE__;

        $this->etiqueta = 'Extension Permitida';

        if(!isset($_SESSION['init'][$tabla])) {

            unset($_SESSION['init']['doc_tipo_documento']);
            unset($_SESSION['init']['doc_extension']);

            new doc_tipo_documento(link: $this->link);
            new doc_extension(link: $this->link);

            $catalago = array();
            $catalago[] = array('id'=>1,'doc_tipo_documento_id'=>1,'doc_extension_id'=>1);
            $catalago[] = array('id'=>2,'doc_tipo_documento_id'=>2,'doc_extension_id'=>1);
            $catalago[] = array('id'=>3,'doc_tipo_documento_id'=>3,'doc_extension_id'=>7);
            $catalago[] = array('id'=>4,'doc_tipo_documento_id'=>4,'doc_extension_id'=>2);
            $catalago[] = array('id'=>5,'doc_tipo_documento_id'=>5,'doc_extension_id'=>4);
            $catalago[] = array('id'=>6,'doc_tipo_documento_id'=>6,'doc_extension_id'=>3);


            $r_alta_bd = (new _defaults())->alta_defaults(catalogo: $catalago, entidad: $this);
            if (errores::$error) {
                $error = $this->error->error(mensaje: 'Error al insertar', data: $r_alta_bd);
                print_r($error);
                exit;
            }
            $_SESSION['init'][$tabla] = true;
        }

    }
}