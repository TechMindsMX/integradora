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
	protected $integradoId;

	public function getSolicitud( ){
		$sesion = JFactory::getSession();
		$this->integradoId = $sesion->get('integradoId', null, 'integrado');

		if (!isset($this->dataModelo)) {
			$this->dataModelo = new Integrado;
			
			if( !$this->dataModelo->isValidPrincipal($this->integradoId, JFactory::getUser()->id) ){
				JFactory::getApplication()->redirect('index.php?option=com_mandatos', 'no tienes permisos para ver este elemento');
			}
			
			$this->dataModelo = new IntegradoSimple($this->integradoId);

            $status = array(1,3,50,99);
            if(isset($this->dataModelo->integrados[0]->integrado->status)) {
                if (in_array($this->dataModelo->integrados[0]->integrado->status, $status)) {
                    JFactory::getApplication()->redirect('index.php?option=com_integrado&Itemid=207', 'El status de la solicitud no permite edición');
                }
            }
		}
		$this->dataModelo->user->integradoId = $this->integradoId;
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

