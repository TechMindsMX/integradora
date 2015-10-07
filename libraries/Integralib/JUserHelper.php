<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 02-Oct-15
 * Time: 12:42 PM
 */

namespace Integralib;


class JUserHelper
{

    /**
     * @param \JUser $JUser
     *
     * #return an array of IntegradoSimple
     * @return array|null
     */
    public static function getActiveIntegrados(\JUser $JUser)
    {
        $integrados = null;
        foreach ($JUser->integrados as $integrado) {
            $int = new \IntegradoSimple($integrado->integradoId);
            if ($int->isActive()) {
                $integrados[] = $int;
            }
        }

        return $integrados;

    }
}