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
		JToolBarHelper::title(JText::_('COM_INEGRADO_MANAGER_INTEGRADO_EDIT'));
        JToolBarHelper::custom('integrado.guarda', 'extrahello.png', 'extrahello_f2.png', 'Extra Hello', true);
		
		JToolBarHelper::save('integrado.save');
		JToolBarHelper::cancel('integrado.cancel', 'JTOOLBAR_CLOSE');
	}

}
