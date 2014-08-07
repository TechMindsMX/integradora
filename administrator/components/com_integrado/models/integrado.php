<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modeladmin');
jimport('integradora.integrado');

class IntegradoModelIntegrado extends JModelAdmin
{
	public function __construct($config = array())
    {   
        parent::__construct($config);
	}
    public function getItem()
    {
    	var_dump($this);
    	$integ_id = 1;
		
    	$integrado = new ReflectionClass('Integrado');
		$item = $integrado->newInstance($integ_id);

		return $item;
    }

    public function getTable($type = 'Integrado', $prefix = 'IntegradoTable', $config = array()) 
    {
        return JTable::getInstance($type, $prefix, $config);
    }

   public function getForm($data = array(), $loadData = true) 
    {
        $form = $this->loadForm('com_integrado.integrado', 'integrado',
                                array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) 
        {
            return false;
        }
        return $form;
	}

	protected function loadFormData() 
    {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_integrado.edit.integrado.data', array());
        if (empty($data)) 
        {
            $data = $this->getItem();
        }
        return $data;
    }		
}