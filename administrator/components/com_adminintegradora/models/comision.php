<?php
defined ('_JEXEC') or die('Restricted access');

jimport ('integradora.gettimone');

class AdminintegradoraModelComision extends JModelItem
{
	public $comision;

	public $catalogos;

	public function __construct ($config = array ()) {
		$this->app = JFactory::getApplication ();
		$this->catalogos = new Catalogos();

		$this->catalogos->triggers = $this->getCatalogoTriggers();

		parent::__construct ($config);
	}

	public function getCatalogosComisiones () {
		$this->catalogos->types = $this->catalogos->getComisionesTypes ();
		$this->catalogos->status = $this->catalogos->getBasicStatus();
		$this->catalogos->frequencyTimes = $this->catalogos->getComisionesFrecuencyTimes();

		return $this->catalogos;
	}

	public function getComision () {
		$params = $this->app->input->getArray (array ('comisionId' => 'int'));

		if($params['comisionId'] !== 0) {
			$comision = getFromTimOne::getComisiones($params['comisionId']);
			$this->comision = $comision[0];
			$this->idToNames ();
		} else {
			$this->comision = null;
		}

		return $this->comision;
	}

	private function idToNames () {
		$this->comision->typeName = $this->getTypeName ($this->comision->type);
		$this->comision->statusName = $this->getStatusName ($this->comision->status);
		$this->comision->frequencyMsg = $this->getFrequencyMsg ($this->comision->frequencyTimes);
	}

	private function getTypeName ($value) {
		$array = $this->catalogos->getComisionesTypes ();

		return $array[$value];
	}

	private function getStatusName ($value) {
		$array = $this->catalogos->getBasicStatus();

		return $array[$value];
	}

	private function getFrequencyMsg ($frequencyTimes) {
		return JText::sprintf ('COM_ADMININTEGRADORA_COMISIONES_FRECUENCIA', $frequencyTimes);
	}

	private function getCatalogoTriggers( ){
		return getFromTimOne::getTriggersComisiones();
	}

}
