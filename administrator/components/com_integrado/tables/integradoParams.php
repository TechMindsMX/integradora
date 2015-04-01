<?php

defined('_JEXEC') or die;

class IntegradoTableIntegradoParams extends JTable
{
	/**
	 * @param   JDatabaseDriver  A database connector object
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__integrado_params', 'id', $db);
	}
}
