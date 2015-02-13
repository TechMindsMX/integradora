<?php
/**
 * Clasee para validar los datos enviados por el formulario que lo solicite
 */
include_once 'catalogos.php';

class validador{
    protected $dataPost;
    protected $diccionario;
    protected $currentKey;
    protected $diccionario_value;
    protected $respuesta;
    private $errorMsg = array();

    public function  procesamiento ($data,$diccionario) {
        $this->respuesta = array();
        $this->dataPost = (array)$data;
        $this->diccionario = $diccionario;

        foreach ($this->diccionario as $key => $value) {
            $this->currentKey = $key;

            if ( !empty( $this->dataPost[ $key ] ) || array_key_exists('required', $value) ) {  // prueba si el campo esta vacio y no es requerido
                $failedValidations = array();
                foreach ( $value as $method => $this->diccionario_value ) {

                    if (method_exists ('validador',$method)) {
                        $respuestas[$method] = call_user_func (array ('validador', $method));
                        if (!$respuestas[$method]) {
                            // fallo de vlaidacion
                            $errors[$key]['success'] = false;
                            array_push( $failedValidations, $method );
                        }
                    }
                }
                if (!empty($failedValidations)) {
                    $errors[$key]['msg'] = implode('. ', $this->getMessages($failedValidations) );
                }
                $this->respuesta[$key] = isset($errors[$key]) ? $errors[$key] : true;

            }
        }

        return $this->respuesta;
    }

    protected function getMessages ($methods) {

        $msg = array();
        foreach ( $methods as $method ) {
            $msg[] = isset($this->errorMsg[$method]) ? $this->errorMsg[$method] : JText::_('ERROR_TIPO_DATOS_INCORRECTO');
        }

        return $msg;
    }

    public function allPassed(){
        $return = true;
        foreach ($this->respuesta as $value) {
            if(is_array($value)){
                $return = false;
            }
        }

        return $return;

    }

    public function getRespuestas() {
        return $this->respuesta;
    }

    protected function diccionaroErrores($tipo,$campo){
        $tipos = array('Longitud');
    }

    protected function email () {
        $email = $this->dataPost[$this->currentKey];
        $regex = '/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/';

        if (preg_match ($regex,
                        $email) == 1
        ) {
            $respuesta = true;
        } else {
            $respuesta = false;
        }

        return $respuesta;
    }

    protected function curp () {
        $curp = strtoupper($this->dataPost[$this->currentKey]);
        $regex = '/^[A-Z]{4}([0-9]{2})(1[0-2]|0[1-9])([0-3][0-9])([H M]{1})([A-Z]{2})([A-Z]{3})([A-Z0-9]{2})$/';

        if (preg_match ($regex,
                        $curp,
                        $coicidencias) == 1
        ) {
            $respuesta = true;
        } else {
            $respuesta = false;
        }

        return $respuesta;
    }

    protected  function date () {
        $regex = '/^(19|20)\d\d[\-\/.](0[1-9]|1[012])[\-\/.](0[1-9]|[12][0-9]|3[01])$/';

        if (preg_match ($regex, $this->dataPost[$this->currentKey]) == 1) {
            $respuesta = true;
        } else {
            $respuesta = false;
        }

        return $respuesta;
    }

    public function rfc () {
        $rfc = strtoupper($this->dataPost[$this->currentKey]);

        $regex = '/^[A-Z]{3,4}([0-9]{2})(1[0-2]|0[1-9])([0-3][0-9])([A-Z0-9]{3,4})$/';

        if (preg_match ($regex, $rfc, $coicidencias) == 1) {
            $respuesta = true;
        } else {
            $respuesta = false;
            $this->errorMsg[__FUNCTION__] = JText::sprintf('VALIDATION_RFC_INVALID', $this->diccionario_value);
        }

        return $respuesta;
    }

    public function rfc_fisica () {
        $rfc = strtoupper($this->dataPost[$this->currentKey]);

        $regex = '/^[A-Z]{4}([0-9]{2})(1[0-2]|0[1-9])([0-3][0-9])([A-Z0-9]{3,4})$/';

        if (preg_match ($regex, $rfc, $coicidencias) == 1) {
            $respuesta = true;
        } else {
            $respuesta = false;
        }

        return $respuesta;
    }

    public function rfc_moral () {
        $rfc = strtoupper($this->dataPost[$this->currentKey]);

        $regex = '/^[A-Z]{3}([0-9]{2})(1[0-2]|0[1-9])([0-3][0-9])([A-Z0-9]{3,4})$/';

        if (preg_match ($regex, $rfc, $coicidencias) == 1) {
            $respuesta = true;
        } else {
            $respuesta = false;
        }

        return $respuesta;
    }

    protected function length () {
        $this->errorMsg[__FUNCTION__] = JText::sprintf('VALIDATION_LENGTH', $this->diccionario_value);

        if (strlen ($this->dataPost[$this->currentKey]) == $this->diccionario_value) {
            $respuesta = true;
        } else {
            $respuesta = false;
        }
        return $respuesta;
    }

    protected function maxlength () {
        if (strlen ($this->dataPost[$this->currentKey]) <= $this->diccionario_value) {
            $respuesta = true;
        } else {
            $respuesta = false;
        }
        return $respuesta;
    }

    protected function minlenght () {
        if (strlen ($this->dataPost[$this->currentKey]) >= $this->diccionario_value) {
            $respuesta = true;
        } else {
            $respuesta = false;
        }
        return $respuesta;
    }

    public function banco_clabe () {
        $respuesta = false;

        if ( ! empty( $this->dataPost[ $this->currentKey ]  ) && strlen($this->dataPost[ $this->currentKey ])==18 ) {
            $clabe = $this->dataPost[$this->currentKey];
            $paso3 = 0;
            $clabeTmp = str_split ($clabe,17);

            $codigoVerificador = intval ($clabeTmp[1]);
            $clabesepa = str_split ($clabeTmp[0]);
            $ponderaciones = array (3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7);
            $claveBanco = isset($this->dataPost['db_banco_codigo']) ? $this->dataPost['db_banco_codigo'] : $this->diccionario_value;
            $claveBancoClabe = $clabesepa[0] . $clabesepa[1] . $clabesepa[2];

            foreach ($clabesepa as $key => $value) {
                $paso1[] = intval ($value) * $ponderaciones[$key];

                $paso2[] = $paso1[$key] % 10;

                $paso3 = $paso3 + $paso2[$key];
            }

            $paso4 = $paso3 % 10;
            $paso5 = 10 - $paso4;
            $paso6 = $paso5 % 10;
            $verificacion = $paso6 == $codigoVerificador;
            $verificabanco = $claveBanco == $claveBancoClabe;

            if ($verificacion && $verificabanco) {
                $respuesta = true;
            }
        }

        return $respuesta;
    }

    protected function number () {
        $regex = '/^[0-9\ ]+$/';
        if (preg_match ($regex, $this->dataPost[$this->currentKey]) == 1) {
            $respuesta = true;
        } else {
            $respuesta = false;
        }

        return $respuesta;
    }

    protected function float () {
        $regex  = '/^-?(?:\d+|\d*\.\d+)$/';
        if(preg_match($regex, $this->dataPost[$this->currentKey]) == 1){
            $respuesta = true;
        }else{
            $respuesta = false;
        }

        return $respuesta;
    }

    protected function string () {
        $regex = '/^[a-zA-Z ñ Ñ á Á éÉ íÍ óÓ úÚ \ . \']+$/';

        if (preg_match ($regex, $this->dataPost[$this->currentKey]) == 1) {
            $respuesta = true;
        } else {
            $respuesta = false;
        }

        return $respuesta;
    }

    protected function alphaNumber () {
        $regex = '/^[0-9a-zA-Z ñ Ñ á Á éÉ íÍ óÓ úÚ . \- \_]+$/';

        if (preg_match ($regex, $this->dataPost[$this->currentKey]) == 1) {
            $respuesta = true;
        } else {
            $respuesta = false;
        }

        return $respuesta;
    }

    protected function text(){
        $regex = '/[A-Za-z0-9_~\-!@#\$%\^&\*\(\) ((.*)\n*)]+$/';

        if (preg_match ($regex, $this->dataPost[$this->currentKey]) == 1) {
            $respuesta = true;
        } else {
            $respuesta = false;
        }

        return $respuesta;
    }

    protected function referenciaBancaria () {
        $regex = '/^[0-9a-zA-Z\-]{21}+$/';

        if (preg_match ($regex, $this->dataPost[$this->currentKey]) == 1) {
            $respuesta = true;
        } else {
            $respuesta = false;
        }

        return $respuesta;
    }

    protected function required( ){
        $this->errorMsg[__FUNCTION__] = JText::_('VALIDATION_FIELD_IS_REQUIRED');
        return $this->notNull();
    }

    protected function notNull (){
        $this->errorMsg[__FUNCTION__] = JText::_('VALIDATION_FIELD_IS_REQUIRED');

	    $input = $this->dataPost[$this->currentKey];
	    if(is_string($input)) {
	        $input = trim($input);
	    }

        return !empty($input);
    }

    protected function phone(){
        $regex =   '/^[1-9]{1}?[0-9]{9}$/';

        if(preg_match($regex, $this->dataPost[$this->currentKey])){
            $respuesta = true;
        } else {
            $respuesta = false;
        }

        return $respuesta;
    }

    protected function min()
    {
        $this->errorMsg[__FUNCTION__] = JText::sprintf('VALIDATION_MIN_VALUE', $this->diccionario_value);
        return floatval($this->dataPost[$this->currentKey]) >= $this->diccionario_value;
    }

    protected function max()
    {
        $this->errorMsg[__FUNCTION__] = JText::sprintf('VALIDATION_MAX_VALUE', $this->diccionario_value);
        return floatval($this->dataPost[$this->currentKey]) <= $this->diccionario_value;
    }

}