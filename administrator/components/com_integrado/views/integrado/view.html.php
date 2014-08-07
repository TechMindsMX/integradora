<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.view');

/**
 * 
 */
class IntegradoViewIntegrado extends JViewLegacy {

	protected $items;

	protected $pagination;

	protected $state;
	
	function display($tpl = null) {

        $items = $this->get('Item');
		echo "<pre>";
		print_r($items);
		echo "</pre>";
		exit;
		
        $state = $this->get('State');
		
		$pagination = $this->get('Pagination');
 
                if (count($errors = $this->get('Errors'))) 
        {
                JError::raiseError(500, implode('<br />', $errors));
                return false;
        }

        $this->items = $items;
        $this->pagination = $pagination;
		
		$this->addToolBar();
		
		$this->sortDirection = $state->get('list.direction');
		$this->sortColumn = $state->get('list.ordering');
		
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
