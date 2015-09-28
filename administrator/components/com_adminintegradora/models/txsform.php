<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.modellist');
jimport('integradora.integrado');
jimport('integradora.integrado');
jimport('integradora.gettimone');

/**
 * Methods supporting a list of Facturas records.
 */
class AdminintegradoraModeltxsform extends JModelList {

    public $integradora;

    public function __construct($config = array()) {
        $this->integradora = new \Integralib\Integrado();
        $this->integradora = new IntegradoSimple($this->integradora->getIntegradoraUuid());

        parent::__construct($config);
    }

    public function getIntegrados(){
        $integrados = getFromTimOne::getintegrados(50);

        return $integrados;
    }

    public function getTxNoIdent()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('*')
            ->from($db->quoteName('#__txs_banco_integrado'))
            ->where($db->quoteName('identified') . ' = 0');

        $db->setQuery($query);

        return $db->loadObjectList();
    }

    public function getConfirmacion()
    {
        $post      = array('idtx'=>'INT', 'integradoId' => 'STRING');
        $input     = JFactory::getApplication()->input->getArray($post);
        $respuesta = new stdClass();
        $db        = JFactory::getDbo();
        $query     = $db->getQuery(true);

        $query->select('*')->from($db->quoteName('#__txs_banco_integrado'))->where($db->quoteName('id') . ' = ' . $db->quote($input['idtx']));
        $db->setQuery($query);
        $txInfo               = $db->loadObject();
        $respuesta->integrado = new IntegradoSimple($input['integradoId']);
        $txInfo->date         = date('d-m-Y', $txInfo->date);
        $txInfo->cuenta       = $this->integradora->getAccountData($txInfo->cuenta);
        $respuesta->txInfo    = $txInfo;

        return $respuesta;
    }
}
