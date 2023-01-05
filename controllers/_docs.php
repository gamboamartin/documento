<?php
namespace gamboamartin\documento\controllers;

class _docs {

    public function download(bool $header, string $ruta_absoluta): string
    {
        if($header) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($ruta_absoluta) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($ruta_absoluta));
            flush(); // Flush system output buffer
            readfile($ruta_absoluta);
        }
        return file_get_contents($ruta_absoluta);
    }

}