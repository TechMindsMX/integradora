<?php
/**
 * Clasee para validar los datos enviados por el formulario que lo solicite
 */
include_once 'catalogos.php';

class validador {
	public static function procesamiento($data, $diccionario, $seccion = null){
		foreach ($data as $key => $value) {
			$columna 	= substr($key, 3);
			$clave 		= substr($key, 0,3);
			
			if($clave == 'dp_' && ('personales' == $seccion) ){
				$post[$columna] = $value;
			}elseif($clave == 'de_' && ('empresa' == $seccion) ){
				$post[$columna] = $value;
			}elseif($clave == 'db_' && ('bancos' == $seccion) ){
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
	
	public static function valida_curp($data){
		$curp	= $data['curp'];
		$regex	= '/^[A-Z]{4}([0-9]{2})(1[0-2]|0[1-9])([0-3][0-9])([H M]{1})([A-Z]{2})([A-Z]{3})([A-Z0-9]{2})$/';
		
		if( preg_match($regex, $curp, $coicidencias) == 1){
			$respuesta = true;
		}else{
			$respuesta = false;
		}
		
		return $respuesta;
	}
	
	public static function valida_fecha_nacimiento($data){
		$fecha = $data['fecha_nacimiento'];
		$regex = '/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/';
		
		if( preg_match($regex, $fecha) == 1){
			$respuesta = true;
		}else{
			$respuesta = false;
		}
		
		return $respuesta;
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
	
	public static function valida_banco_clabe($data){
		$clabe				= $data['banco_clabe'];
		$clabe	= '012180028515732163';
	
		$paso3 				= 0;
		$clabeTmp			= str_split($clabe,17);
		$codigoVerificador	= intval($clabeTmp[1]);
		$clabesepa			= str_split($clabeTmp[0]);
		$ponderaciones 		= array(3,7,1,3,7,1,3,7,1,3,7,1,3,7,1,3,7);
		$claveBanco			= $data['banco_nombre'];
		$claveBancoClabe	= $clabesepa[0].$clabesepa[1].$clabesepa[2];
		
		foreach ($clabesepa as $key => $value) {
			$paso1[] = intval($value)*$ponderaciones[$key];
		
			$paso2[] = $paso1[$key]%10;
			
			$paso3 = $paso3+$paso2[$key];
		}
		
		$paso4 			= $paso3%10;
		$paso5 			= 10-$paso4;
		$paso6 			= $paso5%10;
		$verificacion	= $paso6 == $codigoVerificador;
		$verificabanco	= $claveBanco == $claveBancoClabe;
		
		if($verificacion and $verificabanco){
			$respuesta = true;
		}else{
			$respuesta = false;
		}
		
		return $respuesta;
	}
}
?>