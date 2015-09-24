<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.modellist');
jimport('integradora.integrado');
jimport('integradora.integrado');
jimport('integradora.gettimone');

/**
 * Methods supporting a list of Facturas records.
 */
class AdminintegradoraModeltxslist extends JModelList {

    public function __construct($config = array()) {

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
            ->where($db->quoteName('integradoId') . ' = '. $db->quote(INTEGRADOID_CONCENTRADORA) . ' AND ' . $db->quoteName('identified') . ' = 1');

        $db->setQuery($query);

        $results = $db->loadObjectList();

        var_dump($results);exit;
    }
}
