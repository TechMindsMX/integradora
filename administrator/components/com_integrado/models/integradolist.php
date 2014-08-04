<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');


class IntegradoModelIntegradolist extends JModelList
{
        public function getListQuery()
        {
            $db = JFactory::getDBO();
            $query 	= $db->getQuery(true);
			$q2		= $db->getQuery(true);
            $query
                ->select($db->quoteName(array('a.integrado_id','a.status','a.pers_juridica', 'b.razon_social', 'c.name')))
                ->from($db->quoteName('#__integrado', 'a'))
				->join('LEFT', $db->quoteName('#__integrado_datos_empresa', 'b') . ' ON ('. $db->quoteName('a.integrado_id') . ' = ' . $db->quoteName('b.integrado_id') .')' )
				->join('LEFT', $db->quoteName('#__users', 'c') . ' ON ('. $db->quoteName('c.id') . ' = (' .
					$q2->select($db->quoteName('d.user_id'))
					 	->from($db->quoteName('#__integrado_users', 'd'))
					 	->where($db->quoteName('d.integrado_id') . ' = ' . $db->quoteName('a.integrado_id'))
					 	.'))' )
				->where($db->quoteName('a.status'). ' IS NOT NULL');

			return $query;
        }
		public function getItems()
		{
			$items = parent::getItems();
			
			return $items;
		}
}