<?php
namespace base\frontend;
use base\orm\modelo_base;
use gamboamartin\errores\errores;
use JetBrains\PhpStorm\Pure;
use PDO;
use stdClass;

class selects{
    private errores $error;
    private validaciones_directivas $validacion;
    #[Pure] public function __construct(){
        $this->error = new errores();
        $this->validacion = new validaciones_directivas();
    }

    /**
     * PARAMS ORDER P INT
     * @param string $tabla
     * @param string $name_input
     * @return string|array
     */
    private function campo_name_html(string $name_input, string $tabla): string|array
    {
        $tabla = trim($tabla);
        if($tabla === ''){
            return $this->error->error('Error tabla no puede venir vacia',$tabla);
        }
        $campo_name_html = $tabla. '_id';

        if($name_input !==''){
            $campo_name_html = $name_input;
        }
        return $campo_name_html;
    }
    /**
     * FULL
     * @param string $tabla
     * @return array
     */
    PUBLIC function columnas_base_select(string $tabla): array
    {
        $tabla = trim($tabla);
        if($tabla === ''){
            return $this->error->error(mensaje: 'Error tabla no puede venir vacio', data: $tabla,
                params: get_defined_vars());
        }
        $columnas[] = $tabla.'_id';
        $columnas[] = $tabla.'_codigo';
        $columnas[] = $tabla.'_descripcion';
        return $columnas;
    }

    /**
     * FULL
     * @param array $columnas
     * @param string $tabla
     * @return array
     */
    private function columnas_input_select(array $columnas, string $tabla): array
    {
        $tabla = trim($tabla);
        if($tabla === ''){
            return $this->error->error(mensaje: 'Error tabla no puede venir vacio', data: $tabla,
                params: get_defined_vars());
        }
        if(count($columnas) === 0){

            $columnas = $this->columnas_base_select(tabla: $tabla);
            if(errores::$error) {
                return $this->error->error(mensaje: 'Error al generar columnas base',data:  $columnas,
                    params: get_defined_vars());
            }
        }
        return $columnas;
    }

    /**
     *
     * @param bool $todos
     * @param PDO $link
     * @param string $name_modelo
     * @param array $filtro
     * @return array|stdClass
     */
    private function data_bd(bool $todos, PDO $link, string $name_modelo, array $filtro): array|stdClass
    {
        $valida = $this->validacion->valida_data_modelo(name_modelo: $name_modelo);
        if(errores::$error){
            return  $this->error->error("Error al validar modelo",$valida);
        }

        $modelo = (new modelo_base($link))->genera_modelo(modelo: $name_modelo);
        if (errores::$error) {
            return $this->error->error('Error al generar modelo', $modelo);
        }
        if(!$todos) {
            $resultado = $modelo->obten_registros_activos(order: array(),filtro:  $filtro);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al obtener registros', data: $resultado,
                    params: get_defined_vars());
            }
        }
        else{
            $resultado = $modelo->obten_registros();
            if (errores::$error) {
                return $this->error->error('Error al obtener registros del modelo '.$modelo->tabla, $resultado);
            }
        }
        return $resultado;
    }

    /**
     * FULL
     * @param string $valor
     * @param string $tabla
     * @param array $data_extra
     * @param array $data_con_valor
     * @param array $columnas
     * @return array|stdClass
     */
    private function data_for_select(array $columnas, array $data_con_valor,array $data_extra, string $tabla,
                                     string $valor): array|stdClass
    {
        $tabla = trim($tabla);
        if($tabla === ''){
            return $this->error->error(mensaje: 'Error tabla no puede venir vacio', data: $tabla,
                params: get_defined_vars());
        }

        $datos = new stdClass();
        $datos->valor = $valor;
        $datos->tabla = $tabla;
        $datos->data_extra = $data_extra;
        $datos->data_con_valor = $data_con_valor;

        $columnas = $this->columnas_input_select(columnas: $columnas,tabla:  $tabla);
        if(errores::$error) {
            return $this->error->error(mensaje: 'Error al generar columnas', data: $columnas,
                params: get_defined_vars());
        }

        $valida = $this->validacion->valida_estructura_input_base(columnas: $columnas,tabla: $tabla);
        if(errores::$error) {
            return $this->error->error(mensaje: 'Error al validar estructura de input', data: $valida,
                params: get_defined_vars());
        }

        $datos->columnas = $columnas;
        return $datos;
    }

    /**
     * P INT P ORDER
     * @param stdClass $datos
     * @return array|stdClass
     */
    private function data_html_for_select(stdClass $datos): array|stdClass
    {
        $keys = array('ln','size','tabla','cols','disabled','required','tipo_letra','aplica_etiqueta','name_input',
            'etiqueta','multiple','inline','registros');
        foreach($keys as $key){
            if(!isset($datos->$key)){
                return $this->error->error('Error no existe datos '.$key,$datos);
            }
        }

        $keys = array('cols');
        foreach($keys as $key){
            if(!is_numeric($datos->$key)){
                return $this->error->error('Error debe ser un entero datos '.$key,$datos);
            }
        }

        $keys = array('registros');
        foreach($keys as $key){
            if(!is_array($datos->$key)){
                return $this->error->error('Error debe ser un array datos '.$key,$datos);
            }
        }

        $ln_html = (new params_inputs())->ln(ln: $datos->ln, size: $datos->size);
        if(errores::$error) {
            return $this->error->error('Error al generar ln '.$datos->tabla,$ln_html);
        }

        $header_fg = (new forms())->header_form_group(cols: $datos->cols);
        if(errores::$error) {
            return $this->error->error('Error al generar header '.$datos->tabla,$header_fg);
        }

        $contenedor = $this->genera_contenedor_select(cols: $datos->cols,disabled: $datos->disabled,
            required: $datos->required,tabla: $datos->tabla, tipo_letra: $datos->tipo_letra,
            aplica_etiqueta:  $datos->aplica_etiqueta, etiqueta: $datos->etiqueta,inline: $datos->inline,
            multiple: $datos->multiple, name_input:  $datos->name_input, size: $datos->size);
        if(errores::$error){
            return $this->error->error('Error al generar contenedor', $contenedor);

        }

        $options_html = $this->options_html(datos:  $datos, registros: $datos->registros);
        if(errores::$error){
            return $this->error->error('Error al generar options', $options_html);
        }

        $datos->ln_html = $ln_html;
        $datos->header_fg = $header_fg;
        $datos->contenedor = $contenedor;
        $datos->options_html = $options_html;

        return $datos;


    }

    /**
     * P ORDER P INT PROBADO
     * @param array $value
     * @param string $columna
     * @param int $i
     * @param string $separador_select_columnas
     * @return array|string
     */
    private function data_option(string $columna, int $i, string $separador_select_columnas, array $value):array|string{
        $columna = trim($columna);
        if($columna === ''){
            return $this->error->error('Error la columna esta vacia', $columna);
        }
        $separador = '';
        if(!isset($value[$columna])){
            return $this->error->error('Error no existe dato en registro columna '.$columna, $value);
        }
        if($i > 0) {
            $separador .= $separador_select_columnas;
        }
        return $separador .' '.$value[$columna].' ';
    }

    /**
     * P INT P ORDER PROBADO
     * @param array $columnas
     * @param array $value
     * @param string $separador_select_columnas
     * @param int $i
     * @return array|string
     */
    private function data_options_select(array $columnas, int $i, string $separador_select_columnas,
                                        array $value): array|string
    {
        if(count($columnas) === 0){
            return $this->error->error('Error columnas esta vacio', $columnas);
        }
        $html = '';
        foreach ($columnas as $columna){
            $columna = trim($columna);
            if($columna === ''){
                return $this->error->error('Error la columna esta vacia', $columnas);
            }
            if(!isset($value[$columna])){
                return $this->error->error('Error no existe dato en registro columna '.$columna, $value);
            }

            $data_option = $this->data_option(columna: $columna,i: $i,
                separador_select_columnas: $separador_select_columnas, value: $value);
            if(errores::$error){
                return $this->error->error('Error al generar data option', $data_option);
            }
            $html.=$data_option;
            $i++;
        }
        return $html;
    }


    /**
     * ERROREV
     * @param bool $todos
     * @param PDO $link
     * @param string $name_modelo
     * @param array $filtro
     * @return array
     */
    private function data_select(bool $todos, PDO $link, string $name_modelo, array $filtro):array{

        $resultado = $this->data_bd(todos: $todos,link:  $link,name_modelo:  $name_modelo,filtro:  $filtro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener registros', data: $resultado,
                params: get_defined_vars());
        }

        if(count($resultado->registros) === 0){
            return $this->error->error('Error no existen registros del modelo '.$name_modelo, $resultado);
        }
        return $resultado->registros;
    }

    /**
     * PROBADO-PARAMS ORDER P INT
     * @param string $llave_json
     * @return array|stdClass
     */
    private function elemento_select_fijo(string $llave_json): array|stdClass
    {
        $llave_json = trim($llave_json);
        if($llave_json === ''){
            return $this->error->error('Error $llave_json esta vacia',$llave_json);
        }
        $explode_datos = explode(":", $llave_json);
        if(count($explode_datos)!==2){
            return $this->error->error('Error $llaves_valores debe venir en formato json string',$explode_datos);
        }

        $data = new stdClass();
        $data->key = trim($explode_datos[0]);
        $data->dato = trim($explode_datos[1]);

        return $data;
    }

    /**
     * PARAMS ORDER P INT
     * @param string $llaves_valores
     * @return array
     */
    public function elementos_for_select_fijo(string $llaves_valores): array
    {
        $elementos_select = array();
        $explode_llaves = explode(',',$llaves_valores);
        foreach ($explode_llaves as  $value){

            $data = (new selects())->elemento_select_fijo(llave_json: $value);
            if(errores::$error){
                return $this->error->error('Error al data para option', $data);
            }

            $elementos_select[$data->key] = $data->dato;
        }
        return $elementos_select;

    }



    /**
     * PARAMS ORDER P INT
     * Genera un contenedor div
     * @param string $tabla Tabla - estructura modelo sistema
     * @param int $cols Columnas para asignacion de html entre 1 y 12
     * @param bool $disabled si disabled el input queda deshabilitado
     * @param bool $required si required el input es obligatorio en su captura
     * @param string $tipo_letra
     * @param bool $aplica_etiqueta
     * @param string $name_input
     * @param string $etiqueta
     * @param bool $multiple
     * @param string $size
     * @param bool $inline
     * @return array|string informacion de div en forma html
     * @example
     *     $contenedor = $this->genera_contenedor_select($tabla,$cols,$disabled,$required,$tipo_letra, $aplica_etiqueta,$name_input,$etiqueta);
     * @uses  directivas
     * @internal $this->valida_elementos_base_input($tabla,$cols);
     * @internal $this->genera_texto_etiqueta($etiqueta_label, $tipo_letra);
     */
    private function genera_contenedor_select(int $cols,bool $disabled, bool $required, string $tabla,
                                              string $tipo_letra, bool $aplica_etiqueta = true, string $etiqueta = '',
                                              bool $inline = false, bool $multiple = false, string $name_input = '',
                                              string $size = 'md'):array|string{//FIN PROT

        $valida_elementos = $this->validacion->valida_elementos_base_input(cols: $cols, tabla: $tabla);
        if(errores::$error){
            return $this->error->error('Error al validar elementos',$valida_elementos);
        }


        $etiqueta_label_mostrable = (new etiquetas())->etiqueta_label(etiqueta:  $etiqueta, tabla: $tabla, tipo_letra: $tipo_letra);
        if (errores::$error) {
            return $this->error->error('Error al generar etiqueta',$etiqueta_label_mostrable);
        }


        $inline_html_lb = (new class_css())->inline_html_lb(inline:  $inline, size: $size);
        if (errores::$error) {
            return $this->error->error('Error al generar inline',$inline_html_lb);
        }

        $html = "";
        if($aplica_etiqueta) {
            $html .=  "<label class='col-form-label-$size $inline_html_lb' for='$tabla'>$etiqueta_label_mostrable</label>";
        }

        $campo_name_html = $this->campo_name_html(name_input:  $name_input, tabla: $tabla);
        if (errores::$error) {
            return $this->error->error('Error al generar campo name',$campo_name_html);
        }


        $css_class = $tabla. '_id';

        $etiqueta_title = $etiqueta_label_mostrable;
        $css_id = 'select_'.$tabla;

        $disabled_html = (new params_inputs())->disabled_html(disabled: $disabled);
        if (errores::$error) {
            return $this->error->error('Error al generar disabled',$disabled_html);
        }

        $required_html = (new params_inputs())->required_html(required: $required);
        if (errores::$error) {
            return $this->error->error('Error al generar required',$required_html);
        }

        $multiple_data = (new params_inputs())->multiple_html(multiple: $multiple);
        if (errores::$error) {
            return $this->error->error('Error al generar multiple',$multiple_data);
        }

        $inline_html = (new class_css())->inline_html(inline: $inline, size: $size);
        if (errores::$error) {
            return $this->error->error('Error al generar inline',$inline_html);
        }

        $html .= "<select name='$campo_name_html$multiple_data->data' $disabled_html class='$css_class  
                    selectpicker form-control form-control-$size $inline_html' data-live-search='true' title='$etiqueta_title'  
                    id='$css_id' $required_html $multiple_data->multiple >";

        return $html;
    }

    /**
     * P INT P ORDER
     * @param string $valor
     * @param string $tabla
     * @param array $data_extra
     * @param array $data_con_valor
     * @param array $value
     * @return array|string
     */
    private function html_content_option(array $data_con_valor, array $data_extra, string $tabla, string $valor, array $value): array|string
    {
        $valor_envio = (new values())->valor_envio(valor: $valor);
        if(errores::$error){
            return $this->error->error('Error al generar valor', $valor_envio);
        }

        $data_content = (new params_inputs())->data_content_option(data_con_valor: $data_con_valor,
            data_extra:  $data_extra, tabla:  $tabla, valor_envio: $valor_envio, value: $value);
        if(errores::$error){
            return $this->error->error('Error al generar data de contenido', $data_content);
        }

        $content_option = (new values())->content_option(data_extra_html:  $data_content->data_extra_html,
            selected: $data_content->selected, value_html: $data_content->value_html);
        if(errores::$error){
            return $this->error->error('Error al generar contenido option', $content_option);
        }
        return $content_option;
    }

    /**
     * P INT ERROREV
     * @param string $valor
     * @param string $tabla
     * @param array $data_extra
     * @param array $data_con_valor
     * @param array $columnas
     * @param PDO $link
     * @param array $registros
     * @param string $select_vacio_alta
     * @param array $filtro
     * @param bool $todos
     * @param bool $ln
     * @param string $size
     * @param int $cols
     * @param bool $disabled
     * @param bool $required
     * @param string $tipo_letra
     * @param bool $aplica_etiqueta
     * @param string $name_input
     * @param string $etiqueta
     * @param bool $multiple
     * @param bool $inline
     * @return array|stdClass
     */
    public function init_datos_select(string $valor, string $tabla, array $data_extra, array $data_con_valor,
                                         array $columnas, PDO $link, array $registros, string $select_vacio_alta,
                                         array $filtro, bool $todos, bool $ln, string $size, int $cols, bool $disabled,
                                         bool $required, string $tipo_letra, bool $aplica_etiqueta, string $name_input,
                                         string $etiqueta, bool $multiple, bool $inline ): array|stdClass
    {
        $datos = $this->data_for_select(columnas:$columnas, data_con_valor: $data_con_valor, data_extra:$data_extra,
            tabla: $tabla ,valor: $valor );
        if(errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener datos para columnas',data:  $datos,
                params: get_defined_vars());
        }


        $registros = $this->registros_for_select(link: $link,datos:  $datos,registros:  $registros,
            select_vacio_alta: $select_vacio_alta, filtro: $filtro,todos:  $todos,tabla:  $tabla);
        if(errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener registros '.$tabla,data: $registros,
                params: get_defined_vars());
        }


        $datos->ln = $ln;
        $datos->size = $size;
        $datos->cols = $cols;
        $datos->disabled = $disabled;
        $datos->required = $required;
        $datos->tipo_letra = $tipo_letra;
        $datos->aplica_etiqueta = $aplica_etiqueta;
        $datos->name_input = $name_input;
        $datos->etiqueta = $etiqueta;
        $datos->multiple = $multiple;
        $datos->inline = $inline;
        $datos->registros = $registros;

        $datos = $this->data_html_for_select(datos: $datos);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar options',data:  $datos, params: get_defined_vars());

        }

        return $datos;
    }

    /**
     *
     * Genera los registros a mostrar en un select
     * @param bool $select_vacio_alta
     * @param array $filtro
     * @param string $name_modelo
     * @param PDO $link
     * @param bool $todos
     *
     * @return array conjunto de datos del resultado del modelo
     * @example
     *      $registros = $this->obten_registros_select($select_vacio_alta,$modelo, $filtro,$todos);
     *
     * @uses directivas
     * @internal $modelo->obten_registros_activos(array(), $filtro);
     * @internal $modelo->obten_registros();
     * @internal $modelo->obten_registros_activos(array(), $filtro);
     */
    private function obten_registros_select(bool $select_vacio_alta, array $filtro,string $name_modelo, PDO $link,
                                            bool $todos= false): array
    {

        $registros = array();

        if(!$select_vacio_alta) {
            $registros = $this->data_select($todos, $link, $name_modelo, $filtro);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al obtener registros del modelo '.$name_modelo,
                    data: $registros, params: get_defined_vars());
            }
        }
        elseif(count($filtro)>0) {
            $registros = $this->registros_activos($link,$name_modelo,$filtro);
            if(errores::$error){
                return $this->error->error('Error al obtener registros', $registros);
            }
        }

        return $registros;
    }

    /**
     * P INT P ORDER
     * @param string $valor
     * @param string $tabla
     * @param array $data_extra
     * @param array $data_con_valor
     * @param array $value
     * @param array $columnas
     * @param string $separador_select_columnas
     * @param int $i
     * @return array|string
     */
    private function option_select(array $columnas, array $data_con_valor, array $data_extra, int $i,
                                   string $separador_select_columnas, string $tabla, string $valor,  array $value): array|string
    {

        if(count($columnas) === 0){
            return $this->error->error('Error columnas esta vacio', $columnas);
        }

        $content_option = $this->html_content_option(data_con_valor: $data_con_valor, data_extra: $data_extra,
            tabla: $tabla, valor: $valor,value:  $value);
        if(errores::$error){
            return $this->error->error('Error al generar contenido option', $content_option);
        }



        $data_options = $this->data_options_select(columnas: $columnas, i:  $i,
            separador_select_columnas:  $separador_select_columnas, value:  $value);
        if(errores::$error){
            return $this->error->error('Error al generar data options en tabla '.$tabla, $data_options);
        }
        return  "<option $content_option > $data_options </option>";


    }

    /**
     * PARAMS ORDER P INT
     * @param string $key
     * @param string $value_data
     * @param string $value_select
     * @return array|string
     */
    private function option_for_select(string $key, string $value_data, string $value_select): array|string
    {
        $selected = (new selects())->selected_value(value_base: $value_data,value_tabla: $value_select);
        if(errores::$error){
            return $this->error->error('Error al generar selected', $selected);
        }

        $option = (new selects())->option_value(key: $key, selected: $selected, value: $value_data);
        if(errores::$error){
            return $this->error->error('Error al generar option', $option);
        }
        return $option;
    }

    /**
     * PARAMS ORDER P INT
     * @param array $elementos_select
     * @param string $valor
     * @return array|string
     */
    public function options_for_select(array $elementos_select, string $valor): array|string
    {
        $options = '';
        foreach ($elementos_select as $key => $value){

            $option = (new selects())->option_for_select(key:  $key, value_data: $value,value_select:  $valor);
            if(errores::$error){
                return $this->error->error('Error al generar option', $option);
            }

            $options .= $option;
        }
        return $options;
    }


    /**
     * PARAMS ORDER P INT
     * @param string $key
     * @param string $selected
     * @param string $value
     * @return string
     */
    private function option_value(string $key, string $selected, string $value): string
    {
        return "<option value = '$value' $selected>".$key."</option>";
    }



    /**
     * P INT P ORDER
     * @param array $registros
     * @param stdClass $datos
     * @return array|string
     */
    private function options_html(stdClass $datos, array $registros): array|string
    {

        $keys = array('valor','tabla','data_extra','data_con_valor','columnas');
        foreach($keys aS $key){
            if(!isset($datos->$key)){
                return $this->error->error('Error datos->'.$key.' Debe existir', $datos);
            }
        }

        $keys = array('data_extra','data_con_valor','columnas');
        foreach($keys as $key){
            if(!is_array($datos->$key)){
                return $this->error->error('Error datos->'.$key.' Debe ser un array', $datos);
            }
        }


        $html = '';
        $i = 0;
        $separador_select_columnas = ' ';
        foreach ($registros as $key => $value) {

            if(!is_array($value)){
                return $this->error->error('Error al value debe ser un array', array('value'=>$value,'key'=>$key));
            }

            $option_select = $this->option_select(columnas:  $datos->columnas, data_con_valor: $datos->data_con_valor,
                data_extra:  $datos->data_extra,  i: $i, separador_select_columnas: $separador_select_columnas,
                tabla: $datos->tabla, valor: $datos->valor, value: $value);
            if(errores::$error){
                return $this->error->error('Error al generar  option', $option_select);
            }
            $html.=$option_select;
            $i = 0;

        }
        return $html;
    }

    private function registros_activos(PDO $link, string $name_modelo, array $filtro){
        $modelo = (new modelo_base($link))->genera_modelo($name_modelo);
        if (errores::$error) {
            return $this->error->error('Error al generar modelo', $modelo);
        }
        $resultado = $modelo->obten_registros_activos(array(), $filtro);
        if(errores::$error){
            return $this->error->error('Error al obtener registros', $resultado);
        }
        return $resultado['registros'];
    }

    /**
     * ERROREV
     * @param PDO $link
     * @param stdClass $datos
     * @param array $registros
     * @param string $select_vacio_alta
     * @param array $filtro
     * @param bool $todos
     * @param string $tabla
     * @return array
     */
    private function registros_for_select(PDO $link, stdClass $datos, array $registros, string $select_vacio_alta,
                                          array $filtro, bool $todos, string $tabla): array
    {
        if(!isset($datos->tabla)){
            return $this->error->error(mensaje: 'Error no existe tabla en datos',data: $datos,
                params: get_defined_vars());
        }
        if(trim($datos->tabla) === ''){
            return $this->error->error(mensaje: 'Error tabla esta vacia',data: $datos, params: get_defined_vars());
        }

        $registros = $this->registros_select($registros, $select_vacio_alta, $filtro, $todos,$tabla, $link);
        if(errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener registros '.$tabla,data: $registros,
                params: get_defined_vars());
        }

        return $registros;
    }

    /**
     * ERROREV
     * @param array $registros
     * @param bool $select_vacio_alta
     * @param array $filtro
     * @param bool $todos
     * @param string $name_modelo
     * @param PDO $link
     * @return array
     */
    private function registros_select(array $registros, bool $select_vacio_alta, array $filtro, bool $todos, string $name_modelo, PDO $link): array
    {

        if(count($registros)===0 ) {
            $registros = $this->obten_registros_select($select_vacio_alta, $filtro, $name_modelo, $link,$todos);
            if(errores::$error) {
                return $this->error->error(mensaje: 'Error al obtener registros '.$name_modelo,data: $registros,
                    params: get_defined_vars());
            }
        }
        return $registros;
    }

    /**
     * PROBADO-PARAMS ORDER P INT
     * @param string $value_base
     * @param string $value_tabla
     * @return string|array
     */
    private function selected_value(string $value_base, string $value_tabla): string|array
    {
        $value_base = trim($value_base);
        $value_tabla = trim($value_tabla);

        if($value_base === ''){
            return $this->error->error('Error $value_base esta vacio ',$value_base);
        }


        $selected = '';
        if($value_base === $value_tabla){
            $selected = 'selected';
        }
        return $selected;
    }

}
