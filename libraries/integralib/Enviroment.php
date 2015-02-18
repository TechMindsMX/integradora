<?php
/**
 * Created by PhpStorm.
 * User: rlyon
 * Date: 2/10/2015
 * Time: 11:36 PM
 */

namespace Integralib;

class Enviroment {

	protected $filename = 'integra.env';

	public function setEnvVariables() {
		define( "MEDIA_FILES", "media/archivosJoomla/" );

		$enviroment = $this->readEnviromentFile();

		if ( method_exists($this, $enviroment['AMBIENTE']) ) {
			call_user_func( array($this, strtolower( $enviroment['AMBIENTE'] ) ) );
		} else {
			$this->produccion();
		}

	}

	private function readEnviromentFile() {
		$filename = __DIR__.'/'.$this->filename;
		$buffer = array();
		$source_file = fopen( $filename, "r" ) or die("Couldn't open $filename");

		$file = fread($source_file, 4096);
		$tmp = array_filter( explode( "\n" , $file) );
		foreach ( $tmp as $lineNum => $line ) {
			$line = explode('=', $line);
			$buffer[$line[0]] = trim($line[1]);
		}

		return $buffer;
	}

	public function integradora() {
		$middle           = "api-stage.timone.mx";
		$puertoTimOne     = "";
		$controllerTimOne = "/timone/services/";

		define( "MIDDLE", 'http://' . $middle );
		define( "PUERTO", $puertoTimOne );
		define( "TIMONE", $controllerTimOne );
		define( "TIMONE_ROUTE", $middle.$controllerTimOne.'integra/' );
		define( "FACTURA_ROUTE", 'api.timone-factura.mx/factura/' );

		define( "SEPOMEX_SERVICE", "http://api.timone-sepomex.mx/sepomexes/" );
	}

	public function qaintegradora() {
		$middle = "api-qa.timone.mx";
		$puertoTimOne =  "";
		$controllerTimOne =  "/timone/services/";

		define( "MIDDLE", 'http://'.$middle);
		define( "PUERTO", $puertoTimOne);
		define( "TIMONE", $controllerTimOne);
		define( "TIMONE_ROUTE", $middle.$controllerTimOne.'integra/' );
		define( "FACTURA_ROUTE", 'api.timone-factura.mx/factura/' );

		define( "SEPOMEX_SERVICE", "http://api.timone-sepomex.mx/sepomexes/" );
	}

	public function produccion() {
		$middle           = "integra.trama.mx";
		$puertoTimOne     = "";
		$controllerTimOne = "/timone/services/";

		define( "MIDDLE", 'http://' . $middle );
		define( "PUERTO", $puertoTimOne );
		define( "TIMONE", $controllerTimOne );
		define( "TIMONE_ROUTE", $middle.$controllerTimOne.'integra/' );
		define( "FACTURA_ROUTE", 'factura.trama.mx/factura/' );

		define("SEPOMEX_SERVICE", "http://sepomex.trama.mx/sepomexes/");
	}

	public function localhost() {
		$middle           = "api-stage.timone.mx";
		$puertoTimOne     = "";
		$controllerTimOne = "/timone/services/";

		define( "MIDDLE", 'http://' . $middle );
		define( "PUERTO", $puertoTimOne );
		define( "TIMONE", $controllerTimOne );
		define( "TIMONE_ROUTE", $middle.$controllerTimOne.'integra/' );
		define( "FACTURA_ROUTE", 'api.timone-factura.mx/factura/' );

		define( "SEPOMEX_SERVICE", "http://api.timone-sepomex.mx/sepomexes/" );
	}
}