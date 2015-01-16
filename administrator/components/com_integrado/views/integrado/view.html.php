<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class IntegradoViewIntegrado extends JViewLegacy {

	public function display($tpl = null) {

//		$form = $this -> get('Form');
		$item = $this -> get('Item');
		$verifications = $this -> get('Verifications');

		if (count($errors = $this -> get('Errors'))) {
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

//		$this->form = $form;
		$this->item = $item;
		$this->verifications = $verifications;

		$this -> addToolBar();

		parent::display($tpl);
	}

	protected function addToolBar() {
		$input = JFactory::getApplication() -> input;
		$input -> set('hidemainmenu', true);
		JToolBarHelper::title(JText::_('COM_INTEGRADO_MANAGER_INTEGRADO_EDIT'));
		
		JToolBarHelper::save('integrado.save');
		JToolBarHelper::cancel('integrado.cancel', 'JTOOLBAR_CLOSE');
	}

}
