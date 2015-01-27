<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');


class IntegradoModelIntegrados extends JModelList
{
	public function __construct($config = array())
    {   
        $config['filter_fields'] = array(
                'a.integrado_id',
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
            ->select($db->quoteName(array('a.integrado_id','a.status','a.pers_juridica', 'a.createdDate', 'b.razon_social', 'c.name', 'p.nombre_representante')))
            ->from($db->quoteName('#__integrado', 'a'))
			->join('LEFT', $db->quoteName('#__integrado_datos_empresa', 'b') . ' ON ('. $db->quoteName('a.integrado_id') . ' = ' . $db->quoteName('b.integrado_id') .')' )
			->join('LEFT', $db->quoteName('#__integrado_datos_personales', 'p') . ' ON ('. $db->quoteName('a.integrado_id') . ' = ' . $db->quoteName('p.integrado_id') .')' )
			->join('LEFT', $db->quoteName('#__users', 'c') . ' ON ('. $db->quoteName('c.id') . ' = (' .
				$q2->select($db->quoteName('d.user_id'))
				 	->from($db->quoteName('#__integrado_users', 'd'))
				 	->where($db->quoteName('d.integrado_id') . ' = ' . $db->quoteName('a.integrado_id')
							.' AND '.$db->quoteName('integrado_principal').' = 1')
				 	.'))' )
			->where($db->quoteName('a.status'). ' IS NOT NULL')
			->order($db->escape($this->getState('list.ordering', 'a.status')).' '.
                $db->escape($this->getState('list.direction', 'ASC')));

		return $query;
    }
	public function getItems()
	{
		$items = parent::getItems();
var_dump($items);
		return $items;
	}
	protected function populateState($ordering = null, $direction = null) {
    	parent::populateState('a.status', 'ASC');
	}
}