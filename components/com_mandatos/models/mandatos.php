<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.integrado');
jimport('integradora.rutas');
jimport('integradora.catalogos');

class MandatosModelMandatos extends JModelItem {

	public $data;

	/**
	 * @return mixed
     */
	function getAlta() {
		$sesion = JFactory::getSession();
		$integradoId = $sesion->get('integradoId', null, 'integrado');

		$this->data = new stdClass();
		$this->data->cliprov = new stdClass();
		$this->data->project = new stdClass();
		$this->data->product = new stdClass();
		$this->data->cliprov->count = count(getFromTimOne::getClientes($integradoId));
		$this->data->project->count = count(getFromTimOne::getProyects($integradoId));
		$this->data->product->count = count(getFromTimOne::getProducts($integradoId));

		return $this->data;
	}
}

