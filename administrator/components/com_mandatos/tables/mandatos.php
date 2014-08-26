<?php

defined('_JEXEC') or die;

class IntegradoTableIntegrado extends JTable
{
	/**
	 * @param   JDatabaseDriver  A database connector object
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__integrado', 'integrado_id', $db);
	}
}
