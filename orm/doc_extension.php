<?php
namespace gamboamartin\documento\models;
use base\orm\_defaults;
use base\orm\modelo;
use gamboamartin\errores\errores;
use PDO;
use stdClass;


class doc_extension extends modelo{ //FINALIZADAS
    /**
     * DEBUG INI
     * accion constructor.
     * @param PDO $link
     */
    public function __construct(PDO $link){
        $tabla = 'doc_extension';
        $columnas = array($tabla=>false);
        $campos_obligatorios = array();
        parent::__construct(link: $link,tabla:  $tabla,campos_obligatorios: $campos_obligatorios, columnas:  $columnas);
        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Extension';


        if(!isset($_SESSION['init'][$tabla])) {

            $catalogo = array();
            $catalogo[] = array('id'=>1,'codigo' => 'xml', 'descripcion' => 'xml');
            $catalogo[] = array('id'=>2,'codigo' => 'txt', 'descripcion' => 'txt');
            $catalogo[] = array('id'=>3,'codigo' => 'cer', 'descripcion' => 'cer');
            $catalogo[] = array('id'=>4,'codigo' => 'key', 'descripcion' => 'key');
            $catalogo[] = array('id'=>5,'codigo' => 'xlsx', 'descripcion' => 'xlsx');
            $catalogo[] = array('id'=>6,'codigo' => 'docx', 'descripcion' => 'docx');
            $catalogo[] = array('id'=>7,'codigo' => 'jpg', 'descripcion' => 'jpg');
            $catalogo[] = array('id'=>8,'codigo' => 'png', 'descripcion' => 'png');
            $catalogo[] = array('id'=>9,'codigo' => 'pdf', 'descripcion' => 'pdf');
            $catalogo[] = array('id'=>10,'codigo' => 'pem', 'descripcion' => 'pem');


            $r_alta_bd = (new _defaults())->alta_defaults(catalogo: $catalogo, entidad: $this);
            if (errores::$error) {
                $error = $this->error->error(mensaje: 'Error al insertar', data: $r_alta_bd);
                print_r($error);
                exit;
            }
            $_SESSION['init'][$tabla] = true;
        }


    }




    /**
     * PRUEBA P ORDER P INT
     * Esta funcion obtinen de la id de la extension
     * @param string $extension Descripcion de extension de documento a insertar
     * @return array|mixed
     */
    public function doc_extension_id(string $extension): int|array
    {

        if($extension === ''){
            return $this->error->error(mensaje: 'Error extension no puede venir vacia', data: $extension);
        }

        $filtro['doc_extension.descripcion'] = $extension;

        $r_extension = $this->filtro_and(filtro: $filtro);
        if(errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener extensiones', data: $r_extension);
        }
        if($r_extension->n_registros === 0){
            return $this->error->error(mensaje: 'Error no existe la extension', data: $extension);
        }

        return (int)$r_extension->registros[0]['doc_extension_id'];
    }
}