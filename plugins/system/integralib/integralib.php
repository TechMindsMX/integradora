<?php
/**
 * Created by PhpStorm.
 * User: rlyon
 * Date: 2/10/2015
 * Time: 11:21 PM
 */
defined('_JEXEC') or die;

class plgSystemIntegralib extends JPlugin {

	public function onAfterInitialise() {
		$middle = "demo.trama.mx";
		$puertoTimOne =  "";
		$controllerTimOne =  "/timone/services/";
		$hostname = $middle.$puertoTimOne.$controllerTimOne;

		define("MIDDLE", 'http://'.$middle);
		define("PUERTO", $puertoTimOne);
		define("TIMONE", $controllerTimOne);
		define("SEPOMEX_SERVICE", "http://sepomex.trama.mx/sepomexes/");
//define("SEPOMEX_SERVICE", "http://api.timone-sepomex.mx/sepomexes/");
		define("MEDIA_FILES", "media/archivosJoomla/");

		JLoader::registerNamespace('Integralib', JPATH_LIBRARIES);
	}
}