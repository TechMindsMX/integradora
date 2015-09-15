<?php
/**
 * Created by PhpStorm.
 * User: rlyon
 * Date: 2/10/2015
 * Time: 11:36 PM
 */

namespace Integralib;

final class Environment
{

    protected $filename = 'integra.env';

    private $userHome = 'C:/Users/Ricardo/.integradora';

    public function setEnvVariables()
    {
        $environment = $this->readEnviromentFile();

        define("MEDIA_FILES", $environment["MEDIA_FILES"]);
        define('XML_FILES_PATH', $environment['XML_FILES_PATH']);

        if (method_exists($this, $environment['AMBIENTE'])) {
            define('DEBUG', $environment['AMBIENTE']);
            define('ENVIROMENT_TIMONE', $environment['AMBIENTE_TIMONE']);
            call_user_func(array($this, strtolower($environment['AMBIENTE'])));
        } else {
            $this->produccion();
        }

        define('OAUTH_USERNAME', $environment['OAUTH_USERNAME']);
        define('OAUTH_PASSWORD', $environment['OAUTH_PASSWORD']);
        define('OAUTH_CLIENT_ID', $environment['OAUTH_CLIENT_ID']);
        define('OAUTH_CLIENT_SECRET', $environment['OAUTH_CLIENT_SECRET']);
        define('OAUTH_GRANT_TYPE', $environment['OAUTH_GRANT_TYPE']);

        SeedIntegradora::seedIntegradora($environment['AMBIENTE']);

        unset($environment);
    }

    private function readEnviromentFile()
    {
        $file = $this->userHome . '/' . $this->filename;

        if (file_exists($file)) {
            $json = file_get_contents($file);
            $data = json_decode($json, true);
        } else {
            die('Configuration file missing');
        }

        return $data;
    }

    public function integradora()
    {
        $middle = "api-stage.timone.mx/";
        $puertoTimOne = "";
        $controllerTimOne = "timone/services/";

        define("MIDDLE", 'http://' . $middle);
        define("PUERTO", $puertoTimOne);
        define("TIMONE", $controllerTimOne);
        define("TIMONE_ROUTE", $middle . $controllerTimOne . 'integra/');
        define("TOKEN_ROUTE", $middle . 'timone/oauth/');
        define("FACTURA_ROUTE", 'api.timone-factura.mx/facturacion/services/');
        define("TOKEN_FACT_ROUTE", 'api.timone-factura.mx/facturacion/oauth/');

        define("SEPOMEX_SERVICE", "http://api.timone-sepomex.mx/sepomexes/");

        define("INTEGRADORA_UUID", 'a4ac6c870f9411e5aaf3bc764e10ce72');
    }

    public function qaintegradora()
    {
        $middle = "api-qa.timone.mx/";
        $puertoTimOne = "";
        $controllerTimOne = "timone/services/";

        define("MIDDLE", 'http://' . $middle);
        define("PUERTO", $puertoTimOne);
        define("TIMONE", $controllerTimOne);
        define("TIMONE_ROUTE", $middle . $controllerTimOne . 'integra/');
        define("TOKEN_ROUTE", $middle . 'timone/oauth/');
        define("FACTURA_ROUTE", 'api.timone-factura.mx/facturacion/services/');
        define("TOKEN_FACT_ROUTE", 'api.timone-factura.mx/facturacion/oauth/');

        define("SEPOMEX_SERVICE", "http://api.timone-sepomex.mx/sepomexes/");

        define("INTEGRADORA_UUID", 'd9e9f5c4fe2e4a0ebfbfeaa46c0bc528');
    }

    public function produccion_sandbox()
    {
        $middle = "integra.trama.mx/";
        $puertoTimOne = "";
        $controllerTimOne = "timone/services/";

        define("MIDDLE", 'http://' . $middle);
        define("PUERTO", $puertoTimOne);
        define("TIMONE", $controllerTimOne);
        define("TIMONE_ROUTE", $middle . $controllerTimOne . 'integra/');
        define("TOKEN_ROUTE", $middle . 'timone/oauth/');
        define("FACTURA_ROUTE", 'factura.trama.mx/facturacion/services');
        define("TOKEN_FACT_ROUTE", 'factura.trama.mx/facturacion/oauth/');

        define("SEPOMEX_SERVICE", "http://sepomex.trama.mx/sepomexes/");

        define("INTEGRADORA_UUID", 'a4ac6c870f9411e5aaf3bc764e10ce72');
    }

    public function produccion()
    {
        $middle = "api.iecce.mx/";
        $puertoTimOne = "";
        $controllerTimOne = "timone/services/";

        define("MIDDLE", 'http://' . $middle);
        define("PUERTO", $puertoTimOne);
        define("TIMONE", $controllerTimOne);
        define("TIMONE_ROUTE", $middle . $controllerTimOne . 'integra/');
        define("TOKEN_ROUTE", $middle . 'timone/oauth/');
        define("FACTURA_ROUTE", 'facturacion.iecce.mx/facturacion/services');
        define("TOKEN_FACT_ROUTE", 'api.timone-factura.mx/facturacion/oauth/');

        define("SEPOMEX_SERVICE", "http://sepomex.trama.mx/sepomexes/");

        define("INTEGRADORA_UUID", '36763e75138011e5aaf3bc764e10ce72');
    }

    public function localhost()
    {
        $middle = "api-stage.timone.mx/";
        $puertoTimOne = "";
        $controllerTimOne = "timone/services/";

        define("MIDDLE", 'http://' . $middle);
        define("PUERTO", $puertoTimOne);
        define("TIMONE", $controllerTimOne);
        define("TIMONE_ROUTE", $middle . $controllerTimOne . 'integra/');
        define("TOKEN_ROUTE", $middle . 'timone/oauth/');
        define("FACTURA_ROUTE", 'api.timone-factura.mx/facturacion/services');
        define("TOKEN_FACT_ROUTE", 'api.timone-factura.mx/facturacion/oauth/');

        define("SEPOMEX_SERVICE", "http://api.timone-sepomex.mx/sepomexes/");

        define("INTEGRADORA_UUID", 'd9e9f5c4fe2e4a0ebfbfeaa46c0bc528');
    }


}