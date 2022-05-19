<?php
namespace tests;
use gamboamartin\errores\errores;
use gamboamartin\test\test;
use models\doc_documento;
use models\doc_extension;
use models\doc_extension_permitido;
use stdClass;

class base_test extends test{
    private errores $error;
    public function __construct(?string $name = null, array $data = [], string $dataName = '', string $tipo_conexion = 'PDO')
    {
        parent::__construct($name, $data, $dataName, $tipo_conexion);
        $this->error = new errores();
    }

    protected function alta_extension(int $id = 1, string $codigo = '1', string $descripcion = 'a'): array|stdClass
    {
        $_SESSION['usuario_id'] = 1;
        $doc_extension['id'] = $id;
        $doc_extension['codigo'] = $codigo;
        $doc_extension['descripcion'] = $descripcion;
        $alta_extension = (new doc_extension($this->link))->alta_registro(registro: $doc_extension);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar extension', data: $alta_extension);
        }
        return $alta_extension;
    }

    protected function alta_extension_permitida(int $id, int $doc_extension_id, int $doc_tipo_documento_id): bool|array
    {
        $_SESSION['usuario_id'] = 1;
        $doc_extension_permitido['id'] = $id;
        $doc_extension_permitido['doc_tipo_documento_id'] = $doc_extension_id;
        $doc_extension_permitido['doc_extension_id'] = $doc_tipo_documento_id;
        $alta_extension_permitido = (new doc_extension_permitido($this->link))->alta_registro(registro: $doc_extension_permitido);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar extension permitido', data: $alta_extension_permitido);
        }
        return true;
    }

    protected function elimina_documento(): bool|array
    {
        $elimina_documento = (new doc_documento($this->link))->elimina_todo();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al eliminar documento', data: $elimina_documento);
        }
        return true;
    }

    protected function elimina_extension(): bool|array
    {

        $elimina_documento = $this->elimina_documento();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al eliminar documento', data: $elimina_documento);
        }
        $elimina_extension_permitido = $this->elimina_extension_permitido();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al eliminar $elimina_extension_permitido',
                data: $elimina_extension_permitido);
        }

        $elimina_extension = (new doc_extension($this->link))->elimina_todo();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al eliminar extensiones', data: $elimina_extension);
        }
        return true;
    }

    protected function elimina_extension_permitido(): bool|array
    {

        $elimina_extension_permitido = (new doc_extension_permitido($this->link))->elimina_todo();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al eliminar extension permitido', data: $elimina_extension_permitido);
        }
        return true;
    }

    protected function existe_extension(int $id = 1): bool|array
    {
        $filtro = array();
        $filtro['doc_extension.id'] = $id;
        $existe_extension = (new doc_extension($this->link))->existe(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al verificar extension', data: $existe_extension);
        }
        return $existe_extension;
    }
    protected function existe_extension_permitido(int $id = 1): bool|array
    {
        $filtro = array();
        $filtro['doc_extension_permitido.id'] = $id;
        $existe_extension_permitido = (new doc_extension_permitido($this->link))->existe(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al verificar $existe_extension_permitido',
                data: $existe_extension_permitido);
        }
        return $existe_extension_permitido;
    }

    protected function inserta_extension(int $id = 1, string $codigo = '1', string $descripcion = 'pdf' ): bool|array
    {
        $existe_extension = $this->existe_extension();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al verificar extension', data: $existe_extension);
        }

        if(!$existe_extension) {
            $alta_extension = $this->alta_extension(descripcion: $descripcion);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al insertar extension', data: $alta_extension);
            }
        }

        return true;
    }

    protected function inserta_extension_permitido(int $id = 1, int $doc_extension_id = 1, 
                                                   int $doc_tipo_documento_id = 1): bool|array
    {
        $existe_extension_permitido = $this->existe_extension_permitido(id: $id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al verificar $existe_extension_permitido', data: $existe_extension_permitido);
        }

        if(!$existe_extension_permitido) {
            $alta_extension_permitido = $this->alta_extension_permitida(id:$id,doc_extension_id: $doc_extension_id,
                doc_tipo_documento_id: $doc_tipo_documento_id);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al insertar extension', data: $alta_extension_permitido);
            }
        }

        return true;
    }

}
