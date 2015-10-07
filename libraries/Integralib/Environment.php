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

    public static function setEnvVariables($path, $filename)
    {
        $environment = self::readEnviromentFile($path, $filename);

        if ( isset( $environment ) && !empty( $environment ) ) {
//            integradora
            define('DEBUG', $environment['DEBUG']);
            define("MEDIA_FILES", $environment["MEDIA_FILES"]);
            define('XML_FILES_PATH', $environment['XML_FILES_PATH']);
            define('INTEGRADORA_UUID', $environment['INTEGRADORA_UUID']);
            define('INTEGRADOID_CONCENTRADORA', $environment['INTEGRADOID_CONCENTRADORA']);

//            timone
            define("MIDDLE", $environment['MIDDLE']);
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

//            File uploads
            define('MAX_UPLOAD_FILE_SIZE', self::file_upload_max_size());

        } else {
            die( 'Configuration file corrupt or missing' );
        }

        unset( $environment );
    }

    private static function readEnviromentFile($path, $filename)
    {
        $file = $path . $filename;

        if (file_exists($file)) {
            $json = file_get_contents($file);
            $data = json_decode($json, true);
        } else {
            die( 'Configuration file missing' );
        }

        return $data;
    }

    // Returns a file size limit in bytes based on the PHP upload_max_filesize
    // and post_max_size
    private static function file_upload_max_size() {
        static $max_size = -1;

        if ($max_size < 0) {
            // Start with post_max_size.
            $max_size = self::parse_size(ini_get('post_max_size'));

            // If upload_max_size is less, then reduce. Except if upload_max_size is
            // zero, which indicates no limit.
            $upload_max = self::parse_size(ini_get('upload_max_filesize'));
            if ($upload_max > 0 && $upload_max < $max_size) {
                $max_size = $upload_max;
            }
        }
        return $max_size;
    }

    private static function parse_size($size) {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
        $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
        if ($unit) {
            // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        }
        else {
            return round($size);
        }
    }

}