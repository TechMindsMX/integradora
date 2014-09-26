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
			$value->frequencyTypeName = $this->getFrequencyTypeName($value->frequencyType);
			$value->statusName = $this->getStatusName($value->status);

			$this->items[$key] = $value;
		}


	}

	private function getTypeName ($value) {
		$array = $this->cat->getTypesComisiones();

		return $array[$value];
	}

	private function getFrequencyTypeName ($value) {
		$array = $this->cat->getFrequenciesComisiones();

		return $array[$value];
	}

	private function getStatusName ($value) {
		$array = $this->cat->getStatusComisiones();

		return $array[$value];
	}

}