<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');
jimport('integradora.integrado');

class IntegradoModelIntegrado extends JModelLegacy
{
	public function __construct($config = array())
    {   
        parent::__construct($config);
	}
    public function getItem()
    {
    	$integ_id = 1;
		
    	$integrado = new ReflectionClass('Integrado');
		$item = $integrado->newInstance($integ_id);

		return $item;
    }

}