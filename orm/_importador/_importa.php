<?php
namespace gamboamartin\documento\models\importador;

use gamboamartin\documento\models\doc_documento;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class _importa
{
    private errores $error;

    public function __construct()
    {
        $this->error = new errores();

    }

    final public function genera_doc_importa(int $doc_tipo_documento_id, PDO $link): array|stdClass
    {
        $modelo_doc_documento = (new doc_documento(link: $link));

        $doc_documento_ins = array();
        $doc_documento_ins['doc_tipo_documento_id'] = $doc_tipo_documento_id;

        $alta_doc = $modelo_doc_documento->alta_documento(registro: $doc_documento_ins,file: $_FILES['doc_origen']);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar documento', data: $alta_doc);
        }
        return $alta_doc;

    }


}