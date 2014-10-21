<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');
jimport('integradora.gettimone');

class AdminintegradoraModelComisions extends JModelList
{
	public $items;

	protected $cat;

	public function __construct($config = array())
	{
		$this->cat = new Catalogos();

		parent::__construct($config);
	}
	public function getItems()
	{
		$this->items = getFromTimOne::getComisiones();

		$this->idToNames();

		return $this->items;
	}

	private function idToNames () {
		foreach ($this->items as $key => $value) {
			$value->typeName = $this->getTypeName($value->type);
			$value->statusName = $this->getStatusName($value->status);
			$value->frequencyMsg = $this->getFrequencyMsg($value->frequencyTime);

			$this->items[$key] = $value;
		}


	}

	private function getTypeName ($value) {
		$array = $this->cat->getComisionesTypes();

		return $array[$value];
	}

	private function getStatusName ($value) {
		$array = $this->cat->getBasicStatus();

		return $array[$value];
	}

	private function getFrequencyMsg ($frequencyTime) {
		return JText::sprintf('COM_ADMININTEGRADORA_COMISIONES_FRECUENCIA', $frequencyTime);
	}

}