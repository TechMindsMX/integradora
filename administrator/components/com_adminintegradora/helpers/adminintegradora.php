<?php
use Integralib\Integrado;
use Integralib\Txs;

defined('_JEXEC') or die;

class AdminintegradoraHelper {
    public static function addSubmenu($vName = '') {
        JHtmlSidebar::addEntry(
            JText::_('LBL_REGISTRO_TX_BANCO'),
            'index.php?option=com_adminintegradora&view=conciliacionbancoform',
            $vName == 'conciliacionbanco'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_TXS_CONCILIACION_TITLE'),
            'index.php?option=com_adminintegradora&view=txslist',
            $vName == 'listadotxni'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_FACTURAS_LISTADO_ODD'),
            'index.php?option=com_adminintegradora&view=oddlist',
            $vName == 'listadoODD'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_FACTURAS_LISTADO_ODV'),
            'index.php?option=com_adminintegradora&view=odvlist',
            $vName == 'listadoODV'
        );

        JHtmlSidebar::addEntry(
            JText::_('BTN_GOBACK'),
            'index.php?option=com_adminintegradora',
            $vName == 'Regresar'
        );

    }

    public static function getActions() {
        $user = JFactory::getUser();
        $result = new JObject;

        $assetName = 'com_facturas';

        $actions = array(
            'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
        );

        foreach ($actions as $action) {
            $result->set($action, $user->authorise($action, $assetName));
        }

        return $result;
    }

    /**
     * @param Integrado $integradora
     * @return mixed
     */
    public static function getBancosIntegradora(Integrado $integradora)
    {
        $integradora->getIntegradora();

        return $integradora->integrado->integrados[0]->datos_bancarios;
    }

    public static function getBanknameAccount($bancos, $idCuenta)
    {
        foreach ( $bancos as $banco ) {
            if ($banco->datosBan_id == $idCuenta) {
                $bankName = $banco->bankName;
                $bankAccount = substr(!empty($banco->banco_cuenta) ? $banco->banco_cuenta : $banco->banco_clabe, -4, 4);
            }
        }

        return $bankName.' - '.$bankAccount;
    }

    public static function getTransacciones($orden){
        $db         = JFactory::getDbo();
        $query      = $db->getQuery(true);

        $query->select( 'tm.*, bi.referencia, bi.amount, bi.cuenta' )
            ->from($db->quoteName('#__txs_timone_mandato', 'tm'))
            ->join('LEFT', $db->quoteName('#__txs_banco_integrado', 'bi') . ' ON (bi.id = (SELECT rel.id_txs_banco FROM flpmu_txs_banco_timone_relation AS rel WHERE rel.id_txs_timone = tm.id))');

        try{
            $db->setQuery($query);
            $result = $db->loadObjectList();
        }catch (Exception $e){
            var_dump($e);
        }

        foreach ($result as $tx) {
            $tx->balance = self::getTxBalance($tx);
            if( (($orden->integradoId == $tx->integradoId)) && ($tx->balance > 0) ) {
                $return[$tx->id] = $tx;
            }
        }

        return $return;
    }

    /**
     * @param $trans
     * @return float|int
     */
    private static function getTxBalance( $trans ) {
        $txs = new Txs();

        return $txs->calculateBalance($trans);
    }
}
