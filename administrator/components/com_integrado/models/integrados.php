<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');
jimport('integradora.catalogos');
jimport('integradora.gettimone');


class IntegradoModelIntegrados extends JModelList
{
	public function __construct($config = array())
    {   
        $config['filter_fields'] = array(
                'a.integradoId',
                'a.status'
        );
        parent::__construct($config);
	}

    public function getListQuery()
    {
        $db = JFactory::getDBO();
        $query 	= $db->getQuery(true);
		$q2		= $db->getQuery(true);
        $query
            ->select($db->quoteName(array('a.integradoId','a.status','a.pers_juridica', 'a.createdDate', 'b.razon_social', 'p.nom_comercial', 'c.name', 'p.nombre_representante')))
            ->from($db->quoteName('#__integrado', 'a'))
			->join('LEFT', $db->quoteName('#__integrado_datos_empresa', 'b') . ' ON ('. $db->quoteName('a.integradoId') . ' = ' . $db->quoteName('b.integradoId') .')' )
			->join('LEFT', $db->quoteName('#__integrado_datos_personales', 'p') . ' ON ('. $db->quoteName('a.integradoId') . ' = ' . $db->quoteName('p.integradoId') .')' )
			->join('LEFT', $db->quoteName('#__users', 'c') . ' ON ('. $db->quoteName('c.id') . ' = (' .
				$q2->select($db->quoteName('d.user_id'))
				 	->from($db->quoteName('#__integrado_users', 'd'))
				 	->where($db->quoteName('d.integradoId') . ' = ' . $db->quoteName('a.integradoId')
							.' AND '.$db->quoteName('integrado_principal').' = 1')
				 	.'))' )
			->where($db->quoteName('a.status'). ' IN (1,3,50,99)'
				.' AND '.$db->quoteName('a.integradoId').' IN (SELECT '.$db->quoteName('#__integrado_users.integradoId').' FROM '.$db->quoteName('#__integrado_users').')'
			  		)
			->order($db->escape($this->getState('list.ordering', 'a.status')).' '.
                $db->escape($this->getState('list.direction', 'ASC')));

		return $query;
    }

    public function getItems()
	{
		$items = parent::getItems();

		$items = Integralib\IntegradoHelper::filterEmptyIntegrados($items);

		return $items;
	}

    protected function populateState($ordering = null, $direction = null) {
    	parent::populateState('a.createdDate', 'desc');
	}

	public function getCatalogos() {
		$catalog = new Catalogos();

		$catalog->getPesonalidadesJuridicas();

		$catalog->getStatusSolicitud();

		return $catalog;
	}

}