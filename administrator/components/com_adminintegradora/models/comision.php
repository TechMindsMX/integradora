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

		parent::__construct ($config);
	}

	public function getCatalogosComisiones () {
		$this->catalogos->types = $this->catalogos->getComisionesTypes ();
		$this->catalogos->status = $this->catalogos->getComisionesStatus ();
		$this->catalogos->frequencyTimes = $this->catalogos->getComisionesFrecuencyTimes();

		return $this->catalogos;
	}

	public function getComision () {
		$params = $this->app->input->getArray (array ('id' => 'int'));
		$this->comision = getFromTimOne::getComisionById ($params['id']);

		$this->idToNames ();

		$comision[] = $this->comision;

		return $comision;
	}

	private function idToNames () {
		$this->comision->typeName = $this->getTypeName ($this->comision->type);
		$this->comision->statusName = $this->getStatusName ($this->comision->status);
		$this->comision->frequencyMsg = $this->getFrequencyMsg ($this->comision->frequencyTime);
	}

	private function getTypeName ($value) {
		$array = $this->catalogos->getComisionesTypes ();

		return $array[$value];
	}

	private function getStatusName ($value) {
		$array = $this->catalogos->getComisionesStatus ();

		return $array[$value];
	}

	private function getFrequencyMsg ($frequencyTime) {
		return JText::sprintf ('COM_ADMININTEGRADORA_COMISIONES_FRECUENCIA',
							   $frequencyTime);
	}

}
