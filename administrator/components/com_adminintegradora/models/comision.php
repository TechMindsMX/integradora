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
		$this->catalogos->status = $this->catalogos->getBasicStatus();
		$this->catalogos->frequencyTimes = $this->catalogos->getComisionesFrecuencyTimes();

		return $this->catalogos;
	}

	public function getComision () {
		$params = $this->app->input->getArray (array ('comisionId' => 'int'));

		if($params['comisionId'] !== 0) {
			$this->comision = getFromTimOne::getComisionById ($params['comisionId']);
			$this->idToNames ();
		} else {
			$this->comision = null;
		}


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
		$array = $this->catalogos->getBasicStatus();

		return $array[$value];
	}

	private function getFrequencyMsg ($frequencyTime) {
		return JText::sprintf ('COM_ADMININTEGRADORA_COMISIONES_FRECUENCIA', $frequencyTime);
	}

}
