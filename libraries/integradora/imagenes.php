<?php

class manejoImagenes {

	public static function cargar_imagen($tipo, $usuario, $archivos, $key) {
		$validaciones = (($tipo === 'image/jpeg') || ($tipo === 'image/gif') || ($tipo === 'image/png') );

			if ($validaciones && getimagesize($archivos["tmp_name"])) {
				move_uploaded_file($archivos["tmp_name"], MEDIA_FILES . $usuario.'_'.$key . ".jpg");
                $regreso = MEDIA_FILES . $usuario.'_'.$key . ".jpg";
			} elseif('application/pdf' == $archivos['type']) {
				move_uploaded_file($archivos["tmp_name"], MEDIA_FILES . $usuario.'_'.$key . ".pdf");
                $regreso = MEDIA_FILES . $usuario.'_'.$key . ".pdf";
			} else{
                $regreso = 'verificar';
			}

		$logdata = implode(' | ',array(JFactory::getUser()->id, JFactory::getSession()->get('integradoId', null, 'integrado'), __METHOD__.':'.__LINE__, json_encode( array($tipo, $archivos, $validaciones, $regreso) ) ) );
		JLog::add($logdata,JLog::DEBUG, 'bitacora');

        return $regreso;
	}

	public static function resize($uploaded, $ruta, $tipo, $nombre, $max_ancho, $max_alto) {
		$rutaImagenOriginal = $uploaded;

		switch($tipo) {
			case 'png' :
				$img_original = imagecreatefrompng($rutaImagenOriginal);
				break;
			case 'image/jpeg' :
				$img_original = imagecreatefromjpeg($rutaImagenOriginal);
				break;
			case 'gif' :
				$img_original = imagecreatefromgif($rutaImagenOriginal);
				break;
		}

		list($ancho, $alto) = getimagesize($rutaImagenOriginal);

		$x_ratio = $max_ancho / $ancho;
		$y_ratio = $max_alto / $alto;

		if (($ancho <= $max_ancho) && ($alto <= $max_alto)) {
			$ancho_final = $ancho;
			$alto_final = $alto;
		} elseif (($x_ratio * $alto) < $max_alto) {
			$alto_final = ceil($x_ratio * $alto);
			$ancho_final = $max_ancho;
		} else {
			$ancho_final = ceil($y_ratio * $ancho);
			$alto_final = $max_alto;
		}

		$tmp = imagecreatetruecolor($ancho_final, $alto_final);

		imagecopyresampled($tmp, $img_original, 0, 0, 0, 0, $ancho_final, $alto_final, $ancho, $alto);
		imagedestroy($img_original);

		$calidad = 90;
		imagejpeg($tmp, $ruta . $nombre . '.jpg', $calidad);
	}

	public static function resizeAndCrop($uploaded, $ruta, $fileName, $max_ancho, $max_alto) {

		$source_path = $uploaded;

		list($source_width, $source_height, $source_type) = getimagesize($source_path);

		switch ($source_type) {
			case IMAGETYPE_GIF :
				$source_gdim = imagecreatefromgif($source_path);
				break;
			case IMAGETYPE_JPEG :
				$source_gdim = imagecreatefromjpeg($source_path);
				break;
			case IMAGETYPE_PNG :
				$source_gdim = imagecreatefrompng($source_path);
				break;
		}

		$source_aspect_ratio = $source_width / $source_height;
		$desired_aspect_ratio = $max_ancho / $max_alto;

		if ($source_aspect_ratio > $desired_aspect_ratio) {
			/*
			 * Triggered when source image is wider
			 */
			$temp_height = $max_alto;
			$temp_width = ( int )($max_alto * $source_aspect_ratio);
		} else {
			/*
			 * Triggered otherwise (i.e. source image is similar or taller)
			 */
			$temp_width = $max_ancho;
			$temp_height = ( int )($max_ancho / $source_aspect_ratio);
		}

		/*
		 * Resize the image into a temporary GD image
		 */
		$temp_gdim = imagecreatetruecolor($temp_width, $temp_height);
		imagecopyresampled($temp_gdim, $source_gdim, 0, 0, 0, 0, $temp_width, $temp_height, $source_width, $source_height);

		/*
		 * Copy cropped region from temporary image into the desired GD image
		 */
		$x0 = ($temp_width - $max_ancho) / 2;
		$y0 = ($temp_height - $max_alto) / 2;
		$desired_gdim = imagecreatetruecolor($max_ancho, $max_alto);
		imagecopy($desired_gdim, $temp_gdim, 0, 0, $x0, $y0, $max_ancho, $max_alto);

		/*
		 * save the image in file-system or database
		 */
		$archivo = $ruta . $fileName . '.jpg';

		clearstatcache();
		imagejpeg($desired_gdim, $archivo, 90);
	}

}
?>