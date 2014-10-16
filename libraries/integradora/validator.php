<?php
/**
 * Clasee para validar los datos enviados por el formulario que lo solicite
 */
include_once 'catalogos.php';

class validador{
    public $dataPost;

    public function procesamiento ($data,$diccionario,$seccion = null) {
        $this->dataPost = $data;
		foreach ($data as $key => $value) {

			if ($value != '') {
				if (isset($diccionario[$key]['length'])) {
					$minlength = isset($diccionario[$key]['minlength']) ? $diccionario[$key]['minlength'] : null;

					$respuesta[$key] = self::validalength ($value, $diccionario[$key]['length'], $minlength);

					if (!$respuesta[$key]) {
						$respuesta[$key] = self::salir (JText::_('ERROR_LONGITUD_INCORRECTA'));
					}
				}

				if (isset($diccionario[$key]['tipo'])) {
					$method = 'valida_' . $diccionario[$key]['tipo'];

					if (method_exists ('validador',$method) && ($value != '')) {
						$respuesta[$key] = call_user_func (array ('validador', $method), $value);
						if (!$respuesta[$key]) {
							$respuesta[$key] = self::salir (JText::_('ERROR_TIPO_DATOS_INCORRECTO'));
						}
					}

				}

			}
		}

		return $respuesta;
	}

	protected static function salir ($msg) {
		$response = array ('success' => false, 'msg' => $msg);

		return $response;
	}

	public static function noErrors ($array) {
		foreach($array as $value){
			if(is_array($value)) {
				return false;
			}
		}
		return true;
	}

	protected function valida_email ($data) {
		$email = $data;
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

	protected function valida_curp ($data) {
		$curp = $data;
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

	protected function valida_fecha ($data) {
		$fecha = $data;
		$regex = '/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/';

		if (preg_match ($regex,$fecha) == 1) {
			$respuesta = true;
		} else {
			$respuesta = false;
		}

		return $respuesta;
	}

	public function valida_rfc ($data) {
		$rfc = $data;

		//if ($clave == 'de_') {
		//	$regex = '/^[A-Z]{3}([0-9]{2})(1[0-2]|0[1-9])([0-3][0-9])([A-Z0-9]{3,4})$/';
		//} elseif ($clave == 'dp_') {
		//	$regex = '/^[A-Z]{4}([0-9]{2})(1[0-2]|0[1-9])([0-3][0-9])([A-Z0-9]{3,4})$/';
		//} else {
			$regex = '/^[A-Z]{3,4}([0-9]{2})(1[0-2]|0[1-9])([0-3][0-9])([A-Z0-9]{3,4})$/';
		//}

		if (preg_match ($regex, $rfc, $coicidencias) == 1
		) {
			$respuesta = true;
		} else {
			$respuesta = false;
		}

		return $respuesta;
	}

	protected function validalength ($valor, $length, $minlength = null) {
		if (is_null ($minlength)) {
			if (strlen ($valor) <= $length) {
				$respuesta = true;
			} else {
				$respuesta = false;
			}
		} else {
			if ((strlen ($valor) == $length) && (strlen ($valor) == $minlength)) {
				$respuesta = true;
			} else {
				$respuesta = false;
			}
		}

		return $respuesta;
	}

	public function valida_banco_clabe ($data) {
		$clabe = $data;
		$paso3 = 0;
		$clabeTmp = str_split ($clabe,17);

		$codigoVerificador = intval ($clabeTmp[1]);
		$clabesepa = str_split ($clabeTmp[0]);
		$ponderaciones = array (3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7);
		$claveBanco = $this->dataPost['db_banco_codigo'];
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
		} else {
			$respuesta = false;
		}

		return $respuesta;
	}

	protected function valida_number ($valor) {
		$regex = '/^[0-9\ ]+$/';
		if (preg_match ($regex, $valor) == 1) {
			$respuesta = true;
		} else {
			$respuesta = false;
		}

		return $respuesta;
	}

	protected static function valida_float ($valor) {
        $regex  = '/^-?(?:\d+|\d*\.\d+)$/';
		if(preg_match($regex, $valor) == 1){
			$respuesta = true;
		}else{
			$respuesta = false;
		}

		return $respuesta;
	}

	protected function valida_string ($valor) {
		$regex = '/^[a-zA-Z ñ Ñ á Á éÉ íÍ óÓ úÚ \ . \']+$/';

		if (preg_match ($regex, $valor) == 1) {
			$respuesta = true;
		} else {
			$respuesta = false;
		}

		return $respuesta;
	}

	protected function valida_alphaNumber ($valor) {
		$regex = '/^[0-9a-zA-Z ñ Ñ á Á éÉ íÍ óÓ úÚ . ]+$/';

		if (preg_match ($regex, $valor) == 1) {
			$respuesta = true;
		} else {
			$respuesta = false;
		}

		return $respuesta;
	}
}

?>