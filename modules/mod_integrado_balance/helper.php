<?php

/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 09-Sep-15
 * Time: 12:07 PM
 */
class ModIntegradoBalanceHelper
{
    /**
     * @return array
     */
    public static function getBalances()
    {
        $sesion = JFactory::getSession();
        $integradoId = $sesion->get('integradoId', null, 'integrado');

        $integrado = \Integralib\IntFactory::getIntegrdoSimple( $integradoId );

        $balances = [
            'total' => $integrado->getBalance(),
            'blocked' => $integrado->getBlockedBalance(),
        ];

        $balances['available'] = $balances['total'] - $balances['blocked'];

        return $balances;
    }

}