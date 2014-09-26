<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.view');

/**
 *
 */
class AdminintegradoraViewAdminintegradora extends JViewLegacy {

	protected $items;

	protected $pagination;

	protected $state;

	function display($tpl = null) {

		$items = $this->get('Items');

		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		$this->items = $items;

		$this->addToolBar();

		parent::display($tpl);

		$this->setDocument();
	}

	protected function addToolBar()
	{
		JToolBarHelper::title(JText::_('COM_ADMININTEGRADORA_MANAGER_TITULO'));
//		JToolBarHelper::deleteList('', 'adminintegradora.delete');
//		JToolBarHelper::editList('adminintegradora.edit');
//		JToolBarHelper::addNew('adminintegradora.add');
	}

	protected function setDocument()
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_ADMININTEGRADORA_MANAGER_TITULO'));
	}

}
