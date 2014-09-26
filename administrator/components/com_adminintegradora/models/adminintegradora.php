<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modeladmin');
jimport('integradora.integrado');
jimport('integradora.catalogos');
jimport('integradora.gettimone');

class AdminintegradoraModelAdminintegradora extends JModelLegacy
{
	public $items;

	protected $cat;

	public function __construct($config = array())
    {
		$this->cat = new Catalogos();

		parent::__construct($config);
	}

}