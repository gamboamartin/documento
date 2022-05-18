<?php
namespace gamboamartin\base_modelos;

use gamboamartin\errores\errores;
use gamboamartin\validacion\validacion;

/**
 *  REV
 *      P ORDER
 *      P INT
 *      PROBADO
 *      FALTA DOC
 */
class base_modelos extends validacion
{

    /**
     * FULL
     * Válida los datos de una lista de entrada, debe existir la clase y no pueden venir los elementos vacios
     * También debe existe el namespace models
     * @param string $seccion
     * @param string $accion
     * @return array|bool
     */
    public function valida_datos_lista_entrada(string $accion, string $seccion): array|bool
    {
        $seccion = str_replace('models\\', '', $seccion);
        $clase_model = 'models\\' . $seccion;

        if ($seccion === '') {
            return $this->error->error(mensaje: 'Error seccion no puede venir vacio',data:  $seccion,
                params: get_defined_vars());
        }
        if (!class_exists($clase_model)) {
            return $this->error->error(mensaje:'Error no existe la clase', data:$seccion, params: get_defined_vars());
        }
        if ($accion === '') {
            return $this->error->error(mensaje:'Error no existe la accion', data:$accion, params: get_defined_vars());
        }

        return true;
    }

    /**
     * PROBADO P ORDER P INT
     * Funcion para validar si una vivienda puede ser o no entregada
     * @param array $cliente
     * @return array|bool
     */
    public function valida_entrega_cliente(array $cliente): array|bool
    {
        $keys = array('estado_entrega_entregada','estado_entrega_inicial');
        $valida = $this->valida_statuses(keys:$keys, registro: $cliente);
        if(errores::$error){
            return $this->error->error('Error al validar cliente', $valida);
        }
        if ($cliente['estado_entrega_entregada'] === 'activo') {
            return $this->error->error('Error la vivienda ha sido entregada', $cliente);
        }
        if ($cliente['estado_entrega_inicial'] === 'activo') {
            return $this->error->error(
                'Error la vivienda no puede ser entregada porque cliente[estado_entrega_inicial] es activo',
                $cliente);
        }
        return true;
    }


    /**
     * FULL
     * Válida si una operacion en un registro está inactiva en su campo status data error
     * @param bool $aplica_transaccion_inactivo
     * @param int $registro_id
     * @param string $tabla
     * @param array $registro
     * @return array|bool
     */
    public function valida_transaccion_activa(bool  $aplica_transaccion_inactivo, array $registro, int $registro_id, string $tabla): array|bool
    {
        $tabla = trim($tabla);
        if($tabla === ''){
            return $this->error->error(mensaje: 'Error la tabla esta vacia', data: $tabla,params: get_defined_vars());
        }
        if (!$aplica_transaccion_inactivo) {
            if ($registro_id <= 0) {
                return $this->error->error(mensaje:'Error el id debe ser mayor a 0',data: $registro_id,
                    params: get_defined_vars());
            }
            $key = $tabla . '_status';
            if (!isset($registro[$key])) {
                return $this->error->error(mensaje:'Error no existe el registro con el campo ' . $tabla . '_status',
                    data:$registro,params: get_defined_vars());
            }
            if ($registro[$tabla . '_status'] === 'inactivo') {
                return $this->error->error(mensaje:'Error el registro no puede ser manipulado',data: $registro,
                    params: get_defined_vars());
            }
        }

        return true;
    }



}