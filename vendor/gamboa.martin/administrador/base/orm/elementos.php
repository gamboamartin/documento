<?php
namespace base\orm;
use gamboamartin\errores\errores;
use JetBrains\PhpStorm\Pure;

class elementos{
    private errores $error;
    private validaciones $validacion;
    #[Pure] public function __construct(){
        $this->error = new errores();
        $this->validacion = new validaciones();
    }

    /**
     * P ORDER P INT
     * @param array $campo
     * @return string|array
     */
    public function campo_tabla_externa(array $campo): string|array
    {
        $keys = array('elemento_lista_campo');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys, registro: $campo);
        if(errores::$error){
            return $this->error->error("Error al validar campo", $valida);
        }

        $aplica_el_campo = !isset($campo['elemento_lista_campo_tabla_externa'])
            ||(string)$campo['elemento_lista_campo_tabla_externa']==='';

        if($aplica_el_campo){
            $campo_tabla_externa = $campo['elemento_lista_campo'];
        }
        else{
            $campo_tabla_externa = $this->data_campo_tabla_externa($campo);
            if(errores::$error){
                return $this->error->error("Error al obtener campo tabla externa", $campo_tabla_externa);
            }
        }
        return $campo_tabla_externa;
    }

    /**
     * P ORDER P INT
     * @param array $campo
     * @return string
     */
    public function columnas_elemento_lista(array $campo): string
    {
        if(!isset($campo['elemento_lista_columnas']) ||(string)$campo['elemento_lista_columnas']===''){
            $elemento_lista_columnas = '';
        }
        else{
            $elemento_lista_columnas = $campo['elemento_lista_columnas'];
        }
        return $elemento_lista_columnas;
    }

    /**
     * P ORDER P INT PROBADO
     * @param array $campo
     * @return array|string
     */
    private function data_campo_tabla_externa(array $campo): array|string
    {
        $keys = array('elemento_lista_campo_tabla_externa');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys, registro: $campo);
        if(errores::$error){
            return $this->error->error("Error al validar campo", $valida);
        }
        return (string)$campo['elemento_lista_campo_tabla_externa'];
    }

    /**
     * P ORDER P INT
     * @param array $campo
     * @return string
     */
    public function elemento_lista_css_id(array $campo): string
    {
        if(!isset($campo['elemento_lista_css_id']) ||(string)$campo['elemento_lista_css_id']===''){
            $elemento_lista_css_id = '';
        }
        else{
            $elemento_lista_css_id = $campo['elemento_lista_css_id'];
        }
        return $elemento_lista_css_id;
    }

    /**
     * P ORDER P INT
     * @param array $campo
     * @return string
     */
    public function llaves_valores(array $campo): string
    {
        if(!isset($campo['elemento_lista_llaves_valores']) ||(string)$campo['elemento_lista_llaves_valores']===''){
            $elemento_lista_llaves_valores = '';
        }
        else{
            $elemento_lista_llaves_valores = $campo['elemento_lista_llaves_valores'];
        }
        return $elemento_lista_llaves_valores;
    }

    /**
     * P ORDER P INT
     * @param array $campo
     * @return string
     */
    public function pattern(array $campo): string
    {
        if(!isset($campo['elemento_lista_pattern']) ||(string)$campo['elemento_lista_pattern']===''){
            $elemento_lista_pattern = '';
        }
        else{
            $elemento_lista_pattern = $campo['elemento_lista_pattern'];
        }
        return $elemento_lista_pattern;
    }

    /**
     * P ORDER P INT
     * @param array $campo
     * @return string
     */
    public function separador_columnas(array $campo): string
    {
        if(!isset($campo['elemento_lista_separador_select_columnas']) ||(string)$campo['elemento_lista_separador_select_columnas']===''){
            $elemento_lista_separador_select_columnas = '';
        }
        else{
            $elemento_lista_separador_select_columnas = $campo['elemento_lista_separador_select_columnas'];
        }
        return $elemento_lista_separador_select_columnas;
    }

    /**
     * P ORDER P INT
     * @param array $campo
     * @return string
     */
    public function tabla_ext_renombrada(array $campo): string
    {
        if(!isset($campo['elemento_lista_tabla_externa_renombrada']) ||(string)$campo['elemento_lista_tabla_externa_renombrada']===''){
            $elemento_lista_tabla_externa_renombrada = '';
        }
        else{
            $elemento_lista_tabla_externa_renombrada = $campo['elemento_lista_tabla_externa_renombrada'];
        }
        return $elemento_lista_tabla_externa_renombrada;
    }

}
