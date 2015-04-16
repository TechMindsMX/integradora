<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modeladmin');
jimport('integradora.integrado');

class IntegradoModelIntegradoParams extends JModelAdmin{
    public $integ_id;

    public function __construct($config = array()){
		parent::__construct($config);
	}

	public function getItem($pk = null){
		$input = JFactory::getApplication()->input;
		$this->integ_id = ($input->get('integrado_id', 0, 'int') ? $input->get('integrado_id', 0, 'int') : $input->get('id', 0, 'int'));

		$integrado = new IntegradoSimple($this->integ_id);
		$item = $integrado;
        $item->comisiones = $this->getComisiones();
        $item->dataSaved = $this->checkIfDataExist($integrado);

		return $item;
	}

    private function getComisiones(){
        return getFromTimOne::getComisiones(null,true);
    }

	public function getTable($type = 'IntegradoParams', $prefix = 'IntegradoTable', $config = array()){
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true){
	}

    public function checkIfDataExist($integrado){
        $respuesta       = array();
        $comisionesInteg = getFromTimOne::getComisionesOfIntegrado($this->integ_id);

        if( (!is_null($comisionesInteg)) && (isset($integrado->integrados[0]->params->params)) ) {
            $respuesta['comisiones'] = $comisionesInteg;
            $respuesta['params'] = $integrado->integrados[0]->params->params;
        }

        return $respuesta;
    }
}