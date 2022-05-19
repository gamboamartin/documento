<?php
namespace models;
use base\orm\modelo;
use gamboamartin\errores\errores;
use PDO;
use stdClass;


class doc_tipo_documento extends modelo{ //FINALIZADAS
    /**
     * DEBUG INI
     * accion constructor.
     * @param PDO $link
     */
    public function __construct(PDO $link){
        $tabla = __CLASS__;
        $columnas = array($tabla=>false);
        $campos_obligatorios = array();
        parent::__construct(link: $link,tabla:  $tabla,campos_obligatorios: $campos_obligatorios, columnas:  $columnas);
    }

    /**
     * PRUEBA P ORDER P INT
     * @param string $extension
     * @param array $extensiones_permitidas
     * @return bool
     */
    public function es_extension_permitida(string $extension, array $extensiones_permitidas): bool|array
    {
        if($extension === '') {
            return $this->error->error(mensaje: 'Error extension no puede venir vacio', data: $extension);
        }

        $es_extension_permitida = false;
        foreach ($extensiones_permitidas as $extension_permitida){
            if($extension_permitida['doc_extension_descripcion'] === $extension){
                $es_extension_permitida = true;
                break;
            }
        }

        return  $es_extension_permitida;
    }

    public function extensiones_permitidas(int $tipo_documento_id): array
    {
        $filtro['doc_tipo_documento.id'] = $tipo_documento_id;

        $extension_permitido = (new doc_extension_permitido($this->link))->filtro_and(filtro: $filtro);
        if(errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener extensiones', data: $extension_permitido);
        }

        return $extension_permitido->registros;
    }

    public function valida_extension_permitida(string $extension, int $tipo_documento_id): bool|array
    {
        $extensiones_permitidas = $this->extensiones_permitidas(tipo_documento_id: $tipo_documento_id);
        if(errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener extensiones', data: $extensiones_permitidas);
        }

        $es_extension_permitida = $this->es_extension_permitida(extension: $extension,
            extensiones_permitidas: $extensiones_permitidas);
        if(errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener extensiones', data: $extensiones_permitidas);
        }

        return $es_extension_permitida;
    }
}