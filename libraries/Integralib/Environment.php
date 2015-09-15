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

    protected $filename = 'qa.json';

    private $userHome = 'C:/Users/Ricardo/.integradora';

    public function setEnvVariables()
    {
        $environment = $this->readEnviromentFile();

        if ( isset( $environment ) && !empty( $environment ) ) {
//            integradora
            define('DEBUG', $environment['DEBUG']);
            define("MEDIA_FILES", $environment["MEDIA_FILES"]);
            define('XML_FILES_PATH', $environment['XML_FILES_PATH']);
            define('INTEGRADORA_UUID', $environment['INTEGRADORA_UUID']);

//            timone
            define("MIDDLE", 'http://' . $environment['MIDDLE']);
            define("PUERTO", $environment['PUERTOTIMONE']);
            define("TIMONE", $environment['CONTROLLERTIMONE']);
            define("TIMONE_ROUTE", MIDDLE . $environment['CONTROLLERTIMONE'] . 'integra/');
            define("TOKEN_ROUTE", MIDDLE . 'timone/oauth/');
            define('ENVIRONMENT_TIMONE', $environment['AMBIENTE_TIMONE']);

//            sepomex
            define('SEPOMEX_SERVICE', $environment['SEPOMEX_SERVICE']);

//            oauth credentials
            define('OAUTH_USERNAME', $environment['OAUTH']['USERNAME']);
            define('OAUTH_PASSWORD', $environment['OAUTH']['PASSWORD']);
            define('OAUTH_CLIENT_ID', $environment['OAUTH']['CLIENT_ID']);
            define('OAUTH_CLIENT_SECRET', $environment['OAUTH']['CLIENT_SECRET']);
            define('OAUTH_GRANT_TYPE', $environment['OAUTH']['GRANT_TYPE']);

//            invoicing
            define('TOKEN_FACT_ROUTE', $environment['TOKEN_FACT_ROUTE']);
            define('FACTURA_ROUTE', $environment['FACTURA_ROUTE']);

            SeedIntegradora::seedIntegradora($this->userHome, 'integradora-seed-'.$this->filename);
        } else {
            die( 'Configuration file corrupt or missing' );
        }

        unset( $environment );
    }

    private function readEnviromentFile()
    {
        $file = $this->userHome . '/' . $this->filename;

        if (file_exists($file)) {
            $json = file_get_contents($file);
            $data = json_decode($json, true);
        } else {
            die( 'Configuration file missing' );
        }

        return $data;
    }

}