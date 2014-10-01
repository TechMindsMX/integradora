<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.view');

/**
 *
 */
class AdminintegradoraViewComision extends JViewLegacy {

	public $comision;

	public $cats;

//	protected $pagination;
//
//	protected $state;

	function display($tpl = null) {

		$comision = $this->get('Comision');
		$cats = $this->get('CatalogosComisiones');

		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		$this->comision = $comision;
		$this->cats = $cats;

		$this->addToolBar();

		parent::display($tpl);

		$this->setDocument();
	}

	protected function addToolBar()
	{
		JToolBarHelper::title(JText::_('COM_ADMININTEGRADORA_MANAGER_TITULO'));
		JToolbarHelper::apply('comision.apply');
		JToolBarHelper::save('comision.save');
		JToolbarHelper::cancel('comision.cancel');
	}

	protected function setDocument()
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_ADMININTEGRADORA_MANAGER_TITULO'));
	}

}
