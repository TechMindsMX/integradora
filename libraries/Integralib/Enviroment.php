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

	    define( 'XML_FILES_PATH', 'media/facturas/');

	    if ( method_exists($this, $enviroment['AMBIENTE']) ) {
            define('ENVIROMENT_INTEGRA', $enviroment['AMBIENTE']);
            define('ENVIROMENT_TIMONE', $enviroment['AMBIENTE_TIMONE'] );
            call_user_func( array($this, strtolower( $enviroment['AMBIENTE']) ) );
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
        $middle           = "api-stage.timone.mx/";
        $puertoTimOne     = "";
        $controllerTimOne = "timone/services/";

        define( "MIDDLE", 'http://' . $middle );
        define( "PUERTO", $puertoTimOne );
        define( "TIMONE", $controllerTimOne );
        define( "TIMONE_ROUTE", $middle.$controllerTimOne.'integra/' );
        define( "TOKEN_ROUTE", $middle.'timone/oauth/' );
        define( "FACTURA_ROUTE", 'api.timone-factura.mx/facturacion/' );

        define( "SEPOMEX_SERVICE", "http://api.timone-sepomex.mx/sepomexes/" );

        define("INTEGRADORA_UUID", 'a4ac6c870f9411e5aaf3bc764e10ce72');
    }

    public function qaintegradora() {
        $middle = "api-qa.timone.mx/";
        $puertoTimOne =  "";
        $controllerTimOne =  "timone/services/";

        define( "MIDDLE", 'http://'.$middle);
        define( "PUERTO", $puertoTimOne);
        define( "TIMONE", $controllerTimOne);
        define( "TIMONE_ROUTE", $middle.$controllerTimOne.'integra/' );
	    define( "TOKEN_ROUTE", $middle.'timone/oauth/' );
        define( "FACTURA_ROUTE", 'api.timone-factura.mx/facturacion/' );

        define( "SEPOMEX_SERVICE", "http://api.timone-sepomex.mx/sepomexes/" );

        define("INTEGRADORA_UUID", 'd9e9f5c4fe2e4a0ebfbfeaa46c0bc528');
    }

    public function produccion_sandbox() {
        $middle           = "integra.trama.mx/";
        $puertoTimOne     = "";
        $controllerTimOne = "timone/services/";

        define( "MIDDLE", 'http://' . $middle );
        define( "PUERTO", $puertoTimOne );
        define( "TIMONE", $controllerTimOne );
        define( "TIMONE_ROUTE", $middle.$controllerTimOne.'integra/' );
	    define( "TOKEN_ROUTE", $middle.'timone/oauth/' );
        define( "FACTURA_ROUTE", 'factura.trama.mx/facturacion/' );

        define("SEPOMEX_SERVICE", "http://sepomex.trama.mx/sepomexes/");

        define("INTEGRADORA_UUID", 'a4ac6c870f9411e5aaf3bc764e10ce72');
    }

    public function produccion() {
        $middle           = "api.iecce.mx/";
        $puertoTimOne     = "";
        $controllerTimOne = "timone/services/";

        define( "MIDDLE", 'http://' . $middle );
        define( "PUERTO", $puertoTimOne );
        define( "TIMONE", $controllerTimOne );
        define( "TIMONE_ROUTE", $middle.$controllerTimOne.'integra/' );
        define( "TOKEN_ROUTE", $middle.'timone/oauth/' );
        define( "FACTURA_ROUTE", 'facturacion.iecce.mx/facturacion/' );

        define("SEPOMEX_SERVICE", "http://sepomex.trama.mx/sepomexes/");

        define("INTEGRADORA_UUID", '36763e75138011e5aaf3bc764e10ce72');
    }

    public function localhost() {
        $middle           = "api-stage.timone.mx/";
        $puertoTimOne     = "";
        $controllerTimOne = "timone/services/";

        define( "MIDDLE", 'http://' . $middle );
        define( "PUERTO", $puertoTimOne );
        define( "TIMONE", $controllerTimOne );
        define( "TIMONE_ROUTE", $middle.$controllerTimOne.'integra/' );
	    define( "TOKEN_ROUTE", $middle.'timone/oauth/' );
        define( "FACTURA_ROUTE", 'api.timone-factura.mx/facturacion/' );

        define( "SEPOMEX_SERVICE", "http://api.timone-sepomex.mx/sepomexes/" );

        define("INTEGRADORA_UUID", 'a4ac6c870f9411e5aaf3bc764e10ce72');
    }
}