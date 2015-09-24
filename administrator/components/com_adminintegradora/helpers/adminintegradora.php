<?php
use Integralib\Integrado;

defined('_JEXEC') or die;

class AdminintegradoraHelper {
    public static function addSubmenu($vName = '') {
        JHtmlSidebar::addEntry(
            JText::_('LBL_REGISTRO_TX_BANCO'),
            'index.php?option=com_adminintegradora&view=conciliacionbancoform',
            $vName == 'conciliacionbanco'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_FACTURAS_LISTADO_TX_NI'),
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
}
