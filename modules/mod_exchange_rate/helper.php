<?php

/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 09-Sep-15
 * Time: 12:07 PM
 */
class ModExchangeRateHelper
{

    /**
     * @param $params
     *
     * @return stdClass
     */
    public static function getExchangeRate($params)
    {
        $resultado = self::getData();

        if ( ! empty( $resultado )) {
            $dom = new DomDocument();
            $dom->loadXML($resultado);
            $xmlDatos = $dom->getElementsByTagName("Obs");
            if ($xmlDatos->length > 1) {
                $data = new stdClass();

                $item     = $xmlDatos->item(1);
                $date = new DateTime($item->getAttribute('TIME_PERIOD'));
                $data->fecha_tc = $date->format('d-M-Y');
                $data->tc       = $item->getAttribute('OBS_VALUE');
            }
            return $data;
        }

    }

    /**
     * @return string
     */
    private static function getData()
    {
        $resultado = '';
        $fecha_tc  = '';
        $tc        = '';
        $client    = new SoapClient(null, array (
            'location' => 'http://www.banxico.org.mx:80/DgieWSWeb/DgieWS?WSDL',
            'uri'      => 'http://DgieWSWeb/DgieWS?WSDL',
            'encoding' => 'ISO-8859-1',
            'trace'    => 1
        ));
        try {
            $resultado = $client->tiposDeCambioBanxico();

            return $resultado;
        } catch (SoapFault $exception) {

        }

        return $resultado;
    }

    public static function checkRequiredExtensionInstalled()
    {
        if (!extension_loaded('SOAP')) {
            throw new Exception('SOAP_EXTENSION_MISSING', 500);
        }
    }

}