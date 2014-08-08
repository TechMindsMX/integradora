<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class IntegradoViewIntegrado extends JViewLegacy {

	public function display($tpl = null) {

		$form = $this -> get('Form');
		$item = $this -> get('Item');

		if (count($errors = $this -> get('Errors'))) {
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		$this -> form = $form;
		$this -> item = $item;

		$this -> addToolBar();

		parent::display($tpl);
	}

	protected function addToolBar() {
		$input = JFactory::getApplication() -> input;
		$input -> set('hidemainmenu', true);
		$isNew = ($this -> item -> integrado -> integrado_id == 0);
		JToolBarHelper::title($isNew ? JText::_('COM_INTEGRADO_MANAGER_INTEGRADO_NEW') : JText::_('COM_INEGRADO_MANAGER_INTEGRADO_EDIT'));
		JToolBarHelper::save('integrado.save');
		JToolBarHelper::cancel('integrado.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
	}

}
