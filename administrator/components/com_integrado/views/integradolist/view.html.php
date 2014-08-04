<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.view');

/**
 * 
 */
class IntegradoViewIntegradolist extends JViewLegacy {
	
		function display($tpl = null) {

                $items = $this->get('Items');
                $pagination = $this->get('Pagination');
 
                if (count($errors = $this->get('Errors'))) 
                {
                        JError::raiseError(500, implode('<br />', $errors));
                        return false;
                }

                $this->items = $items;
                $this->pagination = $pagination;
				
				$this->addToolBar();
 
                parent::display($tpl);
        }
		
		protected function addToolBar() 
        {
                JToolBarHelper::title(JText::_('COM_INTEGRADO_MANAGER_TITULO'));
                // JToolBarHelper::deleteList('', 'integrado.delete');
                JToolBarHelper::editList('integrado.edit');
                // JToolBarHelper::addNew('integrado.add');
        }
		
}
