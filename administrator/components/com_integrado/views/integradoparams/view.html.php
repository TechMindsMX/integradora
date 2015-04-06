<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class IntegradoViewIntegradoParams extends JViewLegacy {

    public $item;

    public function display($tpl = null) {

        $this->item = $this -> get('Item');

		if (count($errors = $this -> get('Errors'))) {
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		$this -> addToolBar();

		parent::display($tpl);
	}

	protected function addToolBar() {
		$input = JFactory::getApplication() -> input;
		$input -> set('hidemainmenu', true);
		JToolBarHelper::title(JText::_('COM_INTEGRADO_MANAGER_INTEGRADO_EDIT'));
		
		JToolBarHelper::save('integradoparams.save');
		JToolBarHelper::cancel('integrado.cancel', 'JTOOLBAR_CLOSE');
	}

}
