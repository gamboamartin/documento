<?php

use base\conexion;
use gamboamartin\errores\errores;

$_SESSION['usuario_id'] = 2;

require "init.php";
require 'vendor/autoload.php';

$con = new conexion();
$link = conexion::$link;

$link->beginTransaction();
$administrador = new gamboamartin\administrador\instalacion\instalacion();

$instala = $administrador->instala(link: $link);
if(errores::$error){
    (new errores())->error(mensaje: 'Error al instalar',data:  $instala);
    if($link->inTransaction()) {
        $link->rollBack();
    }
    $out = array_reverse(errores::$out);
    foreach ($out as $msj){
        echo $msj;
        echo "<br>";
        echo "<hr>";
    }
    die('Error');
}



$documento = new gamboamartin\documento\instalacion\instalacion();

$instala = $documento->instala(link: $link);
if(errores::$error){
    (new errores())->error(mensaje: 'Error al instalar',data:  $instala);
    if($link->inTransaction()) {
        $link->rollBack();
    }
    $out = array_reverse(errores::$out);
    foreach ($out as $msj){
        echo $msj;
        echo "<br>";
        echo "<hr>";
    }
    die('Error');
}


if($link->inTransaction()) {
    $link->commit();
}


