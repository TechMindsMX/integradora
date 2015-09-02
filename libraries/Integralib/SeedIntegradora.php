<?php
/**
 * Created by PhpStorm.
 * User: lutek
 * Date: 28/08/2015
 * Time: 08:34 AM
 */

namespace Integralib;


class SeedIntegradora
{
    /**
     * @param string $filename Nombre del archivo json sin extencion igual al nombre del ambiente
     */
    public static function seedIntegradora($filename = 'qaintegradora')
    {
        $url = getcwd() . '/libraries/Integralib/' . $filename . '.json';

        if (file_exists($url)) {
            $json = file_get_contents($url);
            $json = json_decode($json);

            $integradoraRFC = $json->integrado_datos_empresa->rfc;

            $db = \JFactory::getDbo();

            $query = $db->getQuery(true);
            $query->select('*')
                ->from('#__integrado_datos_empresa')
                ->where($db->quoteName('rfc') . ' = ' . $db->quote($integradoraRFC));

            $db->setQuery($query);
            $db->execute();

            try {

                if ($db->getNumRows() === 0) {
                    $db->transactionStart();

                    foreach ($json as $key => $value) {
                        if($key == 'instrumentos' || $key == 'datos_bancarios') {
                            foreach ($value as $key => $value) {
                                foreach ($value as $datos) {
                                    $datos->integradoId = $json->integrado->integradoId;
                                    $db->insertObject('#__'.$key, $datos);
                                }
                            }
                        }else{
                            $value->integradoId = $json->integrado->integradoId;;
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