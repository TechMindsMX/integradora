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
		$input = JFactory::getApplication()->input;
		$integ_id = ($input->get('integrado_id',0,'int') ? $input->get('integrado_id',0,'int') : $input->get('id',0,'int'));
		
    	$integrado = new ReflectionClass('IntegradoSimple');
		$item = $integrado->newInstance($integ_id);

		$item->catalogos = $this->getCatalogos();

		switch (intval($item->integrados[0]->integrado->status)) {
			case 0: // Nueva solicitud 0
					$validos = array(2,3,99);
				break;
			case 1: // para revision nuevamente 1
					$validos = array(2,3,99);
				break;
			case 2: // Devuelto 2
					$validos = array(1);
				break;
			case 3: // contrato 3
					$validos = array(50,99);
				break;
			case 50: // integrado 50
					$validos = array();
				break;
			case 99: // cancelada 99
					$validos = array();
				break;
			default:
					$validos = array();
				break;
		}
		$item->transicion_status = $validos;

		$item->integrados[0]->datos_personales->nacionalidad = $this->getNacionalidad($item->integrados[0]->datos_personales->nacionalidad);
		
		return $item;
    }
	public function getCatalogos() {
		$catalogos = new Catalogos;
		
		$catalogos->getStatusSolicitud();
		
		return $catalogos;
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
	function getNacionalidad($id) {
		return Integrado::getNationalityNameFromId($id);
	} 
}