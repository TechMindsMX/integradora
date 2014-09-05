<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.integrado');
jimport('integradora.rutas');
jimport('integradora.catalogos');

/**
 * Modelo de datos para formulario de solicitud de alta de integrado
 */
class IntegradoModelSolicitud extends JModelItem {
	
	protected $dataModelo;
	
	public function getSolicitud($integradoId = null){
		$app 			= JFactory::getApplication();
		$input 			= $app->input;
		$data			= $input->getArray();
		$integradoId	= isset($data['integradoId'])?$data['integradoId']:null;

		if (!isset($this->dataModelo)) {
			$this->dataModelo = new Integrado;
			
			if( !$this->dataModelo->isValid($integradoId, JFactory::getUser()->id) ){
				JFactory::getApplication()->redirect('index.php/component/mandatos', 'no tienes permisos para ver este elemento');
			}
			
			$integrado = new ReflectionClass('integradoSimple');
			$this->dataModelo = $integrado->newInstance($integradoId);
		}
		$this->dataModelo->user->integradoId = $integradoId;
		$this->dataModelo->integrados = $this->dataModelo->integrados[0];

		return $this->dataModelo;
	}
	
	public function getCatalogos() {
		$catalogos = new Catalogos;
		
		$catalogos->getNacionalidades();
		$catalogos->getEstados();
		$catalogos->getBancos();
		
		return $catalogos;
	}
}

