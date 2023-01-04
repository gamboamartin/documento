<?php
namespace gamboamartin\documento\controllers;



class controlador_adm_grupo extends \gamboamartin\acl\controllers\controlador_adm_grupo {
    protected array $not_actions = array('usuarios','asigna_permiso');

}