<?php
/**
 * Created by PhpStorm.
 * User: rlyon
 * Date: 2/10/2015
 * Time: 11:36 PM
 */

namespace Integralib;

class Enviroment
{

    protected $filename = 'integra.env';

    public function setEnvVariables()
    {
        define("MEDIA_FILES", "media/archivosJoomla/");

        $enviroment = $this->readEnviromentFile();

        define('XML_FILES_PATH', 'media/facturas/');

        if (method_exists($this, $enviroment['AMBIENTE'])) {
            define('ENVIROMENT_INTEGRA', $enviroment['AMBIENTE']);
            define('ENVIROMENT_TIMONE', $enviroment['AMBIENTE_TIMONE']);
            call_user_func(array($this, strtolower($enviroment['AMBIENTE'])));
        } else {
            $this->produccion();
        }
        $this->seedIntegradora();
    }

    private function readEnviromentFile()
    {
        $filename = __DIR__ . '/' . $this->filename;
        $buffer = array();
        $source_file = fopen($filename, "r") or die("Couldn't open $filename");

        $file = fread($source_file, 4096);
        $tmp = array_filter(explode("\n", $file));
        foreach ($tmp as $lineNum => $line) {
            $line = explode('=', $line);
            $buffer[$line[0]] = trim($line[1]);
        }

        return $buffer;
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
        define("FACTURA_ROUTE", 'api.timone-factura.mx/facturacion/');

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
        define("FACTURA_ROUTE", 'api.timone-factura.mx/facturacion/');

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
        define("FACTURA_ROUTE", 'factura.trama.mx/facturacion/');

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
        define("FACTURA_ROUTE", 'facturacion.iecce.mx/facturacion/');

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
        define("FACTURA_ROUTE", 'api.timone-factura.mx/facturacion/');

        define("SEPOMEX_SERVICE", "http://api.timone-sepomex.mx/sepomexes/");

        define("INTEGRADORA_UUID", 'd9e9f5c4fe2e4a0ebfbfeaa46c0bc528');
    }

    public function seedIntegradora($filename = 'qaintegradora')
    {
        $url = getcwd() . '/libraries/Integralib/' . $filename . '.json';

        if (file_exists($url)) {
            $json = file_get_contents($url);
            $json = json_decode($json);

            $integradoIdSeed = $json->integrado->integradoId;

            $db = \JFactory::getDbo();

            $query = $db->getQuery(true);
            $query->select('integradoId')
                ->from('#__integrado')
                ->where($db->quoteName('integradoId') . ' = ' . $db->quote($integradoIdSeed));

            $db->setQuery($query);
            $db->execute();

            try {

                if ($db->getNumRows() === 0) {
                    $db->transactionStart();

                    foreach ($json as $key => $value) {
                        if($key == 'instrumentos' || $key == 'datos_bancarios') {
                            foreach ($value as $key => $value) {
                                foreach ($value as $datos) {
                                    $datos->integradoId = $integradoIdSeed;
                                    $db->insertObject('#__'.$key, $datos);
                                }
                            }
                        }else{
                            $value->integradoId = $integradoIdSeed;
                            $db->insertObject('#__' . $key, $value);
                        }
                    }
                    $db->transactionCommit();
                }
            } catch (\RuntimeException $e) {
                $db->transactionRollback();

                \JLog::addLogger(
                    array(
                        'text_file' => date('d-m-Y').'_critical_emergency.php'
                    ),
                    \JLog::CRITICAL + \JLog::EMERGENCY,
                    array('enviroment')
                );
                \JLog::add('No se creo integradora, el sistema no puede operar, '.$e->getMessage(), \JLog::CRITICAL, 'enviroment');

                die('No es posible operar con el sistema, por favor contacte a su administrador.');
            }
        }
    }
}