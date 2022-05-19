<?php
namespace models;
use base\orm\modelo;
use config\generales;
use gamboamartin\errores\errores;
use gamboamartin\plugins\files;
use PDO;
use stdClass;


class doc_documento extends modelo{ //FINALIZADAS
    /**
     * DEBUG INI
     * accion constructor.
     * @param PDO $link
     */
    public function __construct(PDO $link){
        $tabla = __CLASS__;
        $columnas = array($tabla=>false, 'doc_tipo_documento'=>$tabla, 'doc_extension'=>$tabla);
        $campos_obligatorios = array('doc_tipo_documento_id', 'doc_extension_id');
        parent::__construct(link: $link,tabla:  $tabla,campos_obligatorios: $campos_obligatorios, columnas:  $columnas);
    }

    public function alta_bd(): array|stdClass
    {
        $extension = (new files())->extension($_FILES['name']);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error obtener extension', data: $extension);
        }

        $validaciones = $this->validaciones_documentos(extension: $extension, grupo_id: $_SESSION['grupo_id'],
            tipo_documento_id: $this->registro['doc_tipo_documento_id']);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error validar documento', data: $validaciones);
        }

        $extension_id = (new doc_extension($this->link))->doc_extension_id(extension: $extension);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error obtener extension id', data: $extension_id);
        }

        $nombre_doc = (new files())->nombre_doc(tipo_documento_id: $this->registro['doc_tipo_documento_id'],
            extension: $extension);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error obtener nombre documento', data: $extension);
        }

        $ruta_directorio = 'archivos/'.$this->tabla.'/';
        $ruta_absoluta_directorio = (new generales())->path_base.$ruta_directorio;

        $this->registro['status'] = 'activo';
        $this->registro['nombre'] = $nombre_doc;
        $this->registro['ruta_relativa'] = $ruta_directorio.$nombre_doc;
        $this->registro['ruta_absoluta'] = $ruta_absoluta_directorio.$nombre_doc;
        $this->registro['doc_extension_id'] = $extension_id;

        $r_alta_doc = parent::alta_bd();
        if(errores::$error){
            return $this->error->error('Error al guardar registro', $r_alta_doc);
        }

        $guarda = (new files())->guarda_archivo_fisico(contenido_file:  file_get_contents($_FILES['tmp_name']),
            ruta_file: $this->registro['ruta_absoluta']);
        if(errores::$error){
            return $this->error->error('Error al guardar archivo', $guarda);
        }

        return $r_alta_doc;
    }


    public function validaciones_documentos(string $extension, int $grupo_id, int $tipo_documento_id): bool|array
    {
        $tiene_permiso = (new doc_acl_tipo_documento($this->link))->tipo_documento_permiso(
            grupo_id: $grupo_id, tipo_documento_id: $tipo_documento_id);
        if($tiene_permiso){
            return $this->error->error(mensaje: 'Error no tiene permiso de alta', data: $tiene_permiso);
        }

        $extension_permitida = (new doc_tipo_documento($this->link))->valida_extension_permitida(extension: $extension,
            tipo_documento_id: $tipo_documento_id);
        if($extension_permitida){
            return $this->error->error(mensaje: 'Error la extension del documento no es validad',
                data: $extension_permitida);
        }

        return true;
    }
}