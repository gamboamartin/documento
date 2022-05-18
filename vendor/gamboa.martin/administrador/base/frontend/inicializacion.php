<?php
namespace base\frontend;
use gamboamartin\errores\errores;
use JetBrains\PhpStorm\Pure;
use stdClass;


class inicializacion{
    private errores $error;
    private validaciones_directivas $validacion;
    #[Pure] public function __construct(){
        $this->error = new errores();
        $this->validacion = new validaciones_directivas();
    }

    /**
     *
     * P ORDER P INT
     * @param array $acciones_asignadas
     * @return array
     */
    public function acciones(array $acciones_asignadas): array{
        $acciones = array();
        foreach ($acciones_asignadas as $accion){
            if(!is_array($accion)){
                return $this->error->error('Error $acciones_asignadas[] debe ser un array', $accion);
            }
            $keys = array('accion_descripcion');
            $valida = $this->validacion->valida_existencia_keys(keys:  $keys, registro: $accion);
            if(errores::$error){
                return $this->error->error("Error al validar registro", $valida);
            }
            $acciones[] = $accion['accion_descripcion'];
        }
        return $acciones;
    }

    /**
     * PROBADO P ORDER P INT
     * @param array $elementos_lista
     * @return array|stdClass
     */
    private function asigna_datos_campo(array $elementos_lista): array|stdClass
    {
        if(count($elementos_lista) === 0){
            return $this->error->error("Error elemento_lista no puede venir vacio", $elementos_lista);
        }
        $campos = array();
        $etiqueta_campos = array();
        foreach ($elementos_lista as $registro){
            if(!is_array($registro)){
                return $this->error->error('Error $elementos_lista[] debe ser un array', $registro);
            }
            if(!isset($registro['elemento_lista_representacion'])){
                $registro['elemento_lista_representacion'] = '';
            }

            $valida = $this->validacion->valida_elemento_lista_template(registro: $registro);
            if(errores::$error){
                return $this->error->error("Error al validar registro", $valida);
            }
            $keys = array('elemento_lista_etiqueta');
            $valida = $this->validacion->valida_existencia_keys(keys: $keys, registro: $registro);
            if(errores::$error){
                return $this->error->error("Error al validar registro", $valida);
            }

            $datos_campo = $this->datos_campo($registro);
            if(errores::$error){
                return $this->error->error('Error al inicializar $datos_campo', $datos_campo);
            }
            $campos[] = $datos_campo;
            $etiqueta_campos[] = $registro['elemento_lista_etiqueta'];
        }

        $data = new stdClass();
        $data->campos = $campos;
        $data->etiqueta_campos = $etiqueta_campos;

        return $data;
    }

    public function campo_filtro(array $elemento_lista): array|string
    {
        $datas = $this->limpia_datas_minus($elemento_lista);
        if(errores::$error){
            return $this->error->error('Error al limpiar datos', $datas);
        }

        return $datas->seccion . '.' . $datas->campo;
    }
    /**
     * PROBADO P ORDER P INT
     * @param array $elementos_lista
     * @return stdClass|array
     */
    public function campos_lista(array $elementos_lista): stdClass|array{
        if(count($elementos_lista) === 0){
            return $this->error->error("Error elemento_lista no puede venir vacio", $elementos_lista);
        }


        $data = $this->asigna_datos_campo(elementos_lista: $elementos_lista);
        if(errores::$error){
            return $this->error->error('Error al inicializar $datos', $data);
        }



        return $data;

    }

    /**
     * PROBADO P ORDER P INT
     * @param array $registro
     * @return array
     */
    private function datos_campo(array $registro): array
    {
        if(!isset($registro['elemento_lista_representacion'])){
            $registro['elemento_lista_representacion'] = '';
        }
        $valida = $this->validacion->valida_elemento_lista_template($registro);
        if(errores::$error){
            return $this->error->error("Error al validar registro", $valida);
        }

        $datos_campo['nombre_campo'] = $registro['elemento_lista_descripcion'];
        $datos_campo['tipo'] = $registro['elemento_lista_tipo'];
        $datos_campo['representacion'] = $registro['elemento_lista_representacion'];

        return $datos_campo;
    }

    /**
     * P ORDER P INT
     * @param array $data
     * @param string $key
     * @return string
     */
    private function limpia_minus(array $data, string $key): string
    {
        $txt = $data[$key];
        $txt = trim($txt);
        return strtolower($txt);
    }

    private function limpia_datas_minus(array $elemento_lista): array|stdClass
    {
        $seccion = $this->limpia_minus($elemento_lista, 'seccion_descripcion');
        if(errores::$error){
            return $this->error->error('Error al limpiar txt', $seccion);
        }
        $campo = $this->limpia_minus($elemento_lista, 'elemento_lista_campo');
        if(errores::$error){
            return $this->error->error('Error al limpiar txt', $campo);
        }
        $data = new stdClass();
        $data->seccion = $seccion;
        $data->campo = $campo;
        return $data;

    }





}
