<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.view');

/**
 * 
 */
class ProyectosViewProyectos extends JViewLegacy {

	function display($tpl = null) {
exit('aqui');
        parent::display($tpl);
		
		$this->setDocument();
    }
	
	protected function addToolBar() 
    {
        JToolBarHelper::title(JText::_('COM_INTEGRADO_MANAGER_TITULO'));
        // JToolBarHelper::deleteList('', 'integrado.delete');
        JToolBarHelper::editList('integrado.edit');
        // JToolBarHelper::addNew('integrado.add');
    }
	
	protected function setDocument() 
    {
        $document = JFactory::getDocument();
        $document->setTitle(JText::_('COM_INTEGRADO_ADMINISTRATION'));
    }

}
