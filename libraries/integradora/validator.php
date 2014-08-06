<?php
/**
 * Clasee para validar los datos enviados por el formulario que lo solicite
 */
class validador {
	public static function procesamiento($data, $diccionario, $seccion = null){
		foreach ($data as $key => $value) {
			$columna 	= substr($key, 3);
			$clave 		= substr($key, 0,3);
			
			if($clave == 'dp_' && ('personales' == $seccion) ){
				$post[$columna] = $value;
			}elseif($clave == 'de_' && ('empresa' == $seccion) ){
				$post[$columna] = $value;
			}elseif($clave == 'db_' && ('bancarios' == $seccion) ){
				$post[$columna] = $value;
			}
		}
		$post['integrado_id'] = $data['integrado_id'];
		$post['pers_juridica'] = $data['pers_juridica'];

		foreach ($post as $key => $value) {
			
			if(isset($diccionario[$key]['length']) ){
				$respuesta[$key] = self::validalength($value,$diccionario[$key]['length']);
			}
			$method = 'valida_'.$key;
			if(method_exists(new validador,$method) && ($value != '') ){
				$respuesta[$key] = call_user_func(array('validador',$method), $post);
			}
		}
		
		var_dump($respuesta);
		exit;
	}
	
	public static function valida_curp($a){
		echo 'en valida curp <br />';
	}
	
	public static function valida_fecha_nacimiento($a){
		echo 'en valida fecha<br />';
	}
	
	public static function valida_rfc($data){
		$tipoPersona 	= $data['pers_juridica'];
		$rfc			= $data['rfc'];
		$regex			= '/^[A-Z]{3,4}([0-9]{2})(1[0-2]|0[1-9])([0-3][0-9])([A-Z0-9]{3,4})$/';
		
		
		if( preg_match($regex, $rfc, $coicidencias) == 1){
			$respuesta = true;
		}else{
			$respuesta = false;
		}
		
		return $respuesta;
	}
	
	public static function validalength($valor,$length){
		if(strlen($valor) <= $length){
			$respuesta = true;
		}else{
			$respuesta = false;
		}
		
		return $respuesta;
	}
}
?>