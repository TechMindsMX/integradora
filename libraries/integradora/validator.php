<?php
/**
 * Clasee para validar los datos enviados por el formulario que lo solicite
 */
include_once 'catalogos.php';

class validador
{

	public static function procesamiento ($data,
										  $diccionario,
										  $seccion = null) {
		foreach ($data as $key => $value) {

			if ($value != '') {
				if (isset($diccionario[$key]['length'])) {
					$minlength = isset($diccionario[$key]['minlength']) ? $diccionario[$key]['minlength'] : null;

					$respuesta[$key] = self::validalength ($value,
														   $diccionario[$key]['length'],
														   $minlength);

					if (!$respuesta[$key]) {
						$respuesta[$key] = self::salir (JText::_('ERROR_LONGITUD_INCORRECTA'));
					}
				}

				if (isset($diccionario[$key]['tipo'])) {
					$method = 'valida_' . $diccionario[$key]['tipo'];

					if (method_exists ('validador',
									   $method) && ($value != '')
					) {
						$respuesta[$key] = call_user_func (array ('validador', $method), $value, $key);
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

	protected function valida_email ($data,
									 $campo) {
		$email = $data[$campo];
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

	protected function valida_curp ($data,
									$campo) {
		$curp = $data[$campo];
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

	protected function valida_fecha_nacimiento ($data,
												$campo) {
		$fecha = $data[$campo];
		$regex = '/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/';

		if (preg_match ($regex,
						$fecha) == 1
		) {
			$respuesta = true;
		} else {
			$respuesta = false;
		}

		return $respuesta;
	}

	public function valida_rfc ($data,
								$campo) {
		$rfc = $data[$campo];

		if ($clave == 'de_') {
			$regex = '/^[A-Z]{3}([0-9]{2})(1[0-2]|0[1-9])([0-3][0-9])([A-Z0-9]{3,4})$/';
		} elseif ($clave == 'dp_') {
			$regex = '/^[A-Z]{4}([0-9]{2})(1[0-2]|0[1-9])([0-3][0-9])([A-Z0-9]{3,4})$/';
		} else {
			$regex = '/^[A-Z]{3,4}([0-9]{2})(1[0-2]|0[1-9])([0-3][0-9])([A-Z0-9]{3,4})$/';
		}

		if (preg_match ($regex,
						$rfc,
						$coicidencias) == 1
		) {
			$respuesta = true;
		} else {
			$respuesta = false;
		}

		return $respuesta;
	}

	protected function validalength ($valor,
									 $length,
									 $minlength = null) {
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

	public function valida_banco_clabe ($data,
										$campo) {
		$clabe = $data[$campo];
		$paso3 = 0;
		$clabeTmp = str_split ($clabe,
							   17);
		$codigoVerificador = intval ($clabeTmp[1]);
		$clabesepa = str_split ($clabeTmp[0]);
		$ponderaciones = array (3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7);
		$claveBanco = $data['db_banco_nombre'];
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

	protected function valida_number ($valor,
									   $campo) {
		$regex = '/^[0-9\ ]+$/';
		if (preg_match ($regex, $valor) == 1) {
			$respuesta = true;
		} else {
			$respuesta = false;
		}

		return $respuesta;
	}

	protected static function valida_float ($valor,
											$campo) {
		$regex	= '/^\d*\.{1}\d*?$/';
		if(preg_match($regex, $valor) == 1){
			$respuesta = true;
		}else{
			$respuesta = false;
		}

		return $respuesta;
	}

	protected function valida_string ($valor,
									   $campo) {
		$regex = '/^[a-zA-Z ñ Ñ á Á éÉ íÍ óÓ úÚ \ . \']+$/';

		if (preg_match ($regex, $valor) == 1) {
			$respuesta = true;
		} else {
			$respuesta = false;
		}

		return $respuesta;
	}

	protected function valida_alphaNumber ($valor,
										   $campo) {
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